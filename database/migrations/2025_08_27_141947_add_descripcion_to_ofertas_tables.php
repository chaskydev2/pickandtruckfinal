<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('ofertas_carga', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('peso');
        });
        Schema::table('ofertas_ruta', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('capacidad');
        });
    }

    public function down(): void {
        Schema::table('ofertas_carga', function (Blueprint $table) {
            $table->dropColumn('descripcion');
        });
        Schema::table('ofertas_ruta', function (Blueprint $table) {
            $table->dropColumn('descripcion');
        });
    }
};
