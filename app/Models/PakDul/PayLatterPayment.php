<?php

namespace App\Models\PakDul;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayLatterPayment extends Model
{
    use HasFactory;

    protected $table = 'paylatter_payments';
    protected $fillable = [
        'payment_code',
        'paylatter_transaction_id',
        'amount',
        'payment_method',
        'payment_details',
        'status',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'json',
        'paid_at' => 'datetime'
    ];

    public function transaction()
    {
        return $this->belongsTo(PayLatterTransaction::class, 'paylatter_transaction_id');
    }

    // Generate kode pembayaran unik
    public static function generatePaymentCode()
    {
        do {
            $code = 'PAY' . date('Ymd') . rand(1000, 9999);
        } while (self::where('payment_code', $code)->exists());

        return $code;
    }
}
