<?php

// Este script ajusta los permisos del directorio de almacenamiento

echo "Verificando y corrigiendo permisos de directorios de almacenamiento...\n";

$directories = [
    'storage/app',
    'storage/app/public',
    'storage/app/public/documents',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
];

foreach ($directories as $directory) {
    if (!file_exists($directory)) {
        echo "Creando directorio: {$directory}\n";
        mkdir($directory, 0755, true);
    }
    
    echo "Ajustando permisos para: {$directory}\n";
    chmod($directory, 0755);
}

echo "¡Permisos ajustados correctamente!\n";
