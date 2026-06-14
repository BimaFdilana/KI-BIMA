<?php
namespace App\Models\Toko;

use App\Models\Barang\BarangKI;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokoSellingDetail extends Model
{
    use HasFactory;
    
    protected $table = 'toko_selling_detail';
    
    protected $fillable = [
        'transaction_id',
        'barangki_id',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];
    
    // Fixed relationship with TokoSelling using the correct foreign key
    public function selling()
    {
        return $this->belongsTo(TokoSelling::class, 'transaction_id', 'increment_id');
    }
    
    public function toko_selling()
    {
        return $this->belongsTo(TokoSelling::class, 'transaction_id', 'increment_id');
    }
    
    // Relationship with BarangKI
    public function barangki()
    {
        return $this->belongsTo(BarangKI::class, 'barangki_id', 'id');
    }
}