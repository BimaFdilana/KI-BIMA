<?php

namespace App\Models\BelanjaCepat;

use App\Models\Barang\BarangKI;
use App\Models\Toko\TokoModel;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $table = 'penilaian';
    
    protected $fillable = [
        'toko_id',
        'barangki_id',
        'subkriteria_id',
        'amount'
    ];

    /**
     * Get the toko that owns this penilaian.
     */
    public function toko()
    {
        return $this->belongsTo(TokoModel::class, 'toko_id');
    }

    /**
     * Get the barang KI that owns this penilaian.
     */
    public function barangKI()
    {
        return $this->belongsTo(BarangKI::class, 'barangki_id');
    }

    /**
     * Get the subkriteria that owns this penilaian.
     */
    public function subkriteria()
    {
        return $this->belongsTo(Subkriteria::class, 'subkriteria_id');
    }
}