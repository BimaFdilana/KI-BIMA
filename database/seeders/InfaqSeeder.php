<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InfaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed untuk infaq_lists
        $infaqLists = [
            [
                'name' => 'Operasional Masjid',
                'dana_dibutuhkan' => 1000000,
                'slug' => 'operasional-masjid',
                'description' => 'Dana untuk kebutuhan operasional harian masjid seperti listrik, air, kebersihan, dan pemeliharaan rutin',
                'category' => 'operasional',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Pembangunan dan Renovasi',
                'dana_dibutuhkan' => 1000000,
                'slug' => 'pembangunan-dan-renovasi',
                'description' => 'Dana untuk pembangunan fasilitas baru atau renovasi masjid dan sekitarnya',
                'category' => 'pembangunan',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Santunan Yatim Piatu',
                'dana_dibutuhkan' => 1000000,
                'slug' => 'santunan-yatim-piatu',
                'description' => 'Dana untuk memberikan santunan kepada anak yatim piatu di sekitar masjid',
                'category' => 'sosial',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Bantuan Fakir Miskin',
                'dana_dibutuhkan' => 1000000,
                'slug' => 'bantuan-fakir-miskin',
                'description' => 'Dana untuk membantu keluarga kurang mampu dan fakir miskin di lingkungan sekitar',
                'category' => 'sosial',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Kegiatan Dakwah',
                'dana_dibutuhkan' => 1000000,
                'slug' => 'kegiatan-dakwah',
                'description' => 'Dana untuk mendukung kegiatan dakwah, kajian, dan pengajian rutin',
                'category' => 'operasional',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Bantuan Bencana Alam',
                'dana_dibutuhkan' => 1000000,
                'slug' => 'bantuan-bencana-alam',
                'description' => 'Dana siap pakai untuk membantu korban bencana alam',
                'category' => 'bencana',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Pendidikan Anak',
                'dana_dibutuhkan' => 1000000,
                'slug' => 'pendidikan-anak',
                'description' => 'Dana untuk mendukung pendidikan anak-anak kurang mampu, beasiswa, dan peralatan sekolah',
                'category' => 'sosial',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Kesehatan Masyarakat',
                'dana_dibutuhkan' => 1000000,
                'slug' => 'kesehatan-masyarakat',
                'description' => 'Dana untuk program kesehatan, pengobatan gratis, dan bantuan medis',
                'category' => 'sosial',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Infaq Umum',
                'dana_dibutuhkan' => 1000000,
                'slug' => 'infaq-umum',
                'description' => 'Dana infaq yang dapat dialokasikan sesuai kebutuhan mendesak masjid',
                'category' => 'umum',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Program Ramadhan',
                'dana_dibutuhkan' => 1000000,
                'slug' => 'program-ramadhan',
                'description' => 'Dana khusus untuk kegiatan bulan Ramadhan seperti takjil, sahur on the road, dan santunan',
                'category' => 'operasional',
                'is_active' => false, // Dinonaktifkan karena musiman
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        DB::table('infaq_lists')->insert($infaqLists);

        // Seed untuk infaq_histories (contoh data)
        // Pastikan sudah ada data di tabel toko, users, dan toko_selling sebelum menjalankan seeder ini
        $infaqHistories = [
            [
                'toko_id' => 1, // Sesuaikan dengan ID toko yang ada
                'user_id' => 1, // Sesuaikan dengan ID user yang ada
                'infaq_list_id' => 1, // Operasional Masjid
                'amount' => 50000.00,
                'status' => 'completed',
                'donor_name' => 'Ahmad Subagja',
                'note' => 'Semoga berkah untuk operasional masjid',
                'payment_method' => 'cash',
                'selling_id' => '1', // Sesuaikan dengan ID selling yang ada
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'toko_id' => 1,
                'user_id' => 2, // Sesuaikan dengan ID user yang ada
                'infaq_list_id' => 3, // Santunan Yatim Piatu
                'amount' => 100000.00,
                'status' => 'completed',
                'donor_name' => 'Siti Aminah',
                'note' => 'Untuk anak-anak yatim piatu',
                'payment_method' => 'transfer',
                'selling_id' => '2',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'toko_id' => 1,
                'user_id' => 1,
                'infaq_list_id' => 9, // Infaq Umum
                'amount' => 25000.00,
                'status' => 'pending',
                'donor_name' => 'Hamba Allah',
                'note' => null,
                'payment_method' => 'cash',
                'selling_id' => '3',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'toko_id' => 1,
                'user_id' => 3, // Sesuaikan dengan ID user yang ada
                'infaq_list_id' => 4, // Bantuan Fakir Miskin
                'amount' => 75000.00,
                'status' => 'completed',
                'donor_name' => 'Budi Santoso',
                'note' => 'Bantuan untuk keluarga kurang mampu',
                'payment_method' => 'digital_wallet',
                'selling_id' => '4',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'toko_id' => 1,
                'user_id' => 2,
                'infaq_list_id' => 2, // Pembangunan dan Renovasi
                'amount' => 200000.00,
                'status' => 'completed',
                'donor_name' => 'CV. Berkah Jaya',
                'note' => 'Bantuan untuk renovasi musholla',
                'payment_method' => 'transfer',
                'selling_id' => '5',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        // Uncomment baris di bawah jika sudah memiliki data di tabel terkait
        DB::table('infaq_histories')->insert($infaqHistories);

        $this->command->info('Infaq lists seeded successfully!');
    }
}
