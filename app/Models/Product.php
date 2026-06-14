<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'nama',
        'slug',
        'kategori',
        'subtitle',
        'deskripsi',
        'badge',
        'gambar',
        'fitur',
    ];

    protected $casts = [
        'fitur' => 'array'
    ];
}