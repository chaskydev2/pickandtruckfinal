<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Administrator;

class AccountController extends Controller
{
    /**
     * Mostrar la página de cuenta bloqueada
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function showBlocked(Request $request)
    {
        $email = $request->query('email');
        $adminEmail = $request->query('admin_email');
        
        // Si no se proporcionó el correo del administrador, intentar obtenerlo de la base de datos
        if (!$adminEmail) {
            $admin = Administrator::where('is_active', true)->first();
            $adminEmail = $admin ? $admin->email : 'soporte@pickutruck.com';
        }
        
        $contactUrl = route('contact.admin', [
            'email' => $email,
            'admin_email' => $adminEmail
        ]);
        
        return view('auth.blocked', [
            'email' => $email,
            'adminEmail' => $adminEmail,
            'contactUrl' => $contactUrl
        ]);
    }
    
    /**
     * Redirigir al cliente de correo con los datos del administrador
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function contactAdmin(Request $request)
    {
        $email = $request->query('email');
        $adminEmail = $request->query('admin_email');
        
        $mailto = "mailto:{$adminEmail}?subject=Cuenta%20bloqueada&body=Por%20favor,%20necesito%20ayuda%20con%20mi%20cuenta%20bloqueada.%0A%0AUsuario:%20" . urlencode($email);
        
        return redirect()->away($mailto);
    }
}
