<?php

namespace Database\Factories\Toko;

use App\Models\Barang\BarangKI;
use App\Models\Toko\TokoKeranjang;
use App\Models\Toko\TokoModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class TokoKeranjangFactory extends Factory
{
    protected $model = TokoKeranjang::class;

    public function definition(): array
    {
        return [
            'toko_id' => TokoModel::factory(),
            'barangki_id' => BarangKI::factory(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}