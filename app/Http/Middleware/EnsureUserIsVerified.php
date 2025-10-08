<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->verified) {
            // Asegurémonos de registrar para fines de depuración
            \Log::info('Usuario no verificado intentando acceder a: ' . $request->url(), [
                'user_id' => Auth::id(),
                'route_name' => $request->route()->getName()
            ]);

            // Lista de rutas permitidas
            $allowedRoutes = [
                'profile.document-submission', 
                'profile.upload-document',
                'logout',
                'login',
                'register',
                'password.request',
                'password.email',
                'password.reset',
                'password.update',
            ];

            // Si la ruta no está en la lista de permitidas, redirigir
            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('profile.document-submission')
                    ->with('warning', 'Debe completar la verificación de documentos para acceder a esta sección.');
            }
        }

        return $next($request);
    }
}
