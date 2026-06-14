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
        Schema::create('toko_biaya_operasional', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('toko_id');
            $table->string('kategori', 100); // e.g., "Listrik", "Gaji", "Sewa", "Transportasi"
            $table->text('deskripsi')->nullable();
            $table->bigInteger('jumlah'); // Amount in rupiah
            $table->date('tanggal'); // Date of expense
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('toko_id')
                ->references('id')
                ->on('toko')
                ->onDelete('cascade');

            // Index for faster queries
            $table->index(['toko_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toko_biaya_operasional');
    }
};
