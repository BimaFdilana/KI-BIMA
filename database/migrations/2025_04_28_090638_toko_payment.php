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
        // Membuat tabel toko_payment
        Schema::create('toko_payment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('transaction_id')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('toko_id');
            $table->decimal('total', 10, 2);
            $table->string('payment_method')->nullable();
            $table->enum('payment_type', ['Virtual', 'Cash', 'Pakdul'])->default('Cash');
            $table->text('admin_note')->nullable();
            $table->enum('status', [
                'paid',
                'pending',
                'failed',
                'unknown',
                'partial_success',  // Status baru untuk transaksi yang sebagian berhasil
                'success',
                'delivery',
                'cancelled',
                'refund_requested',
                'refunded',
            ])->default('pending');
            $table->string('snap_token')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('toko_id')->references('id')->on('toko')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Membuat tabel toko_pesanan dengan status yang lebih detail
        Schema::create('toko_pesanan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('barangki_id');
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total', 10, 2);
            $table->enum('status', [
        'paid',
                'pending',
                'failed',
                'unknown',
                'partial_success',  // Status baru untuk transaksi yang sebagian berhasil
                'success',
                'delivery',
                'cancelled',
                'refund_requested',
                'refunded',
            ])->default('pending');

            // Field tambahan untuk tracking
            $table->text('notes')->nullable(); // Catatan untuk masalah atau delay
            $table->timestamp('estimated_delivery')->nullable(); // Estimasi pengiriman
            $table->timestamp('actual_delivery')->nullable(); // Waktu pengiriman aktual

            $table->timestamps();

            $table->foreign('payment_id')->references('id')->on('toko_payment')->onDelete('cascade');
            $table->foreign('barangki_id')->references('id')->on('barang_ki')->onDelete('cascade');
        });

        Schema::create('toko_keranjang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('toko_id');
            $table->unsignedBigInteger('barangki_id');
            $table->integer('quantity')->nullable()->default(0);
            $table->timestamps();

            $table->foreign('toko_id')->references('id')->on('toko')->onDelete('cascade');
            $table->foreign('barangki_id')->references('id')->on('barang_ki')->onDelete('cascade');
        });

        // Tabel utama untuk transaksi penjualan
        Schema::create('toko_selling', function (Blueprint $table) {
            $table->id('increment_id');
            $table->string('id_transaction')->unique();
            $table->foreignId('toko_id')->constrained('toko')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_harga', 15, 2);
            $table->enum('status', [
                'pending',
                'failed',
                'partial_success',  // Status baru untuk penjualan yang sebagian berhasil
                'success',
            ])->default('pending');
            $table->string('metode_pembayaran');
            $table->boolean('is_online')->default(false);
            $table->timestamps();
        });

        // Tabel detail transaksi penjualan dengan status per item
        Schema::create('toko_selling_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->foreign('transaction_id')
                ->references('increment_id')
                ->on('toko_selling')
                ->onDelete('cascade');
            $table->foreignId('barangki_id')
                ->constrained('barang_ki')
                ->onDelete('cascade');
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);

            // Status per item dalam selling detail
            $table->enum('item_status', [
                'pending',
                'confirmed',
                'processing',
                'ready',
                'delivered',
                'problem',
                'delayed',
                'cancelled',
                'success'
            ])->default('pending');

            $table->text('notes')->nullable(); // Catatan khusus untuk item ini
            $table->timestamp('estimated_delivery')->nullable();
            $table->timestamp('actual_delivery')->nullable();

            $table->timestamps();
        });

        // Tabel log untuk tracking perubahan status
        Schema::create('toko_transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type'); // 'payment' atau 'selling'
            $table->unsignedBigInteger('transaction_id'); // ID dari toko_payment atau toko_selling
            $table->unsignedBigInteger('item_id')->nullable(); // ID dari toko_pesanan atau toko_selling_detail
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable(); // User yang mengupdate
            $table->timestamps();

            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toko_transaction_logs');
        Schema::dropIfExists('toko_selling_detail');
        Schema::dropIfExists('toko_selling');
        Schema::dropIfExists('toko_pesanan');
        Schema::dropIfExists('toko_payment');
        Schema::dropIfExists('toko_keranjang');
    }
};
