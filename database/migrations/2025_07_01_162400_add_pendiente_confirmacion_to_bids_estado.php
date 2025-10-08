<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Modificar la columna estado para incluir 'pendiente_confirmacion'
        DB::statement("ALTER TABLE `bids` 
            MODIFY COLUMN `estado` ENUM('pendiente', 'aceptado', 'rechazado', 'pendiente_confirmacion', 'terminado') 
            NOT NULL DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        // Revertir el cambio si es necesario
        DB::statement("ALTER TABLE `bids` 
            MODIFY COLUMN `estado` ENUM('pendiente', 'aceptado', 'rechazado', 'terminado') 
            NOT NULL DEFAULT 'pendiente'");
    }
};
