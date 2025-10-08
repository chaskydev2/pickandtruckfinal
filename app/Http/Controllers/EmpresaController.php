<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EmpresaController extends Controller
{
    /**
     * Mostrar el perfil de la empresa del usuario.
     */
    public function show(Request $request)
    {
        try {
            Log::info('Método show de EmpresaController ejecutado');
            
            $user = Auth::user();
            
            if (!$user) {
                Log::error('Usuario no autenticado intentando acceder a EmpresaController@show');
                return redirect()->route('login');
            }
            
            // Comprobar si la empresa existe
            $empresa = $user->empresa;
            
            if (!$empresa) {
                // Crear una empresa básica para evitar errores
                Log::info("No se encontró empresa para el usuario {$user->id}, creando empresa básica");
                $empresa = new Empresa();
                $empresa->nombre = $user->name . ' (Empresa)';
                $empresa->user_id = $user->id;
                $empresa->save();
                
                Log::info("Empresa temporal creada con ID {$empresa->id}");
                
                // Recargar la relación
                $user->load('empresa');
                $empresa = $user->empresa;
                
                // Redireccionar al formulario de edición con un mensaje
                return redirect()->route('empresas.edit')
                    ->with('warning', 'No se encontró información de tu empresa. Por favor, completa el perfil.');
            }

            Log::info("Mostrando empresa {$empresa->id} para usuario {$user->id}");
            return view('empresas.show', compact('empresa'));
        } catch (\Exception $e) {
            Log::error("Error en show: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            // En vez de redireccionar al home, vamos al formulario de edición
            return redirect()->route('empresas.edit')
                ->with('error', 'Ocurrió un error al mostrar el perfil de empresa: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar el formulario para crear/editar la empresa del usuario.
     */
    public function edit()
    {
        $user = Auth::user();
        $empresa = $user->empresa;

        return view('empresas.edit', compact('empresa'));
    }

    /**
     * Actualizar o crear la empresa del usuario.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'descripcion' => 'nullable|string',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'sitio_web' => 'nullable|string|max:255',
        ]);

        // Procesar el logo si se subió uno nuevo
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            try {
                $file = $request->file('logo');
                $fileName = uniqid() . '_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Directorio para logos de empresas
                $directory = 'empresas/logos';
                $publicPath = public_path($directory);
                
                // Asegurar que el directorio existe
                if (!file_exists($publicPath)) {
                    if (!mkdir($publicPath, 0755, true)) {
                        Log::error("No se pudo crear el directorio: {$publicPath}");
                        return back()->with('error', 'Error al crear directorio para logos.');
                    }
                }
                
                // Mover el archivo al directorio público
                $file->move($publicPath, $fileName);
                
                // URL completa para el logo
                $baseUrl = url('/') . '/';
                $logoUrl = $baseUrl . $directory . '/' . $fileName;
                
                $validatedData['logo'] = $logoUrl;
                
                // Eliminar logo anterior si existe
                if ($user->empresa && $user->empresa->logo) {
                    $oldLogo = $user->empresa->logo;
                    if (strpos($oldLogo, url('/')) === 0) {
                        $oldLogoPath = str_replace(url('/') . '/', '', $oldLogo);
                        if (file_exists(public_path($oldLogoPath))) {
                            unlink(public_path($oldLogoPath));
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error al subir el logo: " . $e->getMessage());
                return back()->with('error', 'Error al subir el logo: ' . $e->getMessage());
            }
        }

        // Actualizar o crear la empresa
        try {
            if ($user->empresa) {
                $user->empresa->update($validatedData);
                Log::info("Empresa actualizada para el usuario: {$user->id}");
            } else {
                $validatedData['user_id'] = $user->id;
                $empresa = Empresa::create($validatedData);
                Log::info("Empresa creada para el usuario: {$user->id}");
            }
            
            return redirect()->route('empresas.show')->with('success', 'Información de la empresa actualizada correctamente.');
        } catch (\Exception $e) {
            Log::error("Error al guardar empresa: " . $e->getMessage());
            return back()->with('error', 'Error al guardar la información: ' . $e->getMessage());
        }
    }
}
