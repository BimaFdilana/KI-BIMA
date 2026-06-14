<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('brands')->insert([
            // Brand Makanan & Minuman untuk Warung
            ['name' => 'Indofood', 'description' => 'Perusahaan makanan terbesar di Indonesia', 'photo' => null],
            ['name' => 'Wings Food', 'description' => 'Perusahaan makanan dan minuman Indonesia', 'photo' => null],
            ['name' => 'Mayora', 'description' => 'Perusahaan makanan dan minuman terkemuka', 'photo' => null],
            ['name' => 'Nippon Indosari Corpindo', 'description' => 'Perusahaan roti dan bakery', 'photo' => null],
            ['name' => 'Garuda Food', 'description' => 'Perusahaan makanan ringan Indonesia', 'photo' => null],
            ['name' => 'Orang Tua Group', 'description' => 'Perusahaan minuman dan makanan tradisional', 'photo' => null],
            ['name' => 'Kapal Api Group', 'description' => 'Perusahaan kopi terbesar Indonesia', 'photo' => null],
            ['name' => 'Coca-Cola Indonesia', 'description' => 'Perusahaan minuman ringan', 'photo' => null],
            ['name' => 'Frisian Flag Indonesia', 'description' => 'Perusahaan susu dan produk dairy', 'photo' => null],
            ['name' => 'Nestle Indonesia', 'description' => 'Perusahaan makanan dan minuman', 'photo' => null],
            ['name' => 'Mondelez Indonesia', 'description' => 'Perusahaan biskuit dan coklat', 'photo' => null],
            ['name' => 'Kalbe Nutritionals', 'description' => 'Perusahaan nutrisi dan kesehatan', 'photo' => null],
            ['name' => 'Ajinomoto Indonesia', 'description' => 'Perusahaan bumbu dan penyedap rasa', 'photo' => null],
            ['name' => 'Tiga Pilar Sejahtera', 'description' => 'Perusahaan makanan dan beras', 'photo' => null],
            ['name' => 'Danone Indonesia', 'description' => 'Perusahaan air mineral dan produk susu', 'photo' => null],

            // Brand Kebutuhan Rumah Tangga untuk Warung
            ['name' => 'Unilever Indonesia', 'description' => 'Perusahaan produk konsumen dan kebersihan', 'photo' => null],
            ['name' => 'Procter & Gamble Indonesia', 'description' => 'Perusahaan produk perawatan rumah', 'photo' => null],
            ['name' => 'Wings Group', 'description' => 'Perusahaan produk konsumen Indonesia', 'photo' => null],
            ['name' => 'Kino Indonesia', 'description' => 'Perusahaan produk perawatan dan kebersihan', 'photo' => null],
            ['name' => 'Lion Wings', 'description' => 'Perusahaan produk kebersihan rumah tangga', 'photo' => null],
            ['name' => 'Wilmar International', 'description' => 'Perusahaan minyak goreng dan produk kelapa sawit', 'photo' => null],
            ['name' => 'Musim Mas', 'description' => 'Perusahaan minyak goreng Indonesia', 'photo' => null],
            ['name' => 'Smart', 'description' => 'Perusahaan minyak goreng dan margarin', 'photo' => null],
            ['name' => 'Sinar Mas Agro', 'description' => 'Perusahaan minyak goreng dan tepung', 'photo' => null],
            ['name' => 'Bogasari', 'description' => 'Perusahaan tepung terigu terbesar Indonesia', 'photo' => null],
            ['name' => 'Japfa', 'description' => 'Perusahaan telur dan produk unggas', 'photo' => null],
            ['name' => 'Charoen Pokphand Indonesia', 'description' => 'Perusahaan pakan ternak dan produk unggas', 'photo' => null],
            ['name' => 'Budi Starch & Sweetener', 'description' => 'Perusahaan pemanis dan tepung', 'photo' => null],
            ['name' => 'Sido Muncul', 'description' => 'Perusahaan jamu dan obat tradisional', 'photo' => null],
            ['name' => 'Nyonya Meneer', 'description' => 'Perusahaan jamu tradisional Indonesia', 'photo' => null],
        ]);
    }
}
