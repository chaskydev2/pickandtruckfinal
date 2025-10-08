<?php

/**
 * Este script actualiza las URLs de documentos existentes en la base de datos
 * para que sean URLs completas en lugar de rutas relativas.
 */
echo "Iniciando actualización de URLs de documentos...\n\n";

// Cargar entorno Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\UserDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Dominio base para URLs completas
//$baseUrl = 'https://app.pickntruck.com/';
$baseUrl = config('app.url') . '/';

// Obtener todos los documentos
$documents = UserDocument::all();
echo "Total de documentos encontrados: " . $documents->count() . "\n";

// Iniciar transacción
DB::beginTransaction();
$updated = 0;
$errors = 0;

try {
    foreach ($documents as $doc) {
        $currentPath = $doc->file_path;
        
        // Verificar si ya es una URL completa
        if (str_starts_with($currentPath, 'http://') || str_starts_with($currentPath, 'https://')) {
            // Quitar dominio y dejar solo el path relativo
            $parsed = parse_url($currentPath, PHP_URL_PATH);
            $doc->file_path = ltrim($parsed, '/');
            $doc->save();
        }
        
        // Construir nueva URL completa
        $newUrl = ltrim($currentPath, '/');
        
        try {
            // Actualizar el documento
            $doc->file_path = $newUrl;
            $doc->save();
            
            echo "✓ Documento ID {$doc->id} actualizado: {$newUrl}\n";
            $updated++;
        } catch (\Exception $e) {
            echo "✗ Error al actualizar documento ID {$doc->id}: {$e->getMessage()}\n";
            Log::error("Error al actualizar documento: " . $e->getMessage());
            $errors++;
        }
    }
    
    // Confirmar cambios si todo salió bien
    if ($errors === 0) {
        DB::commit();
        echo "\nTodos los documentos fueron actualizados correctamente.\n";
    } else {
        DB::rollBack();
        echo "\nSe encontraron errores. Ningún cambio fue guardado.\n";
    }
} catch (\Exception $e) {
    DB::rollBack();
    echo "\nError general: " . $e->getMessage() . "\n";
    Log::error("Error general en actualización de documentos: " . $e->getMessage());
}

echo "\nResumen de actualización:\n";
echo "- Documentos actualizados: {$updated}\n";
echo "- Errores encontrados: {$errors}\n";
echo "- Total de documentos: " . $documents->count() . "\n";

echo "\nActualización completada.\n";
