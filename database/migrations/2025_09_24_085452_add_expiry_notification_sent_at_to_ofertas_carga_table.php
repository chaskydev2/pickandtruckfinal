<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('ofertas_carga', function (Blueprint $table) {
            $table->timestamp('expiry_notification_sent_at')->nullable()->after('updated_at');
        });
    }
    public function down(): void {
        Schema::table('ofertas_carga', function (Blueprint $table) {
            $table->dropColumn('expiry_notification_sent_at');
        });
    }
};