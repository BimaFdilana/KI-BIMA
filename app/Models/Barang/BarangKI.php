<?php

namespace App\Models\Barang;

use App\Models\Toko\TokoKeranjang;
use App\Models\Toko\BarangToko;
use App\Models\Toko\TokoPesanan;
use App\Models\Toko\TokoSellingDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangKI extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'barang_ki';

    protected $fillable = [
        'barang_id',
        'id_barcode',
        'satuan_id',
        'quantity',
        'sold_quantity',
        'price_buy',
        'price_sell',
        'price_up',
        'discount_amount',
        'discount_percentage',
        'discount_start',
        'discount_end',
        'expired_time',
        'status'
    ];

    protected $casts = [
        'expired_time' => 'datetime',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function getBestDiscountedPrice()
    {
        // Margin dihitung sebagai persentase dari price_sell (konsisten dengan BarangKIService::calculateOriginalPrice)
        $priceSell = $this->price_sell + ($this->price_sell * ($this->barang->subcategory->margin / 100));
        $bestPrice = $priceSell;
        $discountType = null;
        $discountPercent = 0;

        // Diskon amount
        if ($this->discount_amount && $this->discount_amount < $priceSell) {
            $bestPrice = $this->discount_amount;
            $discountType = 'amount';
            $discountPercent = round(($priceSell - $bestPrice) / $priceSell * 100, 2);
        }

        // Diskon percentage
        if ($this->discount_percentage) {
            $priceByPercent = $priceSell - ($priceSell * ($this->discount_percentage / 100));
            $percentValue = round($this->discount_percentage, 2);

            // Pilih diskon yang menghasilkan harga lebih murah
            if ($priceByPercent < $bestPrice) {
                $bestPrice = $priceByPercent;
                $discountType = 'percentage';
                $discountPercent = $percentValue;
            }
        }

        return [
            'harga_diskon' => $bestPrice,
            'tipe_diskon' => $discountType,
            'persen_diskon' => $discountPercent
        ];
    }

    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id');
    }

    public function satuan()
    {
        return $this->belongsTo(SatuanItem::class, 'satuan_id');
    }

    public function setExpiredTimeAttribute($value)
    {
        $this->attributes['expired_time'] = Carbon::parse($value);
    }

    public function tokoKeranjang()
    {
        return $this->hasMany(TokoKeranjang::class, 'barangki_id');
    }

    // Fixed: This was incorrectly referencing TokoKeranjang instead of BarangToko
    public function barangToko()
    {
        return $this->hasMany(BarangToko::class, 'barangki_id');
    }

    public function barangIO()
    {
        return $this->hasMany(BarangIOModel::class, 'barangki_id');
    }

    // Add relationship to TokoSellingDetail for better navigation
    public function sellingDetails()
    {
        return $this->hasMany(TokoSellingDetail::class, 'barangki_id');
    }

    public function tokoPesanan()
    {
        return $this->hasMany(TokoPesanan::class, 'barangki_id');
    }
}
