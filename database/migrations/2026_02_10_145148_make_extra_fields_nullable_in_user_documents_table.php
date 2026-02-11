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
        Schema::table('user_documents', function (Blueprint $table) {
            // Hacer nullable los campos adicionales que causan problemas
            if (Schema::hasColumn('user_documents', 'tipo_documento')) {
                $table->string('tipo_documento')->nullable()->change();
            }
            if (Schema::hasColumn('user_documents', 'nombre_archivo')) {
                $table->string('nombre_archivo')->nullable()->change();
            }
            if (Schema::hasColumn('user_documents', 'ruta_archivo')) {
                $table->string('ruta_archivo')->nullable()->change();
            }
            if (Schema::hasColumn('user_documents', 'mime_type')) {
                $table->string('mime_type')->nullable()->change();
            }
            if (Schema::hasColumn('user_documents', 'estado')) {
                $table->string('estado')->nullable()->change();
            }
            if (Schema::hasColumn('user_documents', 'observaciones')) {
                $table->text('observaciones')->nullable()->change();
            }
            if (Schema::hasColumn('user_documents', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_documents', function (Blueprint $table) {
            // No revertimos para evitar problemas
        });
    }
};
