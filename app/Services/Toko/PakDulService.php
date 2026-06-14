<?php

namespace App\Services\Toko;

use App\Models\Auth\UserModel;
use App\Models\PakDul\PayLatterAccount;
use App\Models\PakDul\PayLatterConfig;
use App\Models\PakDul\PayLatterTransaction;
use App\Models\PakDul\PayLatterPayment;
use App\Models\Toko\TokoModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class PakDulService
{
    /**
     * Inisialisasi akun PayLatter untuk user di toko tertentu
     */
    public function initializeAccount(UserModel $user, TokoModel $toko)
    {
        // Cek apakah toko memiliki PayLatter aktif
        if (!$toko->paylatter_enabled()) {
            throw new Exception('PayLatter tidak aktif di toko ini');
        }

        // Cek apakah sudah memiliki akun
        $existingAccount = PayLatterAccount::where('user_id', $user->id)
            ->where('toko_id', $toko->id)
            ->first();

        if ($existingAccount) {
            return $existingAccount;
        }

        // Ambil konfigurasi PayLatter toko
        $config = PayLatterConfig::first();
        if (!$config || !$config->is_active) {
            throw new Exception('Konfigurasi PayLatter tidak ditemukan atau tidak aktif');
        }

        // Buat akun baru
        $account = PayLatterAccount::create([
            'user_id' => $user->id,
            'toko_id' => $toko->id,
            'credit_limit' => $config->default_limit,
            'available_credit' => $config->default_limit,
            'used_credit' => 0,
            'status' => 'active'
        ]);

        return $account;
    }

    /**
     * Cek apakah user bisa menggunakan PayLatter untuk jumlah tertentu
     */
    public function canUsePaylatter(UserModel $user, TokoModel $toko, $amount)
    {
        $account = $this->getOrCreateAccount($user, $toko);

        return $account->status === 'active' &&
            $account->available_credit >= $amount &&
            !$this->hasOverdueTransactions($account);
    }

    /**
     * Buat transaksi PayLatter baru
     */
    public function createTransaction(UserModel $user, TokoModel $toko, $amount, $orderId = null)
    {
        \Log::info('Pembayaran Pakdul' . $user->id . ' ' . $toko->id . ' ' . $amount);
        return DB::transaction(function () use ($user, $toko, $amount, $orderId) {
            $account = $this->getOrCreateAccount($user, $toko);

            if (!$this->canUsePaylatter($user, $toko, $amount)) {
                throw new Exception('Tidak dapat menggunakan PayLatter untuk transaksi ini');
            }

            $config = PayLatterConfig::first();

            // Buat transaksi
            $transaction = PayLatterTransaction::create([
                'transaction_code' => PayLatterTransaction::generateTransactionCode(),
                'paylatter_account_id' => $account->id,
                'order_id' => $orderId,
                'principal_amount' => $amount,
                'total_amount' => $amount,
                'remaining_amount' => $amount,
                'due_date' => now()->addDays($config->max_loan_days),
                'grace_period_end' => now()->addDays($config->grace_period_days),
                'status' => 'active'
            ]);

            // Update akun
            $account->update([
                'used_credit' => $account->used_credit + $amount,
                'available_credit' => $account->available_credit - $amount
            ]);

            return $transaction;
        });
    }

    /**
     * Proses pembayaran PayLatter
     */
    public function processPayment(PayLatterTransaction $transaction, $amount, $paymentMethod, $paymentDetails = [])
    {
        return DB::transaction(function () use ($transaction, $amount, $paymentMethod, $paymentDetails) {
            // Update total amount dengan bunga dan denda terbaru
            $transaction->updateTotalAmount();

            if ($amount > $transaction->remaining_amount) {
                throw new Exception('Jumlah pembayaran melebihi sisa tagihan');
            }

            // Buat record pembayaran
            $payment = PayLatterPayment::create([
                'payment_code' => PayLatterPayment::generatePaymentCode(),
                'paylatter_transaction_id' => $transaction->id,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'payment_details' => $paymentDetails,
                'status' => 'success',
                'paid_at' => now()
            ]);

            // Update transaksi
            $transaction->update([
                'paid_amount' => $transaction->paid_amount + $amount,
                'remaining_amount' => $transaction->remaining_amount - $amount
            ]);

            $account = $transaction->account;

            // Jika lunas
            if ($transaction->remaining_amount <= 0) {
                $transaction->update([
                    'status' => 'paid',
                    'paid_at' => now()
                ]);

                // Update available credit
                $account->update([
                    'used_credit' => $account->used_credit - $transaction->principal_amount,
                    'available_credit' => $account->available_credit + $transaction->principal_amount,
                    'last_payment_date' => now()
                ]);

                // Update statistik pembayaran
                if ($transaction->paid_at <= $transaction->due_date) {
                    $account->increment('successful_payments');
                } else {
                    $account->increment('late_payments');
                }

                // Cek untuk peningkatan limit
                $this->checkAndIncreaseCreditLimit($account);
            }

            return $payment;
        });
    }

    /**
     * Cek dan tingkatkan limit kredit otomatis
     */
    public function checkAndIncreaseCreditLimit(PayLatterAccount $account)
    {
        if (!$account->isEligibleForLimitIncrease()) {
            return false;
        }

        $increaseAmount = $account->calculateLimitIncrease();
        if ($increaseAmount <= 0) {
            return false;
        }

        $config = PayLatterConfig::first();
        $newLimit = $account->credit_limit + $increaseAmount;

        // Pastikan tidak melebihi maksimal limit
        if ($newLimit > $config->max_limit) {
            $newLimit = $config->max_limit;
            $increaseAmount = $newLimit - $account->credit_limit;
        }

        if ($increaseAmount <= 0) {
            return false;
        }

        return $this->increaseCreditLimit($account, $increaseAmount, 'good_payment', 'Peningkatan otomatis berdasarkan riwayat pembayaran yang baik');
    }

    /**
     * Tingkatkan limit kredit
     */
    public function increaseCreditLimit(PayLatterAccount $account, $increaseAmount, $reason = 'manual_increase', $notes = null, $approvedBy = null)
    {
        return DB::transaction(function () use ($account, $increaseAmount, $reason, $notes, $approvedBy) {
            $oldLimit = $account->credit_limit;
            $newLimit = $oldLimit + $increaseAmount;

            // Update akun
            $account->update([
                'credit_limit' => $newLimit,
                'available_credit' => $account->available_credit + $increaseAmount
            ]);

            // Simpan riwayat
            PayLatterLimitHistory::create([
                'paylatter_account_id' => $account->id,
                'old_limit' => $oldLimit,
                'new_limit' => $newLimit,
                'increase_amount' => $increaseAmount,
                'reason' => $reason,
                'notes' => $notes,
                'approved_by' => $approvedBy
            ]);

            return $account;
        });
    }

    /**
     * Dapatkan atau buat akun PayLatter
     */
    private function getOrCreateAccount(UserModel $user, TokoModel $toko)
    {
        $config = PayLatterConfig::first();
        return PayLatterAccount::firstOrCreate(
            ['user_id' => $user->id, 'toko_id' => $toko->id],
            [
                'credit_limit' => $config->default_limit,
                'available_credit' => $config->default_limit,
                'used_credit' => 0,
                'status' => 'active'
            ]
        );
    }

    /**
     * Cek apakah ada transaksi yang menunggak
     */
    private function hasOverdueTransactions(PayLatterAccount $account)
    {
        return $account->transactions()
            ->where('status', 'overdue')
            ->exists();
    }

    /**
     * Dapatkan ringkasan akun PayLatter
     */
    public function getAccountSummary(UserModel $user, TokoModel $toko)
    {
        $account = PayLatterAccount::where('user_id', $user->id)
            ->where('toko_id', $toko->id)
            ->first();

        if (!$account) {
            return null;
        }

        $activeTransactions = $account->transactions()
            ->get();

        $activeFormated = $activeTransactions->map(function ($transaction) {
            return [
                'transaction_code' => $transaction->transaction_code,
                'order_id' => $transaction->order->transaction_id,
                'principal_amount' => $transaction->principal_amount,
                'interest_amount' => $transaction->interest_amount,
                'penalty_amount' => $transaction->penalty_amount,
                'total_amount' => $transaction->total_amount,
                'paid_amount' => $transaction->paid_amount,
                'remaining_amount' => $transaction->remaining_amount,
                'due_date' => $transaction->due_date,
                'grace_period_end' => $transaction->grace_period_end,
                'status' => $transaction->status,
                'paid_at' => $transaction->paid_at,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at
            ];
        });

        $successfullTransactions = $account->transactions()
            ->where('status', 'paid')
            ->get();

        $successFormatted = $successfullTransactions->map(function ($transaction) {
            return [
                'transaction_code' => $transaction->transaction_code,
                'order_id' => $transaction->order->transaction_id,
                'principal_amount' => $transaction->principal_amount,
                'interest_amount' => $transaction->interest_amount,
                'penalty_amount' => $transaction->penalty_amount,
                'total_amount' => $transaction->total_amount,
                'paid_amount' => $transaction->paid_amount,
                'remaining_amount' => $transaction->remaining_amount,
                'due_date' => $transaction->due_date,
                'grace_period_end' => $transaction->grace_period_end,
                'status' => $transaction->status,
                'paid_at' => $transaction->paid_at,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at
            ];
        });

        foreach ($activeTransactions as $transaction) {
            $transaction->updateTotalAmount();
        }

        return [
            'account' => $account->fresh(),
            'dataPakdul' => $activeFormated,
            'total_outstanding' => $activeTransactions->sum('remaining_amount'),
            'credit_score' => $account->calculateCreditScore(),
            'eligible_for_increase' => $account->isEligibleForLimitIncrease(),
            'suggested_increase' => $account->calculateLimitIncrease()

        ];
    }

    /**
     * Proses transaksi yang jatuh tempo (untuk cron job)
     */
    public function processOverdueTransactions()
    {
        $overdueTransactions = PayLatterTransaction::where('status', 'active')
            ->where('due_date', '<', now())
            ->get();

        foreach ($overdueTransactions as $transaction) {
            $transaction->updateTotalAmount();
        }

        return $overdueTransactions->count();
    }
}
