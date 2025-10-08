<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        // Para MySQL necesitamos modificar la columna enum directamente
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bids MODIFY COLUMN estado ENUM('pendiente', 'aceptado', 'rechazado', 'terminado') DEFAULT 'pendiente'");
        } 
        // Para PostgreSQL u otros motores, podemos usar un enfoque diferente
        else {
            // Primero eliminar la restricción actual
            Schema::table('bids', function (Blueprint $table) {
                $table->dropColumn('estado');
            });

            // Luego crear una nueva columna con los valores actualizados
            Schema::table('bids', function (Blueprint $table) {
                $table->enum('estado', ['pendiente', 'aceptado', 'rechazado', 'terminado'])->default('pendiente');
            });
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        // Para MySQL necesitamos revertir el cambio directamente
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bids MODIFY COLUMN estado ENUM('pendiente', 'aceptado', 'rechazado') DEFAULT 'pendiente'");
        } 
        // Para PostgreSQL u otros motores, usamos el mismo enfoque que en up()
        else {
            // Primero eliminar la restricción actualizada
            Schema::table('bids', function (Blueprint $table) {
                $table->dropColumn('estado');
            });

            // Luego crear la columna original
            Schema::table('bids', function (Blueprint $table) {
                $table->enum('estado', ['pendiente', 'aceptado', 'rechazado'])->default('pendiente');
            });
        }
    }
};
