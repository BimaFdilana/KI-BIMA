<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlugIn extends Model
{
    protected $fillable = [
    'nama',
    'subtitle',
    'deskripsi',
    'fitur',
    'gambar'
];

protected $casts = [
    'fitur' => 'array'
];
}
