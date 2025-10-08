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
        Schema::create('ofertas_ruta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('tipo_camion'); // Campo para el tipo de camión como string
            $table->string('origen');
            $table->string('destino');
            $table->dateTime('fecha_inicio');
            // $table->dateTime('fecha_fin'); // Se elimina la fecha de fin
            $table->integer('capacidad');
            // Otros campos relevantes para la oferta de ruta, por ejemplo:
            // $table->text('descripcion')->nullable();
            $table->decimal('precio_referencial', 8, 2);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ofertas_ruta');
    }
};