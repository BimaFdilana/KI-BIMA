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
        Schema::create('recovery_code', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('code');
            $table->timestamp('last_used')->nullable();
            $table->integer('hasUsed')->default(0); // Optional: track failed attempts
            $table->string('last_used_device')->nullable();
            $table->timestamps();

            // Index untuk mempercepat pencarian
            $table->index(['user_id', 'hasUsed', 'last_used', 'last_used_device']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recovery_code');
    }
};
