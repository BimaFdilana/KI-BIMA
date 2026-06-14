<?php

namespace App\Models\Komunitas;

use App\Models\Auth\UserModel;
use Illuminate\Database\Eloquent\Model;

class KomunitasCommentLike extends Model
{
    protected $table = 'komunitas_comment_likes';
    protected $fillable = ['user_id', 'comment_id'];

    public function user()
    {
        return $this->belongsTo(UserModel::class);
    }

    public function comment()
    {
        return $this->belongsTo(KomunitasComment::class, 'comment_id');
    }
}
