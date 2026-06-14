<?php

namespace App\Models\Barang;

use Illuminate\Database\Eloquent\Model;

class TypeItem extends Model
{
    protected $table = 'type_barang';
    protected $fillable = ['name', 'description'];
    public $timestamps = false;

    // Relationship with items
    public function barang()
    {
        return $this->hasMany(BarangModel::class);
    }
}
