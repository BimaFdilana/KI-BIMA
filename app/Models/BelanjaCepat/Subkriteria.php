<?php

namespace App\Models\BelanjaCepat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subkriteria extends Model
{
    use HasFactory;
    
    protected $table = 'subkriteria';
    
    protected $fillable = [
        'kriteria_id',
        'nama',
        'prioritas',
        'bobot',
        'deskripsi',
        'nilai_min',
        'nilai_max'
    ];
    
    /**
     * Get the kriteria that owns this subkriteria.
     */
    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id');
    }
    
    /**
     * Get the penilaian for this subkriteria.
     */
    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'subkriteria_id');
    }
    
    /**
     * Memastikan subkriteria default tersedia di database
     * Method ini dapat dipanggil oleh seeder atau pada awal aplikasi
     */
    public static function ensureDefaultSubkriteriaExists()
    {
        // Pastikan kriteria sudah ada
        Kriteria::ensureDefaultKriteriaExists();
        
        // Ambil semua kriteria
        $allKriteria = Kriteria::all()->keyBy('nama');
        
        $defaultSubkriteria = [
            // Subkriteria untuk Penjualan
            [
                'kriteria_id' => $allKriteria['Penjualan']->id,
                'nama' => 'Tinggi',
                'prioritas' => 1,
                'bobot' => 1.0,
                'deskripsi' => 'Penjualan > 10 unit per minggu',
                'nilai_min' => 10,
                'nilai_max' => 999999
            ],
            [
                'kriteria_id' => $allKriteria['Penjualan']->id,
                'nama' => 'Sedang',
                'prioritas' => 2,
                'bobot' => 0.7,
                'deskripsi' => 'Penjualan 5-10 unit per minggu',
                'nilai_min' => 5,
                'nilai_max' => 10
            ],
            [
                'kriteria_id' => $allKriteria['Penjualan']->id,
                'nama' => 'Rendah',
                'prioritas' => 3,
                'bobot' => 0.4,
                'deskripsi' => 'Penjualan < 5 unit per minggu',
                'nilai_min' => 0,
                'nilai_max' => 5
            ],
            
            // Subkriteria untuk Perputaran Stok
            [
                'kriteria_id' => $allKriteria['Perputaran Stok']->id,
                'nama' => 'Cepat',
                'prioritas' => 1,
                'bobot' => 1.0,
                'deskripsi' => 'Perputaran stok > 1.5 per minggu',
                'nilai_min' => 1.5,
                'nilai_max' => 999999
            ],
            [
                'kriteria_id' => $allKriteria['Perputaran Stok']->id,
                'nama' => 'Normal',
                'prioritas' => 2,
                'bobot' => 0.7,
                'deskripsi' => 'Perputaran stok 0.5-1.5 per minggu',
                'nilai_min' => 0.5,
                'nilai_max' => 1.5
            ],
            [
                'kriteria_id' => $allKriteria['Perputaran Stok']->id,
                'nama' => 'Lambat',
                'prioritas' => 3,
                'bobot' => 0.4,
                'deskripsi' => 'Perputaran stok < 0.5 per minggu',
                'nilai_min' => 0,
                'nilai_max' => 0.5
            ],
            
            // Subkriteria untuk Ketersediaan Stok
            [
                'kriteria_id' => $allKriteria['Ketersediaan Stok']->id,
                'nama' => 'Rendah',
                'prioritas' => 1,
                'bobot' => 1.0,
                'deskripsi' => 'Stok < 5 unit',
                'nilai_min' => 0,
                'nilai_max' => 5
            ],
            [
                'kriteria_id' => $allKriteria['Ketersediaan Stok']->id,
                'nama' => 'Sedang',
                'prioritas' => 2,
                'bobot' => 0.6,
                'deskripsi' => 'Stok 5-20 unit',
                'nilai_min' => 5,
                'nilai_max' => 20
            ],
            [
                'kriteria_id' => $allKriteria['Ketersediaan Stok']->id,
                'nama' => 'Tinggi',
                'prioritas' => 3,
                'bobot' => 0.3,
                'deskripsi' => 'Stok > 20 unit',
                'nilai_min' => 20,
                'nilai_max' => 999999
            ],
            
            // Subkriteria untuk Kategori Barang
            [
                'kriteria_id' => $allKriteria['Kategori Barang']->id,
                'nama' => 'Harian',
                'prioritas' => 1,
                'bobot' => 1.0,
                'deskripsi' => 'Barang kebutuhan harian',
            ],
            [
                'kriteria_id' => $allKriteria['Kategori Barang']->id,
                'nama' => 'Mingguan',
                'prioritas' => 2,
                'bobot' => 0.7,
                'deskripsi' => 'Barang kebutuhan mingguan',
            ],
            [
                'kriteria_id' => $allKriteria['Kategori Barang']->id,
                'nama' => 'Bulanan',
                'prioritas' => 3,
                'bobot' => 0.4,
                'deskripsi' => 'Barang kebutuhan bulanan',
            ],
            
            // Subkriteria untuk Kadaluwarsa
            [
                'kriteria_id' => $allKriteria['Kadaluwarsa']->id,
                'nama' => 'Segera',
                'prioritas' => 1,
                'bobot' => 1.0,
                'deskripsi' => 'Kadaluwarsa dalam 7 hari',
                'nilai_min' => 0,
                'nilai_max' => 7
            ],
            [
                'kriteria_id' => $allKriteria['Kadaluwarsa']->id,
                'nama' => 'Akan Datang',
                'prioritas' => 2,
                'bobot' => 0.7,
                'deskripsi' => 'Kadaluwarsa dalam 8-15 hari',
                'nilai_min' => 8,
                'nilai_max' => 15
            ],
            [
                'kriteria_id' => $allKriteria['Kadaluwarsa']->id,
                'nama' => 'Masih Lama',
                'prioritas' => 3,
                'bobot' => 0.3,
                'deskripsi' => 'Kadaluwarsa > 15 hari',
                'nilai_min' => 15,
                'nilai_max' => 999999
            ],
        ];
        
        foreach ($defaultSubkriteria as $subkriteria) {
            if (isset($subkriteria['kriteria_id'], $subkriteria['nama'])) {
                self::firstOrCreate(
                    [
                        'kriteria_id' => $subkriteria['kriteria_id'],
                        'nama' => $subkriteria['nama']
                    ],
                    $subkriteria
                );
            }
        }
    }
}