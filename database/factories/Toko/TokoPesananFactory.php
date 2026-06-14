<?php
namespace Database\Factories\Toko;

use App\Models\Barang\BarangKI;
use App\Models\Toko\TokoPesanan;
use App\Models\Toko\TokoPayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TokoPesananFactory extends Factory
{
    protected $model = TokoPesanan::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $price = 50000;
        
        return [
            'payment_id' => TokoPayment::factory(),
            'barangki_id' => BarangKI::factory(),
            'price' => $price,
            'quantity' => $quantity,
            'total' => $price * $quantity,
            'status' => $this->faker->randomElement(['pending', 'success']),
            'created_at' => Carbon::now()->subDays(rand(1, 28)),
            'updated_at' => Carbon::now()->subDays(rand(1, 28)),
            // 'created_at' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 28)),
            // 'updated_at' => Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 28)),
        ];
    }
}