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
        Schema::create('truck_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Nombre del tipo de camión
            $table->text('description')->nullable();    // Descripción opcional
            $table->boolean('active')->default(true);   // Estado activo/inactivo
            $table->timestamps();                       // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('truck_types');
    }
};