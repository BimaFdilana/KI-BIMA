<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang\Category;
use App\Models\Barang\Subcategory;
use App\Models\Barang\Brand;
use App\Models\Barang\TypeItem;
use App\Models\Barang\SatuanItem;

class AdditionalMasterDataSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'UMKM', 'Cosmetic', 'Minuman', 'Produk Segar', 'Ibu dan Anak',
            'Perlengkapan Kantor', 'Kesehatan', 'Pet Food', 'Snack & Biscuit',
            'Makanan', 'Sarapan', 'Kebutuhan Rumah Tangga', 'Automotive',
            'Hotel & Restauran', 'Produk Tembakau', 'Umum'
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat], ['description' => $cat, 'photo' => null]);
        }

        $defaultCat = Category::where('name', 'Umum')->first();

        $subcategories = [
            'Bumbu makanan', 'Produk Luar kulit', 'Jus/Sari Buah', 'Minuman ringan',
            'Sirup, Madu & SKM', 'Air Mineral', 'Buah', 'Sayuran', 'Perlengkapan Bayi',
            'Makanan Bayi dan Anak', 'Susu', 'Mainan dan Hobi', 'Alat Tulis dan Perlengkapan Kantor',
            'Obat-obatan dan suplemen', 'Makanan Hewan Peliharaan', 'Snack', 'Biskuit dan Wafer',
            'Makanan Kaleng', 'Makanan Instan', 'Teh', 'Kopi', 'Susu dan Yoghurt', 'Minuman Instan',
            'Roti, Pastry dan Cereal', 'Pembasmi/Pengharum', 'Pembalut/popok dewasa',
            'Deterjen dan pembersih', 'Peralatan Rumah Tangga', 'Elektronik', 'Tisu', 'Peralatan',
            'Peralatan Mobil', 'Zat Adiktif', 'Makanan ringan', 'Cokelat', 'Kapas'
        ];

        if ($defaultCat) {
            foreach ($subcategories as $sub) {
                // Untuk amannya, kita buat subkategori di kategori "Umum" jika belum ada
                // Sebaiknya hindari duplikasi nama
                $exists = Subcategory::where('name', $sub)->first();
                if (!$exists) {
                    Subcategory::create([
                        'category_id' => $defaultCat->id,
                        'name' => $sub,
                        'margin' => 0,
                    ]);
                }
            }
        }

        $brands = [
            'Indofood', 'Wings Food', 'Mayora', 'Nippon Indosari Corpindo', 'Garuda Food',
            'Orang Tua Group', 'Kapal Api Group', 'Coca-Cola Indonesia', 'Frisian Flag Indonesia',
            'Nestle Indonesia', 'Mondelez Indonesia', 'Kalbe Nutritionals', 'Ajinomoto Indonesia',
            'Tiga Pilar Sejahtera', 'Danone Indonesia', 'Unilever Indonesia', 'Procter & Gamble Indonesia',
            'Wings Group', 'Kino Indonesia', 'Lion Wings', 'Wilmar International', 'Musim Mas',
            'Smart', 'Sinar Mas Agro', 'Bogasari', 'Japfa', 'Charoen Pokphand Indonesia',
            'Budi Starch & Sweetener', 'Sido Muncul', 'Nyonya Meneer', 'Dji Sam Soe', 'GAGA',
            'Fumakilla', '2Tang', '7 Up', '888 CALIFORNIA NEW ORANGE', 'Goodlook', 'Alicafe',
            'A&W', 'Kimono', 'Purina', 'AAA', 'Abakus', 'Alfredo', 'ABC', 'Zwitsal', 'Nise',
            'Glite', 'Green Leaf', 'Scarlett', 'Gatsby', 'Gabicci', 'Enchanteur', 'Formula',
            'Softness', 'Sariayu Martha Tilaar', 'Nikita', 'Mili', 'Tempo Scan', 'Ninja Umbrella',
            'Shoon Fatt', 'Wayang', 'Absolute', 'Access Mild', 'Nourish', 'Actifed', 'Enesis Group',
            'Want Want', 'Abbott', 'Adidas', 'Afitson', 'Aganol', 'Mama Suka', 'Nicko Fazi',
            'Double Swallow Sun', 'Gandour', 'Swallow Globe Brand', 'Satelit', 'Nutrijell', 'Nips',
            'Agnesia', 'Aguaria', 'Etienne Agner', 'AIM', 'Nissin', 'KH Lovely', 'Lucky Star',
            'Mulia Plast', 'Jhedy', 'MGT', 'SIP (Shinpo)', 'SOLAR', 'Lion Star', 'Nita', 'ATARI',
            'BSR', 'Calista', 'Nitchi', 'Jian Di', 'Nivea', 'Nitto', 'KH RIHIN', 'MEI LY', 'Ainie',
            'Doremi (SA)', 'Ades', 'Ox', 'Air Wick', 'DAISO', 'Sarang Tawon', 'CENDANA', 'Ajaib',
            'HAWAII', 'Onemed', 'Ika', 'GAP', 'Ikan Layang', 'PT. Pangan Lestari', 'Cap Kapal Layar',
            'Garam Meja 77', 'Cap 2 Anak Pintar', 'Cap Anak Terbang', 'Cap Kapal', 'Cap Kapal Dewa Ruci',
            'Cap Pagoda Emas', 'Putri Duyung', 'Prima', 'Moon Star', 'Supra Salt', 'Teri Besar',
            'Yolech Putih', 'Gardoe', 'Garglin', 'Garibori', 'Gario', 'Kenko', 'Garnier', 'NIXXA',
            'Triangle', 'NoMos', 'NONA', 'Garuda'
        ];

        foreach ($brands as $b) {
            Brand::firstOrCreate(['name' => $b], ['description' => '-']);
        }

        $tipeBarang = [
            'Harian', 'Mingguan', 'Bulanan', 'Tahunan'
        ];

        foreach ($tipeBarang as $tb) {
            TypeItem::firstOrCreate(['name' => $tb], ['description' => '-']);
        }

        $satuans = [
            'Ton' => ['cut_name' => 'ton', 'type' => 'berat'],
            'Kilogram' => ['cut_name' => 'kg', 'type' => 'berat'],
            'Gram' => ['cut_name' => 'g', 'type' => 'berat'],
            'Miligram' => ['cut_name' => 'mg', 'type' => 'berat'],
            'Liter' => ['cut_name' => 'l', 'type' => 'volume'],
            'Mililiter' => ['cut_name' => 'ml', 'type' => 'volume'],
            'Cubic Meter' => ['cut_name' => 'm3', 'type' => 'volume'],
            'Galon' => ['cut_name' => 'galon', 'type' => 'volume'],
            'Cup' => ['cut_name' => 'cup', 'type' => 'unit'],
            'Meter' => ['cut_name' => 'm', 'type' => 'panjang'],
            'Centimeter' => ['cut_name' => 'cm', 'type' => 'panjang'],
            'Milimeter' => ['cut_name' => 'mm', 'type' => 'panjang'],
            'Inci' => ['cut_name' => 'inch', 'type' => 'panjang'],
            'Kilometer' => ['cut_name' => 'km', 'type' => 'panjang'],
            'Meter Persegi' => ['cut_name' => 'm2', 'type' => 'area'],
            'Hektar' => ['cut_name' => 'ha', 'type' => 'area'],
            'Are' => ['cut_name' => 'are', 'type' => 'area'],
            'Sentimeter Persegi' => ['cut_name' => 'cm2', 'type' => 'area'],
            'Tahun' => ['cut_name' => 'thn', 'type' => 'waktu'],
            'Bulan' => ['cut_name' => 'bln', 'type' => 'waktu'],
            'Minggu' => ['cut_name' => 'mgg', 'type' => 'waktu'],
            'Hari' => ['cut_name' => 'hr', 'type' => 'waktu'],
            'Jam' => ['cut_name' => 'jam', 'type' => 'waktu'],
            'Menit' => ['cut_name' => 'mnt', 'type' => 'waktu'],
            'Detik' => ['cut_name' => 'dtk', 'type' => 'waktu'],
            'Derajat Celcius' => ['cut_name' => 'C', 'type' => 'suhu'],
            'Derajat Fahrenheit' => ['cut_name' => 'F', 'type' => 'suhu'],
            'Kelvin' => ['cut_name' => 'K', 'type' => 'suhu'],
            'Pcs' => ['cut_name' => 'pcs', 'type' => 'unit'],
            'Lusin' => ['cut_name' => 'lsn', 'type' => 'unit'],
            'Rim' => ['cut_name' => 'rim', 'type' => 'unit'],
            'Kodi' => ['cut_name' => 'kodi', 'type' => 'unit'],
            'Gross' => ['cut_name' => 'gross', 'type' => 'unit'],
            'Botol' => ['cut_name' => 'btl', 'type' => 'unit'],
            'Keping' => ['cut_name' => 'kpg', 'type' => 'unit'],
            'Batang' => ['cut_name' => 'btg', 'type' => 'unit'],
            'Sachet' => ['cut_name' => 'sct', 'type' => 'unit'],
            'Lembar' => ['cut_name' => 'lbr', 'type' => 'unit'],
            'Pack' => ['cut_name' => 'pack', 'type' => 'unit'],
            'Karton' => ['cut_name' => 'ktn', 'type' => 'unit'],
            'Kaleng' => ['cut_name' => 'klg', 'type' => 'unit'],
            'Kotak' => ['cut_name' => 'ktk', 'type' => 'unit'],
            'Roll' => ['cut_name' => 'roll', 'type' => 'unit'],
            'Kg' => ['cut_name' => 'kg', 'type' => 'berat'],
            'Slop' => ['cut_name' => 'slop', 'type' => 'unit'],
            'Karung' => ['cut_name' => 'krg', 'type' => 'unit'],
            'Pouch' => ['cut_name' => 'pch', 'type' => 'unit'],
            'Renteng' => ['cut_name' => 'rtg', 'type' => 'unit'],
            'Pot' => ['cut_name' => 'pot', 'type' => 'unit'],
            'Bungkus' => ['cut_name' => 'bks', 'type' => 'unit'],
            'Tube' => ['cut_name' => 'tb', 'type' => 'unit'],
            'Jerigen' => ['cut_name' => 'jrg', 'type' => 'unit'],
            'Toples' => ['cut_name' => 'tpls', 'type' => 'unit'],
        ];

        foreach ($satuans as $name => $data) {
            $existing = SatuanItem::where('name', $name)->orWhere('cut_name', $data['cut_name'])->first();
            if (!$existing) {
                SatuanItem::create([
                    'name' => $name,
                    'cut_name' => $data['cut_name'],
                    'type' => $data['type'],
                    'selling' => 'true',
                    'description' => '-'
                ]);
            }
        }
        
        // Pembeli
        // Pembeli di Excel tidak ada model khusus, jadi tidak diseeder ke database.
    }
}
