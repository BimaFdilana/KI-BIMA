<?php

namespace App\Models\Komunitas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KomunitasPostMedia extends Model
{
    use SoftDeletes;

    protected $table = 'komunitas_post_media';
    protected $fillable = ['post_id', 'file_path', 'type', 'order'];

    public function post()
    {
        return $this->belongsTo(KomunitasPost::class, 'post_id');
    }
}
