<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DemoRequest;
use App\Mail\DemoRequestReceived;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class LandingController extends Controller
{
    /**
     * Handle demo request from landing page.
     */
    public function storeDemoRequest(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
                'phone' => ['required', 'string', 'max:20'],
                'companyName' => ['required', 'string', 'max:255'],
                'companyType' => ['required', 'string', 'in:forwarder,trucking'],
                'additionalInfo' => ['nullable', 'string', 'max:1000'],
            ]);

            // Map company type to role
            $role = $validated['companyType'] === 'forwarder' ? User::ROLE_FORWARDER : User::ROLE_CARRIER;

            // Check if user already exists
            $existingUser = User::where('email', $validated['email'])->first();
            
            if ($existingUser) {
                // Create demo request for existing user
                $demoRequest = DemoRequest::create([
                    'user_id' => $existingUser->id,
                    'additional_info' => $validated['additionalInfo'] ?? null,
                    'status' => DemoRequest::STATUS_PENDING,
                    'requested_at' => now(),
                ]);

                // Respond immediately
                $response = response()->json([
                    'success' => true,
                    'message' => 'Demo request submitted successfully',
                    'user_id' => $existingUser->id,
                ], 200);

                // Send emails after response (non-blocking)
                register_shutdown_function(function() use ($existingUser, $demoRequest) {
                    try {
                        Mail::to($existingUser->email)->send(new DemoRequestReceived($existingUser, $demoRequest));
                        Log::info('Demo request email sent successfully to: ' . $existingUser->email);
                    } catch (\Exception $e) {
                        Log::error('Failed to send demo request email', [
                            'email' => $existingUser->email,
                            'error' => $e->getMessage(),
                        ]);
                    }
                });

                return $response;
            }

            // Create new user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make(bin2hex(random_bytes(16))), // Random password
                'role' => $role,
                'company_name' => $validated['companyName'],
                'verified' => false,
                'estado' => 'Activo',
            ]);

            // Create demo request
            $demoRequest = DemoRequest::create([
                'user_id' => $user->id,
                'additional_info' => $validated['additionalInfo'] ?? null,
                'status' => DemoRequest::STATUS_PENDING,
                'requested_at' => now(),
            ]);

            // Respond immediately
            $response = response()->json([
                'success' => true,
                'message' => 'Demo request submitted successfully',
                'user_id' => $user->id,
            ], 201);

            // Send emails after response (non-blocking)
            register_shutdown_function(function() use ($user, $demoRequest, $role, $validated) {
                // Send email notification to user
                try {
                    Mail::to($user->email)->send(new DemoRequestReceived($user, $demoRequest));
                    Log::info('Demo request email sent to user: ' . $user->email);
                } catch (\Exception $e) {
                    Log::error('Failed to send demo request email to user', [
                        'email' => $user->email,
                        'error' => $e->getMessage(),
                    ]);
                }

                // Send notification to support
                try {
                    Mail::to(config('mail.from.address', 'soporte@pickntruck.com'))->send(
                        new \App\Mail\SupportNotification('Nueva Solicitud de Demo', [
                            'Nombre' => $user->name,
                            'Empresa' => $user->company_name,
                            'Email' => $user->email,
                            'Teléfono' => $user->phone,
                            'Tipo' => $role === User::ROLE_FORWARDER ? 'Forwarder' : 'Carrier',
                            'Información Adicional' => $validated['additionalInfo'] ?? 'N/A',
                        ])
                    );
                    Log::info('Demo request notification sent to support');
                } catch (\Exception $e) {
                    Log::error('Failed to send demo request notification to support', [
                        'error' => $e->getMessage(),
                    ]);
                }
            });

            return $response;

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Demo request error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}
