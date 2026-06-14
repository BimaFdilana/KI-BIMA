<?php

namespace App\Models\Toko;

use Illuminate\Database\Eloquent\Model;

class BiayaOperasional extends Model
{
    protected $table = 'toko_biaya_operasional';

    protected $fillable = [
        'toko_id',
        'kategori',
        'deskripsi',
        'jumlah',
        'tanggal'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'integer',
    ];

    /**
     * Relasi ke toko
     */
    public function toko()
    {
        return $this->belongsTo(TokoModel::class, 'toko_id');
    }
}
