<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatuanItemSeeder extends Seeder
{
    public function run()
    {
        DB::table('satuan_items')->insert([
            // ================== TIPE BERAT ==================
            [
                'level' => 4,
                'name' => 'Ton',
                'cut_name' => 'ton',
                'type' => 'berat',
                'description' => 'Satuan berat utama'
            ],
            [
                'level' => 3,
                'name' => 'Kilogram',
                'cut_name' => 'kg',
                'type' => 'berat',
                'description' => '1/1000 dari Ton'
            ],
            [
                'level' => 2,
                'name' => 'Gram',
                'cut_name' => 'g',
                'type' => 'berat',
                'description' => '1/1000 dari Kilogram'
            ],
            [
                'level' => 1,
                'name' => 'Miligram',
                'cut_name' => 'mg',
                'type' => 'berat',
                'description' => '1/1000 dari Gram'
            ],

            // ================== TIPE VOLUME ==================
            [
                'level' => 2,
                'name' => 'Liter',
                'cut_name' => 'L',
                'type' => 'volume',
                'description' => 'Satuan dasar untuk volume cairan'
            ],
            [
                'level' => 1,
                'name' => 'Mililiter',
                'cut_name' => 'mL',
                'type' => 'volume',
                'description' => '1/1000 dari Liter'
            ],
            [
                'level' => 1,
                'name' => 'Cubic Meter',
                'cut_name' => 'm³',
                'type' => 'volume',
                'description' => 'Satuan volume dalam kubik'
            ],
            [
                'level' => 1,
                'name' => 'Galon',
                'cut_name' => 'galon',
                'type' => 'volume',
                'description' => 'Satuan volume biasa untuk air minum'
            ],
            [
                'level' => 1,
                'name' => 'Cup',
                'cut_name' => 'cup',
                'type' => 'volume',
                'description' => 'Satuan kecil untuk bahan masakan atau minuman'
            ],

            // ================== TIPE PANJANG ==================
            [
                'level' => 3,
                'name' => 'Meter',
                'cut_name' => 'm',
                'type' => 'panjang',
                'description' => 'Satuan dasar untuk panjang'
            ],
            [
                'level' => 2,
                'name' => 'Centimeter',
                'cut_name' => 'cm',
                'type' => 'panjang',
                'description' => '1/100 dari Meter'
            ],
            [
                'level' => 1,
                'name' => 'Milimeter',
                'cut_name' => 'mm',
                'type' => 'panjang',
                'description' => '1/1000 dari Meter'
            ],
            [
                'level' => 1,
                'name' => 'Inci',
                'cut_name' => 'inci',
                'type' => 'panjang',
                'description' => 'Satuan panjang dalam sistem imperial'
            ],
            [
                'level' => 1,
                'name' => 'Kilometer',
                'cut_name' => 'km',
                'type' => 'panjang',
                'description' => '1000 meter'
            ],

            // ================== TIPE LUAS ==================
            [
                'level' => 2,
                'name' => 'Meter Persegi',
                'cut_name' => 'm²',
                'type' => 'luas',
                'description' => 'Satuan luas dasar'
            ],
            [
                'level' => 1,
                'name' => 'Hektar',
                'cut_name' => 'ha',
                'type' => 'luas',
                'description' => 'Satuan luas besar untuk lahan'
            ],
            [
                'level' => 1,
                'name' => 'Are',
                'cut_name' => 'are',
                'type' => 'luas',
                'description' => '100 meter persegi'
            ],
            [
                'level' => 1,
                'name' => 'Sentimeter Persegi',
                'cut_name' => 'cm²',
                'type' => 'luas',
                'description' => 'Luas kecil'
            ],

            // ================== TIPE WAKTU ==================
            [
                'level' => 4,
                'name' => 'Tahun',
                'cut_name' => 'tahun',
                'type' => 'waktu',
                'description' => 'Satuan terbesar dalam waktu'
            ],
            [
                'level' => 3,
                'name' => 'Bulan',
                'cut_name' => 'bulan',
                'type' => 'waktu',
                'description' => '1/12 dari Tahun'
            ],
            [
                'level' => 2,
                'name' => 'Minggu',
                'cut_name' => 'minggu',
                'type' => 'waktu',
                'description' => '7 hari'
            ],
            [
                'level' => 1,
                'name' => 'Hari',
                'cut_name' => 'hari',
                'type' => 'waktu',
                'description' => '24 jam'
            ],
            [
                'level' => 1,
                'name' => 'Jam',
                'cut_name' => 'jam',
                'type' => 'waktu',
                'description' => '60 menit'
            ],
            [
                'level' => 1,
                'name' => 'Menit',
                'cut_name' => 'menit',
                'type' => 'waktu',
                'description' => '60 detik'
            ],
            [
                'level' => 1,
                'name' => 'Detik',
                'cut_name' => 'detik',
                'type' => 'waktu',
                'description' => 'Satuan dasar waktu'
            ],

            // ================== TIPE SUHU ==================
            [
                'level' => 1,
                'name' => 'Derajat Celcius',
                'cut_name' => '°C',
                'type' => 'suhu',
                'description' => 'Satuan suhu umum di Indonesia'
            ],
            [
                'level' => 1,
                'name' => 'Derajat Fahrenheit',
                'cut_name' => '°F',
                'type' => 'suhu',
                'description' => 'Satuan suhu umum di Amerika Serikat'
            ],
            [
                'level' => 1,
                'name' => 'Kelvin',
                'cut_name' => 'K',
                'type' => 'suhu',
                'description' => 'Satuan suhu mutlak'
            ],

            // ================== TIPE UNIT / JUMLAH ==================
            [
                'level' => 1,
                'name' => 'Pcs',
                'cut_name' => 'pcs',
                'type' => 'unit',
                'description' => 'Satuan item individu'
            ],
            [
                'level' => 1,
                'name' => 'Lusin',
                'cut_name' => 'lusin',
                'type' => 'unit',
                'description' => '12 buah'
            ],
            [
                'level' => 1,
                'name' => 'Rim',
                'cut_name' => 'rim',
                'type' => 'unit',
                'description' => '500 lembar kertas'
            ],
            [
                'level' => 1,
                'name' => 'Kodi',
                'cut_name' => 'kodi',
                'type' => 'unit',
                'description' => '20 buah'
            ],
            [
                'level' => 1,
                'name' => 'Gross',
                'cut_name' => 'gross',
                'type' => 'unit',
                'description' => '144 buah (12 lusin)'
            ],
            [
                'level' => 1,
                'name' => 'Botol',
                'cut_name' => 'botol',
                'type' => 'unit',
                'description' => 'Satuan untuk barang dalam kemasan botol'
            ],
            [
                'level' => 1,
                'name' => 'Keping',
                'cut_name' => 'keping',
                'type' => 'unit',
                'description' => 'Satuan untuk barang padat seperti tablet'
            ],
            [
                'level' => 1,
                'name' => 'Batang',
                'cut_name' => 'batang',
                'type' => 'unit',
                'description' => 'Barang bentuk batangan seperti sabun, rokok'
            ],
            [
                'level' => 1,
                'name' => 'Sachet',
                'cut_name' => 'sachet',
                'type' => 'unit',
                'description' => 'Satuan kemasan kecil'
            ],
            [
                'level' => 1,
                'name' => 'Lembar',
                'cut_name' => 'lembar',
                'type' => 'unit',
                'description' => 'Untuk barang berbentuk lembaran'
            ],
            [
                'level' => 2,
                'name' => 'Pack',
                'cut_name' => 'pack',
                'type' => 'unit',
                'description' => 'Kemasan beberapa item'
            ],
            [
                'level' => 3,
                'name' => 'Karton',
                'cut_name' => 'karton',
                'type' => 'unit',
                'description' => 'Kemasan besar untuk banyak item'
            ],
        ]);
    }
}