<?php

namespace App\Models\Barang;

use App\Models\Barang\Image;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangModel extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'barang';
    protected $fillable = [
        'sku',
        'subcategory_id',
        'brand_id',
        'type_id',
        'name',
        'description',
        'early_expiry_days',
        'mid_expiry_days',
        'late_expiry_days',
        'status'
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function generateSku($name)
    {
        $singkatan = substr($name, 0, 5);
        $baseSku = strtoupper(str_replace(' ', '', $singkatan));

        $sku = $baseSku;
        $counter = 1;

        // Cek jika SKU sudah ada di database
        while (BarangModel::where('sku', 'LIKE', $baseSku . '%')->exists()) {
            // Jika sudah ada, tambahkan angka di depan
            $sku = $baseSku . $counter;
            $counter++;
        }

        return $sku;
    }

    public function is_active(): bool
    {
        return $this->status === 'active';
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function type()
    {
        return $this->belongsTo(TypeItem::class, 'type_id');
    }

    public function barangki()
    {
        return $this->hasMany(BarangKI::class, 'barang_id');
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'barang_id');
    }

    public function fromConversions()
    {
        return $this->hasMany(SatuanConversion::class, 'barang_id');
    }

    public function toConversions()
    {
        return $this->hasMany(SatuanConversion::class, 'barang_id');
    }
    public function satuanConversions()
    {
        return $this->hasMany(SatuanConversion::class, 'barang_id', 'id');
    }
}
