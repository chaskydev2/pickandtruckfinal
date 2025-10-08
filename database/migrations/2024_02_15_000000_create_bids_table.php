<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->morphs('bideable'); // Esto creará bideable_type y bideable_id
            $table->decimal('monto', 10, 2);
            $table->dateTime('fecha_hora');
            $table->text('comentario')->nullable();
            $table->enum('estado', ['pendiente', 'aceptado', 'rechazado', 'pendiente_confirmacion', 'terminado'])->default('pendiente');
            $table->boolean('confirmacion_usuario_a')->default(false);
            $table->boolean('confirmacion_usuario_b')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
