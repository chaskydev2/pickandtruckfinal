<?php

/**
 * Este script arregla los problemas con la redirección del perfil de empresa
 */
echo "Revisando y reparando problemas de redirección de empresa...\n\n";

// Cargar el entorno Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Empresa;

// Paso 1: Limpiar todas las cachés
echo "Paso 1: Limpiando todas las cachés del sistema...\n";
Artisan::call('optimize:clear');
echo Artisan::output();

// Paso 2: Verificar rutas
echo "\nPaso 2: Verificando rutas relacionadas con empresas...\n";
$routes = Route::getRoutes();
$empresaRoutes = [];

foreach ($routes as $route) {
    if (str_contains($route->uri(), 'empresa') || ($route->getName() && str_contains($route->getName(), 'empresa'))) {
        $empresaRoutes[] = [
            'uri' => $route->uri(),
            'name' => $route->getName() ?: 'sin nombre',
            'action' => $route->getActionName(),
            'methods' => implode('|', $route->methods()),
        ];
    }
}

if (count($empresaRoutes) > 0) {
    echo "Se encontraron " . count($empresaRoutes) . " rutas relacionadas con empresas:\n";
    foreach ($empresaRoutes as $route) {
        echo "- {$route['uri']} (Nombre: {$route['name']}, Métodos: {$route['methods']})\n";
        echo "  Acción: {$route['action']}\n";
    }
} else {
    echo "No se encontraron rutas relacionadas con empresas. Esto es un problema.\n";
}

// Paso 3: Verificar usuarios y sus empresas
echo "\nPaso 3: Verificando usuarios y empresas...\n";
$users = User::all();
echo "Total de usuarios: " . $users->count() . "\n";

$problemUsers = [];
foreach ($users as $user) {
    if (!$user->empresa) {
        $problemUsers[] = $user;
    }
}

if (count($problemUsers) > 0) {
    echo count($problemUsers) . " usuarios no tienen empresa asociada. Creando empresas básicas...\n";
    
    foreach ($problemUsers as $user) {
        try {
            $empresa = new Empresa();
            $empresa->user_id = $user->id;
            $empresa->nombre = $user->name . ' (Empresa)';
            $empresa->save();
            echo "- Creada empresa para {$user->name} (ID: {$user->id})\n";
        } catch (\Exception $e) {
            echo "- ERROR al crear empresa para {$user->name}: {$e->getMessage()}\n";
        }
    }
} else {
    echo "Todos los usuarios tienen empresa asociada. Bien!\n";
}

// Paso 4: Verificar la recuperación de datos
echo "\nPaso 4: Verificando recuperación de datos de empresa...\n";
$testUser = User::first();

if ($testUser) {
    echo "Usuario de prueba: {$testUser->name} (ID: {$testUser->id})\n";
    
    try {
        $empresa = $testUser->empresa;
        
        if ($empresa) {
            echo "Empresa encontrada: {$empresa->nombre} (ID: {$empresa->id})\n";
            echo "Relación OK.\n";
        } else {
            echo "ERROR: No se pudo obtener la empresa del usuario a pesar de la corrección.\n";
        }
    } catch (\Exception $e) {
        echo "ERROR al recuperar empresa: {$e->getMessage()}\n";
    }
} else {
    echo "No hay usuarios en el sistema para verificar.\n";
}

echo "\nCorrecciones completadas. Reinicie el servidor para aplicar los cambios.\n";
