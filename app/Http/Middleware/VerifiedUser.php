<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifiedUser
{
    public function handle(Request $request, Closure $next)
    {
        // Permitir rutas de document-submission y upload sin verificación
        if ($request->is('profile/document-submission') ||
            $request->is('profile/documents/upload') ||
            $request->is('profile/check-document-status')) {
            return $next($request);
        }
        
        if (Auth::check() && !Auth::user()->verified) {
            return redirect()->route('profile.document-submission')
                ->with('warning', 'Su cuenta debe ser verificada para acceder a esta sección.');
        }

        return $next($request);
    }
}
