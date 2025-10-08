<?php

/**
 * Este script verifica si la tabla 'empresas' existe y la crea si es necesario
 */
echo "Verificando tabla de empresas...\n\n";

// Cargar el entorno Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

// Verificar si la tabla existe
if (Schema::hasTable('empresas')) {
    echo "✓ La tabla 'empresas' ya existe en la base de datos\n";
    
    // Verificar estructura de la tabla
    echo "\nVerificando estructura de la tabla 'empresas'...\n";
    $columns = Schema::getColumnListing('empresas');
    echo "Columnas existentes: " . implode(', ', $columns) . "\n";
    
    // Verificar si faltan columnas importantes
    $requiredColumns = ['id', 'user_id', 'nombre', 'logo', 'descripcion', 'telefono', 'direccion', 'sitio_web', 'verificada', 'created_at', 'updated_at'];
    $missingColumns = array_diff($requiredColumns, $columns);
    
    if (count($missingColumns) > 0) {
        echo "✗ Faltan columnas: " . implode(', ', $missingColumns) . "\n";
        
        // Agregar las columnas faltantes
        echo "Agregando columnas faltantes...\n";
        
        Schema::table('empresas', function (Blueprint $table) use ($missingColumns) {
            if (in_array('user_id', $missingColumns)) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
            if (in_array('nombre', $missingColumns)) {
                $table->string('nombre');
            }
            if (in_array('logo', $missingColumns)) {
                $table->string('logo')->nullable();
            }
            if (in_array('descripcion', $missingColumns)) {
                $table->text('descripcion')->nullable();
            }
            if (in_array('telefono', $missingColumns)) {
                $table->string('telefono')->nullable();
            }
            if (in_array('direccion', $missingColumns)) {
                $table->string('direccion')->nullable();
            }
            if (in_array('sitio_web', $missingColumns)) {
                $table->string('sitio_web')->nullable();
            }
            if (in_array('verificada', $missingColumns)) {
                $table->boolean('verificada')->default(false);
            }
            if (in_array('created_at', $missingColumns)) {
                $table->timestamps();
            }
        });
        
        echo "✓ Columnas faltantes agregadas correctamente\n";
    } else {
        echo "✓ La tabla tiene todas las columnas requeridas\n";
    }
} else {
    echo "✗ La tabla 'empresas' no existe\n";
    
    // Crear la tabla
    echo "Creando la tabla 'empresas'...\n";
    
    Schema::create('empresas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('nombre');
        $table->string('logo')->nullable();
        $table->text('descripcion')->nullable();
        $table->string('telefono')->nullable();
        $table->string('direccion')->nullable();
        $table->string('sitio_web')->nullable();
        $table->boolean('verificada')->default(false);
        $table->timestamps();
    });
    
    echo "✓ Tabla 'empresas' creada correctamente\n";
}

// Verificar si hay registros
$count = DB::table('empresas')->count();
echo "\nHay {$count} registros en la tabla 'empresas'\n";

echo "\nVerificación completada.\n";
