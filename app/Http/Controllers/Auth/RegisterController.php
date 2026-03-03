<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Empresa;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    // ...existing code...

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:forwarder,carrier'],
            // Campos de empresa ahora requeridos para todos
            'company_name' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
        ], [
            // Mensajes personalizados en español
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'Debe ingresar un correo electrónico válido',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'phone.required' => 'El número de teléfono es obligatorio',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'role.required' => 'Debe seleccionar un tipo de cuenta',
            'role.in' => 'El tipo de cuenta seleccionado no es válido',
            'company_name.required' => 'El nombre de la empresa es obligatorio',
            'country.required' => 'El país es obligatorio',
            'city.required' => 'La ciudad es obligatoria',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Crear el usuario con estado 'Activo' y el rol seleccionado
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'estado' => 'Activo',
            'company_name' => $data['company_name'],
            'country' => $data['country'],
            'city' => $data['city'],
        ]);
        
        return $user;
    }
    
    /**
     * Handle a registration request for the application.
     * Sobrescribimos este método para poder procesar el archivo y crear la empresa.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        // Procesar la empresa después de crear el usuario (solo si es carrier)
        if ($request->role === 'carrier') {
            try {
                // Crear la empresa asociada al usuario
                Empresa::create([
                    'user_id'    => $user->id,
                    'nombre'     => $request->company_name,
                    'logo'       => null,
                    'descripcion'=> null,
                    'telefono'   => $request->phone, // Copiado automáticamente del registro
                    'direccion'  => $request->city . ', ' . $request->country,
                    'sitio_web'  => null,
                    'verificada' => false,
                ]);
                
                Log::info("Empresa creada exitosamente para el usuario: {$user->id}");
                
            } catch (\Exception $e) {
                Log::error("Error al crear empresa en el registro: " . $e->getMessage());
                Log::error("Stack trace: " . $e->getTraceAsString());
            }
        }

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 201)
                    : redirect($this->redirectPath());
    }
}