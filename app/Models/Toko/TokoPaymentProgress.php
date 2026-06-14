<?php

namespace App\Models\Toko;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\UserModel;

class TokoPaymentProgress extends Model
{
    use HasFactory;

    protected $table = 'toko_payment_progress';

    protected $fillable = [
        'payment_id',
        'status',
        'keterangan',
        'user_id',
        'created_at',
        'updated_at'
    ];

    // Possible statuses matching database enum
    const STATUS_PAID = 'paid';
    const STATUS_PENDING = 'pending';
    const STATUS_FAILED = 'failed';
    const STATUS_UNKNOWN = 'unknown';
    const STATUS_PARTIAL_SUCCESS = 'partial_success';
    const STATUS_SUCCESS = 'success';
    const STATUS_DELIVERY = 'delivery';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUND_REQUESTED = 'refund_requested';
    const STATUS_REFUNDED = 'refunded';
    // Relationship with pesanan
    public function payment()
    {
        return $this->belongsTo(TokoPayment::class, 'payment_id');
    }

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    // Factory definition
    protected static function newFactory()
    {
        return \Database\Factories\Toko\TokoPaymentProgressFactory::new();
    }
}
