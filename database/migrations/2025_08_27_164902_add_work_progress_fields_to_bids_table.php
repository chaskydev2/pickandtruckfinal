<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            // fecha cuando ambas partes confirman
            if (!Schema::hasColumn('bids', 'fecha_finalizacion')) {
                $table->timestamp('fecha_finalizacion')->nullable()->after('estado');
            }
            // flags de confirmación (si aún no existen)
            if (!Schema::hasColumn('bids', 'confirmacion_usuario_a')) {
                $table->boolean('confirmacion_usuario_a')->default(false)->after('fecha_finalizacion');
            }
            if (!Schema::hasColumn('bids', 'confirmacion_usuario_b')) {
                $table->boolean('confirmacion_usuario_b')->default(false)->after('confirmacion_usuario_a');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            if (Schema::hasColumn('bids', 'fecha_finalizacion')) {
                $table->dropColumn('fecha_finalizacion');
            }
            if (Schema::hasColumn('bids', 'confirmacion_usuario_a')) {
                $table->dropColumn('confirmacion_usuario_a');
            }
            if (Schema::hasColumn('bids', 'confirmacion_usuario_b')) {
                $table->dropColumn('confirmacion_usuario_b');
            }
        });
    }
};
