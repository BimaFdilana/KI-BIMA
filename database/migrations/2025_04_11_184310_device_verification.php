<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('device_id')->unique();
            $table->ipAddress('ip_address');
            $table->string('device_name')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('last_active_at');
            $table->timestamp('expires_at')->default(now());
            $table->string('fcm_token')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'device_id', 'ip_address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
