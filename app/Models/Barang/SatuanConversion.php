<?php

namespace App\Models\Barang;

use App\Models\Barang\BarangModel;
use App\Models\Barang\SatuanItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SatuanConversion extends Model
{
    protected $table = 'satuan_conversions';
    protected $fillable = [
        'barang_id',
        'from_satuan_id',
        'to_satuan_id',
        'conversion_factor'
    ];

    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id');
    }

    public function conversionFrom()
    {
        return $this->belongsTo(SatuanItem::class, 'from_satuan_id');
    }

    public function conversionTo()
    {
        return $this->belongsTo(SatuanItem::class, 'to_satuan_id');
    }
}
