<?php

namespace App\Models\Barang;

use Illuminate\Database\Eloquent\Model;
use App\Models\Barang\Subcategory;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = ['name', 'description', 'photo'];
    // Nonaktifkan timestamps
    public $timestamps = false;
    // Relationship with subcategories
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'category_id');
    }
}
