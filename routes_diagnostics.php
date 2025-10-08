<?php

/**
 * Este script diagnostica problemas con las rutas de la aplicación
 */
echo "Ejecutando diagnóstico de rutas...\n\n";

// Verificar entorno
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\EmpresaController;
use App\Models\Empresa;
use App\Models\User;

// Verificar si la clase del controlador existe
echo "Verificando controlador EmpresaController...\n";
if (class_exists(EmpresaController::class)) {
    echo "✓ La clase EmpresaController existe\n";
    
    // Verificar métodos
    $methods = ['show', 'edit', 'update'];
    foreach ($methods as $method) {
        if (method_exists(EmpresaController::class, $method)) {
            echo "✓ El método '{$method}' existe en EmpresaController\n";
        } else {
            echo "✗ El método '{$method}' NO existe en EmpresaController\n";
        }
    }
} else {
    echo "✗ La clase EmpresaController NO existe\n";
}

// Verificar si las rutas están registradas
echo "\nVerificando rutas registradas para 'empresas'...\n";
$routes = Route::getRoutes();
$empresasRoutes = [];

foreach ($routes as $route) {
    if (str_starts_with($route->getName() ?? '', 'empresas.')) {
        $empresasRoutes[] = [
            'name' => $route->getName(),
            'uri' => $route->uri(),
            'methods' => implode('|', $route->methods()),
            'action' => $route->getActionName(),
        ];
    }
}

if (count($empresasRoutes) > 0) {
    echo "✓ Se encontraron " . count($empresasRoutes) . " rutas de empresas:\n";
    foreach ($empresasRoutes as $route) {
        echo "  - {$route['name']} ({$route['methods']}) {$route['uri']} → {$route['action']}\n";
    }
} else {
    echo "✗ No se encontraron rutas para 'empresas'\n";
}

// Verificar si las vistas existen
echo "\nVerificando vistas...\n";
$views = ['empresas.show', 'empresas.edit'];
foreach ($views as $view) {
    if (view()->exists($view)) {
        echo "✓ La vista '{$view}' existe\n";
    } else {
        echo "✗ La vista '{$view}' NO existe\n";
    }
}

// Verificar si hay empresas en la base de datos
echo "\nVerificando registros de empresas...\n";
try {
    $empresasCount = DB::table('empresas')->count();
    echo "✓ Hay {$empresasCount} empresas registradas\n";
    
    // Mostrar algunas empresas de ejemplo
    if ($empresasCount > 0) {
        $empresas = Empresa::with('user')->limit(5)->get();
        foreach ($empresas as $empresa) {
            echo "  - ID: {$empresa->id}, Nombre: {$empresa->nombre}, Usuario: {$empresa->user->name}\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ Error al verificar empresas: {$e->getMessage()}\n";
}

// Verificar relación con usuarios
echo "\nVerificando relación Usuario-Empresa...\n";
try {
    $user = User::first();
    if ($user) {
        echo "Usuario de prueba: {$user->name} (ID: {$user->id})\n";
        if ($user->empresa) {
            echo "✓ El usuario tiene una empresa asociada: {$user->empresa->nombre} (ID: {$user->empresa->id})\n";
        } else {
            echo "✗ El usuario NO tiene una empresa asociada\n";
        }
    } else {
        echo "✗ No se encontraron usuarios\n";
    }
} catch (\Exception $e) {
    echo "✗ Error al verificar relación: {$e->getMessage()}\n";
}

echo "\nDiagnóstico completado.\n";
