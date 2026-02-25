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
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('tier', ['pioneros', 'visionarios', 'conservadores']);
            $table->enum('billing_cycle', ['monthly', 'annually']);
            $table->decimal('price_paid', 10, 2)->nullable();
            $table->enum('status', ['pending', 'active', 'cancelled'])->default('pending');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('payment_method', ['qr', 'crypto', 'other'])->nullable();
            $table->string('payment_proof_path')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
