<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view (API: not available, error 405).
     */
    public function show(): JsonResponse
    {
        return response()->json(['error' => 'No disponible en API.'], 405);
    }

    /**
     * Confirm the user's password.
     */
    public function store(Request $request): JsonResponse
    {
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            return response()->json([
                'errors' => ['password' => [__('auth.password')]]
            ], 422);
        }
        $request->session()->put('auth.password_confirmed_at', time());
        return response()->json(['message' => 'Contraseña confirmada correctamente.']);
    }
}

