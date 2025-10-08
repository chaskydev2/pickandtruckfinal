<?php

/**
 * Este script configura correctamente el almacenamiento para los documentos.
 * Crea los directorios necesarios y asegura que los enlaces simbólicos estén configurados.
 */
echo "Configurando almacenamiento para documentos...\n";

// Crear enlace simbólico si no existe
if (!file_exists(public_path('storage'))) {
    echo "Creando enlace simbólico de storage...\n";
    symlink(storage_path('app/public'), public_path('storage'));
    echo "Enlace simbólico creado correctamente.\n";
} else {
    echo "El enlace simbólico ya existe.\n";
}

// Asegurar que existan los directorios necesarios
$directories = [
    storage_path('app/public'),
    storage_path('app/public/documents'),
    public_path('storage/documents'),
];

foreach ($directories as $directory) {
    if (!file_exists($directory)) {
        echo "Creando directorio: {$directory}\n";
        mkdir($directory, 0755, true);
        echo "Directorio creado correctamente.\n";
    } else {
        echo "El directorio {$directory} ya existe.\n";
    }
}

echo "Configuración de almacenamiento completada. Los documentos se guardarán en la carpeta pública.\n";
