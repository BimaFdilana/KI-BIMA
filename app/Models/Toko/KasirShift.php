<?php

namespace App\Models\Toko;

use App\Models\Auth\UserModel;
use Illuminate\Database\Eloquent\Model;

class KasirShift extends Model
{
    protected $table = 'kasir_shifts';

    protected $fillable = [
        'kasir_id',
        'toko_id',
        'shift_awal',
        'shift_akhir',
        'total_transaksi_tunai',
        'shift_balance',
        'discrepancy_amount',
        'discrepancy_status',
        'verified_by',
        'verified_at',
        'verification_notes',
        'opened_at',
        'closed_at',
        'notes',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function kasir()
    {
        return $this->belongsTo(UserModel::class, 'kasir_id');
    }

    public function toko()
    {
        return $this->belongsTo(TokoModel::class, 'toko_id');
    }
}
