<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Membership;
use App\Jobs\SendMembershipEmails;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class MembershipController extends Controller
{
    /**
     * Handle membership registration from landing page.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'firstName' => ['required', 'string', 'max:255'],
                'lastName' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
                'phone' => ['required', 'string', 'max:20'],
                'password' => ['required', 'confirmed', Password::min(8)],
                'companyName' => ['required', 'string', 'max:255'],
                'companyType' => ['required', 'string', 'in:forwarder,trucking'],
                'country' => ['required', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:255'],
                'membershipTier' => ['required', 'string', 'in:pioneros,visionarios,conservadores'],
                'billingCycle' => ['required', 'string', 'in:monthly,annually'],
                'paymentProof' => ['nullable', 'file', 'mimes:jpg,jpeg,pdf', 'max:5120'], // 5MB max
            ]);

            // Map company type to role
            $role = $validated['companyType'] === 'forwarder' ? User::ROLE_FORWARDER : User::ROLE_CARRIER;
            
            // Create user
            $user = User::create([
                'name' => $validated['firstName'] . ' ' . $validated['lastName'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => $role,
                'company_name' => $validated['companyName'],
                'country' => $validated['country'],
                'city' => $validated['city'],
                'verified' => false,
                'estado' => 'Activo',
            ]);

            // Calculate price
            $price = $this->calculatePrice($validated['membershipTier'], $validated['billingCycle']);
            
            // Determine payment method
            $paymentMethod = $validated['membershipTier'] === 'pioneros' ? Membership::PAYMENT_QR : Membership::PAYMENT_CRYPTO;

            // Handle payment proof upload
            $paymentProofPath = null;
            if ($request->hasFile('paymentProof')) {
                $paymentProofPath = $request->file('paymentProof')->store('membership_proofs', 'public');
            }

            // Create membership
            $membership = Membership::create([
                'user_id' => $user->id,
                'tier' => $validated['membershipTier'],
                'billing_cycle' => $validated['billingCycle'],
                'price_paid' => $price,
                'status' => Membership::STATUS_PENDING,
                'payment_method' => $paymentMethod,
                'payment_proof_path' => $paymentProofPath,
            ]);

            // Prepare notification data
            $notificationData = [
                'Nombre' => $user->name,
                'Email' => $user->email,
                'Empresa' => $user->company_name,
                'Tier' => ucfirst($validated['membershipTier']),
                'Ciclo' => $validated['billingCycle'] === 'monthly' ? 'Mensual' : 'Anual',
                'Monto' => '$' . number_format($price, 2),
                'Método de Pago' => $paymentMethod === 'qr' ? 'QR' : 'Crypto',
                'Comprobante' => $paymentProofPath ? 'Subido' : 'No subido',
            ];

            // Dispatch email job to queue
            SendMembershipEmails::dispatch($user, $membership, $notificationData);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'user_id' => $user->id,
                'membership_id' => $membership->id,
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
            Log::error('Membership registration error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your registration',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Calculate membership price based on tier and billing cycle.
     */
    private function calculatePrice(string $tier, string $billingCycle): ?float
    {
        $prices = [
            'pioneros' => ['monthly' => 80, 'annually' => null], // 3333 BOB (handled separately)
            'visionarios' => ['monthly' => 80, 'annually' => 808],
            'conservadores' => ['monthly' => 99, 'annually' => 999],
        ];

        return $prices[$tier][$billingCycle] ?? null;
    }
}
