<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Empresa;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone'        => ['required', 'string', 'max:20'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
            'role'         => ['required', 'string', 'in:' . User::ROLE_FORWARDER . ',' . User::ROLE_CARRIER],
            'company_name' => ['required', 'string', 'max:255'],
            'country'      => ['required', 'string', 'max:255'],
            'city'         => ['required', 'string', 'max:255'],
            'terms'        => ['accepted'],
        ], [
            'terms.accepted' => 'Debes aceptar los Términos y Condiciones para continuar.',
        ]);

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
            'estado'       => 'Activo',
            'company_name' => $request->company_name,
            'country'      => $request->country,
            'city'         => $request->city,
        ]);

        // Crear empresa asociada automáticamente
        try {
            Empresa::create([
                'user_id'    => $user->id,
                'nombre'     => $request->company_name,
                'telefono'   => $request->phone,
                'direccion'  => $request->city . ', ' . $request->country,
                'verificada' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al crear empresa en registro: ' . $e->getMessage());
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
