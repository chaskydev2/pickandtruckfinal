<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\OfertaCarga;
use App\Models\OfertaRuta;
use App\Models\Bid;
use App\Models\RequiredDocument;
use App\Models\UserDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function show(): View
    {
        $user = Auth::user();
        $ofertasCarga = $user->ofertasCarga()->with(['cargoType', 'bids'])->latest()->get();
        $ofertasRuta = $user->ofertasRuta()->with(['truckType', 'bids'])->latest()->get();
        $bids = $user->bids()->with('bideable')->latest()->get();

        return view('profile.show', compact('user', 'ofertasCarga', 'ofertasRuta', 'bids'));
    }

    public function documents(): View
    {
        $user = Auth::user();
        $requiredDocuments = RequiredDocument::where('active', true)->get();
        $userDocuments = $user->documents()->with('requiredDocument')->get();
        
        return view('profile.documents', compact('requiredDocuments', 'userDocuments'));
    }

    public function uploadDocument(Request $request): RedirectResponse
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'document_id' => 'required|exists:required_documents,id',
                'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
            ]);

            $user = Auth::user();
            $file = $request->file('document');
            
            if (!$file->isValid()) {
                DB::rollBack();
                return back()->with('error', 'El archivo no es válido, por favor inténtelo de nuevo.');
            }
            
            // Generar un nombre de archivo único para evitar colisiones
            $fileName = uniqid() . '_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Directorio dentro de carpeta pública
            $directory = 'documents/' . $user->id;
            $publicPath = public_path($directory);
            
            // Log para debugging
            Log::info("Intentando guardar documento en: {$publicPath}");
            
            // Asegurar que el directorio existe
            if (!file_exists($publicPath)) {
                if (!mkdir($publicPath, 0755, true)) {
                    Log::error("No se pudo crear el directorio: {$publicPath}");
                    DB::rollBack();
                    return back()->with('error', 'Error al crear directorio para documentos.');
                }
                Log::info("Directorio creado: {$publicPath}");
            }
            
            // Guardar una copia del archivo también en storage por seguridad
            $storagePath = null;
            try {
                $storagePath = Storage::disk('public')->putFileAs(
                    'documents/' . $user->id,
                    $file,
                    $fileName
                );
                Log::info("Archivo guardado en storage: {$storagePath}");
            } catch (\Exception $e) {
                Log::warning("No se pudo guardar copia en storage: " . $e->getMessage());
                // Continuamos aunque falle el storage ya que el público es el importante
            }
            
            // Mover el archivo directamente a la carpeta pública (no storage)
            try {
                if (!$file->move($publicPath, $fileName)) {
                    throw new \Exception("Error al mover archivo");
                }
                Log::info("Archivo movido a carpeta pública: {$publicPath}/{$fileName}");
            } catch (\Exception $e) {
                Log::error("Error al mover archivo a carpeta pública: " . $e->getMessage());
                DB::rollBack();
                return back()->with('error', 'Error al guardar el documento. Por favor, inténtelo de nuevo.');
            }
            
            // Guardar la URL completa para acceso directo externo
            //$baseUrl = 'https://app.pickntruck.com/';
            $baseUrl = config('app.url') . '/';
            $relativePath = $directory . '/' . $fileName;
            $fullUrl = $relativePath;
            
            Log::info("URL completa para BD: {$fullUrl}");
            
            // Verificar documento existente para debug
            $existingDoc = UserDocument::where([
                'user_id' => $user->id,
                'required_document_id' => $request->document_id
            ])->first();
            
            if ($existingDoc) {
                Log::info("Actualizando documento existente ID: {$existingDoc->id}");
            } else {
                Log::info("Creando nuevo registro de documento");
            }
            
            // Actualizar o crear el registro del documento CON DEBUG
            try {
                $userDoc = UserDocument::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'required_document_id' => $request->document_id,
                    ],
                    [
                        'file_path' => $fullUrl,
                        'status' => 'pendiente'
                    ]
                );
                
                Log::info("Documento registrado en BD: ID {$userDoc->id}, Path: {$userDoc->file_path}");
            } catch (\Exception $e) {
                Log::error("Error al guardar en BD: " . $e->getMessage());
                Log::error("SQL: " . $e->getTraceAsString());
                DB::rollBack();
                return back()->with('error', 'Error al registrar el documento en la base de datos.');
            }

            // Verificar que el registro se creó correctamente
            $checkDoc = UserDocument::where([
                'user_id' => $user->id,
                'required_document_id' => $request->document_id
            ])->first();
            
            if (!$checkDoc) {
                Log::error("Verificación post-guardado falló: No se encontró el documento en la BD.");
                DB::rollBack();
                return back()->with('error', 'El documento se guardó pero no se pudo registrar en la base de datos.');
            }
            
            Log::info("Documento verificado OK: ID {$checkDoc->id}, Path: {$checkDoc->file_path}");
            DB::commit();

            return back()->with('success', 'Documento subido correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error general en uploadDocument: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->with('error', 'Error al subir el documento: ' . $e->getMessage());
        }
    }

    /**
     * Muestra la vista de envío de documentos.
     *
     * @return \Illuminate\View\View
     */
    public function documentSubmission(): View
    {
        $user = Auth::user();
        $requiredDocuments = RequiredDocument::where('active', true)->get();
        $userDocuments = $user->documents()->with('requiredDocument')->get();
        
        return view('profile.document_submission', compact('requiredDocuments', 'userDocuments'));
    }

     /**
     * Devuelve el estado de verificación del usuario en JSON.
     * Usado por /user/check-status en el front.
     */
    public function checkStatus(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'No autenticado',
            ], 401);
        }

        // Contar documentos del usuario por estado
        $documents = UserDocument::where('user_id', $user->id)->get();

        return response()->json([
            'ok' => true,
            'user_id' => $user->id,
            'verified' => (bool) $user->verified,
            'estado' => $user->estado,
            'documents' => [
                'total'      => $documents->count(),
                'aprobados'  => $documents->where('status', 'aprobado')->count(),
                'pendientes' => $documents->where('status', 'pendiente')->count(),
                'rechazados' => $documents->where('status', 'rechazado')->count(),
            ],
        ]);
    }
}
