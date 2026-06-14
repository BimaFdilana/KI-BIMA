<?php

namespace App\Models\Toko;

use App\Models\Barang\BarangKI;
use App\Models\Barang\SatuanItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangToko extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'barang_toko';
    
    protected $fillable = [
        'toko_id',
        'barangki_id',
        'quantity',
        'price_buy',
        'price_sell',
        'price_percentage',
        'sold'
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    /**
     * Get the barang_ki that owns the barang_toko
     */
    public function barangKi()
    {
        return $this->belongsTo(BarangKI::class, 'barangki_id');
    }

    /**
     * Get the satuan through barang_ki
     */
    public function satuan()
    {
        return $this->belongsTo(SatuanItem::class, 'satuan_id');
    }

    public function toko()
    {
        return $this->belongsTo(TokoModel::class, 'toko_id');
    }
    /**
     * Get the barang through barang_ki
     * This is a shortcut relationship
     */
    public function barang()
    {
        return $this->belongsTo(BarangKI::class, 'barangki_id')->with('barang');
    }
}