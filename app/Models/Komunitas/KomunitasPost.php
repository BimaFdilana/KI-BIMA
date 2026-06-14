<?php

namespace App\Models\Komunitas;

use App\Models\Auth\UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KomunitasPost extends Model
{
    use SoftDeletes;

    protected $table = 'komunitas_post';
    protected $fillable = ['user_id', 'content', 'likes_count', 'comments_count'];

    public function user()
    {
        return $this->belongsTo(UserModel::class);
    }

    public function media()
    {
        return $this->hasMany(KomunitasPostMedia::class, 'post_id');
    }

    public function likes()
    {
        return $this->hasMany(KomunitasLike::class, 'post_id');
    }

    public function comments()
    {
        return $this->hasMany(KomunitasComment::class, 'post_id')->whereNull('parent_id');
    }

    public function allComments()
    {
        return $this->hasMany(KomunitasComment::class, 'post_id');
    }
}
