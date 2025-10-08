<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Las credenciales proporcionadas no son correctas.'
            ], 401);
        }

        // Eliminar todos los tokens anteriores del usuario
    $user->tokens()->delete();

    // Crear un nuevo token
    $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'verified' => $user->email_verified_at !== null,
            ],
            'token' => $token
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        // Verificar si hay un token en la solicitud
        $token = $request->bearerToken();
        
        // Si hay un token, intentar eliminarlo
        if ($token) {
            try {
                // Buscar y eliminar el token actual
                $user = $request->user();
                if ($user) {
                    $user->currentAccessToken()->delete();
                } else {
                    // Si no se puede obtener el usuario del token, intentar eliminarlo directamente
                    $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                    if ($tokenModel) {
                        $tokenModel->delete();
                    }
                }
            } catch (\Exception $e) {
                // Log del error pero continuar con la respuesta de éxito
                \Log::error('Error al eliminar token: ' . $e->getMessage());
            }
        }
        
        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }
}


