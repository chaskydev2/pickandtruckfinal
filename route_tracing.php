<?php

/**
 * Script para diagnosticar problemas con las rutas específicas
 */
echo "Ejecutando diagnóstico detallado de rutas...\n\n";

// Verificar entorno
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Rutas a verificar
$routesToCheck = [
    '/',
    '/login',
    '/empresas',
    '/empresas/',
    '/empresas/edit',
    '/profile',
];

echo "===== Verificando rutas =====\n";
foreach ($routesToCheck as $uri) {
    echo "\nAnalizando ruta: {$uri}\n";
    
    // Crear una solicitud para la ruta
    $request = Request::create($uri, 'GET');
    
    // Encontrar la ruta que coincide
    $routes = Route::getRoutes();
    $route = null;
    
    foreach ($routes as $r) {
        if ($r->matches($request)) {
            $route = $r;
            break;
        }
    }
    
    if ($route) {
        echo "✓ Ruta encontrada!\n";
        echo "  Nombre: " . ($route->getName() ?: 'Sin nombre') . "\n";
        echo "  Acción: " . $route->getActionName() . "\n";
        echo "  Métodos: " . implode(', ', $route->methods()) . "\n";
        echo "  Middlewares: " . implode(', ', $route->gatherMiddleware()) . "\n";
    } else {
        echo "✗ No se encontró ninguna ruta para esta URI\n";
        
        // Sugerir rutas similares
        echo "  Rutas similares:\n";
        $found = false;
        foreach ($routes as $r) {
            $routeUri = $r->uri();
            if (strpos($routeUri, ltrim($uri, '/')) !== false || strpos(ltrim($uri, '/'), $routeUri) !== false) {
                echo "  - {$routeUri} (" . implode(', ', $r->methods()) . ") → " . ($r->getName() ?: 'Sin nombre') . "\n";
                $found = true;
            }
        }
        
        if (!$found) {
            echo "  No se encontraron rutas similares\n";
        }
    }
}

echo "\nDiagnóstico de rutas completado.\n";

// Mostrar todas las rutas registradas
echo "\n===== Todas las rutas registradas =====\n";
$allRoutes = Route::getRoutes();
foreach ($allRoutes as $route) {
    echo $route->uri() . " (" . implode(',', $route->methods()) . ") → " . ($route->getName() ?: 'Sin nombre') . "\n";
}
