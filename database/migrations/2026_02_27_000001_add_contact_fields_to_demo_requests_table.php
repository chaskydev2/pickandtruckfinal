<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            // Make user_id nullable (demo can exist without a user account)
            $table->foreignId('user_id')->nullable()->change();

            // Add contact fields directly on demo_requests
            $table->string('name')->nullable()->after('user_id');
            $table->string('email')->nullable()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->string('company_name')->nullable()->after('phone');
            $table->string('company_type')->nullable()->after('company_name');
        });
    }

    public function down(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            $table->dropColumn(['name', 'email', 'phone', 'company_name', 'company_type']);
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
