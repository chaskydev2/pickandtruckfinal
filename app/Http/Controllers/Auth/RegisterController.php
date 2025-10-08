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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'empresa_nombre' => ['required', 'string', 'max:255'],
            'empresa_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'empresa_descripcion' => ['nullable', 'string'],
            'empresa_telefono' => ['nullable', 'string', 'max:20'],
            'empresa_direccion' => ['nullable', 'string', 'max:255'],
            'empresa_sitio_web' => ['nullable', 'string', 'max:255'],
        ], [
            // Mensajes personalizados en español
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'Debe ingresar un correo electrónico válido',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'empresa_nombre.required' => 'El nombre de la empresa es obligatorio',
            'empresa_logo.image' => 'El logo debe ser una imagen',
            'empresa_logo.mimes' => 'El logo debe ser un archivo de tipo: jpeg, png, jpg, gif',
            'empresa_logo.max' => 'El logo no debe ser mayor a 2MB',
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
        // Crear el usuario con estado 'Activo' por defecto
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'estado' => 'Activo',
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

        // Procesar la empresa después de crear el usuario
        try {
            $logoUrl = null;
            
            // Procesar el logo si se ha proporcionado
            if ($request->hasFile('empresa_logo') && $request->file('empresa_logo')->isValid()) {
                $file = $request->file('empresa_logo');
                $fileName = uniqid() . '_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Directorio para logos de empresas
                $directory = 'empresas/logos';
                $publicPath = public_path($directory);
                
                // Asegurar que el directorio existe
                if (!file_exists($publicPath)) {
                    if (!mkdir($publicPath, 0755, true)) {
                        Log::error("No se pudo crear el directorio: {$publicPath}");
                    }
                }
                
                // Mover el archivo al directorio público
                $file->move($publicPath, $fileName);
                
                // URL completa para el logo
                //$baseUrl = 'https://app.pickntruck.com/';
                $baseUrl = config('app.url') . '/';
                $logoUrl = $directory . '/' . $fileName;
            }
            
            // Crear la empresa asociada al usuario
            Empresa::create([
                'user_id' => $user->id,
                'nombre' => $request->empresa_nombre,
                'logo' => $logoUrl,
                'descripcion' => $request->empresa_descripcion,
                'telefono' => $request->empresa_telefono,
                'direccion' => $request->empresa_direccion,
                'sitio_web' => $request->empresa_sitio_web,
                'verificada' => false,
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error al crear empresa en el registro: " . $e->getMessage());
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