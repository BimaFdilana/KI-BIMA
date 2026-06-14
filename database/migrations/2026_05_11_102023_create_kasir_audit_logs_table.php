<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kasir_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasir_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('toko_id')->constrained('toko')->onDelete('cascade');
            $table->string('action'); // SHIFT_OPEN, SHIFT_CLOSE, PIN_VERIFY_SUCCESS, PIN_VERIFY_FAILED, etc.
            $table->string('status'); // success, failed, locked
            $table->text('details')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index('kasir_id');
            $table->index('toko_id');
            $table->index('action');
        });
    }

    public function down(): void {
        Schema::dropIfExists('kasir_audit_logs');
    }
};
