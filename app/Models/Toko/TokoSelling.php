<?php

namespace App\Models\Toko;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\UserModel;

class TokoSelling extends Model
{
    use HasFactory;

    protected $table = 'toko_selling';

    protected $primaryKey = 'increment_id';

    protected $fillable = [
        'id_transaction',
        'toko_id',
        'user_id',
        'total_harga',
        'status',
        'metode_pembayaran',
        'is_online'
    ];
    protected $casts = [
        'total_harga' => 'decimal:2',
        'is_online' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }

    // Define relationship with TokoSellingDetail
    public function details()
    {
        return $this->hasMany(TokoSellingDetail::class, 'transaction_id', 'increment_id');
    }

    // Define relationship with TokoModel
    public function toko()
    {
        return $this->belongsTo(TokoModel::class, 'toko_id', 'id');
    }
}
