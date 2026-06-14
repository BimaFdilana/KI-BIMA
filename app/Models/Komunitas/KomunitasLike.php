<?php

namespace App\Models\Komunitas;

use App\Models\Auth\UserModel;
use Illuminate\Database\Eloquent\Model;

class KomunitasLike extends Model
{
    protected $table = 'komunitas_likes';
    protected $fillable = ['user_id', 'post_id'];

    public function user()
    {
        return $this->belongsTo(UserModel::class);
    }

    public function post()
    {
        return $this->belongsTo(KomunitasPost::class, 'post_id');
    }
}
