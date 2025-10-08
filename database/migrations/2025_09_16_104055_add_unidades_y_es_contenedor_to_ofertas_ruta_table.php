<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ofertas_ruta', function (Blueprint $table) {
            // Nuevos campos (nullable para no romper registros viejos)
            $table->unsignedInteger('unidades')->nullable()->after('capacidad');
        });
    }

    public function down(): void
    {
        Schema::table('ofertas_ruta', function (Blueprint $table) {
            $table->dropColumn(['unidades', 'es_contenedor']);
        });
    }
};