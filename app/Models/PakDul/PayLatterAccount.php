<?php

namespace App\Models\PakDul;

use App\Models\Auth\UserModel;
use App\Models\Toko\TokoModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayLatterAccount extends Model
{
    use HasFactory;

    protected $table = 'paylatter_accounts';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'toko_id',
        'credit_limit',
        'available_credit',
        'used_credit',
        'payment_history_score',
        'successful_payments',
        'late_payments',
        'status',
        'last_payment_date'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'available_credit' => 'decimal:2',
        'used_credit' => 'decimal:2',
        'last_payment_date' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class);
    }

    public function toko()
    {
        return $this->belongsTo(TokoModel::class);
    }

    public function transactions()
    {
        return $this->hasMany(PayLatterTransaction::class, 'paylatter_account_id');
    }

    public function limitHistories()
    {
        return $this->hasMany(PayLatterLimitHistory::class);
    }

    // Hitung skor kredit berdasarkan riwayat pembayaran
    public function calculateCreditScore()
    {
        $totalPayments = $this->successful_payments + $this->late_payments;
        if ($totalPayments == 0) return 0;

        $onTimeRate = ($this->successful_payments / $totalPayments) * 100;

        if ($onTimeRate >= 95) return 5; // Excellent
        if ($onTimeRate >= 85) return 4; // Good
        if ($onTimeRate >= 70) return 3; // Fair
        if ($onTimeRate >= 50) return 2; // Poor
        return 1; // Bad
    }

    // Cek apakah layak untuk peningkatan limit
    public function isEligibleForLimitIncrease()
    {
        $creditScore = $this->calculateCreditScore();
        $hasRecentLatePayment = $this->transactions()
            ->where('status', 'overdue')
            ->where('created_at', '>=', now()->subMonths(3))
            ->exists();

        return $creditScore >= 4 && !$hasRecentLatePayment && $this->successful_payments >= 3;
    }

    // Hitung peningkatan limit yang disarankan
    public function calculateLimitIncrease()
    {
        if (!$this->isEligibleForLimitIncrease()) return 0;

        $creditScore = $this->calculateCreditScore();
        $baseIncrease = $this->credit_limit * 0.2; // 20% dari limit saat ini

        $multiplier = match ($creditScore) {
            5 => 1.5, // Excellent: 30%
            4 => 1.0, // Good: 20%
            default => 0.5 // 10%
        };

        return $baseIncrease * $multiplier;
    }
}
