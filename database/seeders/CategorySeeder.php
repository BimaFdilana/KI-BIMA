<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'UMKM', 'description' => 'Produk dari Usaha Mikro, Kecil, dan Menengah.', 'photo' => null],
            ['name' => 'Cosmetic', 'description' => 'Produk kecantikan dan perawatan kulit.', 'photo' => null],
            ['name' => 'Minuman', 'description' => 'Berbagai jenis minuman segar dan kemasan.', 'photo' => null],
            ['name' => 'Produk Segar', 'description' => 'Buah, sayuran, dan bahan makanan segar.', 'photo' => null],
            ['name' => 'Ibu dan Anak', 'description' => 'Produk kebutuhan ibu dan anak.', 'photo' => null],
            ['name' => 'Perlengkapan Kantor', 'description' => 'Alat tulis dan perlengkapan kantor.', 'photo' => null],
            ['name' => 'Kesehatan', 'description' => 'Obat-obatan dan produk kesehatan.', 'photo' => null],
            ['name' => 'Pet Food', 'description' => 'Makanan dan kebutuhan hewan peliharaan.', 'photo' => null],
            ['name' => 'Snack & Biscuit', 'description' => 'Berbagai macam camilan dan biskuit.', 'photo' => null],
            ['name' => 'Makanan', 'description' => 'Berbagai jenis makanan siap saji.', 'photo' => null],
            ['name' => 'Sarapan', 'description' => 'Makanan dan minuman untuk sarapan.', 'photo' => null],
            ['name' => 'Kebutuhan Rumah Tangga', 'description' => 'Produk kebersihan dan peralatan rumah tangga.', 'photo' => null],
            ['name' => 'Automotive', 'description' => 'Suku cadang dan aksesori kendaraan.', 'photo' => null],
            ['name' => 'Hotel & Restauran', 'description' => 'Produk dan perlengkapan untuk hotel dan restoran.', 'photo' => null],
        ]);

        DB::table('sub_categories')->insert([
            ['name' => 'Bumbu makanan', 'category_id' => 1, 'photo' => null, 'margin' => 25],
            ['name' => 'Produk Luar kulit ', 'category_id' => 2, 'photo' => null, 'margin' => 10],
            ['name' => 'Jus/Sari Buah', 'category_id' => 3, 'photo' => null, 'margin' => 10],
            ['name' => 'Minuman ringan', 'category_id' => 3, 'photo' => null, 'margin' => 30],
            ['name' => 'Sirup, Madu & SKM', 'category_id' => 3, 'photo' => null, 'margin' => 25],
            ['name' => 'Air Mineral', 'category_id' => 3, 'photo' => null, 'margin' => 80],
            ['name' => 'Buah', 'category_id' => 4, 'photo' => null, 'margin' => 25],
            ['name' => 'Sayuran', 'category_id' => 4, 'photo' => null, 'margin' => 25],
            ['name' => 'Perlengkapan Bayi', 'category_id' => 5, 'photo' => null, 'margin' => 12],
            ['name' => 'Makanan Bayi dan Anak', 'category_id' => 5, 'photo' => null, 'margin' => 7],
            ['name' => 'Susu', 'category_id' => 5, 'photo' => null, 'margin' => 9],
            ['name' => 'Mainan dan Hobi', 'category_id' => 5, 'photo' => null, 'margin' => 30],
            ['name' => 'Alat Tulis dan Perlengkapan Kantor', 'category_id' => 6, 'photo' => null, 'margin' => 30],
            ['name' => 'Obat-obatan dan suplemen', 'category_id' => 7, 'photo' => null, 'margin' => 20],
            ['name' => 'Makanan Hewan Peliharaan', 'category_id' => 8, 'photo' => null, 'margin' => 15],
            ['name' => 'Snack', 'category_id' => 9, 'photo' => null, 'margin' => 15],
            ['name' => 'Biskuit dan Wafer', 'category_id' => 9, 'photo' => null, 'margin' => 15],
            ['name' => 'Makanan Kaleng', 'category_id' => 10, 'photo' => null, 'margin' => 10],
            ['name' => 'Makanan Instan', 'category_id' => 10, 'photo' => null, 'margin' => 7],
            ['name' => 'Teh', 'category_id' => 11, 'photo' => null, 'margin' => 12],
            ['name' => 'Kopi', 'category_id' => 11, 'photo' => null, 'margin' => 12],
            ['name' => 'Susu dan Yoghurt', 'category_id' => 11, 'photo' => null, 'margin' => 20],
            ['name' => 'Minuman Instan', 'category_id' => 11, 'photo' => null, 'margin' => 20],
            ['name' => 'Roti, Pastry dan Cereal', 'category_id' => 11, 'photo' => null, 'margin' => 20],
            ['name' => 'Pembasmi/Pengharum', 'category_id' => 12, 'photo' => null, 'margin' => 14],
            ['name' => 'Pembalut/popok dewasa', 'category_id' => 12, 'photo' => null, 'margin' => 14],
            ['name' => 'Deterjen dan pembersih', 'category_id' => 12, 'photo' => null, 'margin' => 25],
            ['name' => 'Peralatan Rumah Tangga', 'category_id' => 12, 'photo' => null, 'margin' => 25],
            ['name' => 'Elektronik', 'category_id' => 12, 'photo' => null, 'margin' => 25],
            ['name' => 'Tisu', 'category_id' => 12, 'photo' => null, 'margin' => 25],
            ['name' => 'Peralatan', 'category_id' => 13, 'photo' => null, 'margin' => 25],
            ['name' => 'Peralatan Mobil', 'category_id' => 13, 'photo' => null, 'margin' => 25],
        ]);
    }
}
