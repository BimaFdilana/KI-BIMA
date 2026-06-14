<?php

namespace App\Models\Barang;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'brands';
    protected $fillable = ['name', 'description'];
    public $timestamps = false;

    // Relationship with items
    public function barang()
    {
        return $this->hasMany(BarangModel::class);
    }
}
