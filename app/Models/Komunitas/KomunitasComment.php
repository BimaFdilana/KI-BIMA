<?php

namespace App\Models\Komunitas;

use App\Models\Auth\UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KomunitasComment extends Model
{
    use SoftDeletes;

    protected $table = 'komunitas_comments';
    protected $fillable = ['user_id', 'post_id', 'parent_id', 'content', 'likes_count'];

    public function user()
    {
        return $this->belongsTo(UserModel::class);
    }

    public function post()
    {
        return $this->belongsTo(KomunitasPost::class, 'post_id');
    }

    public function parent()
    {
        return $this->belongsTo(KomunitasComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(KomunitasComment::class, 'parent_id');
    }

    public function likes()
    {
        return $this->hasMany(KomunitasCommentLike::class, 'comment_id');
    }
}
