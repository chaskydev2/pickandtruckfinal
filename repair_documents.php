<?php

/**
 * Este script repara problemas con los registros de documentos
 * Sincroniza los archivos físicos con la base de datos
 */
echo "Iniciando reparación de registros de documentos...\n\n";

// Cargar entorno Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\UserDocument;
use App\Models\User;
use App\Models\RequiredDocument;
use Illuminate\Support\Facades\DB;

// Fase 1: Verificar archivos sin registro en BD
echo "Fase 1: Buscando archivos sin registro en la base de datos...\n";

$documentsDir = public_path('documents');
$allFiles = [];

if (file_exists($documentsDir)) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($documentsDir));
    foreach ($iterator as $file) {
        if ($file->isFile() && !in_array($file->getBasename(), ['.', '..', '.htaccess'])) {
            $relativePath = str_replace(public_path() . '/', '', $file->getPathname());
            $allFiles[$relativePath] = $file->getPathname();
        }
    }
}

echo "Total de archivos físicos encontrados: " . count($allFiles) . "\n";

// Obtener todos los documentos registrados
$registeredFiles = UserDocument::all()->pluck('file_path')->toArray();
echo "Total de documentos registrados en BD: " . count($registeredFiles) . "\n";

// Encontrar archivos sin registro
$unregisteredFiles = array_diff(array_keys($allFiles), $registeredFiles);
echo "Archivos sin registro en BD: " . count($unregisteredFiles) . "\n";

// Fase 2: Intentar reparar registros
echo "\nFase 2: Reparando registros...\n";
$repaired = 0;
$problems = 0;

foreach ($unregisteredFiles as $filePath) {
    // Analizar el nombre del archivo para encontrar el ID de usuario
    // Formato esperado: uniqid_userid_timestamp.extension
    $fileName = basename($filePath);
    $pathParts = pathinfo($filePath);
    $parts = explode('_', $pathParts['filename']);
    
    if (count($parts) >= 3 && is_numeric($parts[1])) {
        $userId = (int)$parts[1];
        
        $user = User::find($userId);
        if (!$user) {
            echo "× No se encontró usuario con ID {$userId} para el archivo {$filePath}\n";
            $problems++;
            continue;
        }
        
        // Intenta encontrar el documento requerido al que podría pertenecer
        // Asumimos que es el primero que el usuario no tenga
        $missingDocs = RequiredDocument::whereDoesntHave('userDocuments', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();
        
        if ($missingDocs->isEmpty()) {
            echo "× El usuario {$userId} ya tiene todos los documentos registrados. No se puede asociar {$filePath}\n";
            $problems++;
            continue;
        }
        
        // Registrar el documento
        try {
            $userDoc = new UserDocument();
            $userDoc->user_id = $userId;
            $userDoc->required_document_id = $missingDocs->first()->id;
            $userDoc->file_path = $filePath;
            $userDoc->status = 'pendiente';
            $userDoc->save();
            
            echo "✓ Documento registrado: {$filePath} para usuario {$userId}\n";
            $repaired++;
            
        } catch (\Exception $e) {
            echo "× Error al registrar {$filePath}: " . $e->getMessage() . "\n";
            $problems++;
        }
    } else {
        echo "× No se pudo determinar el usuario para {$filePath}\n";
        $problems++;
    }
}

// Fase 3: Verificar registros sin archivo
echo "\nFase 3: Verificando registros sin archivo físico...\n";

$missingFiles = 0;
$userDocuments = UserDocument::all();

foreach ($userDocuments as $doc) {
    $fullPath = public_path($doc->file_path);
    
    if (!file_exists($fullPath)) {
        echo "× Documento ID {$doc->id} apunta a un archivo que no existe: {$doc->file_path}\n";
        $missingFiles++;
    }
}

echo "\nResumen de reparación:\n";
echo "- Documentos reparados: {$repaired}\n";
echo "- Problemas encontrados: {$problems}\n";
echo "- Registros con archivo faltante: {$missingFiles}\n";

echo "\nReparación completada.\n";
