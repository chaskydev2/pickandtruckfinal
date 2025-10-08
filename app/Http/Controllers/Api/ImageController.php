<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends Controller
{
    /**
     * Obtener una imagen por su ID de documento
     * 
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function getImage($id)
    {
        try {
            // Buscar el documento
            $document = UserDocument::findOrFail($id);
            
            // Verificar si el usuario autenticado es administrador
            if (!Auth::user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado para ver esta imagen.'
                ], Response::HTTP_FORBIDDEN);
            }
            
            // Obtener la ruta del archivo
            $filePath = $document->file_path;
            
            // Si es una URL completa, extraer la ruta relativa
            if (filter_var($filePath, FILTER_VALIDATE_URL)) {
                $filePath = parse_url($filePath, PHP_URL_PATH);
                $filePath = ltrim($filePath, '/');
            }
            
            // Verificar si el archivo existe en storage público
            $storagePath = storage_path('app/public/' . $filePath);
            if (file_exists($storagePath)) {
                return response()->file($storagePath);
            }
            
            // Si no se encuentra en storage, intentar en la carpeta pública
            $publicPath = public_path($filePath);
            if (file_exists($publicPath)) {
                return response()->file($publicPath);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Imagen no encontrada.'
            ], Response::HTTP_NOT_FOUND);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al recuperar la imagen: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Verificar si una imagen existe y obtener sus metadatos
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkImage($id)
    {
        try {
            // Buscar el documento
            $document = UserDocument::findOrFail($id);
            
            // Verificar si el usuario autenticado es administrador
            if (!Auth::user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado para ver esta información.'
                ], Response::HTTP_FORBIDDEN);
            }
            
            // Obtener la ruta del archivo
            $filePath = $this->getRelativePath($document->file_path);
            
            $fileExists = false;
            $fileSize = 0;
            $mimeType = '';
            
            // Verificar si el archivo existe en storage público
            $storagePath = storage_path('app/public/' . $filePath);
            if (file_exists($storagePath)) {
                $fileExists = true;
                $fileSize = filesize($storagePath);
                $mimeType = mime_content_type($storagePath);
            } 
            // Si no se encuentra en storage, verificar en la carpeta pública
            elseif (file_exists(public_path($filePath))) {
                $fileExists = true;
                $fileSize = filesize(public_path($filePath));
                $mimeType = mime_content_type(public_path($filePath));
            }
            
            // Usar URL relativa que será resuelta por el frontend
            return response()->json([
                'exists' => $fileExists,
                'size' => $fileSize,
                'mime_type' => $mimeType,
                'url' => "/api/images/{$id}"  // URL relativa
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar la imagen: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
