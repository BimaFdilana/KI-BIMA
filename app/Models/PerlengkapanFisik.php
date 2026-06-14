<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerlengkapanFisik extends Model
{
    protected $fillable = [
    'nama',
    'deskripsi',
    'badge',
    'fitur',
    'gambar'
];

protected $casts = [
    'fitur' => 'array'
];
}
