<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ofertas_carga', function (Blueprint $table) {
            $table->enum('tipo_despacho', [
                'despacho_anticipado',
                'despacho_general',
                'no_sabe_no_responde'
            ])->nullable()->after('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ofertas_carga', function (Blueprint $table) {
            $table->dropColumn('tipo_despacho');
        });
    }
};
