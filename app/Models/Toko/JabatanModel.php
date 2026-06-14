<?php

namespace App\Models\Toko;

use App\Models\Auth\UserModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JabatanModel extends Model
{
    use HasFactory;

    protected $table = 'jabatan';

    protected $fillable = [
        'name',
        'slug',
        'level',
        'description',
        'can_invite_users',
        'can_manage_inventory',
        'can_view_reports',
        'can_manage_orders',
    ];

    protected $casts = [
        'can_invite_users' => 'boolean',
        'can_manage_inventory' => 'boolean',
        'can_view_reports' => 'boolean',
        'can_manage_orders' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(UserModel::class, 'toko_user', 'jabatan_id', 'user_id');
    }

    public function tokos()
    {
        return $this->belongsToMany(TokoModel::class, 'toko_user', 'jabatan_id', 'toko_id');
    }
}
