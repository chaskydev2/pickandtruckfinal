<?php

/**
 * Este script configura el directorio público para documentos
 */
echo "Configurando directorio público para documentos...\n";

// Definir rutas de directorios que necesitamos crear
$directories = [
    public_path('documents'),
];

// Asegurar que los directorios existan
foreach ($directories as $directory) {
    if (!file_exists($directory)) {
        echo "Creando directorio: {$directory}...\n";
        if (mkdir($directory, 0755, true)) {
            echo "✓ Directorio creado correctamente.\n";
            
            // Crear un archivo .htaccess para permitir el acceso directo
            $htaccess = $directory . '/.htaccess';
            $htaccessContent = "Options +Indexes\nAllow from all\n";
            
            if (file_put_contents($htaccess, $htaccessContent)) {
                echo "✓ Archivo .htaccess creado para permitir acceso externo.\n";
            } else {
                echo "✗ No se pudo crear el archivo .htaccess.\n";
            }
        } else {
            echo "✗ No se pudo crear el directorio.\n";
        }
    } else {
        echo "✓ El directorio {$directory} ya existe.\n";
    }
}

// Verificar permisos
foreach ($directories as $directory) {
    $perms = substr(sprintf('%o', fileperms($directory)), -4);
    echo "Permisos de {$directory}: {$perms}\n";
    
    // Si es necesario, ajustar permisos
    if ($perms != "0755") {
        echo "Ajustando permisos a 0755...\n";
        chmod($directory, 0755);
    }
}

echo "\nConfiguración completa. El directorio público para documentos está preparado.\n";
echo "Los documentos se guardarán en: " . public_path('documents') . "\n";
echo "URL base para acceso directo: " . url('documents') . "\n";
