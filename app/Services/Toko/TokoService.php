<?php

namespace App\Services\Toko;

use App\Models\Auth\UserModel;
use App\Models\Barang\BarangKI;
use App\Models\Barang\SatuanConversion;
use App\Models\Barang\SatuanItem;
use App\Models\Toko\BarangToko;
use App\Models\Toko\JabatanModel;
use App\Models\Toko\TokoModel;
use App\Models\Toko\TokoUserModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TokoService
{
    public function getTokoByUser($user)
    {
        if ($user->tokos->isNotEmpty()) {
            $tokoKaryawan = TokoUserModel::whereIn('toko_id', $user->tokos->pluck('id'))
                ->where('status', 'active')
                ->get();
            if ($tokoKaryawan->isNotEmpty()) {
                return $tokoKaryawan->first()->toko;
            }
        }
        return null;
    }

    /**
     * Get available satuans for a barang with conversion chain
     */
    private function getAvailableSatuans($barangId, $baseSatuanId)
    {
        try {
            // Ambil semua satuan dan urutkan berdasarkan level (terendah ke tertinggi)
            $allSatuans = SatuanItem::orderBy('level', 'asc')->get();

            // Buat mapping satuan id ke object satuan untuk akses mudah
            $satuanMap = [];
            foreach ($allSatuans as $satuan) {
                $satuanMap[$satuan->id] = $satuan;
            }

            // Ambil semua konversi untuk barang ini
            $conversions = SatuanConversion::where('barang_id', $barangId)->get();

            // Buat struktur konversi dari level terendah ke tertinggi
            $conversionChain = [];
            $currentSatuanId = $baseSatuanId;
            $remainingSatuans = $allSatuans->pluck('id')->toArray();

            // Hapus satuan dasar dari remaining
            $key = array_search($baseSatuanId, $remainingSatuans);
            if ($key !== false) {
                unset($remainingSatuans[$key]);
            }

            // Catat informasi satuan dan konversinya
            $conversionChain[$baseSatuanId] = [
                'id' => $baseSatuanId,
                'name' => $satuanMap[$baseSatuanId]->name,
                'level' => $satuanMap[$baseSatuanId]->level,
                'next_satuan_id' => null,
                'conversion_factor' => null
            ];

            // Temukan rantai konversi dari satuan terendah ke tertinggi
            while (!empty($remainingSatuans)) {
                $found = false;

                foreach ($conversions as $conversion) {
                    if ($conversion->from_satuan_id == $currentSatuanId && in_array($conversion->to_satuan_id, $remainingSatuans)) {
                        $toSatuanId = $conversion->to_satuan_id;

                        // Pastikan kita bergerak ke level yang lebih tinggi
                        if ($satuanMap[$toSatuanId]->level > $satuanMap[$currentSatuanId]->level) {
                            // Update info konversi satuan saat ini
                            $conversionChain[$currentSatuanId]['next_satuan_id'] = $toSatuanId;
                            $conversionChain[$currentSatuanId]['conversion_factor'] = $conversion->conversion_factor;

                            // Tambahkan satuan target ke chain
                            $conversionChain[$toSatuanId] = [
                                'id' => $toSatuanId,
                                'name' => $satuanMap[$toSatuanId]->name,
                                'level' => $satuanMap[$toSatuanId]->level,
                                'next_satuan_id' => null,
                                'conversion_factor' => null
                            ];

                            // Hapus dari remaining dan update current
                            $key = array_search($toSatuanId, $remainingSatuans);
                            if ($key !== false) {
                                unset($remainingSatuans[$key]);
                            }
                            $currentSatuanId = $toSatuanId;
                            $found = true;
                            break;
                        }
                    }
                }

                if (!$found) {
                    break;
                }
            }

            return $conversionChain;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Convert stock to all available units
     */
    private function convertStockToAllUnits($barangId, $baseSatuanId, $amount)
    {
        $conversionChain = $this->getAvailableSatuans($barangId, $baseSatuanId);

        if (empty($conversionChain)) {
            return [$baseSatuanId => $amount];
        }

        // Lakukan konversi dari satuan terendah hingga tertinggi
        $result = [];
        $remainingAmount = $amount;
        $currentSatuanId = $baseSatuanId;

        while (isset($conversionChain[$currentSatuanId]) && $conversionChain[$currentSatuanId]['next_satuan_id'] !== null) {
            $nextSatuanId = $conversionChain[$currentSatuanId]['next_satuan_id'];
            $factor = $conversionChain[$currentSatuanId]['conversion_factor'];

            // Skip if factor is 0 to avoid division by zero
            if (empty($factor) || $factor == 0) {
                $result[$currentSatuanId] = $remainingAmount;
                $remainingAmount = 0;
                break;
            }

            // Gunakan integer arithmetic untuk menghindari floating-point errors
            $conversionRate = (int) round(1 / $factor);

            if ($conversionRate <= 0) {
                $result[$currentSatuanId] = $remainingAmount;
                $remainingAmount = 0;
                break;
            }

            // Melakukan konversi dengan integer division
            $convertedAmount = intdiv($remainingAmount, $conversionRate);
            $remainder = $remainingAmount % $conversionRate;

            if ($convertedAmount > 0) {
                if ($remainder > 0) {
                    $result[$currentSatuanId] = $remainder;
                }
                $remainingAmount = $convertedAmount;
            } else {
                $result[$currentSatuanId] = $remainingAmount;
                $remainingAmount = 0;
            }

            $currentSatuanId = $nextSatuanId;
        }

        // Tambahkan jumlah yang tersisa di satuan tertinggi
        if ($remainingAmount > 0) {
            $result[$currentSatuanId] = $remainingAmount;
        }

        // Pastikan semua satuan dalam chain memiliki value (0 jika tidak ada)
        foreach ($conversionChain as $satuanId => $info) {
            if (!isset($result[$satuanId])) {
                $result[$satuanId] = 0;
            }
        }

        return $result;
    }

    public function convertStock(int $barangki_id, int $amount)
    {
        try {
            // Ambil data BarangKI
            $barangKI = BarangKI::with(['barang', 'satuan'])->find($barangki_id);
            if (!$barangKI) {
                return $amount . ' Unknown';
            }

            $barangId = $barangKI->barang_id;
            $baseSatuanId = $barangKI->satuan_id;
            $baseSatuanName = $barangKI->satuan->name;

            // Convert stock to all units
            $stockByUnit = $this->convertStockToAllUnits($barangId, $baseSatuanId, $amount);

            // Get conversion chain to get satuan names
            $conversionChain = $this->getAvailableSatuans($barangId, $baseSatuanId);

            // Format hasil dari level tertinggi ke terendah
            $formattedResult = [];
            $allSatuans = SatuanItem::orderByDesc('level')->get();

            foreach ($allSatuans as $satuan) {
                if (isset($stockByUnit[$satuan->id]) && $stockByUnit[$satuan->id] > 0) {
                    $formattedResult[] = $stockByUnit[$satuan->id] . ' ' . $satuan->name;
                }
            }

            return implode(', ', $formattedResult);
        } catch (\Exception $e) {
            // Jika terjadi error, kembalikan amount dalam satuan asal
            return $amount . ' ' . ($barangKI->satuan->name ?? 'Unknown');
        }
    }

    public function getBarangSimple($tokoId)
    {
        // Ambil data barang toko berdasarkan toko_id
        $barangToko = BarangToko::where('toko_id', $tokoId)
            ->with(['barangKI' => function ($query) {
                $query->with('barang');
                $query->with('satuan');
            }])
            ->get()
            ->sortByDesc(function ($item) {
                return [$item->quantity, $item->sold];
            })
            ->groupBy(function ($item) {
                return $item->barangki->barang->name . '-' . Carbon::parse($item->barangki->expired_time)->format('Y-m-d H:i:s');
            })
            ->map(function ($groupedItems) {
                // Ambiltema item pertama sebagai referensi
                $firstItem = $groupedItems->first();
                $barangKI = $firstItem->barangKI;
                $barang = $barangKI->barang;

                // Format expired_time
                $expiredTime = Carbon::parse($barangKI->expired_time);
                $expiredTimeFormatted = $expiredTime->diffForHumans([
                    'parts' => 2,
                    'join' => true,
                    'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
                ]);

                // Hitung total baseStock dari semua item dalam group
                $totalBaseStock = $groupedItems->sum('quantity');

                // Get available satuans for this barang
                $conversionChain = $this->getAvailableSatuans($barang->id, $barangKI->satuan_id);

                // Convert stock to all available units
                $stockByUnit = $this->convertStockToAllUnits($barang->id, $barangKI->satuan_id, $totalBaseStock);

                // **NEW: Calculate converted quantities for each unit**
                $stockByUnitWithConversion = $this->calculateConvertedQuantities(
                    $barang->id,
                    $stockByUnit,
                    $conversionChain
                );

                // Prepare satuan arrays
                $satuanData = [];
                $allSatuans = SatuanItem::orderBy('level', 'desc')->get(); // Dari level tertinggi ke terendah

                foreach ($allSatuans as $satuan) {
                    if (isset($conversionChain[$satuan->id])) {
                        // Cari data barang toko yang memiliki satuan ini
                        $barangTokoItem = $groupedItems->first(function ($item) use ($satuan) {
                            return $item->barangKI->satuan_id == $satuan->id;
                        });

                        // Jika tidak ada item dengan satuan ini, buat data default
                        if (!$barangTokoItem) {
                            $barangTokoItem = $firstItem; // fallback ke item pertama
                        }

                        // Calculate price
                        $priceSell = $barangTokoItem->price_sell;
                        if (!$priceSell) {
                            $pricePercentage = $barangTokoItem->price_percentage;
                            $priceSell = $barangTokoItem->price_buy * ($pricePercentage / 100) + $barangTokoItem->price_buy;
                        }

                        // **MODIFIED: Use converted quantity instead of direct stock**
                        $quantity = $stockByUnitWithConversion[$satuan->id] ?? 0;

                        $satuanData[$satuan->name] = [
                            'id_barcode' => $barangKI->id_barcode,
                            'quantity' => $quantity,
                            'quantity_text' => $quantity . ' ' . $satuan->name,
                            'satuan' => $satuan->name,
                            'satuan_id' => $satuan->id,
                            'price_buy' => round($barangTokoItem->price_buy),
                            'price_sell' => round($priceSell),
                        ];
                    }
                }

                return [
                    'name' => $barang->name,
                    'baseStock' => $totalBaseStock,
                    'expired_time' => $expiredTime->toISOString(),
                    'exp_formatted' => $expiredTimeFormatted,
                    'satuan_data' => $satuanData
                ];
            })
            ->values();

        return $barangToko;
    }

    /**
     * Calculate available quantity for each unit including conversion from larger units
     * 
     * @param int $barangId
     * @param array $directStock Direct stock per unit (from database)
     * @param array $conversionChain Conversion chain data
     * @return array Available quantity per unit (including converted stock)
     */
    private function calculateConvertedQuantities($barangId, $directStock, $conversionChain)
    {
        $result = [];
        
        // Get all conversions for this barang
        $conversions = SatuanConversion::where('barang_id', $barangId)->get();
        
        // For each unit in the chain
        foreach ($conversionChain as $satuanId => $unitInfo) {
            // Start with direct stock
            $availableQty = $directStock[$satuanId] ?? 0;
            
            // Add converted stock from larger units (higher level)
            foreach ($conversionChain as $largerSatuanId => $largerUnitInfo) {
                // Skip if same unit or not a larger unit
                if ($largerSatuanId === $satuanId || $largerUnitInfo['level'] <= $unitInfo['level']) {
                    continue;
                }
                
                // Find conversion factor from larger unit to current unit
                $factor = $this->getConversionFactor($conversions, $largerSatuanId, $satuanId, $conversionChain);
                
                if ($factor > 0) {
                    $largerUnitDirectStock = $directStock[$largerSatuanId] ?? 0;
                    $availableQty += ($largerUnitDirectStock * $factor);
                }
            }
            
            $result[$satuanId] = (int) $availableQty;
        }
        
        return $result;
    }

    /**
     * Get conversion factor from one unit to another (can be multi-step)
     * 
     * @param \Illuminate\Database\Eloquent\Collection $conversions All conversions for the barang
     * @param int $fromSatuanId Source unit ID
     * @param int $toSatuanId Target unit ID
     * @param array $conversionChain Conversion chain for navigation
     * @return float Conversion factor
     */
    private function getConversionFactor($conversions, $fromSatuanId, $toSatuanId, $conversionChain)
    {
        // Try direct conversion first
        $directConversion = $conversions
            ->where('from_satuan_id', $fromSatuanId)
            ->where('to_satuan_id', $toSatuanId)
            ->first();
            
        if ($directConversion) {
            return $directConversion->conversion_factor;
        }
        
        // Multi-step conversion: navigate down the chain
        $totalFactor = 1;
        $currentId = $fromSatuanId;
        
        // Maximum 10 steps to prevent infinite loop
        $maxSteps = 10;
        $steps = 0;
        
        while ($currentId != $toSatuanId && $steps < $maxSteps) {
            // Find next step down in the chain
            $nextConversion = $conversions
                ->where('from_satuan_id', $currentId)
                ->filter(function ($conv) use ($conversionChain, $currentId, $toSatuanId) {
                    // Must go to lower level and closer to target
                    if (!isset($conversionChain[$conv->to_satuan_id])) {
                        return false;
                    }
                    return $conversionChain[$conv->to_satuan_id]['level'] < $conversionChain[$currentId]['level'];
                })
                ->sortBy(function ($conv) use ($conversionChain) {
                    // Prefer direct path to target
                    return $conversionChain[$conv->to_satuan_id]['level'];
                })
                ->first();
            
            if (!$nextConversion) {
                break;
            }
            
            $totalFactor *= $nextConversion->conversion_factor;
            $currentId = $nextConversion->to_satuan_id;
            $steps++;
        }
        
        return ($currentId == $toSatuanId) ? $totalFactor : 0;
    }

    public function isBarangValid($barang, $quantity)
    {
        // Cek apakah barang ada
        if (!$barang) {
            return [
                'status' => false,
                'message' => 'Barang tidak ditemukan',
                'code' => 404,
            ];
        }

        // Cek apakah barang statusnya aktif
        if ($barang->status !== 'active') {
            return [
                'status' => false,
                'message' => 'Barang tidak aktif',
                'code' => 400,
            ];
        }

        // Cek apakah barang sudah kedaluwarsa
        if ($barang->expired_time && Carbon::parse($barang->expired_time)->isPast()) {
            return [
                'status' => false,
                'message' => 'Barang sudah kadaluwarsa',
                'code' => 400,
            ];
        }

        // Cek apakah stok barang mencukupi
        if ($barang->quantity < $quantity) {
            return [
                'status' => false,
                'message' => 'Stok barang tidak mencukupi',
                'code' => 400,
            ];
        }

        // Jika semua validasi lulus
        return [
            'status' => true,
            'message' => 'Barang valid',
            'code' => 200,
        ];
    }

    public function changeJabatan(UserModel $user, TokoModel $toko, JabatanModel $newJabatan)
    {
        try {
            DB::beginTransaction();

            // Update user's jabatan in toko
            $toko->users()->updateExistingPivot($user->id, ['jabatan_id' => $newJabatan->id]);

            // Reset and assign new permissions based on jabatan
            $user->syncRoles([]);
            $user->syncPermissions([]);

            // Assign shop role again
            $user->assignRole('shop');

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function assignUserToToko(UserModel $user, TokoModel $toko, JabatanModel $jabatan)
    {
        $user->tokos()->detach();

        $toko->users()->attach($user->id, ['jabatan_id' => $jabatan->id]);
        if ($toko->users()->where('user_id', $user->id)->exists()) {
            try {
                DB::beginTransaction();
                $user->syncRoles([]);
                $user->syncPermissions([]);
                $user->assignRole('shop');
                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollBack();
                return false;
            }
        }
        return false;
    }

    public function fireUserFromToko(UserModel $user, TokoModel $toko)
    {
        try {
            DB::beginTransaction();

            // Remove user from toko
            $toko->users()->detach($user->id);

            // Reset user's roles and permissions
            $user->syncRoles([]);
            $user->syncPermissions([]);

            // Assign guest role
            $user->assignRole('guest');

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
