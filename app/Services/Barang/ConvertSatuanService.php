<?php

namespace App\Services\Barang;

use App\Models\Barang\BarangKI;
use App\Services\Barang\BarangKIService;
use App\Models\Barang\BarangModel;
use App\Models\Barang\SatuanConversion;
use App\Models\Barang\SatuanItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use stdClass;

class ConvertSatuanService
{

    public function __construct(
        protected BarangKIService $barangKIService,
    ) {
        $this->barangKIService = $barangKIService;
    }
    /**
     * Mengonversi seluruh koleksi BarangKI ke satuan terkecil untuk kebutuhan DataTables.
     * @param \Illuminate\Support\Collection $barangKIs
     * @return array ['total' => int/float, 'satuan' => string, 'detail' => array]
     */
    public function convertBarangKeTerkecilDatatables($barangKIs)
    {
        $totalConverted = 0;
        $convertedSatuan = null;
        $detail = [];

        foreach ($barangKIs as $item) {
            $result = $this->convertToSmallestUnit($item, $item->quantity);
            if (isset($result['success']) && $result['success']) {
                // Gunakan converted_amount jika ada, fallback ke original_amount jika tidak
                $convertedAmount = isset($result['converted_amount']) && $result['converted_amount'] !== null ? $result['converted_amount'] : (isset($result['original_amount']) ? $result['original_amount'] : $item->quantity);
                $satuan = isset($result['converted_satuan']) && $result['converted_satuan'] ? $result['converted_satuan'] : ($result['original_satuan'] ?? ($item->satuan->name ?? null));

                // Jika hasil konversi 0 tapi original_amount ada, fallback ke original_amount
                if ($convertedAmount == 0 && isset($result['original_amount']) && $result['original_amount'] > 0) {
                    $convertedAmount = $result['original_amount'];
                }

                $totalConverted += $convertedAmount;
                $convertedSatuan = $satuan;
                $detail[] = [
                    'id' => $item->id,
                    'original_amount' => $item->quantity,
                    'converted_amount' => $convertedAmount,
                    'satuan' => $convertedSatuan,
                ];
            } else {
                // Jika gagal konversi, tetap tampilkan original_quantity dan satuan asli jika ada
                $satuanAsli = method_exists($item, 'satuan') && $item->satuan ? $item->satuan->name : null;
                $detail[] = [
                    'id' => $item->id,
                    'original_amount' => $item->quantity,
                    'converted_amount' => $item->quantity,
                    'satuan' => $satuanAsli,
                    'error' => $result['message'] ?? 'Unknown error'
                ];
            }
        }

        return [
            'total' => $totalConverted,
            'satuan' => $convertedSatuan,
            'detail' => $detail
        ];
    }


    /**
     * Mengonversi jumlah satuan terkecil ke satuan lebih besar (misal: Pcs -> Pack -> Karton) secara bertingkat.
     * @param BarangKI $barangData
     * @param int|float $amount
     * @return array
     */
    public function convertStock($barangki, int $amount)
    {
        try {
            $barang = BarangModel::where('id', $barangki->barang_id)->first();
            $barangId = $barang->id;
            // Ambil semua satuan untuk barang terkait, urutkan dari level terendah ke tertinggi
            $barangSatuanIds = BarangKI::where('barang_id', $barangId)->withTrashed()->pluck('satuan_id');
            $allSatuans = SatuanItem::whereIn('id', $barangSatuanIds)->orderBy('level', 'asc')->get();
            if ($allSatuans->count() <= 1) {
                $satuan = $allSatuans->first();
                return [
                    'raw' => [
                        $satuan->id => $amount
                    ],
                    'formatted' => [$amount . ' ' . $satuan->name],
                    'satuans' => [
                        $satuan->id => [
                            'id' => $satuan->id,
                            'name' => $satuan->name,
                            'level' => $satuan->level
                        ]
                    ]
                ];
            }
            // Satuan terkecil = level paling rendah
            $smallestSatuan = $allSatuans->first();
            $baseSatuanId = $smallestSatuan->id;
            $baseSatuanName = $smallestSatuan->name;
            // Buat mapping satuan id ke object satuan
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
                'name' => $baseSatuanName,
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
                    // Tidak bisa menemukan konversi lebih lanjut
                    break;
                }
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

                $conversionRate = (int) round(1 / $factor);

                if ($conversionRate <= 0) {
                    $result[$currentSatuanId] = $remainingAmount;
                    $remainingAmount = 0;
                    break;
                }

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
            // Ambil expired_time dari barangki_id asal
            $formattedResult = [];
            $prices = [];
            foreach ($allSatuans->sortByDesc('level') as $satuan) {
                $sid = $satuan->id;
                if (isset($result[$sid]) && $result[$sid] > 0) {
                    $jumlah = $result[$sid];
                    $formattedResult[] = $jumlah . ' ' . $satuan->name;

                    $barangConvert = BarangKI::where('barang_id', $barangId)
                        ->where('satuan_id', $sid)
                        ->withTrashed()
                        ->where('expired_time', '<=', $barangki->expired_time)
                        ->orderBy('expired_time', 'desc')
                        ->first();

                    if (!$barangConvert) {
                        // Ambil expired_time lebih besar (terdekat)
                        $barangConvert = BarangKI::where('barang_id', $barangId)
                            ->where('satuan_id', $sid)
                            ->withTrashed()
                            ->where('expired_time', '>', $barangki->expired_time)
                            ->orderBy('expired_time', 'asc')
                            ->first();
                    }

                    $hargaBeli = $barangConvert->price_buy ?? 0;
                    $hargaJual = $barangConvert->price_sell ?? 0;
                    $diskonPersen = null;
                    $diskonStatus = 'No';

                    if ($barangConvert) {
                        $discountStart = $barangConvert->discount_start;
                        $discountEnd = $barangConvert->discount_end;
                        $now = Carbon::now();

                        if ($barangConvert->discount_amount || $barangConvert->discount_percentage) {
                            if ($discountStart && $discountEnd && $discountStart <= $now && $now <= $discountEnd) {
                                // Ongoing
                                $hargaNormal = $barangConvert->price_sell;
                                if ($barangConvert->discount_amount) {
                                    $hargaJual = $barangConvert->discount_amount;
                                    $diskonPersen = round(($hargaNormal - $hargaJual) / $hargaNormal * 100) . '%';
                                } elseif ($barangConvert->discount_percentage) {
                                    $hargaJual = $hargaNormal - ($hargaNormal * ($barangConvert->discount_percentage / 100));
                                    $diskonPersen = round($barangConvert->discount_percentage) . '%';
                                }
                                $diskonStatus = 'Ongoing';
                            } elseif ($discountStart && $discountStart > $now) {
                                // Coming
                                $hargaNormal = $barangConvert->price_sell;
                                if ($barangConvert->discount_amount) {
                                    $hargaDiskon = $barangConvert->discount_amount;
                                    $diskonPersen = round(($hargaNormal - $hargaDiskon) / $hargaNormal * 100) . '%';
                                } elseif ($barangConvert->discount_percentage) {
                                    $diskonPersen = round($barangConvert->discount_percentage) . '%';
                                }
                                $diskonStatus = 'Coming';
                            }
                        }
                    }

                    $prices[$sid] = [
                        'harga_beli' => $hargaBeli,
                        'harga_jual' => $hargaJual,
                        'total_beli' => $hargaBeli * $jumlah,
                        'total_jual' => $hargaJual * $jumlah,
                        'diskon_status' => $diskonStatus,
                        'diskon_persen' => $diskonPersen
                    ];
                }
            }

            // Kembalikan kedua format hasil dalam array asosiatif
            return [
                'raw' => $result,           // Format asli: ['satuan_id' => jumlah, ...]
                'formatted' => $formattedResult, // Format string: ['5 pcs', '2 box', ...]
                'satuans' => $satuanMap,      // Daftar satuan yang tersedia
                'prices' => $prices           // Harga per satuan hasil konversi
            ];
        } catch (\Exception $e) {
            $satuanName = $barang->satuan->name ?? 'Unknown';
            return [
                'raw' => [
                    $barang->satuan_id => $amount
                ],
                'formatted' => [$amount . ' ' . $satuanName],
                'satuans' => [
                    $barang->satuan_id => [
                        'id' => $barang->satuan_id,
                        'name' => $satuanName,
                        'level' => $barang->satuan->level ?? 0
                    ]
                ]
            ];
        }
    }

    /**
     * Mendapatkan detail barang berdasarkan barangki_id dan memeriksa apakah satuannya bisa dikonversi.
     *
     * @param int $barangki_id
     * @return array
     */


    public function convertToSmallestUnit($barangData, $amount, $expiredTime = null)
    {

        try {
            $barangID = $barangData->id;
            $barangEarly = BarangKI::withTrashed()->find($barangID);

            if (!$barangEarly) {
                return [
                    'success' => false,
                    'message' => 'Barang tidak ditemukan.',
                ];
            }

            $fromSatuan = SatuanItem::find($barangEarly->satuan_id);

            if (!$fromSatuan) {
                return [
                    'success' => false,
                    'message' => 'Satuan tidak ditemukan.',
                ];
            }
            // Ambil semua satuan dan urutkan berdasarkan level (terendah ke tertinggi)
            $barangSatuanIds = BarangKI::withTrashed()
                ->where('barang_id', $barangEarly->barang_id)
                ->when($expiredTime, function ($query) use ($expiredTime) {
                    return $query->where('expired_time', $expiredTime);
                })
                ->pluck('satuan_id');
            $allSatuans = SatuanItem::where('type', $fromSatuan->type)
                ->whereIn('id', $barangSatuanIds)
                ->orderBy('level', 'asc')
                ->get();
            $smallestSatuan = $allSatuans->first();
            if (!$smallestSatuan) {
                return [
                    'success' => false,
                    'message' => 'Tidak dapat menemukan satuan terkecil.',
                ];
            }

            // Jika barang sudah menggunakan satuan terkecil, tidak perlu konversi
            if ($fromSatuan->id == $smallestSatuan->id) {
                return [
                    'success' => true,
                    'original_amount' => $amount,
                    'original_satuan' => $fromSatuan->name,
                    'original_satuan_id' => $fromSatuan->id,
                    'converted_amount' => $amount, // Tidak perlu konversi
                    'converted_satuan' => $smallestSatuan->name,
                    'converted_satuan_id' => $smallestSatuan->id,
                    'barangki_id' => $barangEarly->id, // Gunakan ID barang saat ini
                    'conversion_steps' => [] // Tidak ada langkah konversi
                ];
            }

            // Cari BarangKI dengan satuan terkecil
            $smallestBarangKI = BarangKI::withTrashed()->where('satuan_id', $smallestSatuan->id)
                ->where('barang_id', $barangEarly->barang_id)
                ->when($expiredTime, function ($query) use ($expiredTime) {
                    return $query->where('expired_time', $expiredTime);
                })
                ->first();

            if (!$smallestBarangKI) {
                // Cari satuan dengan level yang lebih tinggi (terdekat)
                $nextHigherSatuan = $allSatuans->where('level', '>', $smallestSatuan->level)
                    ->sortBy('level')
                    ->first();

                if (!$nextHigherSatuan) {
                    return [
                        'success' => false,
                        'message' => 'Tidak dapat menemukan satuan yang sesuai untuk konversi.',
                    ];
                }

                // Cari BarangKI dengan satuan yang lebih tinggi
                $higherBarangKI = BarangKI::withTrashed()
                    ->where('satuan_id', $nextHigherSatuan->id)
                    ->where('barang_id', $barangEarly->barang_id)
                    ->first();

                if (!$higherBarangKI) {
                    return [
                        'success' => false,
                        'message' => 'Tidak dapat menemukan barang dengan satuan yang lebih tinggi.',
                    ];
                }

                // Update smallestBarangKI dengan yang lebih tinggi
                $smallestBarangKI = $higherBarangKI;
                $smallestSatuan = $nextHigherSatuan;
            }

            // Buat mapping satuan id ke object satuan untuk akses mudah
            $satuanMap = [];
            foreach ($allSatuans as $satuan) {
                $satuanMap[$satuan->id] = $satuan;
            }

            // Ambil semua konversi untuk barang ini
            $conversions = SatuanConversion::where('barang_id', $barangEarly->barang_id)->get();

            // Buat struktur konversi dari satuan asal sampai satuan terkecil
            $conversionPath = [];
            $currentSatuanId = $fromSatuan->id;

            // Mencari jalur konversi dari satuan asal ke satuan terkecil
            while ($currentSatuanId != $smallestSatuan->id) {
                $found = false;
                $currentLevel = $satuanMap[$currentSatuanId]->level;

                // Cari satuan dengan level lebih rendah yang terdekat
                $nextLowerLevel = $allSatuans
                    ->where('level', '<', $currentLevel)
                    ->sortByDesc('level')
                    ->first();

                if (!$nextLowerLevel) {
                    break;
                }

                // Cari konversi dari satuan saat ini ke satuan level lebih rendah
                $conversion = $conversions
                    ->where('from_satuan_id', $currentSatuanId)
                    ->where('to_satuan_id', $nextLowerLevel->id)
                    ->first();

                if ($conversion) {
                    $conversionPath[] = [
                        'from_satuan_id' => $currentSatuanId,
                        'from_satuan_name' => $satuanMap[$currentSatuanId]->name,
                        'to_satuan_id' => $nextLowerLevel->id,
                        'to_satuan_name' => $nextLowerLevel->name,
                        'conversion_factor' => $conversion->conversion_factor
                    ];

                    $currentSatuanId = $nextLowerLevel->id;
                    $found = true;
                }

                if (!$found) {
                    return [
                        'success' => false,
                        'message' => 'Tidak dapat menemukan jalur konversi ke satuan terkecil.'
                    ];
                }
            }

            // Hitung total konversi
            $convertedAmount = $amount;
            foreach ($conversionPath as $step) {
                $convertedAmount *= $step['conversion_factor'];
            }

            // Buat detail konversi untuk respons
            $conversionDetails = [];
            $stepAmount = $amount;

            foreach ($conversionPath as $step) {
                $convertAmount = $stepAmount * $step['conversion_factor'];
                $conversionDetails[] = [
                    'from_satuan' => $step['from_satuan_name'],
                    'to_satuan' => $step['to_satuan_name'],
                    'conversion_factor' => $step['conversion_factor'],
                    'from_amount' => $stepAmount,
                    'to_amount' => $convertAmount
                ];
                $stepAmount = $convertAmount;
            }

            return [
                'success' => true,
                'original_amount' => $amount,
                'original_satuan' => $fromSatuan->name,
                'original_satuan_id' => $fromSatuan->id,
                'converted_amount' => $convertedAmount,
                'converted_satuan' => $smallestSatuan->name,
                'converted_satuan_id' => $smallestSatuan->id,
                'barangki_id' => $smallestBarangKI->id,
                'conversion_steps' => $conversionDetails
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error saat konversi: ' . $e->getMessage()
            ];
        }
    }

    public function getBarangDetailsAndConversionStatus(int $barangki_id, int $amount)
    {
        // Ambil data BarangKI berdasarkan barangki_id
        $barangki = BarangKI::withTrashed()->find($barangki_id);

        if (!$barangki) {
            return ['error' => 'BarangKI dengan ID ' . $barangki_id . ' tidak ditemukan.'];
        }

        // Ambil satuan dari BarangKI
        $satuanItem = SatuanItem::find($barangki->satuan_id);

        if (!$satuanItem) {
            return ['error' => 'Satuan dengan ID ' . $barangki->satuan_id . ' tidak ditemukan.'];
        }

        // Ambil informasi barang yang relevan
        $barangDetails = [
            'expired_time' => $barangki->expired_time,
            'barang_id' => $barangki->barang_id,
            'satuan_id' => $barangki->satuan_id,
            'satuan_type' => $satuanItem->type,
            'satuan_level' => $satuanItem->level,
            'amount' => $amount . ' ' . $barangki->satuan->name,
        ];

        $convertedBarangki = $this->getConvertedBarangKI($barangki_id, $barangki->expired_time, $amount);


        $barangDetails['converted_barangki'] = $convertedBarangki;

        return $barangDetails;
    }

    /**
     * Mencari barang yang cocok setelah konversi.
     *
     * @param int $barangki_id
     * @param string $expired_time
     * @param int $amount
     * @return array|null
     */
    private function getConvertedBarangKI(int $barangki_id, $expired_time, int $amount)
    {
        try {
            $barangEarly = BarangKI::withTrashed()->find($barangki_id);
            if (!$barangEarly) {
                return null;
            }

            $fromSatuan = SatuanItem::find($barangEarly->satuan_id);
            if (!$fromSatuan) {
                return null;
            }

            // Ambil semua satuan dengan tipe yang sama, urutkan berdasarkan level (terendah ke tertinggi)
            $matchingSatuans = SatuanItem::where('type', $fromSatuan->type)
                ->orderBy('level', 'asc')
                ->get();

            // Buat koleksi semua BarangKI yang cocok dengan expired time dan barang_id
            $allMatchingBarangKIs = BarangKI::where('barang_id', $barangEarly->barang_id)
                ->where('expired_time', $expired_time)
                ->whereIn('satuan_id', $matchingSatuans->pluck('id'))
                ->withTrashed()
                ->with('satuan')
                ->get()
                ->keyBy('satuan_id');

            // Ambil semua konversi untuk barang ini
            $conversions = SatuanConversion::where('barang_id', $barangEarly->barang_id)->get();

            $convertedBarangki = [];

            // Tambahkan data untuk setiap BarangKI yang ditemukan
            foreach ($allMatchingBarangKIs as $barangKI) {
                $convertedBarangki[] = [
                    'barangki' => [
                        'id' => $barangKI->id,
                        'barcode' => $barangKI->id_barcode,
                        'name' => $barangKI->barang->name,
                        'expired' => $barangKI->expired_time,
                        'satuan' => $barangKI->satuan->name,
                        'satuan_level' => $barangKI->satuan->level,
                        'price_sell' => $barangKI->price_sell,
                        'price_up' => $barangKI->price_up,
                    ],
                ];
            }

            // Temukan BarangKI dengan level terendah
            $lowestLevelBarangKI = $allMatchingBarangKIs
                ->sortBy(function ($item) {
                    return $item->satuan->level;
                })
                ->first();

            if ($lowestLevelBarangKI) {
                $lowestLevel = $lowestLevelBarangKI->satuan->level;

                // Hitung konversi total dari satuan asal ke satuan terkecil
                $convertedAmount = $amount;
                $currentSatuan = $fromSatuan;

                while ($currentSatuan->level > $lowestLevel) {
                    // Cari satuan level berikutnya yang lebih rendah
                    $nextLowerSatuan = $matchingSatuans
                        ->where('level', '<', $currentSatuan->level)
                        ->sortByDesc('level')
                        ->first();

                    if (!$nextLowerSatuan) {
                        break;
                    }

                    // Ambil faktor konversi
                    $conversionFactor = $conversions
                        ->where('from_satuan_id', $currentSatuan->id)
                        ->where('to_satuan_id', $nextLowerSatuan->id)
                        ->first();

                    if ($conversionFactor) {
                        $convertedAmount *= $conversionFactor->conversion_factor;
                        $currentSatuan = $nextLowerSatuan;
                    } else {
                        break;
                    }
                }

                // Update jumlah konversi untuk BarangKI dengan level terendah
                foreach ($convertedBarangki as &$item) {
                    if ($item['barangki']['id'] == $lowestLevelBarangKI->id) {
                        $item['converted_amount'] = $convertedAmount;
                        break;
                    }
                }

                // Hitung dan tambahkan factor rate untuk semua barangki
                foreach ($convertedBarangki as &$item) {
                    $itemSatuanId = $allMatchingBarangKIs->where('id', $item['barangki']['id'])->first()->satuan_id;
                    $itemSatuan = $matchingSatuans->where('id', $itemSatuanId)->first();

                    // Hitung rate konversi dari satuan asal
                    if ($fromSatuan->level > $itemSatuan->level) {
                        // Konversi dari satuan lebih tinggi ke lebih rendah
                        $directConversion = $conversions
                            ->where('from_satuan_id', $fromSatuan->id)
                            ->where('to_satuan_id', $itemSatuanId)
                            ->first();

                        if ($directConversion) {
                            $item['barangki']['rate'] = $directConversion->conversion_factor;
                        } else {
                            // Jika tidak ada konversi langsung, hitung dari rantai konversi
                            $totalFactor = 1;
                            $tempSatuanId = $fromSatuan->id;

                            while ($tempSatuanId != $itemSatuanId) {
                                $nextConversion = $conversions
                                    ->where('from_satuan_id', $tempSatuanId)
                                    ->filter(function ($conv) use ($matchingSatuans, $tempSatuanId) {
                                        $fromLevel = $matchingSatuans->where('id', $tempSatuanId)->first()->level;
                                        $toLevel = $matchingSatuans->where('id', $conv->to_satuan_id)->first()->level;
                                        return $toLevel < $fromLevel;
                                    })
                                    ->sortBy(function ($conv) use ($matchingSatuans) {
                                        return $matchingSatuans->where('id', $conv->to_satuan_id)->first()->level;
                                    })
                                    ->first();

                                if ($nextConversion) {
                                    $totalFactor *= $nextConversion->conversion_factor;
                                    $tempSatuanId = $nextConversion->to_satuan_id;
                                } else {
                                    break;
                                }
                            }

                            $item['barangki']['rate'] = $totalFactor;
                        }
                    } else if ($fromSatuan->level < $itemSatuan->level) {
                        // Konversi dari satuan lebih rendah ke lebih tinggi
                        $directConversion = $conversions
                            ->where('from_satuan_id', $fromSatuan->id)
                            ->where('to_satuan_id', $itemSatuanId)
                            ->first();

                        if ($directConversion) {
                            $item['barangki']['rate'] = $directConversion->conversion_factor;
                        } else {
                            // Jika tidak ada konversi langsung, hitung dari rantai konversi
                            $totalFactor = 1;
                            $tempSatuanId = $fromSatuan->id;

                            while ($tempSatuanId != $itemSatuanId) {
                                $nextConversion = $conversions
                                    ->where('from_satuan_id', $tempSatuanId)
                                    ->filter(function ($conv) use ($matchingSatuans, $tempSatuanId) {
                                        $fromLevel = $matchingSatuans->where('id', $tempSatuanId)->first()->level;
                                        $toLevel = $matchingSatuans->where('id', $conv->to_satuan_id)->first()->level;
                                        return $toLevel > $fromLevel;
                                    })
                                    ->sortBy(function ($conv) use ($matchingSatuans) {
                                        return $matchingSatuans->where('id', $conv->to_satuan_id)->first()->level;
                                    })
                                    ->first();

                                if ($nextConversion) {
                                    $totalFactor *= $nextConversion->conversion_factor;
                                    $tempSatuanId = $nextConversion->to_satuan_id;
                                } else {
                                    break;
                                }
                            }

                            $item['barangki']['rate'] = $totalFactor;
                        }
                    } else {
                        // Satuan sama
                        $item['barangki']['rate'] = 1;
                    }
                }
            }

            return $convertedBarangki;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function findLargestUnit($barangData)
    {
        if ($barangData instanceof BarangKI) {
            $barangKIId = $barangData->id;
        } elseif ($barangData instanceof stdClass) {
            $barangKIId = $barangData->barangki_id;
        } else {
            $barangKIId = $barangData;
        }


        $barangEarly = BarangKI::where('id', $barangKIId)->withTrashed()->first();

        $barangSatuanIds = BarangKI::where('barang_id', $barangEarly->barang_id)->withTrashed()->pluck('satuan_id');

        $allSatuans = SatuanItem::where('type', $barangEarly->satuan->type)
            ->whereIn('id', $barangSatuanIds)
            ->orderBy('level', 'desc')
            ->where('selling', true)
            ->get();

        $largestSatuan = $allSatuans->first();

        $largestBarangKI = BarangKI::where('satuan_id', $largestSatuan->id)
            ->where('barang_id', $barangEarly->barang_id)
            ->withTrashed()
            ->first();



        return [
            'success' => true,
            'data' => $largestBarangKI,
        ];
    }

    public function convertToLargestUnit($barangData, $amount)
    {
        try {
            $barangID = $barangData->id;
            $barangEarly = BarangKI::where('id', $barangID)->withTrashed()->first();

            if (!$barangEarly) {
                return [
                    'success' => false,
                    'message' => 'Barang tidak ditemukan.',
                ];
            }

            $fromSatuan = SatuanItem::find($barangEarly->satuan_id);

            if (!$fromSatuan) {
                return [
                    'success' => false,
                    'message' => 'Satuan tidak ditemukan.',
                ];
            }

            // Ambil semua satuan dan urutkan berdasarkan level (tertinggi ke terendah)
            $barangSatuanIds = BarangKI::where('barang_id', $barangEarly->barang_id)->withTrashed()->pluck('satuan_id');
            $allSatuans = SatuanItem::where('type', $fromSatuan->type)
                ->whereIn('id', $barangSatuanIds)
                ->orderBy('level', 'desc')
                ->get();

            // Temukan satuan terbesar (level paling tinggi)
            $largestSatuan = $allSatuans->first();

            if (!$largestSatuan) {
                return [
                    'success' => false,
                    'message' => 'Tidak dapat menemukan satuan terbesar.',
                ];
            }

            // Jika barang sudah menggunakan satuan terbesar, tidak perlu konversi
            if ($fromSatuan->id == $largestSatuan->id) {
                return [
                    'success' => true,
                    'original_amount' => $amount,
                    'original_satuan' => $fromSatuan->name,
                    'original_satuan_id' => $fromSatuan->id,
                    'converted_amount' => $amount, // Tidak perlu konversi
                    'converted_satuan' => $largestSatuan->name,
                    'converted_satuan_id' => $largestSatuan->id,
                    'barangki_id' => $barangEarly->id, // Gunakan ID barang saat ini
                    'conversion_steps' => [] // Tidak ada langkah konversi
                ];
            }

            // Cari BarangKI dengan satuan terbesar
            $largestBarangKI = BarangKI::where('satuan_id', $largestSatuan->id)
                ->where('barang_id', $barangEarly->barang_id)
                ->where('expired_time', $barangData->expired_time)
                ->first();

            if (!$largestBarangKI) {
                return [
                    'success' => false,
                    'message' => 'Tidak dapat menemukan barang dengan satuan terbesar.',
                ];
            }

            // Buat mapping satuan id ke object satuan untuk akses mudah
            $satuanMap = [];
            foreach ($allSatuans as $satuan) {
                $satuanMap[$satuan->id] = $satuan;
            }

            // Ambil semua konversi untuk barang ini
            $conversions = SatuanConversion::where('barang_id', $barangEarly->barang_id)->get();

            // Buat struktur konversi dari satuan asal sampai satuan terbesar
            $conversionPath = [];
            $currentSatuanId = $fromSatuan->id;

            // Mencari jalur konversi dari satuan asal ke satuan terbesar
            while ($currentSatuanId != $largestSatuan->id) {
                $found = false;
                $currentLevel = $satuanMap[$currentSatuanId]->level;

                // Cari satuan dengan level lebih tinggi yang terdekat
                $nextHigherLevel = $allSatuans
                    ->where('level', '>', $currentLevel)
                    ->sortBy('level')
                    ->first();

                if (!$nextHigherLevel) {
                    break;
                }

                // Cari konversi dari satuan saat ini ke satuan level lebih tinggi
                $conversion = $conversions
                    ->where('from_satuan_id', $currentSatuanId)
                    ->where('to_satuan_id', $nextHigherLevel->id)
                    ->first();

                // Jika tidak ditemukan konversi langsung, coba cari konversi terbalik
                if (!$conversion) {
                    $reverseConversion = $conversions
                        ->where('from_satuan_id', $nextHigherLevel->id)
                        ->where('to_satuan_id', $currentSatuanId)
                        ->first();

                    if ($reverseConversion) {
                        // Hitung faktor konversi terbalik
                        $conversionFactor = 1 / $reverseConversion->conversion_factor;

                        $conversionPath[] = [
                            'from_satuan_id' => $currentSatuanId,
                            'from_satuan_name' => $satuanMap[$currentSatuanId]->name,
                            'to_satuan_id' => $nextHigherLevel->id,
                            'to_satuan_name' => $nextHigherLevel->name,
                            'conversion_factor' => $conversionFactor
                        ];

                        $currentSatuanId = $nextHigherLevel->id;
                        $found = true;
                    }
                } else {
                    $conversionPath[] = [
                        'from_satuan_id' => $currentSatuanId,
                        'from_satuan_name' => $satuanMap[$currentSatuanId]->name,
                        'to_satuan_id' => $nextHigherLevel->id,
                        'to_satuan_name' => $nextHigherLevel->name,
                        'conversion_factor' => $conversion->conversion_factor
                    ];

                    $currentSatuanId = $nextHigherLevel->id;
                    $found = true;
                }

                if (!$found) {
                    return [
                        'success' => false,
                        'message' => 'Tidak dapat menemukan jalur konversi ke satuan terbesar.'
                    ];
                }
            }

            // Hitung total konversi
            $convertedAmount = $amount;
            foreach ($conversionPath as $step) {
                $convertedAmount /= $step['conversion_factor'];
            }

            // Buat detail konversi untuk respons
            $conversionDetails = [];
            $stepAmount = $amount;

            foreach ($conversionPath as $step) {
                $convertAmount = $stepAmount / $step['conversion_factor'];
                $conversionDetails[] = [
                    'from_satuan' => $step['from_satuan_name'],
                    'to_satuan' => $step['to_satuan_name'],
                    'conversion_factor' => $step['conversion_factor'],
                    'from_amount' => $stepAmount,
                    'to_amount' => $convertAmount
                ];
                $stepAmount = $convertAmount;
            }

            return [
                'success' => true,
                'original_amount' => $amount,
                'original_satuan' => $fromSatuan->name,
                'original_satuan_id' => $fromSatuan->id,
                'converted_amount' => $convertedAmount,
                'converted_satuan' => $largestSatuan->name,
                'converted_satuan_id' => $largestSatuan->id,
                'barangki_id' => $largestBarangKI->id,
                'conversion_steps' => $conversionDetails
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error saat konversi: ' . $e->getMessage()
            ];
        }
    }
}
