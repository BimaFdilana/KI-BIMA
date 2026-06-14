<?php

namespace App\Models\Toko;

use App\Models\Auth\UserModel;
use Illuminate\Database\Eloquent\Model;

class KasirProfile extends Model
{
    protected $table = 'kasir_profiles';

    protected $fillable = [
        'user_id',
        'toko_id',
        'pin',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function toko()
    {
        return $this->belongsTo(TokoModel::class, 'toko_id');
    }
}
