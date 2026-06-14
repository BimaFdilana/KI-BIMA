<?php

namespace App\Models\Toko;

use App\Models\Auth\UserModel;
use Illuminate\Database\Eloquent\Model;

class TokoInvitation extends Model
{
    protected $table = 'toko_invitations';

    protected $fillable = [
        'toko_id',
        'inviter_id',
        'invited_id',
        'jabatan_id',
        'message',
        'status',
    ];
    protected $casts = [
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function toko()
    {
        return $this->belongsTo(TokoModel::class);
    }
    public function inviter()
    {
        return $this->belongsTo(UserModel::class, 'inviter_id');
    }
    public function invited()
    {
        return $this->belongsTo(UserModel::class, 'invited_id');
    }
    public function jabatan()
    {
        return $this->belongsTo(JabatanModel::class);
    }
}
