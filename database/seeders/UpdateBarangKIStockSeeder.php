<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang\BarangKI;
use Carbon\Carbon;

class UpdateBarangKIStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mengambil seluruh data barang KI
        $barangKIs = BarangKI::all();
        $count = 0;

        foreach ($barangKIs as $barangKI) {
            // 1. Generate Stock acak (50 sampai 500)
            $quantity = rand(50, 500);
            
            // 2. Generate Sold Quantity acak (0 sampai 50)
            $soldQuantity = rand(0, 50);
            
            // 3. Generate Expired Time (antara 3 bulan sampai 2 tahun ke depan)
            $expiredTime = Carbon::now()->addDays(rand(90, 730));

            $discountAmount = null;
            $discountPercentage = null;
            $discountStart = null;
            $discountEnd = null;

            // 4. Buat probabilitas 15% barang ini sedang diskon
            if (rand(1, 100) <= 15) {
                // Tentukan diskon berupa nominal (amount) atau persentase (percentage)
                if (rand(0, 1) == 0 && $barangKI->price_sell > 1000) {
                    // Diskon Nominal: Maksimal 20% dari harga jual (dibulatkan per 100 rupiah)
                    $maxDiscount = $barangKI->price_sell * 0.20;
                    if ($maxDiscount >= 500) {
                        $discountAmount = rand(5, min(50, intval($maxDiscount / 100))) * 100; 
                    }
                } else {
                    // Diskon Persentase: 5% sampai 20%
                    $discountPercentage = rand(5, 20);
                }

                // Tanggal mulai diskon (beberapa hari yang lalu sampai hari ini)
                $discountStart = Carbon::now()->subDays(rand(0, 5));
                // Tanggal berakhir diskon (1 minggu sampai 1 bulan ke depan)
                $discountEnd = Carbon::now()->addDays(rand(7, 30));
            }

            // Update ke database tanpa mengganggu relasi awal
            $barangKI->update([
                'quantity' => $quantity,
                'sold_quantity' => $soldQuantity,
                'expired_time' => $expiredTime,
                'discount_amount' => $discountAmount,
                'discount_percentage' => $discountPercentage,
                'discount_start' => $discountStart,
                'discount_end' => $discountEnd,
                'status' => 'active',
            ]);

            $count++;
        }

        // Tampilkan pesan berhasil di terminal
        $this->command->info("✅ Berhasil meng-update {$count} data Barang KI dengan stock, diskon, dan expired date acak!");
    }
}
