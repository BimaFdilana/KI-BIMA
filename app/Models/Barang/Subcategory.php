<?php

namespace App\Models\Barang;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $table = 'sub_categories';
    protected $fillable = ['category_id', 'name', 'photo', 'margin'];
    // Nonaktifkan timestamps
    public $timestamps = false;
    // Relationship with items
    public function barang()
    {
        return $this->hasMany(BarangModel::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
