<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KriteriaAndSubkriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seeder untuk tabel kriteria
        $kriteria = [
            ['nama' => 'Stock Habis', 'prioritas' => 1, 'bobot' => 0.52],
            ['nama' => 'Stock', 'prioritas' => 2, 'bobot' => 0.27],
            ['nama' => 'Penjualan', 'prioritas' => 3, 'bobot' => 0.15],
            ['nama' => 'Kategory', 'prioritas' => 4, 'bobot' => 0.06],
        ];

        // Insert data ke tabel kriteria
        DB::table('kriteria')->insert($kriteria);

        // Mendapatkan ID kriteria yang baru saja dimasukkan
        $kriteriaIds = DB::table('kriteria')->pluck('id')->toArray();

        // Seeder untuk tabel subkriteria
        $subkriteria = [
            ['kriteria_id' => $kriteriaIds[0], 'nama' => 'Ya', 'prioritas' => 1, 'bobot' => 0.75],
            ['kriteria_id' => $kriteriaIds[0], 'nama' => 'Tidak', 'prioritas' => 2, 'bobot' => 0.25],
            ['kriteria_id' => $kriteriaIds[1], 'nama' => '<10', 'prioritas' => 1, 'bobot' => 0.61],
            ['kriteria_id' => $kriteriaIds[1], 'nama' => '10-100', 'prioritas' => 2, 'bobot' => 0.28],
            ['kriteria_id' => $kriteriaIds[1], 'nama' => '>100', 'prioritas' => 3, 'bobot' => 0.11],
            ['kriteria_id' => $kriteriaIds[2], 'nama' => '>1000', 'prioritas' => 1, 'bobot' => 0.61],
            ['kriteria_id' => $kriteriaIds[2], 'nama' => '500-1000', 'prioritas' => 2, 'bobot' => 0.28],
            ['kriteria_id' => $kriteriaIds[2], 'nama' => '<500', 'prioritas' => 3, 'bobot' => 0.11],
            ['kriteria_id' => $kriteriaIds[3], 'nama' => 'Harian', 'prioritas' => 1, 'bobot' => 0.61],
            ['kriteria_id' => $kriteriaIds[3], 'nama' => 'Mingguan', 'prioritas' => 2, 'bobot' => 0.28],
            ['kriteria_id' => $kriteriaIds[3], 'nama' => 'Bulanan', 'prioritas' => 3, 'bobot' => 0.11],
        ];

        // Insert data ke tabel subkriteria
        DB::table('subkriteria')->insert($subkriteria);
    }
}
