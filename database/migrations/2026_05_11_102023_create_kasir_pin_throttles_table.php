<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kasir_pin_throttles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasir_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('toko_id')->constrained('toko')->onDelete('cascade');
            $table->integer('failed_attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('locked_until')->nullable();
            $table->timestamps();
            
            $table->unique(['kasir_id', 'toko_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('kasir_pin_throttles');
    }
};
