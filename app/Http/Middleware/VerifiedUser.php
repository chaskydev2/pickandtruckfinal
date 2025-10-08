<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifiedUser
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->verified) {
            \Log::info('Acceso denegado: usuario no verificado', [
                'user_id' => Auth::id(),
                'path' => $request->path()
            ]);
            
            return redirect()->route('profile.document-submission')
                ->with('warning', 'Su cuenta debe ser verificada para acceder a esta sección.');
        }

        return $next($request);
    }
}
