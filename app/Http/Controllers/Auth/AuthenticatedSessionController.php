<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;  // Agregar esta línea
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Obtener el usuario autenticado
        $user = $request->user();
        
        // Verificar si el usuario está bloqueado
        if ($user->estado === 'Bloqueado') {
            Auth::logout();
            // Obtener el correo del administrador activo
            $admin = \App\Models\Administrator::where('is_active', true)->first();
            $adminEmail = $admin ? $admin->email : 'soporte@pickutruck.com';
            
            // Crear el enlace de contacto
            $contactLink = route('contact.admin', [
                'email' => $user->email,
                'admin_email' => $adminEmail
            ]);
            
            // Redirigir a una ruta que mostrará el mensaje con el enlace
            return redirect()->route('account.blocked', [
                'email' => $user->email,
                'admin_email' => $adminEmail
            ]);
        }
        
        // Verificar si el usuario tiene detalles existentes
        if ($user->userDetail) {
            // Actualizar la última hora de inicio de sesión
            $user->last_login_at = now();
            $user->save();
        } else {
            // Crear el detalle del usuario si no existe
            // Eliminado, ya que no se utiliza
        }

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
