<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ServeStorageFiles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si es una solicitud AJAX, API, para /notifications o relacionada con documentos, saltamos este middleware
        if ($request->ajax() || 
            $request->wantsJson() || 
            $request->is('api/*') || 
            $request->is('notifications/*') ||
            $request->is('profile/documents*') ||
            $request->is('profile/document-submission*') ||
            $request->is('profile/upload-document*') ||
            str_contains($request->path(), 'notifications')) {
            return $next($request);
        }
        
        // Si es una solicitud POST (como un envío de formulario), no interceptamos
        if ($request->isMethod('post')) {
            return $next($request);
        }
        
        $path = $request->path();
        
        // Verificar si la solicitud es para un archivo en storage/public
        if (str_starts_with($path, 'app/storage/public/')) {
            $filePath = str_replace('app/storage/public/', '', $path);
            
            // Comprobar si el archivo existe
            if (Storage::disk('public')->exists($filePath)) {
                return response()->file(storage_path('app/public/' . $filePath));
            }
        }

        return $next($request);
    }
}
