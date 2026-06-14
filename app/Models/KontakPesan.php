<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontakPesan extends Model
{
    protected $fillable = [
        'nama',
        'email',
        'pesan',
    ];
}