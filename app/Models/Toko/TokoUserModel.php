<?php

namespace App\Models\Toko;

use App\Models\Auth\UserModel;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TokoUserModel extends Pivot
{
    protected $table = 'toko_user';

    protected $fillable = [
        'user_id',
        'toko_id',
        'jabatan_id',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function toko()
    {
        return $this->belongsTo(TokoModel::class, 'toko_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(JabatanModel::class, 'jabatan_id');
    }
}
