<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * POST /api/auth/forgot-password
     * Body: { "email": "user@example.com" }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Envía el email con el enlace (usa la notificación por defecto o la personalizada)
        $status = Password::sendResetLink([
            'email' => $validated['email'],
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => __($status),
            ], 200);
        }

        // Si el correo no existe o falla el envío
        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
