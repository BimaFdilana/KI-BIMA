<?php

namespace Database\Seeders;

use App\Models\Auth\UserModel;
use App\Models\Barang\BarangKI;
use App\Models\Toko\BarangToko;
use App\Models\Toko\TokoKeranjang;
use App\Models\Toko\TokoModel;
use App\Models\Toko\TokoPayment;
use App\Models\Toko\TokoPesanan;
use App\Models\Toko\TokoPaymentProgress;
use App\Models\Toko\TokoSelling;
use App\Models\Toko\TokoSellingDetail;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DataTokoSeeder extends Seeder
{
    public function run(): void
    {
        $users = UserModel::all();
        $tokos = TokoModel::all();
        $barangkis = BarangKI::all();

        if ($users->isEmpty() || $tokos->isEmpty() || $barangkis->isEmpty()) {
            $this->command->warn('Pastikan tabel user, toko, dan barang_ki sudah memiliki data.');
            return;
        }

        $count = 20;
        $totalRecords = count($tokos) * $count;
        $progressBar = $this->command->getOutput()->createProgressBar($totalRecords);
        $progressBar->start();

        foreach ($tokos as $toko) {
            $this->command->info("Generating data for toko: {$toko->id}");

            for ($i = 0; $i < $count; $i++) {
                $user = $users->random();
                $barangki = $barangkis->random();

                // Create toko_payment
                $payment = TokoPayment::factory()->create([
                    'user_id' => $user->id,
                    'toko_id' => $toko->id,
                ]);

                // Create toko_pesanan
                $pesanan = TokoPesanan::factory()->create([
                    'payment_id' => $payment->id,
                    'barangki_id' => $barangki->id,
                ]);

                // Create progress tracking records with realistic timeline
                // We'll generate between 2-6 progress updates per order
                $numUpdates = rand(2, 6);
                $baseDate = Carbon::now()->subDays(rand(1, 30));

                // List of statuses in chronological order
                $progressStatuses = [
                    TokoPaymentProgress::STATUS_REFUND_REQUESTED,
                    TokoPaymentProgress::STATUS_REFUNDED,
                    TokoPaymentProgress::STATUS_PAID,
                    TokoPaymentProgress::STATUS_PENDING,
                    TokoPaymentProgress::STATUS_FAILED,
                    TokoPaymentProgress::STATUS_UNKNOWN,
                    TokoPaymentProgress::STATUS_SUCCESS,
                    TokoPaymentProgress::STATUS_DELIVERY,
                    TokoPaymentProgress::STATUS_CANCELLED,
                ];

                // Randomly decide if order was cancelled (10% chance)
                $wasCancelled = rand(1, 10) === 1;

                if ($wasCancelled) {
                    // For cancelled orders, we'll pick a random point to cancel
                    $cancelPoint = rand(0, min(2, $numUpdates - 1)); // Cancel at early stages
                    $selectedStatuses = array_slice($progressStatuses, 0, $cancelPoint + 1);
                    $selectedStatuses[] = TokoPaymentProgress::STATUS_CANCELLED;
                } else {
                    // For normal orders, pick consecutive statuses
                    $selectedStatuses = array_slice($progressStatuses, 0, $numUpdates);
                }

                foreach ($selectedStatuses as $index => $status) {
                    // Add some time between status updates (2-12 hours typically)
                    $statusTimestamp = clone $baseDate;
                    $statusTimestamp->addHours($index * rand(2, 12));

                    // Sometimes add random minutes for more realistic timestamps
                    $statusTimestamp->addMinutes(rand(1, 59));

                    TokoPaymentProgress::factory()
                        ->withStatus($status)
                        ->create([
                            'payment_id' => $payment->id,
                            'user_id' => $user->id,
                            'created_at' => $statusTimestamp,
                            'updated_at' => $statusTimestamp,
                        ]);
                }

                // Create toko_keranjang
                TokoKeranjang::factory()->create([
                    'toko_id' => $toko->id,
                    'barangki_id' => $barangki->id,
                ]);

                // Create barang_toko
                BarangToko::factory()->create([
                    'toko_id' => $toko->id,
                    'barangki_id' => $barangki->id,
                ]);

                // Create toko_selling
                $selling = TokoSelling::factory()->create([
                    'toko_id' => $toko->id,
                    'user_id' => $user->id,
                ]);

                // Create toko_selling_detail
                TokoSellingDetail::factory()->create([
                    'transaction_id' => $selling->increment_id,
                    'barangki_id' => $barangki->id,
                ]);

                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->command->newLine();
        $this->command->info("Seeder toko sukses dijalankan. Setiap toko memiliki minimal $count data di setiap tabel dengan history progress pesanan.");
    }
}
