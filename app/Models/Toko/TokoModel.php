<?php

namespace App\Models\Toko;

use App\Models\Auth\UserModel;
use App\Models\PakDul\PayLatterConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class TokoModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'toko';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'address',
        'latitude',
        'longitude',
        'owner_id',
        'edited_by',
        'ki_point',
        'token',
        'status',
        'type',
        'image',
        'edited_at',
        'rek_number',
        'rek_name',
        'rek_bank',
        'verified_at',
        'verified_by',
        'rejection_reason'
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'edited_at', 'verified_at'];

    protected static function booted()
    {
        static::updating(function ($toko) {
            if ($toko->isDirty('status')) {
                $toko->edited_by = Auth::id();
                $toko->edited_at = now();
            }
        });
    }

    // RELASI
    public function owner()
    {
        return $this->belongsTo(UserModel::class, 'owner_id');
    }

    public function editor()
    {
        return $this->belongsTo(UserModel::class, 'edited_by');
    }

    public function verifier()
    {
        return $this->belongsTo(UserModel::class, 'verified_by');
    }

    public function barangs()
    {
        return $this->hasMany(BarangToko::class, 'toko_id');
    }

    public function users()
    {
        return $this->belongsToMany(UserModel::class, 'toko_user', 'toko_id', 'user_id')
            ->withPivot('jabatan_id')
            ->using(TokoUserModel::class);
    }

    public function keranjang()
    {
        return $this->hasMany(TokoKeranjang::class, 'toko_id');
    }

    public function jabatan()
    {
        return $this->belongsToMany(JabatanModel::class, 'toko_user', 'toko_id', 'jabatan_id');
    }

    public function paylatter()
    {
        return $this->hasOne(PayLatterConfig::class, 'toko_id');
    }

    public function payments()
    {
        return $this->hasMany(TokoPayment::class, 'toko_id');
    }

    public function biayaOperasional()
    {
        return $this->hasMany(BiayaOperasional::class, 'toko_id');
    }

    public function paylatter_enabled()

    {
        return $this->paylatter()->where('is_active', 1)->exists();
    }
}
