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
        Schema::create('otp_auth', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type', 20); // phone, email, password, device, etc.
            $table->string('identifier'); // phone number, email, device id, etc.
            $table->string('code');
            $table->timestamp('expires_at');
            $table->integer('attempts')->default(0); // Optional: track failed attempts
            $table->timestamps();

            // Index untuk mempercepat pencarian
            $table->index(['user_id', 'type', 'identifier']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_auth');
    }
};
