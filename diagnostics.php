<?php

// Este script diagnostica problemas con el sistema de almacenamiento
echo "Ejecutando diagnóstico del sistema de almacenamiento...\n";

// Verificar directorios existentes
echo "Verificando directorios...\n";

$directories = [
    'storage/app' => is_dir('storage/app'),
    'storage/app/public' => is_dir('storage/app/public'),
    'storage/app/public/documents' => is_dir('storage/app/public/documents'),
];

foreach ($directories as $dir => $exists) {
    echo "- $dir: " . ($exists ? "EXISTE" : "NO EXISTE") . "\n";
    
    if ($exists) {
        echo "  Permisos: " . substr(sprintf('%o', fileperms($dir)), -4) . "\n";
        echo "  Escribible: " . (is_writable($dir) ? "SÍ" : "NO") . "\n";
    }
}

// Intentar escribir un archivo de prueba
echo "\nProbando escritura en storage/app/public...\n";
$testFile = 'storage/app/public/test_' . time() . '.txt';
$result = file_put_contents($testFile, 'Este es un archivo de prueba creado en ' . date('Y-m-d H:i:s'));

if ($result !== false) {
    echo "✓ Archivo de prueba creado exitosamente: $testFile\n";
    echo "  Tamaño: $result bytes\n";
    
    // Intentar leer el archivo
    echo "  Contenido del archivo: " . file_get_contents($testFile) . "\n";
    
    // Eliminar el archivo de prueba
    if (unlink($testFile)) {
        echo "✓ Archivo de prueba eliminado exitosamente\n";
    } else {
        echo "✗ No se pudo eliminar el archivo de prueba\n";
    }
} else {
    echo "✗ No se pudo crear el archivo de prueba\n";
    echo "  Error: " . error_get_last()['message'] . "\n";
}

echo "\nDiagnóstico completado.\n";
