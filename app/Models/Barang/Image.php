<?php

namespace App\Models\Barang;

use Illuminate\Database\Eloquent\Model;
use App\Models\Barang\BarangModel;

class Image extends Model
{
    protected $table = 'barang_images'; // Sesuaikan dengan nama tabel gambar

    protected $fillable = [
        'url',
        'barang_id',
        'is_main'

    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    protected $attributes = [
        'is_main' => false,
    ];

    protected $appends = [
        'is_main_label',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getIsMainLabelAttribute()
    {
        return $this->is_main ? 'Ya' : 'Tidak';
    }
    // Definisikan relasi many-to-one dengan model BarangModel
    public function barang()
    {
        return $this->belongsTo(BarangModel::class);
    }
}
