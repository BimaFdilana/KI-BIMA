<?php

namespace Database\Factories\Toko;

use App\Models\Auth\UserModel;
use App\Models\Barang\BarangKI;
use App\Models\Toko\TokoModel;
use App\Models\Toko\TokoPayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TokoPaymentFactory extends Factory
{
    protected $model = TokoPayment::class;

    public function definition(): array
    {
        return [
            'transaction_id' => 'TRX-' . $this->faker->unique()->numberBetween(100000, 999999),
            'user_id' => UserModel::factory(),
            'toko_id' => TokoModel::factory(),
            'total' => 100000 + $this->faker->numberBetween(0, 50000),
            'payment_method' => $this->faker->randomElement(['transfer bank', 'qris', 'cod']),
            'payment_type' => $this->faker->randomElement(['Virtual', 'Cash', 'Pakdul']),
            'status' => $this->faker->randomElement(['pending', 'paid', 'failed', 'unknown', 'success', 'delivery']),
            'snap_token' => Str::random(20),
            'created_at' => Carbon::now()->subDays(rand(1, 28)),
            'updated_at' => Carbon::now()->subDays(rand(1, 28)),
        ];
    }
}
