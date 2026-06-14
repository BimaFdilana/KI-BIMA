<?php

namespace App\Services\Barang;

use App\Models\Auth\UserModel;
use App\Models\Barang\BarangIOModel;
use App\Models\Barang\BarangKI;
use App\Models\Toko\TokoModel;
use Exception;

class BarangIOService
{
    public function addBarang($barangkiId, $quantity, $tokoId, $price)
    {
        $pemilikToko = TokoModel::find($tokoId)->first();

        if (!$pemilikToko) {
            throw new Exception('Toko tidak ditemukan');
        }
        try {
            BarangIOModel::create([
                'user_id' => $pemilikToko->owner_id,
                'barangki_id' => $barangkiId,
                'quantity' => $quantity,
                'price' => $price,
                'type' => 'in',
                'status' => 'success',
            ]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function removeBarang($tokoId, $barangkiId, $quantity, $price)
    {
        $pemilikToko = TokoModel::find($tokoId)->first();

        if (!$pemilikToko) {
            throw new Exception('Toko tidak ditemukan');
        }
        try {
            BarangIOModel::create([
                'user_id' => $pemilikToko->owner_id,
                'barangki_id' => $barangkiId,
                'quantity' => $quantity,
                'price' => $price,
                'type' => 'out',
                'status' => 'success',
            ]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function addBarangAdmin(BarangKI $barangKi, $quantity, UserModel $user, $price)
    {
        try {
            BarangIOModel::create([
                'user_id' => $user->id,
                'barangki_id' => $barangKi->id,
                'quantity' => $quantity,
                'price' => $price,
                'type' => 'in',
                'status' => 'success',
            ]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function removeBarangAdmin(BarangKI $barangKi, $quantity, UserModel $user, $price)
    {
        $quantity = $quantity * -1;
        try {
            BarangIOModel::create([
                'user_id' => $user->id,
                'barangki_id' => $barangKi->id,
                'quantity' => $quantity,
                'price' => $price,
                'type' => 'out',
                'status' => 'success',
            ]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
