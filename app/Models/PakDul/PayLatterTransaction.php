<?php

namespace App\Models\PakDul;

use App\Models\Toko\TokoPayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PakDul\PayLatterConfig;

class PayLatterTransaction extends Model
{
    use HasFactory;

    protected $table = 'paylatter_transactions';
    protected $fillable = [
        'transaction_code',
        'paylatter_account_id',
        'order_id',
        'principal_amount',
        'interest_amount',
        'penalty_amount',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'due_date',
        'grace_period_end',
        'status',
        'paid_at'
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'due_date' => 'date',
        'grace_period_end' => 'date',
        'paid_at' => 'datetime'
    ];

    public function account()
    {
        return $this->belongsTo(PayLatterAccount::class, 'paylatter_account_id');
    }

    public function order()
    {
        return $this->belongsTo(TokoPayment::class);
    }

    public function payments()
    {
        return $this->hasMany(PayLatterPayment::class, 'payment_code');
    }

    // Generate kode transaksi unik
    public static function generateTransactionCode()
    {
        do {
            $code = 'PD' . date('Ymd') . rand(1000, 9999);
        } while (self::where('transaction_code', $code)->exists());

        return $code;
    }

    // Hitung bunga berdasarkan hari
    public function calculateInterest()
    {
        if (now()->lte($this->grace_period_end)) {
            return 0; // Tidak ada bunga dalam masa tenggang
        }

        $config = PayLatterConfig::first();
        $daysOverGrace = now()->diffInDays($this->grace_period_end);
        $monthlyRate = $config->interest_rate / 100;
        $dailyRate = $monthlyRate / 30;

        return $this->principal_amount * $dailyRate * $daysOverGrace;
    }

    // Hitung denda keterlambatan
    public function calculatePenalty()
    {
        if (now()->lte($this->due_date) || $this->status === 'paid') {
            return 0;
        }

        $config = PayLatterConfig::first();
        $daysOverdue = now()->diffInDays($this->due_date);
        $penaltyRate = $config->penalty_rate / 100;

        return $this->principal_amount * $penaltyRate * $daysOverdue;
    }

    // Update total amount dengan bunga dan denda terbaru
    public function updateTotalAmount()
    {
        $this->interest_amount = $this->calculateInterest();
        $this->penalty_amount = $this->calculatePenalty();
        $this->total_amount = $this->principal_amount + $this->interest_amount + $this->penalty_amount;
        $this->remaining_amount = $this->total_amount - $this->paid_amount;

        if ($this->remaining_amount <= 0) {
            $this->status = 'paid';
            $this->paid_at = now();
        } elseif (now()->gt($this->due_date)) {
            $this->status = 'overdue';
        }

        $this->save();
    }
}
