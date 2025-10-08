<?php

/**
 * Script para limpiar la caché de rutas y configuración
 */
echo "Limpiando caché...\n";

// Ejecutar comandos de artisan para limpiar caché
$commands = [
    'route:clear',
    'config:clear',
    'cache:clear',
    'view:clear',
    'optimize:clear'
];

foreach ($commands as $command) {
    echo "Ejecutando php artisan {$command}...\n";
    passthru('php artisan ' . $command);
}

echo "\nCaché limpiada correctamente.\n";
