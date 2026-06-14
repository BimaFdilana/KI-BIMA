<?php

namespace App\Http\Controllers\Api\Toko;

use App\Models\Auth\UserModel;
use App\Models\Toko\TokoPayment;
use App\Models\Toko\TokoPesanan;
use App\Models\Toko\BarangToko;
use App\Models\Barang\BarangKI;
use App\Services\Barang\BarangKIService;
use App\Services\Barang\BarangIOService;
use App\Services\Barang\ConvertSatuanService;
use App\Services\Message\NotificationService;
use App\Services\Toko\KeranjangTokoService;
use App\Services\Toko\PakDulService;
use App\Services\Toko\TokoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    protected TokoService $tokoService;
    protected BarangKIService $barangKIService;
    protected KeranjangTokoService $keranjangTokoService;
    protected PakDulService $pakDulService;
    protected NotificationService $notificationService;
    protected BarangIOService $barangIOService;
    protected ConvertSatuanService $convertSatuanService;

    public function __construct(
        TokoService $tokoService,
        NotificationService $notificationService,
        BarangKIService $barangKIService,
        KeranjangTokoService $keranjangTokoService,
        PakDulService $pakDulService,
        BarangIOService $barangIOService,
        ConvertSatuanService $convertSatuanService
    ) {
        $this->tokoService = $tokoService;
        $this->notificationService = $notificationService;
        $this->barangKIService = $barangKIService;
        $this->keranjangTokoService = $keranjangTokoService;
        $this->pakDulService = $pakDulService;
        $this->barangIOService = $barangIOService;
        $this->convertSatuanService = $convertSatuanService;
    }

    /**
     * Proses pembayaran berdasarkan tipe yang dipilih.
     */
    public function processPayment(Request $request): JsonResponse
    {
        // 1. Validasi request
        $validationResponse = $this->validatePaymentRequest($request);
        if ($validationResponse) {
            return $validationResponse;
        }

        $paymentType = $request->payment_type;
        $transactionId = $request->transaction_id;
        $snapToken = $request->snap_token;
        $user = auth()->user();

        // 2. Validasi apakah user memiliki toko
        $toko = $this->tokoService->getTokoByUser($user);
        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.'
            ], 403);
        }

        // 3. Validasi isi keranjang
        $this->keranjangTokoService->updateKeranjangToko($toko);
        $keranjang = $this->keranjangTokoService->getKeranjangBarangAktif($toko);
        if ($keranjang->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang belanja kosong.'
            ], 400);
        }

        // 4. Hitung total harga dan validasi stok
        $totalPriceResult = $this->calculateTotalPrice($keranjang);
        if ($totalPriceResult instanceof JsonResponse) {
            return $totalPriceResult;
        }
        $totalPrice = $totalPriceResult;

        // 5. Proses pembayaran berdasarkan tipe
        $payment = $this->processPaymentByType(
            $paymentType,
            $user,
            $toko,
            $totalPrice,
            $transactionId,
            $keranjang,
            $snapToken
        );

        if ($payment instanceof JsonResponse) {
            return $payment;
        }

        // 6. Finalisasi transaksi
        $this->finalizeTransaction($keranjang, $payment->id);

        // 7. Kirim notifikasi
        if ($user) {
            $this->notificationService->sendOrderStatusNotification(
                $user,
                $transactionId,
                'success',
                'Pesanan berhasil dibuat',
                $payment->total,
                $user,
                "/toko/orders/{$transactionId}"
            );
        }

        // 8. Kembalikan respons sukses
        return $this->buildSuccessResponse($transactionId, $paymentType, $payment->snap_token ?? null);
    }

    /**
     * Validasi request pembayaran.
     */
    private function validatePaymentRequest(Request $request): ?JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'payment_type' => 'required|in:Virtual,Cash,Pakdul',
            'transaction_id' => 'required|string|unique:toko_payment,transaction_id',
            'snap_token' => 'required_if:payment_type,Virtual|nullable|string',
        ], [
            'payment_type.required' => 'Tipe pembayaran wajib diisi.',
            'payment_type.in' => 'Tipe pembayaran harus Virtual, Cash, atau Pakdul.',
            'transaction_id.required' => 'Transaction ID wajib diisi.',
            'transaction_id.unique' => 'Transaction ID sudah digunakan.',
            'snap_token.required_if' => 'Snap token wajib diisi untuk pembayaran Virtual.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data yang diberikan tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        return null;
    }

    /**
     * Hitung total harga keranjang dan validasi stok.
     */
    private function calculateTotalPrice($keranjang): int|JsonResponse
    {
        $totalPrice = 0;

        foreach ($keranjang as $item) {
            $barang = $item->barangki;

            if (!$this->isItemAvailable($barang, $item->quantity)) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok untuk barang '{$barang->barang->name}' tidak mencukupi."
                ], 409);
            }

            $hargaJual = $this->getItemPrice($barang);
            $totalPrice += $hargaJual * $item->quantity;
        }

        return (int) round($totalPrice);
    }

    /**
     * Ambil harga item setelah diskon.
     */
    private function getItemPrice($barang): float
    {
        $cekDiscount = $this->barangKIService->applyDiscountsToBarang($barang);
        return $cekDiscount->final_price;
    }

    /**
     * Cek ketersediaan stok barang.
     */
    private function isItemAvailable($barang, int $requestedQuantity): bool
    {
        return $barang->quantity >= $requestedQuantity;
    }

    /**
     * Proses pembayaran berdasarkan tipe.
     */
    private function processPaymentByType(
        string $paymentType,
        $user,
        $toko,
        int $totalPrice,
        string $transactionId,
        $keranjang,
        ?string $snapToken = null
    ): TokoPayment|JsonResponse {
        return match ($paymentType) {
            'Virtual' => $this->processVirtualPayment($user, $toko, $totalPrice, $transactionId, $snapToken),
            'Cash' => $this->processCashPayment($user, $toko, $totalPrice, $transactionId),
            'Pakdul' => $this->processPakdulPayment($user, $toko, $totalPrice, $transactionId),
            default => response()->json([
                'success' => false,
                'message' => 'Tipe pembayaran tidak valid.'
            ], 400),
        };
    }

    /**
     * Proses pembayaran Virtual dengan snap_token dari request.
     */
    private function processVirtualPayment(
        $user,
        $toko,
        int $totalPrice,
        string $transactionId,
        ?string $snapToken
    ): TokoPayment|JsonResponse {
        DB::beginTransaction();

        try {
            $payment = $this->createPayment([
                'transaction_id' => $transactionId,
                'user_id' => $user->id,
                'toko_id' => $toko->id,
                'total' => $totalPrice,
                'payment_type' => 'Virtual',
                'snap_token' => $snapToken,
            ]);

            DB::commit();

            Log::info('Virtual Payment Created', [
                'transaction_id' => $transactionId,
                'snap_token' => $snapToken,
                'total' => $totalPrice,
            ]);

            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Virtual Payment Failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Pembayaran virtual gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processCashPayment($user, $toko, int $totalPrice, string $transactionId): TokoPayment
    {
        return $this->createPayment([
            'transaction_id' => $transactionId,
            'user_id' => $user->id,
            'toko_id' => $toko->id,
            'total' => $totalPrice,
            'payment_type' => 'Cash',
            'snap_token' => null,
        ]);
    }

    private function processPakdulPayment($user, $toko, int $totalPrice, string $transactionId): TokoPayment|JsonResponse
    {
        if (!$this->pakDulService->canUsePaylatter($user, $toko, $totalPrice)) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran Pakdul gagal, saldo tidak mencukupi.'
            ], 400);
        }

        $payment = $this->createPayment([
            'transaction_id' => $transactionId,
            'user_id' => $user->id,
            'toko_id' => $toko->id,
            'total' => $totalPrice,
            'payment_type' => 'Pakdul',
            'snap_token' => null,
        ]);

        $this->pakDulService->createTransaction($user, $toko, $totalPrice, $payment->id);

        return $payment;
    }

    private function createPayment(array $data): TokoPayment
    {
        return TokoPayment::create($data);
    }

    private function finalizeTransaction($keranjang, int $paymentId): void
    {
        $this->keranjangTokoService->moveItemsToPesanan($keranjang, $paymentId);
        $this->keranjangTokoService->hapusKeranjang($keranjang);
    }

    private function buildSuccessResponse(string $transactionId, string $paymentType, ?string $snapToken = null): JsonResponse
    {
        $message = in_array($paymentType, ['Cash', 'Pakdul'])
            ? 'Pesanan berhasil dibuat.'
            : 'Pembayaran berhasil diproses.';

        $response = [
            'success' => true,
            'message' => $message,
            'transaction_id' => $transactionId,
        ];

        if ($paymentType === 'Virtual' && $snapToken) {
            $response['snap_token'] = $snapToken;
        }

        return response()->json($response, 201);
    }

    /**
     * Update status pembayaran.
     */
    /**
     * Update status pembayaran.
     */
    public function updatePaymentStatus(Request $request, string $transactionId): JsonResponse
    {
        \Illuminate\Support\Facades\Log::info('UpdatePaymentStatus REQUEST', [
            'transaction_id' => $transactionId,
            'request_data' => $request->all(),
        ]);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:paid,pending,failed,unknown,partial_success,success,delivery,cancelled,refund_requested,refunded',
            'barang_ids' => 'nullable|array', // Optional: untuk konfirmasi per-barang
            'barang_ids.*' => 'integer',
        ]);

        if ($validator->fails()) {
            \Illuminate\Support\Facades\Log::warning('UpdatePaymentStatus VALIDATION FAILED', [
                'transaction_id' => $transactionId,
                'errors' => $validator->errors()->toArray(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validasi data gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $payment = TokoPayment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            \Illuminate\Support\Facades\Log::warning('UpdatePaymentStatus PAYMENT NOT FOUND', [
                'transaction_id' => $transactionId,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak ditemukan.',
                'error' => 'Pembayaran tidak ditemukan.'
            ], 404);
        }

        $barangIds = $request->barang_ids ?? [];
        \Illuminate\Support\Facades\Log::info('UpdatePaymentStatus PROCESSING', [
            'transaction_id' => $transactionId,
            'status' => $request->status,
            'barang_ids' => $barangIds,
        ]);

        return $this->processStatusUpdate($payment, $request->status, $barangIds);
    }

    /**
     * Handle callback notifikasi dari Midtrans.
     */
    public function handleNotification(Request $request): JsonResponse
    {
        $this->setupMidtrans();

        try {
            $notification = new \Midtrans\Notification();
            $transactionStatus = $notification->transaction_status;
            $orderId = $notification->order_id;
            $fraudStatus = $notification->fraud_status;

            $payment = TokoPayment::where('transaction_id', $orderId)->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'error' => 'Pembayaran tidak ditemukan.'
                ], 404);
            }

            $status = 'unknown';

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $status = 'pending';
                } else if ($fraudStatus == 'accept') {
                    $status = 'paid';
                }
            } else if ($transactionStatus == 'settlement') {
                $status = 'paid';
            } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire') {
                $status = 'failed';
            } else if ($transactionStatus == 'cancel') {
                $status = 'cancelled';
            } else if ($transactionStatus == 'pending') {
                $status = 'pending';
            }


            Log::info('Midtrans Notification Received', [
                'order_id' => $orderId,
                'status' => $transactionStatus,
                'mapped_status' => $status,
            ]);

            return $this->processStatusUpdate($payment, $status);
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Gagal memproses notifikasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logika inti untuk memperbarui status pembayaran dan pesanan.
     * Support partial confirmation: jika barang_ids diberikan, hanya update items tersebut.
     */
    private function processStatusUpdate(TokoPayment $payment, string $status, array $barangIds = []): JsonResponse
    {
        $transactionId = $payment->transaction_id;
        $user = UserModel::find($payment->user_id);

        $checkStatusResult = $this->keranjangTokoService->checkStatusPayment($transactionId, $status);
        if ($checkStatusResult['error']) {
            return response()->json([
                'success' => false,
                'error' => $checkStatusResult['error']
            ], 400);
        }

        $notifLabel = $this->keranjangTokoService->notifLabel($status);

        try {
            DB::beginTransaction();

            if ($status === 'paid') {
                $this->keranjangTokoService->updateTokoPesanan($payment->id, $status);
                $payment->status = $status;
            } elseif ($status === 'success') {
                // Partial confirmation: jika barang_ids diberikan, update hanya items tersebut
                if (!empty($barangIds)) {
                    // barang_ids sebenarnya adalah toko_pesanan.id (order item ID)
                    Log::info('UpdatePaymentStatus - Partial Confirm', [
                        'payment_id' => $payment->id,
                        'barang_ids' => $barangIds,
                        'status' => $status,
                    ]);

                    // Ambil items yang akan diupdate
                    $pesananItems = TokoPesanan::where('payment_id', $payment->id)
                        ->whereIn('id', $barangIds)
                        ->get();

                    Log::info('UpdatePaymentStatus - Items Found', [
                        'count' => $pesananItems->count(),
                    ]);

                    // Process setiap item: update status dan move ke barang_toko
                    foreach ($pesananItems as $pesanan) {
                        // Update status
                        $pesanan->status = $status;
                        $pesanan->save();

                        // Move ke barang_toko
                        $barangki = BarangKI::find($pesanan->barangki_id);
                        if ($barangki) {
                            $quantity = $pesanan->quantity;
                            $priceFromOrder = $pesanan->price;

                            // Remove dari supply
                            $this->barangIOService->removeBarang($payment->toko_id, $barangki->id, $quantity, $priceFromOrder);

                            // Convert satuan dan add ke barang_toko
                            $details = $this->convertSatuanService->getBarangDetailsAndConversionStatus($barangki->id, $quantity);

                            foreach ($details['converted_barangki'] as $converted) {
                                $convertedAmount = isset($converted['converted_amount']) ? $converted['converted_amount'] : 0;

                                $convertedBarangki = BarangKI::find($converted['barangki']['id']);
                                if ($convertedBarangki) {
                                    $barangDiskon = $this->barangKIService->cekDiskonBarang($convertedBarangki->id_barcode);
                                    $originalPrice = $barangDiskon['data']['original_price'];

                                    $existingBarangToko = BarangToko::where('toko_id', $payment->toko_id)
                                        ->where('barangki_id', $converted['barangki']['id'])
                                        ->first();

                                    if ($existingBarangToko) {
                                        $existingBarangToko->quantity += $convertedAmount;
                                        $existingBarangToko->save();
                                    } else {
                                        $priceSell = $converted['barangki']['price_sell'] ?? $originalPrice;
                                        $pricePercentage = $originalPrice > 0 ? (($priceSell - $originalPrice) / $originalPrice) * 100 : 0;

                                        BarangToko::create([
                                            'toko_id' => $payment->toko_id,
                                            'barangki_id' => $converted['barangki']['id'],
                                            'quantity' => $convertedAmount,
                                            'price_buy' => $originalPrice,
                                            'price_sell' => $priceSell,
                                            'price_percentage' => round($pricePercentage, 2),
                                        ]);
                                    }
                                }
                            }
                        }
                    }

                    Log::info('UpdatePaymentStatus - Items Updated', [
                        'count' => $pesananItems->count(),
                    ]);
                } else {
                    // Backward compatibility: jika tidak ada barang_ids, update semua items
                    $this->keranjangTokoService->updateBarangToko($payment->id, $status, $payment->toko_id);
                }

                // Cek: apakah SEMUA items di payment sudah berhasil?
                $totalItems = TokoPesanan::where('payment_id', $payment->id)->count();
                $successItems = TokoPesanan::where('payment_id', $payment->id)
                    ->where('status', 'success')
                    ->count();

                Log::info('UpdatePaymentStatus - Status Check', [
                    'totalItems' => $totalItems,
                    'successItems' => $successItems,
                    'current_payment_status' => $payment->status,
                ]);

                // Update payment status berdasarkan progress items
                if ($totalItems > 0 && $totalItems === $successItems) {
                    // Semua barang sudah success
                    $payment->status = 'success';
                    Log::info('UpdatePaymentStatus - All Items Success', ['payment_id' => $payment->id]);
                } elseif ($totalItems > 0 && $successItems > 0) {
                    // Ada yang sudah selesai tapi ada yang belum
                    $payment->status = 'partial_success';
                    Log::info('UpdatePaymentStatus - Partial Success', [
                        'payment_id' => $payment->id,
                        'successItems' => $successItems,
                        'totalItems' => $totalItems,
                    ]);
                }
            } else {
                $payment->status = $status;
                // Update status pesanan untuk status selain paid/success (misal: failed, cancelled, refund_requested)
                TokoPesanan::where('payment_id', $payment->id)
                    ->whereIn('status', ['Pending', 'pending', 'paid'])
                    ->update(['status' => $status]);
            }

            $payment->save();

            DB::commit();

            if ($user) {
                $this->notificationService->sendOrderStatusNotification(
                    $user,
                    $transactionId,
                    $payment->status, // Kirim status terakhir payment
                    $notifLabel,
                    $payment->total,
                    $user,
                    "/toko/orders/{$transactionId}"
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Status pesanan berhasil diperbarui.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Process Status Update Failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Gagal memperbarui status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Map status transaksi Midtrans ke status internal. (Deprecated: using processStatusUpdate logic instead)
     */
    private function mapMidtransStatus(string $transactionStatus): string
    {
        return match ($transactionStatus) {
            'capture', 'settlement' => 'paid',
            'pending' => 'pending',
            'cancel' => 'cancelled',
            'deny', 'expire' => 'failed',
            default => 'unknown',

        };
    }

    /**
     * Setup konfigurasi Midtrans.
     */
    private function setupMidtrans(): void
    {
        Config::$clientKey = config('midtrans.client_key');
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }
}
