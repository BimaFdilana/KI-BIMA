<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kasir_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasir_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('toko_id')->constrained('toko')->onDelete('cascade');
            $table->decimal('shift_awal', 15, 2)->default(0);
            $table->decimal('shift_akhir', 15, 2)->nullable();
            $table->decimal('total_transaksi_tunai', 15, 2)->default(0);
            $table->decimal('shift_balance', 15, 2)->nullable();
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kasir_shifts');
    }
};
