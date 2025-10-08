<?php

/**
 * Script para diagnosticar problemas con la ruta específica de empresas
 */
echo "Verificando la ruta /empresas...\n\n";

// Verificar entorno
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\EmpresaController;

// Comprobar que la clase EmpresaController existe y tiene el método show
if (class_exists(EmpresaController::class)) {
    echo "✓ EmpresaController existe\n";
    
    if (method_exists(EmpresaController::class, 'show')) {
        echo "✓ El método 'show' existe en EmpresaController\n";
    } else {
        echo "✗ El método 'show' NO existe en EmpresaController\n";
        exit(1);
    }
} else {
    echo "✗ EmpresaController NO existe\n";
    exit(1);
}

// Comprobar las rutas
$request = Request::create('/empresas', 'GET');
$routes = Route::getRoutes();
$matched = false;

echo "\nVerificando si alguna ruta coincide con /empresas:\n";

foreach ($routes as $route) {
    if ($route->uri() === 'empresas' || $route->uri() === '/empresas') {
        $matched = true;
        echo "✓ Ruta encontrada: {$route->uri()} ({$route->getName()})\n";
        echo "  Acción: " . $route->getActionName() . "\n";
        echo "  Métodos: " . implode(', ', $route->methods()) . "\n";
        
        // Verificar middleware
        $middleware = $route->gatherMiddleware();
        echo "  Middleware: " . implode(', ', $middleware) . "\n";
        
        // Verificar si requiere autenticación
        if (in_array('auth', $middleware) || in_array('web', $middleware)) {
            echo "  ✓ La ruta requiere autenticación\n";
        } else {
            echo "  ✗ La ruta NO requiere autenticación\n";
        }
    }
}

if (!$matched) {
    echo "✗ No se encontró ninguna ruta que coincida con /empresas\n";
    
    // Buscar rutas similares
    echo "\nBuscando rutas similares:\n";
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'empresa') !== false) {
            echo "- {$route->uri()} ({$route->getName()})\n";
        }
    }
    
    // Sugerir solución
    echo "\nSolución: Agregue una ruta en web.php para /empresas:\n";
    echo "Route::get('/empresas', [EmpresaController::class, 'show'])->name('empresas.show');\n";
}

// Verificar vista
if (view()->exists('empresas.show')) {
    echo "\n✓ La vista 'empresas.show' existe\n";
} else {
    echo "\n✗ La vista 'empresas.show' NO existe\n";
    echo "  Solución: Cree la vista en resources/views/empresas/show.blade.php\n";
}

echo "\nVerificación finalizada.\n";
echo "Si la verificación pasó sin errores, intente limpiar la caché de rutas con: php artisan route:clear\n";
echo "Y luego reinicie el servidor: php artisan serve\n";
