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
        Schema::create('ofertas_carga', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('tipo_carga');
            $table->string('origen');
            $table->string('destino');
            // $table->dateTime('fecha_inicio'); // Se mantiene eliminado (opcional)
            $table->decimal('peso', 8, 2);
            $table->dateTime('fecha_inicio');
            // $table->string('dimensiones'); // Se elimina el campo dimensiones
            // Otros campos relevantes para la oferta de carga, por ejemplo:
            // $table->text('descripcion')->nullable();
            $table->decimal('presupuesto', 8, 2); // Se cambia el nombre a presupuesto
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ofertas_carga');
    }
};