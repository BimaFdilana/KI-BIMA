<?php

namespace App\Models\Toko;

use Illuminate\Database\Eloquent\Model;
use App\Models\Barang\BarangKI;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TokoKeranjang extends Model
{
    use HasFactory;
    protected $table = 'toko_keranjang';
    protected $fillable = [
        'order_id',
        'toko_id',
        'barangki_id',
        'quantity',
    ];

    public function toko()
    {
        return $this->belongsTo(TokoModel::class, 'toko_id');
    }

    public function barangKI()
    {
        return $this->belongsTo(BarangKI::class, 'barangki_id');
    }
}
