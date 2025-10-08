<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoDespachoToOfertasRuta extends Migration
{
    public function up()
    {
        Schema::table('ofertas_ruta', function (Blueprint $table) {
            // Agregar el campo tipo_despacho con las opciones de despacho
            $table->enum('tipo_despacho', [
                'despacho_anticipado', 
                'despacho_general', 
                'no_sabe_no_responde'
            ])->nullable()->after('descripcion');  // Agregarlo después del campo descripcion
        });
    }

    public function down()
    {
        Schema::table('ofertas_ruta', function (Blueprint $table) {
            // Eliminar el campo tipo_despacho
            $table->dropColumn('tipo_despacho');
        });
    }
}