<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DemoRequest;
use App\Jobs\SendDemoEmails;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class LandingController extends Controller
{
    /**
     * Handle demo request from landing page.
     * Does NOT create a user account - only stores contact info in demo_requests.
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

            // Save contact info directly in demo_requests (no user account created)
            $demoRequest = DemoRequest::create([
                'name'         => $validated['name'],
                'email'        => $validated['email'],
                'phone'        => $validated['phone'],
                'company_name' => $validated['companyName'],
                'company_type' => $validated['companyType'],
                'additional_info' => $validated['additionalInfo'] ?? null,
                'status'       => DemoRequest::STATUS_PENDING,
                'requested_at' => now(),
            ]);

            // Dispatch email job to queue
            SendDemoEmails::dispatch($demoRequest);

            return response()->json([
                'success' => true,
                'message' => 'Demo request submitted successfully',
            ], 201)->header('Access-Control-Allow-Origin', 'https://pickntruck.com')
                ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type');

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
