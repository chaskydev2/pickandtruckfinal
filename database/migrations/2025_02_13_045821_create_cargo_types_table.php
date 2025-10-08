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
        Schema::create('cargo_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Nombre del tipo de carga
            $table->text('description')->nullable();    // Descripción opcional
    

            $table->timestamps();                       // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cargo_types');
    }
};
