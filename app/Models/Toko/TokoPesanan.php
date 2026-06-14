<?php

namespace App\Models\Toko;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Barang\BarangKI; // Pastikan model User di-import dengan namespace yang benar
use App\Models\Toko\TokoPaymentProgress;

class TokoPesanan extends Model
{
    use HasFactory;

    protected $table = 'toko_pesanan';
    protected $fillable = [
        'payment_id',
        'barangki_id',
        'price',
        'quantity',
        'total',
        'status',
        'notes',
        'estimated_delivery',
        'actual_delivery'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'estimated_delivery' => 'datetime',
        'actual_delivery' => 'datetime',
    ];

    public function progress()
    {
        return $this->hasMany(TokoPaymentProgress::class, 'item_id');
    }

    /**
     * Relasi dengan TokoPayment (many to one)
     */
    public function payment()
    {
        return $this->belongsTo(TokoPayment::class, 'payment_id');
    }

    /**
     * Relasi dengan Barang (many to one)
     */
    public function barangKI()
    {
        return $this->belongsTo(BarangKI::class, 'barangki_id');
    }
}
