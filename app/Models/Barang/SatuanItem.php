<?php

namespace App\Models\Barang;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatuanItem extends Model
{
    use HasFactory;

    protected $table = 'satuan_items';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'level',
        'name',
        'cut_name',
        'type',
        'selling',
        'description'
    ];

    public function barangki()
    {
        return $this->hasMany(BarangKI::class, 'satuan_id');
    }

    public function fromConversions()
    {
        return $this->hasMany(SatuanConversion::class, 'from_satuan_id');
    }

    public function conversionFrom()
    {
        return $this->hasMany(SatuanConversion::class, 'from_satuan_id');
    }

    public function conversionTo()
    {
        return $this->hasMany(SatuanConversion::class, 'to_satuan_id');
    }

}
