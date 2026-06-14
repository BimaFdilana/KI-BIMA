<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use InvalidArgumentException;

class PaymentService
{
    /**
     * Mapping status detail (TokoPaymentProgress) ke kategori (TokoPayment)
     */
    private $validStatuses = [
        'created' => 'pending',
        'payment_pending' => 'pending',
        'payment_confirmed' => 'paid',
        'processing' => 'paid',
        'shipping' => 'delivery',
        'delivered' => 'delivery',
        'completed' => 'success',
        'cancelled' => 'failed',
        'refund_requested' => 'failed',
        'refunded' => 'failed',
    ];

    /**
     * Hirarki kategori status - semakin tinggi angka, semakin maju statusnya
     */
    private $categoryHierarchy = [
        'unknown' => 0,
        'pending' => 1,
        'paid' => 2,
        'delivery' => 3,
        'success' => 4,
        'failed' => 0, // failed bisa dari status manapun
    ];

    /**
     * Kategori yang diizinkan untuk transisi dari setiap kategori
     */
    private $allowedCategoryTransitions = [
        'unknown' => ['pending', 'paid', 'delivery', 'success', 'failed'],
        'pending' => ['paid', 'failed'],
        'paid' => ['delivery', 'failed'],
        'delivery' => ['success', 'failed'],
        'success' => ['failed'], // hanya bisa refund
        'failed' => [], // status final
    ];

    /**
     * Status detail yang diizinkan dalam setiap kategori untuk transisi
     */
    private $allowedDetailTransitions = [
        // Dari pending
        'created' => ['payment_pending', 'cancelled'],
        'payment_pending' => ['payment_confirmed', 'cancelled'],

        // Dari paid
        'payment_confirmed' => ['processing', 'cancelled', 'refund_requested'],
        'processing' => ['shipping', 'cancelled', 'refund_requested'],

        // Dari delivery
        'shipping' => ['delivered', 'cancelled', 'refund_requested'],
        'delivered' => ['completed', 'cancelled', 'refund_requested'],

        // Dari success
        'completed' => ['refund_requested'],

        // Status failed (final)
        'cancelled' => ['refunded'], // bisa refund jika sudah bayar
        'refund_requested' => ['refunded', 'cancelled'],
        'refunded' => [], // final
    ];

    public function notifLabel(string $status)
    {
        switch ($status) {
            case 'pending':
                $description = 'Pembayaran Anda sedang diproses. Mohon tunggu beberapa saat untuk konfirmasi lebih lanjut.';
                break;
            case 'paid':
                $description = 'Terima kasih! Pembayaran Anda telah berhasil kami terima.';
                break;
            case 'failed':
                $description = 'Pembayaran gagal diproses. Silakan coba kembali atau hubungi tim dukungan kami.';
                break;
            case 'unknown':
                $description = 'Status pembayaran tidak dapat dipastikan. Silakan hubungi tim dukungan untuk bantuan lebih lanjut.';
                break;
            case 'success':
                $description = 'Pesanan Anda telah berhasil diproses. Terima kasih telah berbelanja bersama kami!';
                break;
            case 'delivery':
                $description = 'Pesanan Anda sedang dalam perjalanan. Harap bersabar hingga pesanan tiba di alamat tujuan.';
                break;
            case 'created':
                $description = 'Pesanan Anda telah dibuat dan sedang menunggu pembayaran.';
                break;
            case 'payment_pending':
                $description = 'Menunggu konfirmasi pembayaran. Mohon selesaikan pembayaran sesuai instruksi.';
                break;
            case 'payment_confirmed':
                $description = 'Pembayaran Anda telah dikonfirmasi. Pesanan akan segera diproses.';
                break;
            case 'processing':
                $description = 'Pesanan Anda sedang diproses. Kami akan segera mengirimkannya.';
                break;
            case 'shipping':
                $description = 'Pesanan Anda sedang dikirim. Silakan pantau status pengiriman Anda.';
                break;
            case 'delivered':
                $description = 'Pesanan telah berhasil dikirim ke alamat tujuan. Terima kasih!';
                break;
            case 'completed':
                $description = 'Pesanan Anda telah selesai. Kami harap Anda puas dengan layanan kami!';
                break;
            case 'cancelled':
                $description = 'Pesanan telah dibatalkan. Jika ini tidak sesuai, hubungi tim kami segera.';
                break;
            case 'refund_requested':
                $description = 'Permintaan pengembalian dana telah kami terima. Kami akan memprosesnya secepatnya.';
                break;
            case 'refunded':
                $description = 'Pengembalian dana telah berhasil dilakukan. Dana akan masuk ke akun Anda dalam waktu dekat.';
                break;
            default:
                $description = 'Status pesanan telah diperbarui. Silakan cek detail terbaru di akun Anda.';
        }

        return $description;
    }

    /**
     * Validasi apakah status detail valid
     */
    public function isValidDetailStatus($detailStatus)
    {
        return array_key_exists($detailStatus, $this->validStatuses);
    }

    /**
     * Validasi apakah kategori status valid
     */
    public function isValidCategoryStatus($categoryStatus)
    {
        $validCategories = ['unknown', 'pending', 'paid', 'delivery', 'success', 'failed'];
        return in_array($categoryStatus, $validCategories);
    }

    /**
     * Mendapatkan kategori dari status detail
     */
    public function getStatusCategory($detailStatus)
    {
        return $this->validStatuses[$detailStatus] ?? null;
    }

    /**
     * Cek apakah status detail sudah pernah ada di TokoPaymentProgress
     */
    public function hasDetailStatusInProgress($paymentId, $detailStatus)
    {
        if (!$this->isValidDetailStatus($detailStatus)) {
            return false;
        }

        $exists = DB::table('toko_payment_progress')
            ->where('payment_id', $paymentId)
            ->where('status', $detailStatus)
            ->exists();

        return $exists;
    }

    /**
     * Mendapatkan record progress berdasarkan payment_id dan status
     */
    public function getProgressRecord($paymentId, $detailStatus)
    {
        if (!$this->isValidDetailStatus($detailStatus)) {
            return null;
        }

        return DB::table('toko_payment_progress')
            ->where('payment_id', $paymentId)
            ->where('status', $detailStatus)
            ->first();
    }

    /**
     * Cek apakah kategori status sudah pernah ada di TokoPaymentProgress
     */
    public function hasCategoryInProgress($paymentId, $categoryStatus)
    {
        if (!$this->isValidCategoryStatus($categoryStatus)) {
            return false;
        }

        // Ambil semua status detail dalam kategori ini
        $detailStatuses = $this->getDetailStatusesByCategory($categoryStatus);

        $exists = DB::table('toko_payment_progress')
            ->where('payment_id', $paymentId)
            ->whereIn('status', $detailStatuses)
            ->exists();

        return $exists;
    }

    /**
     * Validasi transisi status detail
     */
    public function canTransitionToDetail($currentDetailStatus, $newDetailStatus)
    {
        if (!$this->isValidDetailStatus($currentDetailStatus) || !$this->isValidDetailStatus($newDetailStatus)) {
            return false;
        }

        // Jika status sama, tidak perlu transisi
        if ($currentDetailStatus === $newDetailStatus) {
            return false;
        }

        // Cek apakah transisi detail diizinkan
        $allowedDetails = $this->allowedDetailTransitions[$currentDetailStatus] ?? [];
        if (in_array($newDetailStatus, $allowedDetails)) {
            return true;
        }

        // Jika tidak ada aturan detail, cek berdasarkan kategori
        $currentCategory = $this->getStatusCategory($currentDetailStatus);
        $newCategory = $this->getStatusCategory($newDetailStatus);

        return in_array($newCategory, $this->allowedCategoryTransitions[$currentCategory] ?? []);
    }

    /**
     * Update status payment dengan validasi
     */
    public function updatePaymentStatus($paymentId, $newDetailStatus, $notes = null)
    {
        // Validasi status detail
        if (!$this->isValidDetailStatus($newDetailStatus)) {
            throw new InvalidArgumentException("Status '{$newDetailStatus}' tidak valid");
        }

        // Ambil status terakhir dari TokoPaymentProgress
        $lastProgress = DB::table('toko_payment_progress')
            ->where('payment_id', $paymentId)
            ->orderBy('created_at', 'desc')
            ->first();

        // Validasi transisi jika ada status sebelumnya
        if ($lastProgress && !$this->canTransitionToDetail($lastProgress->status, $newDetailStatus)) {
            throw new Exception("Tidak dapat mengubah status dari '{$lastProgress->status}' ke '{$newDetailStatus}'");
        }

        $newCategory = $this->getStatusCategory($newDetailStatus);

        DB::beginTransaction();
        try {
            // Update kategori status di TokoPayment
            DB::table('toko_payment')
                ->where('id', $paymentId)
                ->update([
                    'status' => $newCategory,
                    'updated_at' => now()
                ]);

            // Cek apakah status detail sudah pernah dicatat
            $existingProgress = $this->getProgressRecord($paymentId, $newDetailStatus);

            if ($existingProgress) {
                // Update record yang sudah ada
                DB::table('toko_payment_progress')
                    ->where('id', $existingProgress->id)
                    ->update([
                        'notes' => $notes,
                        'updated_at' => now()
                    ]);
            } else {
                // Buat record baru jika belum ada
                DB::table('toko_payment_progress')->insert([
                    'payment_id' => $paymentId,
                    'status' => $newDetailStatus,
                    'notes' => $notes,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Cari payment berdasarkan kategori status di TokoPayment
     */
    public function findPaymentsByCategory($categoryStatus)
    {
        if (!$this->isValidCategoryStatus($categoryStatus)) {
            throw new InvalidArgumentException("Status category '{$categoryStatus}' tidak valid");
        }

        return DB::table('toko_payment')
            ->where('status', $categoryStatus)
            ->get();
    }

    /**
     * Cari payment berdasarkan status detail (dari TokoPaymentProgress)
     */
    public function findPaymentsByDetailStatus($detailStatus)
    {
        if (!$this->isValidDetailStatus($detailStatus)) {
            throw new InvalidArgumentException("Status detail '{$detailStatus}' tidak valid");
        }

        // Ambil payment_id yang memiliki status detail terakhir sesuai parameter
        $paymentIds = DB::table('toko_payment_progress as tpp1')
            ->select('tpp1.payment_id')
            ->leftJoin('toko_payment_progress as tpp2', function ($join) {
                $join->on('tpp1.payment_id', '=', 'tpp2.payment_id')
                    ->whereRaw('tpp1.created_at < tpp2.created_at');
            })
            ->whereNull('tpp2.payment_id')
            ->where('tpp1.status', $detailStatus)
            ->pluck('payment_id');

        return DB::table('toko_payment')
            ->whereIn('id', $paymentIds)
            ->get();
    }

    /**
     * Mendapatkan riwayat status detail payment
     */
    public function getPaymentProgressHistory($paymentId)
    {
        return DB::table('toko_payment_progress')
            ->where('payment_id', $paymentId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Cek status detail yang diizinkan untuk transisi selanjutnya
     */
    public function getAllowedNextDetailStatuses($currentDetailStatus)
    {
        if (!$this->isValidDetailStatus($currentDetailStatus)) {
            return [];
        }

        // Ambil dari aturan detail terlebih dahulu
        $allowedDetails = $this->allowedDetailTransitions[$currentDetailStatus] ?? [];

        // Jika tidak ada aturan detail, ambil berdasarkan kategori
        if (empty($allowedDetails)) {
            $currentCategory = $this->getStatusCategory($currentDetailStatus);
            $allowedCategories = $this->allowedCategoryTransitions[$currentCategory] ?? [];

            foreach ($allowedCategories as $category) {
                $detailsInCategory = $this->getDetailStatusesByCategory($category);
                $allowedDetails = array_merge($allowedDetails, $detailsInCategory);
            }
        }

        return array_unique($allowedDetails);
    }

    /**
     * Mendapatkan semua status detail dalam kategori tertentu
     */
    public function getDetailStatusesByCategory($category)
    {
        if ($category === 'unknown') {
            return []; // unknown tidak memiliki status detail
        }

        return array_keys(array_filter($this->validStatuses, function ($cat) use ($category) {
            return $cat === $category;
        }));
    }

    /**
     * Mendapatkan status terakhir dari TokoPaymentProgress
     */
    public function getLastDetailStatus($paymentId)
    {
        $lastProgress = DB::table('toko_payment_progress')
            ->where('payment_id', $paymentId)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastProgress ? $lastProgress->status : null;
    }

    /**
     * Mendapatkan status kategori dari TokoPayment
     */
    public function getCurrentCategoryStatus($paymentId)
    {
        $payment = DB::table('toko_payment')
            ->where('id', $paymentId)
            ->first();

        return $payment ? $payment->status : null;
    }

    /**
     * Mendapatkan semua kategori status
     */
    public function getAllCategories()
    {
        return ['unknown', 'pending', 'paid', 'delivery', 'success', 'failed'];
    }

    /**
     * Mendapatkan semua status detail
     */
    public function getAllDetailStatuses()
    {
        return array_keys($this->validStatuses);
    }

    /**
     * Cek apakah payment dalam status unknown
     */
    public function isPaymentUnknown($paymentId)
    {
        $payment = DB::table('toko_payment')
            ->where('id', $paymentId)
            ->first();

        return $payment && $payment->status === 'unknown';
    }

    /**
     * Reset payment dari unknown ke status normal
     */
    public function resetFromUnknown($paymentId, $newDetailStatus, $notes = null)
    {
        if (!$this->isPaymentUnknown($paymentId)) {
            throw new Exception("Payment tidak dalam status unknown");
        }

        // Gunakan fungsi update normal karena dari unknown bisa ke mana saja
        return $this->updatePaymentStatus($paymentId, $newDetailStatus, $notes);
    }

    public function updateSuccessStatus($paymentId, $newDetailStatus, $notes = null, array $barang = [])
    {
        if (!$this->isPaymentUnknown($paymentId)) {
            throw new Exception("Payment tidak dalam status unknown");
        }

        // Get the payment
        $payment = $this->getPaymentById($paymentId);
        if (!$payment) {
            throw new Exception("Payment not found");
        }

        // Update the payment with new status and barang details
        $payment->status = $newDetailStatus;
        $payment->barang = json_encode($barang); // Store as JSON string
        $payment->notes = $notes;
        $payment->save();

        // Create payment progress record
        $paymentProgress = new TokoPaymentProgress([
            'payment_id' => $paymentId,
            'status' => $newDetailStatus,
            'notes' => $notes,
            'barang' => json_encode($barang) // Store as JSON string
        ]);
        $paymentProgress->save();

        return true;
    }
}

// Contoh penggunaan:

/*
// Inisialisasi
$statusManager = new PaymentService();

// Update status detail (akan otomatis update kategori di TokoPayment)
// Jika status detail sudah pernah ada, maka akan di-update notes-nya
try {
    $statusManager->updatePaymentStatus(123, 'payment_confirmed', 'Payment berhasil dikonfirmasi');
    echo "Status berhasil diupdate";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Update lagi dengan status yang sama akan mengupdate notes
try {
    $statusManager->updatePaymentStatus(123, 'payment_confirmed', 'Payment dikonfirmasi ulang dengan catatan tambahan');
    echo "Status berhasil diupdate (notes diperbarui)";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Cari payment berdasarkan kategori di TokoPayment
$paidPayments = $statusManager->findPaymentsByCategory('paid');
$unknownPayments = $statusManager->findPaymentsByCategory('unknown');

// Cari payment berdasarkan status detail terakhir
$confirmedPayments = $statusManager->findPaymentsByDetailStatus('payment_confirmed');

// Cek status detail yang diizinkan selanjutnya
$lastStatus = $statusManager->getLastDetailStatus(123);
$allowedNext = $statusManager->getAllowedNextDetailStatuses($lastStatus);

// Cek apakah payment dalam status unknown
if ($statusManager->isPaymentUnknown(123)) {
    // Reset dari unknown ke status normal
    $statusManager->resetFromUnknown(123, 'created', 'Reset ke status awal');
}

// Mendapatkan riwayat lengkap
$history = $statusManager->getPaymentProgressHistory(123);
*/