<?php
// Migration 1: Tabel Konfigurasi PayLatter per Toko
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paylatter_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_id')->constrained('toko')->onDelete('cascade');
            $table->decimal('default_limit', 15, 2)->default(500000); // Limit default
            $table->decimal('min_limit', 15, 2)->default(100000); // Minimum limit
            $table->decimal('max_limit', 15, 2)->default(5000000); // Maximum limit
            $table->integer('grace_period_days')->default(7); // Periode bebas bunga
            $table->decimal('interest_rate', 5, 2)->default(5.00); // Bunga per bulan (%)
            $table->integer('max_loan_days')->default(30); // Maksimal hari pinjaman
            $table->decimal('penalty_rate', 5, 2)->default(2.00); // Denda keterlambatan (%)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('paylatter_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('toko_id')->constrained('toko')->onDelete('cascade');
            $table->decimal('credit_limit', 15, 2); // Limit kredit saat ini
            $table->decimal('available_credit', 15, 2); // Kredit tersedia
            $table->decimal('used_credit', 15, 2)->default(0); // Kredit terpakai
            $table->integer('payment_history_score')->default(0); // Skor riwayat pembayaran
            $table->integer('successful_payments')->default(0); // Jumlah pembayaran sukses
            $table->integer('late_payments')->default(0); // Jumlah pembayaran terlambat
            $table->enum('status', ['active', 'suspended', 'closed'])->default('active');
            $table->timestamp('last_payment_date')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'toko_id']); // Satu akun per user per toko
        });

        Schema::create('paylatter_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->foreignId('paylatter_account_id')->constrained('paylatter_accounts')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('toko_payment')->onDelete('set null');
            $table->decimal('principal_amount', 15, 2); // Jumlah pokok
            $table->decimal('interest_amount', 15, 2)->default(0); // Bunga
            $table->decimal('penalty_amount', 15, 2)->default(0); // Denda
            $table->decimal('total_amount', 15, 2); // Total yang harus dibayar
            $table->decimal('paid_amount', 15, 2)->default(0); // Jumlah yang sudah dibayar
            $table->decimal('remaining_amount', 15, 2); // Sisa yang harus dibayar
            $table->date('due_date'); // Tanggal jatuh tempo
            $table->date('grace_period_end'); // Akhir periode bebas bunga
            $table->enum('status', ['active', 'paid', 'overdue', 'cancelled'])->default('active');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('paylatter_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_code')->unique();
            $table->foreignId('paylatter_transaction_id')->constrained('paylatter_transactions')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'transfer', 'ewallet', 'card']);
            $table->text('payment_details')->nullable(); // JSON untuk detail pembayaran
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('paylatter_limit_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paylatter_account_id')->constrained('paylatter_accounts')->onDelete('cascade');
            $table->decimal('old_limit', 15, 2);
            $table->decimal('new_limit', 15, 2);
            $table->decimal('increase_amount', 15, 2);
            $table->enum('reason', ['good_payment', 'manual_increase', 'promotion']);
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::table('toko', function (Blueprint $table) {
            $table->boolean('paylatter_enabled')->default(false)->after('status');
            $table->decimal('paylatter_fee_percentage', 5, 2)->default(0)->after('paylatter_enabled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paylatter_configs');
        Schema::dropIfExists('paylatter_accounts');
        Schema::dropIfExists('paylatter_transactions');
        Schema::dropIfExists('paylatter_payments');
        Schema::dropIfExists('paylatter_limit_histories');
        Schema::table('toko', function (Blueprint $table) {
            $table->dropColumn(['paylatter_enabled', 'paylatter_fee_percentage']);
        });
    }
};
