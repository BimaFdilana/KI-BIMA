<?php

namespace App\Services\Toko;

use App\Models\Barang\BarangKI;
use App\Models\Barang\BarangModel;
use App\Models\Barang\SatuanItem;
use App\Models\Toko\BarangToko;
use App\Models\Toko\TokoKeranjang;
use App\Models\Toko\TokoPayment;
use App\Models\Toko\TokoPesanan;
use App\Services\Barang\BarangIOService;
use App\Services\Barang\BarangKIService;
use App\Services\Barang\ConvertSatuanService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KeranjangTokoService
{
    protected $convertSatuanService;
    protected $barangIOService;
    protected $barangKIService;

    public function __construct(ConvertSatuanService $convertSatuanService, BarangIOService $barangIOService, BarangKIService $barangKIService)
    {
        $this->convertSatuanService = $convertSatuanService;
        $this->barangIOService = $barangIOService;
        $this->barangKIService = $barangKIService;
    }

    public function notifLabel(string $status)
    {
        switch ($status) {
            case 'pending':
                $description = 'Pembayaran Anda sedang dalam proses. Mohon tunggu konfirmasi selanjutnya.';
                break;
            case 'paid':
                $description = 'Pembayaran Anda telah berhasil diterima.';
                break;
            case 'failed':
                $description = 'Pembayaran Anda gagal diproses. Silakan coba lagi atau hubungi dukungan kami.';
                break;
            case 'unknown':
                $description = 'Status pembayaran Anda tidak dapat dipastikan. Silakan hubungi dukungan untuk bantuan lebih lanjut.';
                break;
            case 'success':
                $description = 'Pesanan telah berhasil, Terimakasih!.';
                break;
            case 'delivery':
                $description = 'Pesanan Anda telah dikirim. Silakan tunggu hingga barang sampai di tujuan.';
                break;
            default:
                $description = 'Status pembayaran Anda telah diperbarui.';
        }
        return $description;
    }

    public function checkStatusPayment(string $transactionId, string $status)
    {
        // Cari transaksi berdasarkan ID
        $transaction = TokoPayment::where('transaction_id', $transactionId)->first();

        if (!$transaction) {
            // Mengembalikan error dalam bentuk array, bukan JsonResponse
            return ['error' => 'Transaction not found'];
        }

        // Daftar status yang valid dalam urutan
        $validStatuses = [
            'paid',
            'pending',
            'failed',
            'unknown',
            'success',
            'delivery',
        ];

        // Cek apakah status baru valid
        if (!in_array($status, $validStatuses)) {
            return ['error' => 'Invalid status'];
        }

        // Aturan: Status gagal, sukses, atau unknown tidak bisa diubah
        if (in_array($transaction->status, ['failed', 'success', 'unknown'])) {
            return ['error' => 'Cannot change status after failure or success'];
        }

        // Aturan: Tidak bisa pindah ke status yang sama
        if ($transaction->status === $status) {
            return ['error' => 'Cannot move to the same status'];
        }

        // Semua aturan valid, kembalikan null atau status update yang diizinkan
        return ['error' => null]; // Status bisa diperbarui
    }


    public function updateBarangToko(int $paymentId, string $status, int $tokoId): array
    {
        // Validasi status
        $validStatuses = ['success'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception('Status tidak valid.');
        }

        // Ambil semua pesanan berdasarkan payment_id
        $pesananItems = TokoPesanan::where('payment_id', $paymentId)->get();

        // Pastikan ada pesanan yang ditemukan
        if ($pesananItems->isEmpty()) {
            throw new Exception('Tidak ada pesanan yang ditemukan untuk payment_id ini.');
        }
        $results = [];
        // Proses setiap pesanan
        foreach ($pesananItems as $pesanan) {
            // Perbarui status pesanan
            $pesanan->status = $status;
            $pesanan->save();
            $barangki = BarangKI::find($pesanan->barangki_id);

            if (!$barangki) {
                throw new Exception("Barang dengan ID {$pesanan->barangki_id} tidak ditemukan.");
            }

            $quantity = $pesanan->quantity;

            // Gunakan harga dari pesanan yang sudah disimpan, karena harga ini sudah
            // memperhitungkan diskon saat dimasukkan ke pesanan
            $priceFromOrder = $pesanan->price;

            // Hapus barang dari toko dengan harga pesanan (sudah diperhitungkan diskon)
            $this->barangIOService->removeBarang($tokoId, $barangki->id, $quantity, $priceFromOrder);

            $details = $this->convertSatuanService->getBarangDetailsAndConversionStatus($barangki->id, $quantity);

            foreach ($details['converted_barangki'] as $converted) {
                $convertedAmount = isset($converted['converted_amount']) ? $converted['converted_amount'] : 0;

                // Untuk setiap barang yang dikonversi, periksa diskon spesifik
                $convertedBarangki = BarangKI::find($converted['barangki']['id']);

                if ($convertedBarangki) {
                    // Dapatkan harga asli (non-diskon) untuk setiap barang yang dikonversi
                    $barangDiskon = $this->barangKIService->cekDiskonBarang($convertedBarangki->id_barcode);

                    $originalPrice = $barangDiskon['data']['original_price']; // Harga asli tanpa diskon

                    $existingBarangToko = BarangToko::where('toko_id', $tokoId)
                        ->where('barangki_id', $converted['barangki']['id'])
                        ->first();

                    if ($existingBarangToko) {
                        $existingBarangToko->quantity += $convertedAmount;
                        $existingBarangToko->save();
                    } else {
                        // Jika barang belum ada, buat entri baru dengan harga asli (non-diskon)
                        $priceSell = $converted['barangki']['price_sell'] ?? $originalPrice;
                        $pricePercentage = $originalPrice > 0 ? (($priceSell - $originalPrice) / $originalPrice) * 100 : 0;

                        BarangToko::create([
                            'toko_id' => $tokoId,
                            'barangki_id' => $converted['barangki']['id'],
                            'quantity' => $convertedAmount,
                            'price_buy' => $originalPrice, // Gunakan harga asli, bukan harga diskon
                            'price_sell' => $priceSell,
                            'price_percentage' => round($pricePercentage, 2),
                        ]);
                    }
                }
            }
            $results[] = $details;
        }
        return $results;
    }

    public function hapusKeranjang($keranjang)
    {
        foreach ($keranjang as $item) {
            $item->delete();  // Menghapus item dari keranjang
        }
    }

    public function updateTokoPesanan(int $paymentId, string $status): void
    {
        // Validasi status
        $validStatuses = ['paid'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception('Status tidak valid.');
        }

        // Cari semua pesanan berdasarkan payment_id
        $pesananItems = TokoPesanan::where('payment_id', $paymentId)
            ->where('status', 'pending')
            ->get();

        if ($pesananItems->isEmpty()) {
            throw new Exception('Tidak ada pesanan yang ditemukan untuk payment_id ini.');
        }

        // Perbarui status dan kurangi stok barang jika status 'paid'
        foreach ($pesananItems as $pesanan) {
            // Perbarui status pesanan
            $pesanan->status = $status;
            $pesanan->save();

            // Jika status 'paid', kurangi stok barang sesuai dengan quantity pesanan
            if ($status === 'paid') {
                $barang = BarangKI::find($pesanan->barangki_id);

                if (!$barang) {
                    throw new Exception("Barang dengan ID {$pesanan->barangki_id} tidak ditemukan.");
                }

                // Validasi apakah stok barang dan quantity valid
                if (!is_numeric($barang->quantity) || !is_numeric($pesanan->quantity)) {
                    throw new Exception("Stok atau jumlah pesanan tidak valid untuk barang dengan ID {$barang->id}.");
                }
                // Kurangi stok barang sesuai dengan quantity pesanan
                $barang->quantity -= (int) $pesanan->quantity;
                $barang->sold_quantity += (int) $pesanan->quantity;
                $barang->save();
            }
        }
    }

    public function moveItemsToPesanan($keranjang, $paymentId)
    {
        $barangKIService = new BarangKIService();
        // Memulai transaksi untuk memastikan integritas data
        DB::beginTransaction();

        try {
            foreach ($keranjang as $item) {
                // Mendapatkan barang dari item keranjang
                $barang = $item->barangki;

                // Periksa diskon untuk barang ini
                $diskonInfo = $barangKIService->applyDiscountsToBarang($barang);

                // Gunakan harga diskon jika tersedia, atau harga jual normal jika tidak
                $hargaJual = $diskonInfo->final_price;

                // Memasukkan item ke dalam tabel toko_pesanan
                TokoPesanan::create([
                    'payment_id' => $paymentId,
                    'barangki_id' => $barang->id,
                    'price' => $hargaJual, // Harga jual (sudah termasuk diskon jika ada)
                    'quantity' => $item->quantity,
                    'status' => 'Pending',  // Status bisa disesuaikan (contoh: Pending, Selesai)
                    'total' => $hargaJual * $item->quantity,
                ]);
            }

            // Commit transaksi jika semua berhasil
            DB::commit();
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            throw $e;  // Rethrow exception agar bisa ditangani di controller
        }
    }

    public function generateTransactionId()
    {
        $lastTransaction = TokoPayment::select('transaction_id')
            ->orderBy('transaction_id', 'desc')
            ->first();

        // Jika tidak ada transaksi sebelumnya, set ID berikutnya ke 1
        if (!$lastTransaction) {
            $nextId = 1;
        } else {
            // Mengambil angka dari transaction_id dan menambah 1
            $lastId = intval(substr($lastTransaction->transaction_id, 3)); // Menghilangkan 'KI-'
            $nextId = $lastId + 1;
        }

        // Format ID dengan prefix KI- dan padding 0 untuk 9 digit
        return 'TS-' . str_pad($nextId, 9, '0', STR_PAD_LEFT);
    }

    public function getKeranjangBarangAktif($toko)
    {
        $keranjang = TokoKeranjang::where('toko_id', $toko->id)
            ->whereHas('barangki', function ($query) {
                $query->where('status', 'active');
            })
            ->with('barangki')
            ->get();

        return $keranjang;
    }

    public function updateKeranjangToko($toko)
    {
        $barangService = new BarangKIService();
        $status = false;
        $keranjang = $toko->keranjang()->with('barangki')->get();

        $barangDiproses = [];
        $barangDihapus = 0;

        foreach ($keranjang as $item) {
            $barang = $item->barangki;

            if ($barang) {
                \Log::info('Processing item', [
                    'id' => $barang->id,
                    'barang_id' => $barang->barang_id,
                    'satuan_id' => $barang->satuan_id,
                ]);
                // Identifikasi barang berdasarkan barang_id dan satuan_id
                $key = "{$barang->barang_id}-{$barang->satuan_id}";

                if (!isset($barangDiproses[$key])) {
                    // Cari barang yang valid
                    $barangValid = $barangService->getBarangValid($barang->barang_id, $barang->satuan_id);

                    if ($barangValid) {
                        // Simpan barang valid sebagai barang yang akan dipertahankan
                        $barangDiproses[$key] = $barangValid;

                        // Jika barang di keranjang saat ini bukan barang valid, perbarui ke barang valid
                        if ($item->barangki_id !== $barangValid->id) {
                            $item->barangki_id = $barangValid->id;
                            $item->save();
                        }
                    }
                } else {
                    // Barang valid sudah ada, tambahkan jumlah (amount) ke barang valid
                    $barangValid = $barangDiproses[$key];
                    $keranjangValid = $toko->keranjang()->where('barangki_id', $barangValid->id)->first();

                    if ($keranjangValid) {
                        $keranjangValid->quantity += $item->quantity;
                        $keranjangValid->save();
                    }
                    $status = true;
                    // Hapus barang duplikat dari keranjang
                    $item->delete();
                    $barangDihapus++;
                }
            }
        }

        return [
            'status' => $status,
            'message' => "{$barangDihapus} barang duplikat telah dihapus dari keranjang, jumlahnya ditambahkan ke barang yang valid.",
        ];
    }

    public function addToKeranjang($toko, $barangValid, $quantity)
    {
        // Cek apakah barang sudah ada di keranjang
        $existingItem = TokoKeranjang::where('toko_id', $toko->id)
            ->where('barangki_id', $barangValid->id)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;

            // Validasi jika jumlah yang ditambahkan melebihi stok
            if ($newQuantity > $barangValid->quantity) {
                return response()->json(['error' => 'Jumlah barang melebihi stok yang tersedia.'], 400);
            }

            // Update kuantitas jika ada
            $existingItem->quantity = $newQuantity;
            $existingItem->save();
        } else {
            // Cek apakah barang lebih dari stok
            if ($quantity > $barangValid->quantity) {
                return response()->json(['error' => 'Jumlah barang yang ingin ditambahkan melebihi stok yang tersedia'], 400);
            }

            // Jika belum ada, tambah barang baru ke keranjang
            $keranjang = new TokoKeranjang();
            $keranjang->toko_id = $toko->id;
            $keranjang->barangki_id = $barangValid->id;
            $keranjang->quantity = $quantity;
            $keranjang->save();
        }

        return response()->json([
            'success' => 'Barang berhasil ditambahkan ke keranjang.',
            'data' => [
                'barang_name' => $barangValid->barang->name,
                'toko' => $toko->name,
                'jumlah' => $quantity,
                'total' => $existingItem ? $existingItem->quantity : $quantity,
                'remaining_stock' => $barangValid->quantity
            ],
        ], 200);
    }

    public function addToKeranjangBarangID($toko, $barangID, $satuanId, $quantity)
    {
        $today = Carbon::today();
        $remainingQuantity = $quantity;
        $successItems = [];
        $failedItems = [];
        $barangInfo = null;

        DB::beginTransaction();
        try {
            // Get barang details for response formatting
            $barangInfo =  BarangModel::select('id', 'name')->find($barangID);
            $satuanInfo = SatuanItem::select('id', 'name')->find($satuanId);

            // Process until all quantity is fulfilled or we've tried all available stock
            while ($remainingQuantity > 0) {
                // Get available stock sorted by expiration date (FIFO)
                $barangKI = BarangKI::where('barang_id', $barangID)
                    ->where('satuan_id', $satuanId)
                    ->where('status', 'active')
                    ->where('quantity', '>', 0)
                    ->whereHas('barang', function ($query) use ($today) {
                        $query->whereRaw('DATE_ADD(expired_time, INTERVAL -mid_expiry_days DAY) >= ?', [$today])
                            ->where('status', 'active');
                    })
                    ->whereHas('satuan', function ($query) {
                        $query->where('selling', 'true');
                    })
                    ->orderBy('expired_time', 'asc')
                    ->first();

                // If no more stock is available, break the loop
                if (!$barangKI) {
                    break;
                }

                $availableQuantity = $barangKI->quantity;
                $quantityToAdd = min($remainingQuantity, $availableQuantity);

                // Update stock
                $barangKI->decrement('quantity', $quantityToAdd);

                // Add to cart (upsert pattern)
                $keranjang = TokoKeranjang::where([
                    'toko_id' => $toko->id,
                    'barangki_id' => $barangKI->id,
                ])->first();

                if ($keranjang) {
                    $keranjang->increment('quantity', $quantityToAdd);
                } else {
                    $keranjang = TokoKeranjang::create([
                        'toko_id' => $toko->id,
                        'barangki_id' => $barangKI->id,
                        'quantity' => $quantityToAdd
                    ]);
                }

                $successItems[] = [
                    'barangki_id' => $barangKI->id,
                    'quantity' => $quantityToAdd
                ];

                $remainingQuantity -= $quantityToAdd;
            }

            // Check if all quantity was fulfilled
            if ($remainingQuantity > 0) {
                // Add item to failed items list
                $failedItems[] = [
                    'name' => $barangInfo ? $barangInfo->name : "Item ID: $barangID",
                    'unit_name' => $satuanInfo ? $satuanInfo->name : "Unit ID: $satuanId",
                    'quantity' => $quantity,
                    'error' => "Tidak dapat memenuhi seluruh permintaan. Sisa yang tidak terpenuhi: {$remainingQuantity}"
                ];

                // If we have at least one success, commit those and report partial success
                if (count($successItems) > 0) {
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Sebagian item berhasil ditambahkan ke keranjang',
                        'data' => [
                            'success_count' => count($successItems),
                            'failed_count' => count($failedItems),
                            'success_items' => $successItems,
                            'failed_items' => $failedItems
                        ]
                    ], 207); // 207 Multi-Status
                } else {
                    // No successful additions, so roll back and return error
                    throw new \Exception("Stok tidak mencukupi. Diminta: {$quantity}, Tersedia: " . ($quantity - $remainingQuantity));
                }
            }

            // All quantity fulfilled successfully
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menambahkan ke keranjang',
                'data' => [
                    'success_count' => count($successItems),
                    'failed_count' => 0,
                    'success_items' => $successItems,
                    'failed_items' => []
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            // Prepare error response with meaningful information
            $failedItems[] = [
                'name' => $barangInfo ? $barangInfo->name : "Item ID: $barangID",
                'unit_name' => $satuanInfo ? $satuanInfo->name : "Unit ID: $satuanId",
                'quantity' => $quantity,
                'error' => 'Error menambahkan ke keranjang: ' . $e->getMessage()
            ];

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan item ke keranjang',
                'data' => [
                    'success_count' => 0,
                    'failed_count' => 1,
                    'success_items' => [],
                    'failed_items' => $failedItems
                ]
            ], 400);
        }
    }

    public function removeFromKeranjang($toko, $barangValid, $quantity)
    {
        // Cari barang di keranjang
        $existingItem = TokoKeranjang::where('toko_id', $toko->id)
            ->where('barangki_id', $barangValid->id)
            ->first();

        if (!$existingItem) {
            return response()->json(['error' => 'Barang tidak ditemukan di keranjang.'], 404);
        }

        // Kurangi kuantitas barang yang ada
        $newQuantity = $existingItem->quantity - $quantity;

        if ($newQuantity <= 0) {
            // Hapus barang jika kuantitasnya mencapai 0 atau kurang
            $existingItem->delete();
            return response()->json([
                'success' => 'Barang berhasil dihapus dari keranjang.',
                'data' => [
                    'barang_name' => $barangValid->barang->name,
                    'toko' => $toko->name,
                    'jumlah' => $quantity,
                ],
            ], 200);
        }

        // Update kuantitas jika masih ada
        $existingItem->quantity = $newQuantity;
        $existingItem->save();

        return response()->json([
            'success' => 'Barang berhasil dikurangi dari keranjang.',
            'data' => [
                'barang_name' => $barangValid->barang->name,
                'toko' => $toko->name,
                'jumlah' => $quantity,
                'total' => $newQuantity,
            ],
        ], 200);
    }


    public function changeKeranjang($toko, $barangValid, $quantity)
    {
        // Cek apakah barang sudah ada di keranjang
        $existingItem = TokoKeranjang::where('toko_id', $toko->id)
            ->where('barangki_id', $barangValid->id)
            ->first();

        if (!$existingItem) {
            return response()->json(['error' => 'Barang tidak ditemukan di keranjang.'], 404);
        }

        // Cek apakah jumlah barang yang diubah melebihi stok
        if ($quantity > $barangValid->quantity) {
            return response()->json(['error' => 'Jumlah barang melebihi stok yang tersedia.'], 400);
        }

        // Ubah kuantitas barang
        $existingItem->quantity = $quantity;
        $existingItem->save();

        return response()->json([
            'success' => 'Barang berhasil diubah kuantitasnya.',
            'data' => [
                'barang_name' => $barangValid->barang->name,
                'toko' => $toko->name,
                'jumlah' => $quantity,
            ],
        ], 200);
    }
}
