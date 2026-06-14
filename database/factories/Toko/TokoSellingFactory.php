<?php

namespace Database\Factories\Toko;

use App\Models\Auth\UserModel;
use App\Models\Toko\TokoModel;
use App\Models\Toko\TokoSelling;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TokoSellingFactory extends Factory
{
    protected $model = TokoSelling::class;

    public function definition(): array
    {
        return [
            'id_transaction' => strtoupper(Str::random(12)),
            'toko_id' => TokoModel::factory(),
            'user_id' => UserModel::factory(),
            'total_harga' => 100000,
            'status' => $this->faker->randomElement(['pending', 'success']),
            'metode_pembayaran' => $this->faker->randomElement(['QRIS', 'cash', 'transfer']),
            'is_online' => $this->faker->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}