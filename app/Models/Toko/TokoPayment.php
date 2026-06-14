<?php

namespace App\Models\Toko;

use App\Models\Auth\UserModel;
use App\Models\PakDul\PayLatterPayment;
use App\Models\PakDul\PayLatterTransaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Toko\TokoModel;

class TokoPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'toko_payment';
    protected $fillable = [
        'transaction_id',
        'user_id',
        'toko_id',
        'total',
        'payment_type',
        'payment_method',
        'status',
        'admin_note',
        'snap_token',
    ];

    /**
     * Relasi dengan tabel TokoPesanan (one to many)
     */
    public function pesanan()
    {
        return $this->hasMany(TokoPesanan::class, 'payment_id');
    }

    /**
     * Relasi dengan User (many to one)
     */
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id')->withTrashed(); // Model User di-namespace dengan benar
    }

    public function progress()
    {
        return $this->hasMany(TokoPaymentProgress::class, 'payment_id');
    }
    /**
     * Relasi dengan Toko (many to one)
     */
    public function toko()
    {
        return $this->belongsTo(TokoModel::class, 'toko_id');
    }
    public function pakdulTransaksi()
    {
        return $this->hasOne(PayLatterTransaction::class, 'order_id');
    }

    public function pakdulPayments()
    {
        return $this->hasManyThrough(
            PayLatterPayment::class,
            PayLatterTransaction::class,
            'order_id', // foreign key on paylatter_transactions table that references toko_payments.id
            'paylatter_transaction_id', // foreign key on paylatter_payments table that references paylatter_transactions.id
            'id', // local key on toko_payments table
            'id' // local key on paylatter_transactions table
        );
    }

    // Scope untuk status
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopePartialSuccess($query)
    {
        return $query->where('status', 'partial_success');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Accessor untuk status yang mudah dibaca
    public function getStatusLabelAttribute()
    {
        $labels = [
            'paid' => 'Dibayar',
            'pending' => 'Menunggu',
            'failed' => 'Gagal',
            'unknown' => 'Tidak Diketahui',
            'partial_success' => 'Sebagian Berhasil',
            'success' => 'Berhasil',
            'delivery' => 'Pengiriman',
        ];

        return $labels[$this->status] ?? $this->status;
    }
}
