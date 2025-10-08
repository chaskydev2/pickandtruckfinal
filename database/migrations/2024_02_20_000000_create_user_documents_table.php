<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_documents')) {
            // Crear la tabla solo si no existe
            Schema::create('user_documents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('required_document_id');
                $table->string('file_path');
                $table->enum('status', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
                $table->text('comments')->nullable();
                $table->timestamps();

                // Agregar índices para mejorar el rendimiento
                $table->index('user_id');
                $table->index('required_document_id');
            });
        }

        // Verificar si las tablas referenciadas existen
        if (Schema::hasTable('users') && Schema::hasTable('required_documents')) {
            Schema::table('user_documents', function (Blueprint $table) {
                // Verificar si la columna user_id existe
                if (Schema::hasColumn('user_documents', 'user_id')) {
                    // Eliminar la restricción de clave foránea si existe
                    $sm = Schema::getConnection()->getDoctrineSchemaManager();
                    $foreignKeys = $sm->listTableForeignKeys('user_documents');
                    
                    $foreignKeyExists = false;
                    foreach ($foreignKeys as $foreignKey) {
                        if (in_array('user_id', $foreignKey->getLocalColumns())) {
                            $foreignKeyExists = true;
                            break;
                        }
                    }
                    
                    if (!$foreignKeyExists) {
                        $table->foreign('user_id')
                            ->references('id')
                            ->on('users')
                            ->onDelete('cascade');
                    }
                }
                
                // Verificar si la columna required_document_id existe
                if (Schema::hasColumn('user_documents', 'required_document_id')) {
                    // Verificar si la restricción de clave foránea ya existe
                    $sm = Schema::getConnection()->getDoctrineSchemaManager();
                    $foreignKeys = $sm->listTableForeignKeys('user_documents');
                    
                    $foreignKeyExists = false;
                    foreach ($foreignKeys as $foreignKey) {
                        if (in_array('required_document_id', $foreignKey->getLocalColumns())) {
                            $foreignKeyExists = true;
                            break;
                        }
                    }
                    
                    if (!$foreignKeyExists) {
                        $table->foreign('required_document_id')
                            ->references('id')
                            ->on('required_documents')
                            ->onDelete('cascade');
                    }
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_documents');
    }
};
