<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artikel extends Model
{
    protected $fillable = [
    'judul',
    'slug',
    'deskripsi_singkat',
    'isi',
    'gambar',
    'published_at',
    'views',
];
}