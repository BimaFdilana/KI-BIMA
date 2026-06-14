<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedTipeBarang();
        $this->seedSatuanItems();

        // Data barang dengan brand dan kategori yang sesuai
        $data = [
            // MAKANAN & MINUMAN - HARIAN
            [
                'base_sku' => 'INDMI001',
                'name' => 'Indomie Goreng',
                'brand_name' => 'Indofood',
                'sub_category_id' => 19, // Makanan Instan
                'type_name' => 'Harian',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Mie instan goreng Indomie',
                'early_expiry_days' => 365,
                'mid_expiry_days' => 60,
                'late_expiry_days' => 7,
                'conversion' => [
                    'Karton-Pack' => 40,
                    'Pack-Pcs' => 5,
                ]
            ],
            [
                'base_sku' => 'INDMI002',
                'name' => 'Indomie Kuah Ayam',
                'brand_name' => 'Indofood',
                'sub_category_id' => 19, // Makanan Instan
                'type_name' => 'Harian',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Mie instan kuah rasa ayam',
                'early_expiry_days' => 365,
                'mid_expiry_days' => 60,
                'late_expiry_days' => 7,
                'conversion' => [
                    'Karton-Pack' => 40,
                    'Pack-Pcs' => 5,
                ]
            ],
            [
                'base_sku' => 'COCA001',
                'name' => 'Coca Cola 330ml',
                'brand_name' => 'Coca-Cola Indonesia',
                'sub_category_id' => 4, // Minuman ringan
                'type_name' => 'Harian',
                'satuan' => ['Karton', 'Pack', 'Kotak'],
                'description' => 'Minuman bersoda Coca Cola',
                'early_expiry_days' => 365,
                'mid_expiry_days' => 90,
                'late_expiry_days' => 14,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Kotak' => 12,
                ]
            ],
            [
                'base_sku' => 'AQUA001',
                'name' => 'Air Mineral Aqua 600ml',
                'brand_name' => 'Danone Indonesia',
                'sub_category_id' => 6, // Air Mineral
                'type_name' => 'Harian',
                'satuan' => ['Karton', 'Pack', 'Kotak'],
                'description' => 'Air mineral dalam kemasan',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Kotak' => 12,
                ]
            ],
            [
                'base_sku' => 'KOPI001',
                'name' => 'Kapal Api Kopi Bubuk',
                'brand_name' => 'Kapal Api Group',
                'sub_category_id' => 21, // Kopi
                'type_name' => 'Harian',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Kopi bubuk premium',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'TISU001',
                'name' => 'Tisu Paseo',
                'brand_name' => 'Wings Group',
                'sub_category_id' => 30, // Tisu
                'type_name' => 'Harian',
                'satuan' => ['Karton', 'Pack', 'Roll'],
                'description' => 'Tisu tissue berkualitas',
                'early_expiry_days' => 3 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Roll' => 10,
                ]
            ],

            // KEBUTUHAN MINGGUAN
            [
                'base_sku' => 'SUSU001',
                'name' => 'Susu Frisian Flag',
                'brand_name' => 'Frisian Flag Indonesia',
                'sub_category_id' => 11, // Susu
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Kotak'],
                'description' => 'Susu cair siap minum',
                'early_expiry_days' => 180,
                'mid_expiry_days' => 30,
                'late_expiry_days' => 7,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Kotak' => 12,
                ]
            ],
            [
                'base_sku' => 'DETER001',
                'name' => 'Deterjen Rinso',
                'brand_name' => 'Unilever Indonesia',
                'sub_category_id' => 27, // Deterjen dan pembersih
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Deterjen bubuk untuk mencuci',
                'early_expiry_days' => 3 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'SHAM001',
                'name' => 'Shampo Sunsilk',
                'brand_name' => 'Unilever Indonesia',
                'sub_category_id' => 2, // Produk Luar kulit
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Shampo perawatan rambut',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'MYGR001',
                'name' => 'Minyak Goreng Bimoli',
                'brand_name' => 'Wilmar International',
                'sub_category_id' => 1, // Bumbu makanan
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Minyak goreng kelapa sawit',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],

            // KEBUTUHAN BULANAN
            [
                'base_sku' => 'GULA001',
                'name' => 'Gula Pasir Gulaku',
                'brand_name' => 'Tiga Pilar Sejahtera',
                'sub_category_id' => 1, // Bumbu makanan
                'type_name' => 'Bulanan',
                'satuan' => ['Karung', 'Kg', 'Gram'],
                'description' => 'Gula pasir premium',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karung-Kg' => 50,
                    'Kg-Gram' => 1000,
                ]
            ],
            [
                'base_sku' => 'BERAS001',
                'name' => 'Beras Rojo Lele',
                'brand_name' => 'Tiga Pilar Sejahtera',
                'sub_category_id' => 1, // Bumbu makanan
                'type_name' => 'Bulanan',
                'satuan' => ['Karung', 'Kg', 'Gram'],
                'description' => 'Beras premium kualitas terbaik',
                'early_expiry_days' => 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karung-Kg' => 25,
                    'Kg-Gram' => 1000,
                ]
            ],
            [
                'base_sku' => 'TPMX001',
                'name' => 'Tepung Terigu Bogasari',
                'brand_name' => 'Bogasari',
                'sub_category_id' => 1, // Bumbu makanan
                'type_name' => 'Bulanan',
                'satuan' => ['Karung', 'Kg', 'Gram'],
                'description' => 'Tepung terigu berkualitas',
                'early_expiry_days' => 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karung-Kg' => 25,
                    'Kg-Gram' => 1000,
                ]
            ],

            // SNACK & BISKUIT
            [
                'base_sku' => 'CTTO001',
                'name' => 'Chitato Rasa Sapi Panggang',
                'brand_name' => 'Garuda Food',
                'sub_category_id' => 16, // Snack
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Keripik kentang rasa sapi panggang',
                'early_expiry_days' => 6 * 30,
                'mid_expiry_days' => 60,
                'late_expiry_days' => 7,
                'conversion' => [
                    'Karton-Pack' => 20,
                    'Pack-Pcs' => 10,
                ]
            ],
            [
                'base_sku' => 'OREO001',
                'name' => 'Oreo Biskuit',
                'brand_name' => 'Mondelez Indonesia',
                'sub_category_id' => 17, // Biskuit dan Wafer
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Biskuit sandwich dengan krim',
                'early_expiry_days' => 6 * 30,
                'mid_expiry_days' => 60,
                'late_expiry_days' => 7,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],

            // MAKANAN KALENG & KESEHATAN
            [
                'base_sku' => 'SRDN001',
                'name' => 'Sarden ABC',
                'brand_name' => 'Garuda Food',
                'sub_category_id' => 18, // Makanan Kaleng
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Kotak', 'Kaleng'],
                'description' => 'Sarden ikan dalam saus tomat',
                'early_expiry_days' => 3 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Kotak' => 48,
                    'Kotak-Kaleng' => 12,
                ]
            ],
            [
                'base_sku' => 'VITM001',
                'name' => 'Vitamin C Sido Muncul',
                'brand_name' => 'Sido Muncul',
                'sub_category_id' => 14, // Obat-obatan dan suplemen
                'type_name' => 'Bulanan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Suplemen vitamin C',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 10,
                ]
            ],

            // PRODUK BAYI & ANAK
            [
                'base_sku' => 'POPK001',
                'name' => 'Popok Bayi Pampers',
                'brand_name' => 'Procter & Gamble Indonesia',
                'sub_category_id' => 9, // Perlengkapan Bayi
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Popok bayi anti bocor',
                'early_expiry_days' => 3 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 8,
                    'Pack-Pcs' => 30,
                ]
            ],
            [
                'base_sku' => 'SUSUB001',
                'name' => 'Susu Dancow',
                'brand_name' => 'Nestle Indonesia',
                'sub_category_id' => 11, // Susu
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Susu bubuk untuk anak',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 12,
                    'Pack-Pcs' => 6,
                ]
            ],

            // MAKANAN SIAP SAJI
            [
                'base_sku' => 'ROTI001',
                'name' => 'Roti Tawar Sari Roti',
                'brand_name' => 'Nippon Indosari Corpindo',
                'sub_category_id' => 24, // Roti, Pastry dan Cereal
                'type_name' => 'Harian',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Roti tawar segar',
                'early_expiry_days' => 7,
                'mid_expiry_days' => 3,
                'late_expiry_days' => 1,
                'conversion' => [
                    'Karton-Pack' => 20,
                    'Pack-Pcs' => 10,
                ]
            ],

            // MINUMAN - HARIAN
            [
                'base_sku' => 'TEHP001',
                'name' => 'Teh Pucuk Harum',
                'brand_name' => 'Mayora',
                'sub_category_id' => 20, // Teh
                'type_name' => 'Harian',
                'satuan' => ['Karton', 'Pack', 'Kotak'],
                'description' => 'Teh dalam kemasan siap minum',
                'early_expiry_days' => 365,
                'mid_expiry_days' => 90,
                'late_expiry_days' => 14,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Kotak' => 12,
                ]
            ],
            [
                'base_sku' => 'FANT001',
                'name' => 'Fanta Jeruk 330ml',
                'brand_name' => 'Coca-Cola Indonesia',
                'sub_category_id' => 4, // Minuman ringan
                'type_name' => 'Harian',
                'satuan' => ['Karton', 'Pack', 'Kotak'],
                'description' => 'Minuman bersoda rasa jeruk',
                'early_expiry_days' => 365,
                'mid_expiry_days' => 90,
                'late_expiry_days' => 14,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Kotak' => 12,
                ]
            ],
            [
                'base_sku' => 'JUSC001',
                'name' => 'Jus Buavita Jeruk',
                'brand_name' => 'Unilever Indonesia',
                'sub_category_id' => 3, // Jus/Sari Buah
                'type_name' => 'Harian',
                'satuan' => ['Karton', 'Pack', 'Kotak'],
                'description' => 'Jus jeruk dalam kemasan',
                'early_expiry_days' => 180,
                'mid_expiry_days' => 30,
                'late_expiry_days' => 7,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Kotak' => 12,
                ]
            ],
            [
                'base_sku' => 'MDUS001',
                'name' => 'Madu TJ Johar',
                'brand_name' => 'Orang Tua Group',
                'sub_category_id' => 5, // Sirup, Madu & SKM
                'type_name' => 'Bulanan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Madu murni asli',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'SIRUP001',
                'name' => 'Sirup Marjan Cocopandan',
                'brand_name' => 'Orang Tua Group',
                'sub_category_id' => 5, // Sirup, Madu & SKM
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Sirup rasa cocopandan',
                'early_expiry_days' => 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],

            // MAKANAN INSTAN & KALENG
            [
                'base_sku' => 'POPM001',
                'name' => 'Pop Mie Rasa Ayam',
                'brand_name' => 'Indofood',
                'sub_category_id' => 19, // Makanan Instan
                'type_name' => 'Harian',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Mie instan dalam cup',
                'early_expiry_days' => 365,
                'mid_expiry_days' => 60,
                'late_expiry_days' => 7,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'ABCM001',
                'name' => 'ABC Kecap Manis',
                'brand_name' => 'Garuda Food',
                'sub_category_id' => 1, // Bumbu makanan
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Kecap manis premium',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'CORN001',
                'name' => 'Corned Beef Pronas',
                'brand_name' => 'Garuda Food',
                'sub_category_id' => 18, // Makanan Kaleng
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Kaleng'],
                'description' => 'Kornet sapi dalam kaleng',
                'early_expiry_days' => 3 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Kaleng' => 12,
                ]
            ],
            [
                'base_sku' => 'ABON001',
                'name' => 'Abon Sapi Garuda',
                'brand_name' => 'Garuda Food',
                'sub_category_id' => 18, // Makanan Kaleng
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Abon sapi dalam kaleng',
                'early_expiry_days' => 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],

            // SNACK & BISKUIT
            [
                'base_sku' => 'ROMA001',
                'name' => 'Roma Kelapa',
                'brand_name' => 'Mayora',
                'sub_category_id' => 17, // Biskuit dan Wafer
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Biskuit kelapa renyah',
                'early_expiry_days' => 6 * 30,
                'mid_expiry_days' => 60,
                'late_expiry_days' => 7,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'NABS001',
                'name' => 'Nabati Keju',
                'brand_name' => 'Kalbe Nutritionals',
                'sub_category_id' => 17, // Biskuit dan Wafer
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Wafer dengan rasa keju',
                'early_expiry_days' => 6 * 30,
                'mid_expiry_days' => 60,
                'late_expiry_days' => 7,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'TARO001',
                'name' => 'Taro Snack',
                'brand_name' => 'Garuda Food',
                'sub_category_id' => 16, // Snack
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Keripik kentang rasa taro',
                'early_expiry_days' => 6 * 30,
                'mid_expiry_days' => 60,
                'late_expiry_days' => 7,
                'conversion' => [
                    'Karton-Pack' => 20,
                    'Pack-Pcs' => 10,
                ]
            ],
            [
                'base_sku' => 'CHIL001',
                'name' => 'Chiki Balls',
                'brand_name' => 'Wings Food',
                'sub_category_id' => 16, // Snack
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Snack jagung berbentuk bola',
                'early_expiry_days' => 6 * 30,
                'mid_expiry_days' => 60,
                'late_expiry_days' => 7,
                'conversion' => [
                    'Karton-Pack' => 20,
                    'Pack-Pcs' => 10,
                ]
            ],
            [
                'base_sku' => 'SERL001',
                'name' => 'Sereal Energen',
                'brand_name' => 'Mayora',
                'sub_category_id' => 24, // Roti, Pastry dan Cereal
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Minuman sereal instan',
                'early_expiry_days' => 365,
                'mid_expiry_days' => 90,
                'late_expiry_days' => 14,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],

            // KEBUTUHAN RUMAH TANGGA
            [
                'base_sku' => 'DOVE001',
                'name' => 'Sabun Mandi Dove',
                'brand_name' => 'Unilever Indonesia',
                'sub_category_id' => 2, // Produk Luar kulit
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Sabun mandi pelembab',
                'early_expiry_days' => 3 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'SURF001',
                'name' => 'Deterjen Surf',
                'brand_name' => 'Unilever Indonesia',
                'sub_category_id' => 27, // Deterjen dan pembersih
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Deterjen bubuk anti noda',
                'early_expiry_days' => 3 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'MAMT001',
                'name' => 'Mama Lemon',
                'brand_name' => 'Lion Wings',
                'sub_category_id' => 27, // Deterjen dan pembersih
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Cairan pencuci piring',
                'early_expiry_days' => 3 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'SOFM001',
                'name' => 'Soklin Softergent',
                'brand_name' => 'Wings Group',
                'sub_category_id' => 27, // Deterjen dan pembersih
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Deterjen cair pelembut',
                'early_expiry_days' => 3 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'PANT001',
                'name' => 'Pantene Shampoo',
                'brand_name' => 'Procter & Gamble Indonesia',
                'sub_category_id' => 2, // Produk Luar kulit
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Shampo vitamin rambut',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'TISU002',
                'name' => 'Tisu Nice',
                'brand_name' => 'Wings Group',
                'sub_category_id' => 30, // Tisu
                'type_name' => 'Harian',
                'satuan' => ['Karton', 'Pack', 'Roll'],
                'description' => 'Tisu toilet lembut',
                'early_expiry_days' => 3 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Roll' => 10,
                ]
            ],
            [
                'base_sku' => 'STRR001',
                'name' => 'Stella Regency',
                'brand_name' => 'Wings Group',
                'sub_category_id' => 30, // Tisu
                'type_name' => 'Harian',
                'satuan' => ['Karton', 'Pack', 'Roll'],
                'description' => 'Tisu facial berkualitas',
                'early_expiry_days' => 3 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Roll' => 10,
                ]
            ],

            // PRODUK SUSU & BAYI
            [
                'base_sku' => 'BEAR001',
                'name' => 'Bearbrand Susu Cair',
                'brand_name' => 'Nestle Indonesia',
                'sub_category_id' => 11, // Susu
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Kotak'],
                'description' => 'Susu cair siap minum',
                'early_expiry_days' => 180,
                'mid_expiry_days' => 30,
                'late_expiry_days' => 7,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Kotak' => 12,
                ]
            ],
            [
                'base_sku' => 'ULTRA001',
                'name' => 'Ultra Milk',
                'brand_name' => 'Frisian Flag Indonesia',
                'sub_category_id' => 11, // Susu
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Kotak'],
                'description' => 'Susu UHT rasa cokelat',
                'early_expiry_days' => 180,
                'mid_expiry_days' => 30,
                'late_expiry_days' => 7,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Kotak' => 12,
                ]
            ],
            [
                'base_sku' => 'DIAP001',
                'name' => 'Sweety Diapers',
                'brand_name' => 'Wings Group',
                'sub_category_id' => 9, // Perlengkapan Bayi
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Popok bayi premium',
                'early_expiry_days' => 3 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 8,
                    'Pack-Pcs' => 30,
                ]
            ],
            [
                'base_sku' => 'BBFD001',
                'name' => 'Milna Bubur Bayi',
                'brand_name' => 'Kalbe Nutritionals',
                'sub_category_id' => 10, // Makanan Bayi dan Anak
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Bubur bayi bergizi',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],

            // BUMBU & BAHAN MASAK
            [
                'base_sku' => 'GARAM001',
                'name' => 'Garam Beryodium',
                'brand_name' => 'Tiga Pilar Sejahtera',
                'sub_category_id' => 1, // Bumbu makanan
                'type_name' => 'Bulanan',
                'satuan' => ['Karung', 'Kg', 'Gram'],
                'description' => 'Garam dapur beryodium',
                'early_expiry_days' => 3 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karung-Kg' => 25,
                    'Kg-Gram' => 1000,
                ]
            ],
            [
                'base_sku' => 'MICIN001',
                'name' => 'Micin Ajinomoto',
                'brand_name' => 'Ajinomoto Indonesia',
                'sub_category_id' => 1, // Bumbu makanan
                'type_name' => 'Bulanan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Penyedap rasa masakan',
                'early_expiry_days' => 3 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'SASA001',
                'name' => 'Sasa Santan',
                'brand_name' => 'Indofood',
                'sub_category_id' => 1, // Bumbu makanan
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Santan kelapa instan',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'MARG001',
                'name' => 'Margarin Blue Band',
                'brand_name' => 'Unilever Indonesia',
                'sub_category_id' => 1, // Bumbu makanan
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Margarin untuk masak dan olesan',
                'early_expiry_days' => 365,
                'mid_expiry_days' => 90,
                'late_expiry_days' => 14,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],

            // KESEHATAN & SUPLEMEN
            [
                'base_sku' => 'TOLS001',
                'name' => 'Tolak Angin',
                'brand_name' => 'Sido Muncul',
                'sub_category_id' => 14, // Obat-obatan dan suplemen
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Obat herbal masuk angin',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'KUKO001',
                'name' => 'Kuku Bima Energi',
                'brand_name' => 'Sido Muncul',
                'sub_category_id' => 14, // Obat-obatan dan suplemen
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Minuman herbal penambah energi',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'ANTM001',
                'name' => 'Antimo',
                'brand_name' => 'Kalbe Nutritionals',
                'sub_category_id' => 14, // Obat-obatan dan suplemen
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Obat antimual perjalanan',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],

            // ROTI & PASTRY
            [
                'base_sku' => 'ROTI002',
                'name' => 'Roti Sobek Sari Roti',
                'brand_name' => 'Nippon Indosari Corpindo',
                'sub_category_id' => 24, // Roti, Pastry dan Cereal
                'type_name' => 'Harian',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Roti sobek lembut',
                'early_expiry_days' => 5,
                'mid_expiry_days' => 2,
                'late_expiry_days' => 1,
                'conversion' => [
                    'Karton-Pack' => 20,
                    'Pack-Pcs' => 6,
                ]
            ],
            [
                'base_sku' => 'RWAT001',
                'name' => 'Roti Aoka',
                'brand_name' => 'Nippon Indosari Corpindo',
                'sub_category_id' => 24, // Roti, Pastry dan Cereal
                'type_name' => 'Harian',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Roti manis premium',
                'early_expiry_days' => 5,
                'mid_expiry_days' => 2,
                'late_expiry_days' => 1,
                'conversion' => [
                    'Karton-Pack' => 20,
                    'Pack-Pcs' => 8,
                ]
            ],

            // BUMBU MASAK LAINNYA
            [
                'base_sku' => 'BAWP001',
                'name' => 'Bawang Putih Bubuk',
                'brand_name' => 'Ajinomoto Indonesia',
                'sub_category_id' => 1, // Bumbu makanan
                'type_name' => 'Bulanan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Bumbu bawang putih bubuk',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'LADA001',
                'name' => 'Lada Hitam Bubuk',
                'brand_name' => 'Ajinomoto Indonesia',
                'sub_category_id' => 1, // Bumbu makanan
                'type_name' => 'Bulanan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Bumbu lada hitam bubuk',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],

            // MAKANAN HEWAN
            [
                'base_sku' => 'PETF001',
                'name' => 'Whiskas Makanan Kucing',
                'brand_name' => 'Kalbe Nutritionals',
                'sub_category_id' => 15, // Makanan Hewan Peliharaan
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Makanan kucing bergizi',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ],
            [
                'base_sku' => 'DOGF001',
                'name' => 'Pedigree Makanan Anjing',
                'brand_name' => 'Kalbe Nutritionals',
                'sub_category_id' => 15, // Makanan Hewan Peliharaan
                'type_name' => 'Mingguan',
                'satuan' => ['Karton', 'Pack', 'Pcs'],
                'description' => 'Makanan anjing bergizi',
                'early_expiry_days' => 2 * 365,
                'mid_expiry_days' => 180,
                'late_expiry_days' => 30,
                'conversion' => [
                    'Karton-Pack' => 24,
                    'Pack-Pcs' => 12,
                ]
            ]
        ];

        // Daftar expired durations
        $expiredDurations = [
            '+1 month',
            '+2 months',
            '+3 months',
            '+6 months',
            '+1 year'
        ];

        foreach ($data as $item) {
            // Cari brand berdasarkan nama
            $brandId = DB::table('brands')
                ->where('name', $item['brand_name'])
                ->value('id');

            // Cari type berdasarkan nama
            $typeId = DB::table('type_barang')
                ->where('name', $item['type_name'])
                ->value('id');

            if (!$brandId || !$typeId) {
                continue; // Skip jika brand atau type tidak ditemukan
            }

            // Insert ke tabel barang
            $barangId = DB::table('barang')->insertGetId([
                'subcategory_id' => $item['sub_category_id'],
                'brand_id' => $brandId,
                'type_id' => $typeId,
                'sku' => $item['base_sku'],
                'name' => $item['name'],
                'description' => $item['description'],
                'early_expiry_days' => $item['early_expiry_days'],
                'mid_expiry_days' => $item['mid_expiry_days'],
                'late_expiry_days' => $item['late_expiry_days'],
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Masukkan data ke tabel satuan_conversions
            foreach ($item['conversion'] as $satuanPair => $conversionFactor) {
                $satuans = explode('-', $satuanPair);

                if (count($satuans) === 2) {
                    $fromSatuanName = $satuans[0];
                    $toSatuanName = $satuans[1];

                    $fromSatuanId = DB::table('satuan_items')
                        ->where('name', $fromSatuanName)
                        ->value('id');

                    $toSatuanId = DB::table('satuan_items')
                        ->where('name', $toSatuanName)
                        ->value('id');

                    if ($fromSatuanId && $toSatuanId) {
                        // Forward conversion
                        DB::table('satuan_conversions')->insert([
                            'barang_id' => $barangId,
                            'from_satuan_id' => $fromSatuanId,
                            'to_satuan_id' => $toSatuanId,
                            'conversion_factor' => $conversionFactor,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Reverse conversion
                        DB::table('satuan_conversions')->insert([
                            'barang_id' => $barangId,
                            'from_satuan_id' => $toSatuanId,
                            'to_satuan_id' => $fromSatuanId,
                            'conversion_factor' => 1 / $conversionFactor,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Create multi-level conversions
                        $this->createMultiLevelConversions($barangId, $item['satuan'], $item['conversion']);
                    }
                }
            }

            // Masukkan data ke tabel barang_ki
            foreach ($expiredDurations as $expiredDuration) {
                $expiredTime = Carbon::now()->add($expiredDuration)->format('Y-m-d');

                foreach ($item['satuan'] as $index => $satuanName) {
                    $satuanId = DB::table('satuan_items')
                        ->where('name', $satuanName)
                        ->value('id');

                    $margin = DB::table('sub_categories')
                        ->where('id', $item['sub_category_id'])
                        ->value('margin');

                    // Harga berdasarkan jenis barang dan satuan
                    $priceBuy = $this->calculatePrice($item['type_name'], $satuanName, $index);
                    $priceSell = $priceBuy * ($margin / 100) + $priceBuy;

                    if ($satuanId) {
                        $barcode = $this->generateBarcode($barangId, $item['base_sku'], $index + 1, $expiredTime);

                        DB::table('barang_ki')->insert([
                            'barang_id' => $barangId,
                            'satuan_id' => $satuanId,
                            'id_barcode' => $barcode,
                            'quantity' => $this->calculateQuantity($item['type_name'], $satuanName),
                            'sold_quantity' => 0,
                            'status' => 'active',
                            'price_buy' => $priceBuy,
                            'price_sell' => $priceSell,
                            'price_up' => $margin ?? 0,
                            'expired_time' => $expiredTime,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Calculate realistic prices based on product type and unit
     */
    private function calculatePrice($typeName, $satuanName, $satuanIndex)
    {
        $basePrice = 0;

        switch ($typeName) {
            case 'Harian':
                $basePrice = [2000, 15000, 150000][$satuanIndex] ?? 2000;
                break;
            case 'Mingguan':
                $basePrice = [5000, 50000, 500000][$satuanIndex] ?? 5000;
                break;
            case 'Bulanan':
                $basePrice = [10000, 100000, 1000000][$satuanIndex] ?? 10000;
                break;
            default:
                $basePrice = 5000;
        }

        // Add some variation
        return $basePrice + rand(-$basePrice * 0.3, $basePrice * 0.3);
    }

    /**
     * Calculate realistic quantity based on product type and unit
     */
    private function calculateQuantity($typeName, $satuanName)
    {
        switch ($typeName) {
            case 'Harian':
                return rand(500, 2000); // Higher stock for daily items
            case 'Mingguan':
                return rand(200, 800);  // Medium stock for weekly items
            case 'Bulanan':
                return rand(50, 200);   // Lower stock for monthly items
            default:
                return rand(100, 500);
        }
    }

    /**
     * Create multi-level conversions for units that skip levels
     */
    private function createMultiLevelConversions($barangId, $satuanList, $conversionPairs)
    {
        $satuanMap = [];
        $conversionMap = [];

        foreach ($satuanList as $index => $satuanName) {
            $satuanMap[$satuanName] = $index;
        }

        foreach ($conversionPairs as $pair => $factor) {
            $satuans = explode('-', $pair);
            $conversionMap[$satuans[0]][$satuans[1]] = $factor;
        }

        for ($i = 0; $i < count($satuanList) - 2; $i++) {
            for ($j = $i + 2; $j < count($satuanList); $j++) {
                $fromSatuan = $satuanList[$i];
                $toSatuan = $satuanList[$j];

                $fromSatuanId = DB::table('satuan_items')
                    ->where('name', $fromSatuan)
                    ->value('id');

                $toSatuanId = DB::table('satuan_items')
                    ->where('name', $toSatuan)
                    ->value('id');

                if ($fromSatuanId && $toSatuanId) {
                    $totalConversionFactor = 1;
                    $isValidPath = true;

                    for ($k = $i; $k < $j; $k++) {
                        $currentSatuan = $satuanList[$k];
                        $nextSatuan = $satuanList[$k + 1];

                        if (isset($conversionMap[$currentSatuan][$nextSatuan])) {
                            $totalConversionFactor *= $conversionMap[$currentSatuan][$nextSatuan];
                        } else {
                            $isValidPath = false;
                            break;
                        }
                    }

                    if ($isValidPath) {
                        $existingForward = DB::table('satuan_conversions')
                            ->where('barang_id', $barangId)
                            ->where('from_satuan_id', $fromSatuanId)
                            ->where('to_satuan_id', $toSatuanId)
                            ->exists();

                        if (!$existingForward) {
                            DB::table('satuan_conversions')->insert([
                                'barang_id' => $barangId,
                                'from_satuan_id' => $fromSatuanId,
                                'to_satuan_id' => $toSatuanId,
                                'conversion_factor' => $totalConversionFactor,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }

                        $existingReverse = DB::table('satuan_conversions')
                            ->where('barang_id', $barangId)
                            ->where('from_satuan_id', $toSatuanId)
                            ->where('to_satuan_id', $fromSatuanId)
                            ->exists();

                        if (!$existingReverse) {
                            DB::table('satuan_conversions')->insert([
                                'barang_id' => $barangId,
                                'from_satuan_id' => $toSatuanId,
                                'to_satuan_id' => $fromSatuanId,
                                'conversion_factor' => 1 / $totalConversionFactor,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }
        }
    }

    private function generateBarcode($itemId, $sku, $skuIndex, $expiredTime)
    {
        $formattedItemId = str_pad($itemId, 4, '0', STR_PAD_LEFT);
        $skuNumber = str_pad($skuIndex, 3, '0', STR_PAD_LEFT);
        $expirationDate = \Carbon\Carbon::parse($expiredTime)->format('dmy');

        return "{$formattedItemId}{$skuNumber}{$expirationDate}";
    }

    private function seedTipeBarang()
    {
        $tipeBarang = [
            ['name' => 'Harian', 'description' => 'Barang yang dibutuhkan oleh konsumen setiap hari seperti makanan pokok, minuman, dan kebutuhan dasar.'],
            ['name' => 'Mingguan', 'description' => 'Barang yang dibeli setelah membandingkan kualitas dan harga, biasanya untuk kebutuhan mingguan.'],
            ['name' => 'Bulanan', 'description' => 'Barang yang dibeli untuk kebutuhan bulanan, biasanya dalam jumlah besar atau untuk stok jangka panjang.'],
        ];

        if (DB::table('type_barang')->count() == 0) {
            DB::table('type_barang')->insert($tipeBarang);
        }
    }

    private function seedSatuanItems(): void
    {
        $satuanItems = [
            // Level 1 (Terkecil)
            ['name' => 'Pcs', 'cut_name' => 'Pcs', 'level' => 1, 'type' => 'quantity', 'selling' => 'true', 'description' => 'Pieces/Buah'],
            ['name' => 'Gram', 'cut_name' => 'g', 'level' => 1, 'type' => 'weight', 'selling' => 'true', 'description' => 'Gram'],
            ['name' => 'Kaleng', 'cut_name' => 'Klg', 'level' => 1, 'type' => 'quantity', 'selling' => 'true', 'description' => 'Kaleng'],
            ['name' => 'Kotak', 'cut_name' => 'Ktk', 'level' => 1, 'type' => 'quantity', 'selling' => 'true', 'description' => 'Kotak'],
            ['name' => 'Roll', 'cut_name' => 'Roll', 'level' => 1, 'type' => 'quantity', 'selling' => 'true', 'description' => 'Roll tisu'],

            // Level 2 (Menengah)
            ['name' => 'Pack', 'cut_name' => 'Pack', 'level' => 2, 'type' => 'quantity', 'selling' => 'true', 'description' => 'Pack'],
            ['name' => 'Kg', 'cut_name' => 'Kg', 'level' => 2, 'type' => 'weight', 'selling' => 'true', 'description' => 'Kilogram'],
            ['name' => 'Slop', 'cut_name' => 'Slop', 'level' => 2, 'type' => 'quantity', 'selling' => 'true', 'description' => 'Slop rokok'],

            // Level 3 (Terbesar)
            ['name' => 'Karton', 'cut_name' => 'Karton', 'level' => 3, 'type' => 'quantity', 'selling' => 'true', 'description' => 'Karton box'],
            ['name' => 'Karung', 'cut_name' => 'Krg', 'level' => 3, 'type' => 'quantity', 'selling' => 'true', 'description' => 'Karung'],
        ];

        foreach ($satuanItems as $item) {
            DB::table('satuan_items')->updateOrInsert(
                ['name' => $item['name']],
                [
                    'cut_name' => $item['cut_name'],
                    'level' => $item['level'],
                    'type' => $item['type'],
                    'selling' => $item['selling'],
                    'description' => $item['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
