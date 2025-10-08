<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->estado === 'Bloqueado') {
            // Guardar email antes de cerrar sesión / revocar token
            $userEmail = Auth::user()->email;

            if ($request->expectsJson() || $request->is('api/*')) {
                // 🔐 Modo API (Sanctum token): revocar token actual si existe
                $token = $request->user()?->currentAccessToken();
                if ($token) {
                    $token->delete();
                }

                // Email de contacto admin
                $admin = \App\Models\Administrator::where('is_active', true)->first();
                $adminEmail = $admin?->email ?? 'soporte@pickutruck.com';

                return response()->json([
                    'success'       => false,
                    'status'        => 'blocked',
                    'message'       => 'Su cuenta ha sido bloqueada. Por favor, contacte al administrador en: ' . $adminEmail,
                    'contact_email' => $adminEmail,
                    'subject'       => 'Cuenta bloqueada',
                    'body'          => "Por favor, necesito ayuda con mi cuenta bloqueada.\n\nUsuario: {$userEmail}",
                ], 403);
            }

            // 🌐 Modo WEB (sesión)
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Su cuenta ha sido bloqueada. Por favor, contacte al administrador.',
            ])->onlyInput('email');
        }

        return $next($request);
    }
}
