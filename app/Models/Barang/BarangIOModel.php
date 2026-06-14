<?php

namespace App\Models\Barang;

use App\Models\Auth\UserModel;
use App\Models\Barang\Image;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangIOModel extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'barang_io';
    protected $fillable = [
        'user_id',
        'barangki_id',
        'quantity',
        'price',
        'type',
        'status',
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function barangKI()
    {
        return $this->belongsTo(BarangKI::class, 'barangki_id', 'id');
    }


    public function user()
    {
        return $this->belongsTo(UserModel::class);
    }
}
