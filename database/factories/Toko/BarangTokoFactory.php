<?php

namespace Database\Factories\Toko;

use App\Models\Barang\BarangKI;
use App\Models\Toko\BarangToko;
use App\Models\Toko\TokoModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class BarangTokoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BarangToko::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $priceBuy = $this->faker->numberBetween(10000, 50000);
        $percentage = $this->faker->numberBetween(10, 30);
        $priceSell = $priceBuy + ($priceBuy * $percentage / 100);

        return [
            'toko_id' => TokoModel::factory(),
            'barangki_id' => BarangKI::factory(),
            'price_buy' => $priceBuy,
            'price_sell' => $priceSell,
            'price_percentage' => $percentage,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Configure the factory to set quantity and sold values only for the smallest unit.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (BarangToko $barangToko) {
            // If barangki_id is provided directly, use it
            if ($barangToko->barangki_id instanceof BarangKI) {
                $barangKi = $barangToko->barangki_id;
            } else {
                // Otherwise, retrieve the BarangKI record
                $barangKi = BarangKI::find($barangToko->barangki_id);
            }

            if ($barangKi) {
                // Get the satuan level for this BarangKI
                $satuanLevel = DB::table('satuan_items')
                    ->where('id', $barangKi->satuan_id)
                    ->value('level');

                // Only set quantity and sold if it's the smallest unit (level 1)
                if ($satuanLevel == 1) {
                    $barangToko->quantity = $this->faker->numberBetween(10, 100);
                    $barangToko->sold = $this->faker->numberBetween(0, 50);
                } else {
                    $barangToko->quantity = 0;
                    $barangToko->sold = 0;
                }
            }
        })->afterCreating(function (BarangToko $barangToko) {
            // Same logic after creating to ensure database consistency
            if (!$barangToko->barangki_id instanceof BarangKI) {
                $barangKi = BarangKI::find($barangToko->barangki_id);
                
                if ($barangKi) {
                    $satuanLevel = DB::table('satuan_items')
                        ->where('id', $barangKi->satuan_id)
                        ->value('level');

                    if ($satuanLevel != 1) {
                        // Update the database record to ensure nulls are properly set
                        DB::table('barang_toko')
                            ->where('id', $barangToko->id)
                            ->update([
                                'quantity' => 0,
                                'sold' => 0
                            ]);
                    }
                }
            }
        });
    }
}