<?php

/**
 * Este script diagnostica problemas específicos con los documentos y la base de datos
 */
echo "Ejecutando diagnóstico de documentos y base de datos...\n\n";

// Verificar entorno
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\UserDocument;
use App\Models\User;
use App\Models\RequiredDocument;
use Illuminate\Support\Facades\DB;

// 1. Verificar tablas de base de datos
echo "--- Verificando tablas de base de datos ---\n";

try {
    $tables = ['users', 'required_documents', 'user_documents'];
    
    foreach ($tables as $table) {
        try {
            $count = DB::table($table)->count();
            echo "✓ Tabla {$table}: {$count} registros\n";
        } catch (\Exception $e) {
            echo "✗ Error con tabla {$table}: {$e->getMessage()}\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ Error al verificar tablas: {$e->getMessage()}\n";
}

// 2. Verificar documentos subidos
echo "\n--- Verificando documentos subidos ---\n";
$userDocuments = UserDocument::all();
echo "Total documentos en DB: " . $userDocuments->count() . "\n";

foreach ($userDocuments as $doc) {
    echo "ID: {$doc->id} - User: {$doc->user_id} - Status: {$doc->status} - Path: {$doc->file_path}\n";
    
    // Verificar si el archivo existe
    $fullPath = public_path($doc->file_path);
    $exists = file_exists($fullPath);
    $fileSize = $exists ? filesize($fullPath) : 0;
    
    echo "  - Archivo " . ($exists ? "EXISTE ({$fileSize} bytes)" : "NO EXISTE") . "\n";
    
    // Verificar el usuario y documento requerido
    $user = User::find($doc->user_id);
    $requiredDoc = RequiredDocument::find($doc->required_document_id);
    
    echo "  - Usuario: " . ($user ? $user->name : "NO ENCONTRADO") . "\n";
    echo "  - Documento requerido: " . ($requiredDoc ? $requiredDoc->name : "NO ENCONTRADO") . "\n";
    echo "----------\n";
}

// 3. Verificar directorio de documentos
echo "\n--- Verificando directorio de documentos ---\n";
$documentsDir = public_path('documents');

if (!file_exists($documentsDir)) {
    echo "✗ El directorio de documentos no existe.\n";
    echo "Creando directorio...\n";
    mkdir($documentsDir, 0755, true);
    echo "Directorio creado.\n";
} else {
    echo "✓ El directorio de documentos existe.\n";
    
    // Contar documentos físicos
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($documentsDir));
    $files = array_filter(iterator_to_array($iterator), function($file) {
        return $file->isFile() && !in_array($file->getBasename(), ['.', '..', '.htaccess']);
    });
    
    echo "Total archivos físicos: " . count($files) . "\n";
    
    // Listar algunos archivos de ejemplo
    $count = 0;
    foreach ($files as $file) {
        if ($count++ < 10) { // Mostrar solo los primeros 10 archivos
            echo "  - " . $file->getPathname() . " (" . filesize($file->getPathname()) . " bytes)\n";
        }
    }
    
    if (count($files) > 10) {
        echo "  - ... y " . (count($files) - 10) . " archivos más.\n";
    }
}

// 4. Verificar permisos
echo "\n--- Verificando permisos ---\n";
$directories = [
    public_path('documents'),
    storage_path('app/public/documents'),
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        echo "✗ Directorio no existe: {$dir}\n";
        continue;
    }
    
    $perms = substr(sprintf('%o', fileperms($dir)), -4);
    $isWritable = is_writable($dir);
    
    echo "Directorio: {$dir}\n";
    echo "  - Permisos: {$perms}\n";
    echo "  - Escribible: " . ($isWritable ? "SÍ" : "NO") . "\n";
    
    // Intentar escribir un archivo de prueba
    $testFile = $dir . '/test_' . time() . '.txt';
    try {
        $result = file_put_contents($testFile, 'Test ' . date('Y-m-d H:i:s'));
        if ($result !== false) {
            echo "  - ✓ Prueba de escritura exitosa\n";
            // Eliminar archivo de prueba
            unlink($testFile);
        } else {
            echo "  - ✗ No se pudo escribir archivo de prueba\n";
        }
    } catch (\Exception $e) {
        echo "  - ✗ Error al escribir: " . $e->getMessage() . "\n";
    }
}

// 5. Verificar la configuración del modelo
echo "\n--- Verificando configuración de modelo UserDocument ---\n";
try {
    $fillable = (new UserDocument())->getFillable();
    echo "Campos fillable: " . implode(", ", $fillable) . "\n";
    
    $model = new \ReflectionClass(UserDocument::class);
    echo "Definido en: " . $model->getFileName() . "\n";
    
    $connection = DB::connection()->getDatabaseName();
    echo "Conexión a base de datos: " . $connection . "\n";
} catch (\Exception $e) {
    echo "Error al verificar modelo: " . $e->getMessage() . "\n";
}

echo "\nDiagnóstico completado.\n";
