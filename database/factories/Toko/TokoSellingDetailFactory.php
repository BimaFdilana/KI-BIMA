<?php

namespace Database\Factories\Toko;

use App\Models\Barang\BarangKI;
use App\Models\Toko\TokoSelling;
use App\Models\Toko\TokoSellingDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class TokoSellingDetailFactory extends Factory
{
    protected $model = TokoSellingDetail::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $price = 50000;
        
        return [
            'transaction_id' => TokoSelling::factory(),
            'barangki_id' => BarangKI::factory(),
            'jumlah' => $quantity,
            'harga_satuan' => $price,
            'subtotal' => $price * $quantity,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}