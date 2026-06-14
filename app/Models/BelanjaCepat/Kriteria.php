<?php

namespace App\Models\BelanjaCepat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kriteria extends Model
{
    use HasFactory;
    
    protected $table = 'kriteria';
    
    protected $fillable = [
        'nama',
        'prioritas',
        'bobot',
        'deskripsi'
    ];
    
    /**
     * Get the subkriteria that belong to this kriteria.
     */
    public function subkriteria()
    {
        return $this->hasMany(Subkriteria::class, 'kriteria_id');
    }
    
    /**
     * Mendapatkan nama kriteria yang terkait dengan barang toko
     * 
     * @return array
     */
    public static function getKriteriaToko()
    {
        return [
            'Penjualan',
            'Perputaran Stok',
            'Ketersediaan Stok',
            'Kategori Barang',
            'Kadaluwarsa'
        ];
    }
    
    /**
     * Memastikan kriteria default tersedia di database
     * Method ini dapat dipanggil oleh seeder atau pada awal aplikasi
     */
    public static function ensureDefaultKriteriaExists()
    {
        $defaultKriteria = [
            [
                'nama' => 'Penjualan',
                'prioritas' => 1,
                'bobot' => 0.30,
                'deskripsi' => 'Tingkat penjualan barang dalam periode tertentu'
            ],
            [
                'nama' => 'Perputaran Stok',
                'prioritas' => 2,
                'bobot' => 0.25,
                'deskripsi' => 'Kecepatan perputaran stok barang'
            ],
            [
                'nama' => 'Ketersediaan Stok',
                'prioritas' => 3,
                'bobot' => 0.20,
                'deskripsi' => 'Jumlah stok barang yang tersedia saat ini'
            ],
            [
                'nama' => 'Kategori Barang',
                'prioritas' => 4,
                'bobot' => 0.15,
                'deskripsi' => 'Jenis/kategori barang (Harian, Mingguan, Bulanan)'
            ],
            [
                'nama' => 'Kadaluwarsa',
                'prioritas' => 5,
                'bobot' => 0.10,
                'deskripsi' => 'Status kadaluwarsa barang'
            ],
        ];
        
        foreach ($defaultKriteria as $kriteria) {
            self::firstOrCreate(['nama' => $kriteria['nama']], $kriteria);
        }
    }
}