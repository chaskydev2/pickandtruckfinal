<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\RequiredDocument;
use App\Models\UserDocument;

class DocumentController extends Controller
{
    /**
     * Sube CÉDULA + LICENCIA en una sola llamada.
     * POST /api/documents/upload-batch
     */
    public function uploadBatch(Request $request)
    {
        $request->validate([
            'cedula'   => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'licencia' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $user = $request->user();

        $map = [
            'cedula'   => 'Carnet de Identidad',
            'licencia' => 'Licencia de Conducir',
        ];

        DB::beginTransaction();
        try {
            $results = [];

            foreach ($map as $key => $docName) {
                $file = $request->file($key);

                $required = RequiredDocument::where('name', $docName)
                    ->where('active', true)->first();

                if (!$required) {
                    throw ValidationException::withMessages([
                        $key => ["No se encontró el documento requerido: {$docName}"],
                    ]);
                }

                // Guarda en storage/app/public y en public/documents/{user}
                $relativePath = $this->storeFileForUser($user->id, $file);

                $userDoc = UserDocument::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'required_document_id' => $required->id,
                    ],
                    [
                        'file_path' => $relativePath,   // solo ruta relativa en BD
                        'status'    => 'pendiente',
                        'comments'  => null,
                    ]
                );

                $results[] = [
                    'type'                 => $key,
                    'required_document_id' => $required->id,
                    'status'               => $userDoc->status,
                    'path'                 => $relativePath,
                    'url'                  => $this->publicUrl($relativePath), // SIN /storage
                ];
            }

            DB::commit();

            return response()->json([
                'ok'        => true,
                'message'   => 'Documentos subidos',
                'documents' => $results,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Sube UN solo documento: cedula | licencia.
     * POST /api/documents/upload
     */
    public function uploadSingle(Request $request)
    {
        $data = $request->validate([
            'document_key' => 'required|in:cedula,licencia',
            'file'         => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $user = $request->user();

        $docName = $data['document_key'] === 'cedula'
            ? 'Carnet de Identidad'
            : 'Licencia de Conducir';

        $required = RequiredDocument::where('name', $docName)
            ->where('active', true)->first();

        if (!$required) {
            throw ValidationException::withMessages([
                'document_key' => ["No se encontró el documento requerido: {$docName}"],
            ]);
        }

        DB::beginTransaction();
        try {
            $relativePath = $this->storeFileForUser($user->id, $request->file('file'));

            $userDoc = UserDocument::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'required_document_id' => $required->id,
                ],
                [
                    'file_path' => $relativePath,
                    'status'    => 'pendiente',
                    'comments'  => null,
                ]
            );

            DB::commit();

            return response()->json([
                'ok'       => true,
                'message'  => 'Documento subido',
                'document' => [
                    'type'                 => $data['document_key'],
                    'required_document_id' => $required->id,
                    'status'               => $userDoc->status,
                    'path'                 => $relativePath,
                    'url'                  => $this->publicUrl($relativePath), // SIN /storage
                ],
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Guarda el archivo en:
     * - storage/app/public/documents/{userId}/{filename} (backup)
     * - public/documents/{userId}/{filename}           (servido directo sin /storage)
     * y retorna la RUTA RELATIVA: documents/{userId}/{filename}
     */
    private function storeFileForUser(int $userId, \Illuminate\Http\UploadedFile $file): string
    {
        $dir = "documents/{$userId}";
        $filename = uniqid() . "_{$userId}_" . time() . '.' . $file->getClientOriginalExtension();

        // 1) Guarda en disk 'public' (storage/app/public/...)
        Storage::disk('public')->putFileAs($dir, $file, $filename);

        // 2) Copia a public/ (para servir sin /storage)
        $publicDir = public_path($dir);
        if (!is_dir($publicDir)) {
            @mkdir($publicDir, 0755, true);
        }
        // mover/copy: usamos copy del contenido temporal del UploadedFile
        // move no sirve aquí porque ya fue consumido por putFileAs; volvemos a leer del storage
        $from = Storage::disk('public')->path("{$dir}/{$filename}");
        $to   = $publicDir.DIRECTORY_SEPARATOR.$filename;
        @copy($from, $to);

        return "{$dir}/{$filename}";
    }

    /**
     * Construye URL pública absoluta SIN /storage:
     * asset('documents/...') => http(s)://host/documents/...
     */
    private function publicUrl(string $relativePath): string
    {
        return asset($relativePath);
    }

    public function myDocuments(Request $request)
    {
        $user = $request->user();

        // Claves del frontend -> nombres en BD
        $map = [
            'cedula'   => 'Carnet de Identidad',
            'licencia' => 'Licencia de Conducir',
        ];

        // RequiredDocument activos
        $requireds = RequiredDocument::whereIn('name', array_values($map))
            ->where('active', true)
            ->get()
            ->keyBy('name');

        // UserDocument del usuario para esos requireds
        $userDocs = UserDocument::where('user_id', $user->id)
            ->whereIn('required_document_id', $requireds->pluck('id'))
            ->get()
            ->keyBy('required_document_id');

        $result = [];

        foreach ($map as $key => $docName) {
            $required = $requireds->get($docName);

            if (!$required) {
                // Si no existe el RequiredDocument activo, devolvemos faltante
                $result[] = [
                    'type'                 => $key, // 'cedula' | 'licencia'
                    'required_document_id' => null,
                    'status'               => 'faltante',
                    'comments'             => null,
                    'path'                 => null,
                    'url'                  => null,
                    'updated_at'           => null,
                ];
                continue;
            }

            $userDoc = $userDocs->get($required->id);

            if ($userDoc) {
                $result[] = [
                    'type'                 => $key,
                    'required_document_id' => $required->id,
                    'status'               => $userDoc->status, // pendiente/aprobado/rechazado
                    'comments'             => $userDoc->comments,
                    'path'                 => $userDoc->file_path,
                    'url'                  => $this->publicUrl($userDoc->file_path), // SIN /storage
                    'updated_at'           => optional($userDoc->updated_at)?->toISOString(),
                ];
            } else {
                $result[] = [
                    'type'                 => $key,
                    'required_document_id' => $required->id,
                    'status'               => 'faltante',
                    'comments'             => null,
                    'path'                 => null,
                    'url'                  => null,
                    'updated_at'           => null,
                ];
            }
        }

        return response()->json([
            'ok'        => true,
            'documents' => $result,
        ], 200);
    }
}
