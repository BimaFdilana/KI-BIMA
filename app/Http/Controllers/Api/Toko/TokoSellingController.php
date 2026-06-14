<?php

namespace App\Http\Controllers\Api\Toko;

use App\Models\Barang\BarangKI;
use App\Models\Infaq\InfaqHistory;
use App\Models\Toko\BarangToko;
use App\Models\Toko\TokoSelling;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use App\Services\Toko\TokoService;
use App\Services\Barang\BarangKIService;
use App\Services\Barang\ConvertSatuanService;
use App\Models\Toko\KasirShift;
use Illuminate\Support\Facades\DB;

class TokoSellingController extends BaseController
{
    protected $tokoService;
    protected $barangKIService;
    protected $convertSatuanService;

    public function __construct(
        TokoService $tokoService,
        BarangKIService $barangKIService,
        ConvertSatuanService $convertSatuanService
    ) {
        $this->tokoService = $tokoService;
        $this->barangKIService = $barangKIService;
        $this->convertSatuanService = $convertSatuanService;
    }

    /**
     * Scan barcode untuk cek stok barang
     */
    public function scanBarcode(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'id_barcode' => 'required|string',
            'quantity'   => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $barcode = $request->input('id_barcode');
        $jumlah = $request->input('quantity');

        // Cari barang berdasarkan barcode
        $barangKI = BarangKI::where('id_barcode', $barcode)->first();

        if (!$barangKI) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan.',
            ], 404);
        }

        try {
            // Konversi satuan
            $jumlahBaru = $this->convertSatuanService->convertToSmallestUnit($barangKI, $jumlah, $barangKI->expired_time);

            if (!$jumlahBaru['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $jumlahBaru['message'],
                ], 400);
            }
            $barangToko = BarangToko::where('barangki_id', $jumlahBaru['barangki_id'])->first();

            if (!$barangToko) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang Toko tidak ditemukan.',
                ], 404);
            }

            // Hitung harga jual
            $priceSell = $barangToko->price_sell ?: $barangToko->price_buy * (1 + $barangToko->price_percentage / 100);

            $isQuantitySufficient = $barangToko->quantity >= $jumlahBaru['converted_amount'];

            return response()->json([
                'success' => $isQuantitySufficient,
                'message' => $isQuantitySufficient ? 'Jumlah cukup.' : 'Jumlah barang tidak cukup.',
                'barang' => [
                    'id' => $barangKI->id,
                    'barcode' => $barangKI->id_barcode,
                    'name' => $barangKI->barang->name,
                    'satuan' => $barangKI->satuan->name,
                    'quantity' => $jumlah
                ],
            ], $isQuantitySufficient ? 200 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Simpan transaksi penjualan
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $toko = $this->tokoService->getTokoByUser($user);

        // Shift Validation for Cashiers
        $activeShift = null;
        if ($user->hasRole('kasir')) {
            $activeShift = KasirShift::where('kasir_id', $user->id)
                ->where('toko_id', $toko->id)
                ->whereNull('closed_at')
                ->first();

            if (!$activeShift) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buka shift terlebih dahulu sebelum melakukan transaksi.'
                ], 403);
            }
        }

        // Validasi request
        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
            'metode_pembayaran' => 'required|string',
            'is_online' => 'required|boolean',
            'barang' => 'required|array',
            'barang.*.id_barcode' => 'required|exists:barang_ki,id_barcode',
            'barang.*.jumlah' => 'required|integer|min:1',
            'infaq' => 'nullable|array',
            'infaq.amount' => 'required_with:infaq|numeric|min:0',
            'infaq.tujuan' => 'required_with:infaq|exists:infaq_lists,id',
            'infaq.note' => 'nullable|string|max:1000',
            'infaq.donor_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        // Kelompokkan barang berdasarkan id_barcode
        $groupedBarang = $this->groupBarangByBarcode($validated['barang']);

        // Proses setiap barang
        $result = $this->processBarangItems($groupedBarang, $toko, $validated['status'], $user);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 400);
        }

        $details = $result['details'];
        $formatedDetail = $result['formatedDetail'];
        $totalHarga = array_sum(array_column($formatedDetail, 'subtotal'));

        // Generate ID transaction format
        $prefix = str_pad($toko->id, 3, '0', STR_PAD_LEFT);
        $transactionCount = TokoSelling::where('toko_id', $toko->id)->count();
        $transactionNumber = $transactionCount + 1;
        $transactionSuffix = str_pad($transactionNumber, 7, '0', STR_PAD_LEFT);
        $idTransaction = $prefix . '-' . $transactionSuffix;

        // Begin database transaction
        DB::beginTransaction();

        try {
            // Simpan transaksi utama
            $tokoSelling = TokoSelling::create([
                'toko_id' => $toko->id,
                'user_id' => $user->id,
                'total_harga' => $totalHarga,
                'status' => $validated['status'],
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'is_online' => $validated['is_online'],
                'id_transaction' => $idTransaction
            ]);

            // Simpan detail transaksi dengan increment_id sebagai foreign key
            foreach ($details as $detail) {
                $detail['transaction_id'] = $tokoSelling->increment_id;
                $tokoSelling->details()->create($detail);
            }

            // Proses infaq jika ada
            $infaqData = null;
            if (!empty($validated['infaq']) && isset($validated['infaq']['amount']) && $validated['infaq']['amount'] > 0) {
                $infaq = $validated['infaq'];
                $infaqData = InfaqHistory::create([
                    'toko_id' => $toko->id,
                    'user_id' => $user->id,
                    'infaq_list_id' => $infaq['tujuan'],
                    'amount' => $infaq['amount'],
                    'status' => InfaqHistory::STATUS_PENDING, // Start as pending
                    'donor_name' => $infaq['donor_name'] ?? 'Hamba Allah',
                    'note' => $infaq['note'] ?? null,
                    'payment_method' => $validated['metode_pembayaran'],
                    'selling_id' => $tokoSelling->increment_id,
                ]);

                // Auto-complete untuk pembayaran cash (sama seperti di InfaqController)
                if (strtolower($validated['metode_pembayaran']) === 'cash') {
                    $infaqData->markAsCompleted();
                }
            }

            // Update shift cash total if payment is Cash
            if ($activeShift && strtolower($validated['metode_pembayaran']) === 'cash') {
                $activeShift->increment('total_transaksi_tunai', $totalHarga + ($infaqData ? $infaqData->amount : 0));
            }

            DB::commit();

            $response = [
                'message' => 'Transaksi berhasil disimpan.',
                'transaction' => [
                    'id' => $tokoSelling->id_transaction,
                    'toko' => $tokoSelling->toko->name,
                    'kasir' => $user->name,
                    'jumlah_item' => count($details),
                    'total_harga' => $tokoSelling->total_harga,
                    'total_after_infaq' => $tokoSelling->total_harga + ($infaqData ? $infaqData->amount : 0),
                    'status' => $tokoSelling->status,
                    'metode_pembayaran' => $tokoSelling->metode_pembayaran,
                    'is_online' => $tokoSelling->is_online,
                ],
                'items' => $formatedDetail,
            ];

            if ($infaqData) {
                $response['infaq'] = [
                    'donor_name' => $infaqData->donor_name,
                    'amount' => $infaqData->amount,
                    'tujuan' => $infaqData->infaqList->name,
                    'note' => $infaqData->note,
                ];
            }

            return response()->json($response, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error processing transaction: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses transaksi',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Kelompokkan barang berdasarkan id_barcode
     */
    private function groupBarangByBarcode(array $barangList): array
    {
        $grouped = [];

        foreach ($barangList as $item) {
            $barcode = $item['id_barcode'];
            if (!isset($grouped[$barcode])) {
                $grouped[$barcode] = ['id_barcode' => $barcode, 'jumlah' => 0];
            }
            $grouped[$barcode]['jumlah'] += $item['jumlah'];
        }

        return array_values($grouped);
    }

    /**
     * Proses tiap item barang
     */
    private function processBarangItems(array $groupedBarang, $toko, string $status, $user): array
    {
        $details = [];
        $formatedDetail = [];

        foreach ($groupedBarang as $item) {
            $barangKi = BarangKI::where('id_barcode', $item['id_barcode'])->first();
            if (!$barangKi) {
                return ['success' => false, 'message' => "Barang dengan barcode {$item['id_barcode']} tidak ditemukan."];
            }

            $barangToko = BarangToko::where('barangki_id', $barangKi->id)
                ->where('toko_id', $toko->id)
                ->first();

            if (!$barangToko) {
                return ['success' => false, 'message' => 'Barang tidak ditemukan di toko ini.'];
            }

            $priceSell = $barangToko->price_sell ?: $barangToko->price_buy * (1 + $barangToko->price_percentage / 100);

            // Konversi satuan
            $jumlahBaru = $this->convertSatuanService->convertToSmallestUnit($barangKi, $item['jumlah'], $barangKi->expired_time);

            if (!isset($jumlahBaru['success']) || !$jumlahBaru['success']) {
                $errorMsg = $jumlahBaru['message'] ?? 'Terjadi kesalahan';

                if (str_contains($errorMsg, 'Tidak dapat menemukan barang dengan satuan terkecil')) {
                    $jumlahBaru = [
                        'success' => true,
                        'barangki_id' => $barangKi->id,
                        'converted_amount' => $item['jumlah']
                    ];
                } else {
                    return ['success' => false, 'message' => "Konversi satuan gagal: $errorMsg"];
                }
            }

            if ($status === 'success') {
                $reductBarang = BarangToko::where('barangki_id', $jumlahBaru['barangki_id'])
                    ->where('toko_id', $toko->id)
                    ->first();

                if (!$reductBarang) {
                    return ['success' => false, 'message' => 'Barang tidak ditemukan di toko ini.'];
                }

                if ($reductBarang->quantity < $jumlahBaru['converted_amount']) {
                    return ['success' => false, 'message' => "Stok barang {$barangKi->barang->name} ({$barangKi->satuan->name}) tidak mencukupi."];
                }

                $reductBarang->quantity -= $jumlahBaru['converted_amount'];
                $reductBarang->sold += $jumlahBaru['converted_amount'];
                $reductBarang->save();
            }

            $subtotal = round($priceSell * $item['jumlah']);
            $details[] = [
                'barangki_id' => $barangKi->id,
                'jumlah' => $item['jumlah'],
                'harga_satuan' => round($priceSell),
                'subtotal' => $subtotal,
                'jumlahBaru' => $jumlahBaru,
            ];

            $formatedDetail[] = [
                'name' => $barangKi->barang->name,
                'jumlah' => $item['jumlah'],
                'satuan' => $barangKi->satuan->name,
                'harga_satuan' => round($priceSell),
                'subtotal' => $subtotal,
            ];
        }

        return ['success' => true, 'details' => $details, 'formatedDetail' => $formatedDetail];
    }
}
