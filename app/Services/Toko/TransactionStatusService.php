<?php

namespace App\Services\Toko;

use App\Models\Toko\TokoPayment;
use App\Models\Toko\TokoPesanan;
use App\Models\Toko\TokoSelling;
use App\Models\Toko\TokoSellingDetail;
use App\Models\Toko\TokoPaymentProgress;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionStatusService
{
    /**
     * Update status item dalam transaksi payment
     */
    public function updatePaymentItemStatus($paymentId, $itemId, $newStatus, $notes = null, $estimatedDelivery = null)
    {
        return DB::transaction(function () use ($paymentId, $itemId, $newStatus, $notes, $estimatedDelivery) {
            // Update status item
            $pesanan = TokoPesanan::where('payment_id', $paymentId)
                ->where('id', $itemId)
                ->first();

            if (!$pesanan) {
                throw new \Exception('Item pesanan tidak ditemukan');
            }

            $oldStatus = $pesanan->status;
            $pesanan->status = $newStatus;
            $pesanan->notes = $notes;
            $pesanan->estimated_delivery = $estimatedDelivery;

            if ($newStatus === 'success') {
                $pesanan->actual_delivery = now();
            }

            $pesanan->save();

            // Log perubahan menggunakan TokoPaymentProgress
            $this->logPaymentProgress($paymentId, $newStatus, $notes ?: "Item #{$itemId} status changed from {$oldStatus} to {$newStatus}");

            // Update status transaksi keseluruhan
            $this->updatePaymentOverallStatus($paymentId);

            return $pesanan;
        });
    }

    /**
     * Update status item dalam transaksi selling
     */
    public function updateSellingItemStatus($transactionId, $itemId, $newStatus, $notes = null, $estimatedDelivery = null)
    {
        return DB::transaction(function () use ($transactionId, $itemId, $newStatus, $notes, $estimatedDelivery) {
            $sellingDetail = TokoSellingDetail::where('transaction_id', $transactionId)
                ->where('id', $itemId)
                ->first();

            if (!$sellingDetail) {
                throw new \Exception('Item selling detail tidak ditemukan');
            }

            $oldStatus = $sellingDetail->item_status;
            $sellingDetail->item_status = $newStatus;
            $sellingDetail->notes = $notes;
            $sellingDetail->estimated_delivery = $estimatedDelivery;

            if ($newStatus === 'success') {
                $sellingDetail->actual_delivery = now();
            }

            $sellingDetail->save();

            // Update status transaksi keseluruhan
            $this->updateSellingOverallStatus($transactionId);

            return $sellingDetail;
        });
    }

    /**
     * Update status keseluruhan transaksi payment berdasarkan status item-itemnya
     */
    private function updatePaymentOverallStatus($paymentId)
    {
        $payment = TokoPayment::find($paymentId);
        $pesananItems = TokoPesanan::where('payment_id', $paymentId)->get();

        $statusCounts = $pesananItems->groupBy('status')->map->count();
        $totalItems = $pesananItems->count();

        $oldStatus = $payment->status;
        $newStatus = $this->determineOverallPaymentStatus($statusCounts, $totalItems);

        if ($oldStatus !== $newStatus) {
            $payment->status = $newStatus;
            $payment->save();

            // Log perubahan status keseluruhan menggunakan TokoPaymentProgress
            $this->logPaymentProgress(
                $paymentId,
                $this->mapToProgressStatus($newStatus),
                "Transaction status auto-updated from {$oldStatus} to {$newStatus} based on item statuses"
            );
        }
    }

    /**
     * Update status keseluruhan transaksi selling berdasarkan status item-itemnya
     */
    private function updateSellingOverallStatus($transactionId)
    {
        $selling = TokoSelling::where('increment_id', $transactionId)->first();
        $sellingDetails = TokoSellingDetail::where('transaction_id', $transactionId)->get();

        $statusCounts = $sellingDetails->groupBy('item_status')->map->count();
        $totalItems = $sellingDetails->count();

        $oldStatus = $selling->status;
        $newStatus = $this->determineOverallStatus($statusCounts, $totalItems);

        if ($oldStatus !== $newStatus) {
            $selling->status = $newStatus;
            $selling->save();
        }
    }

    /**
     * Tentukan status keseluruhan untuk payment berdasarkan status item-item
     */
    private function determineOverallPaymentStatus($statusCounts, $totalItems)
    {
        // Jika semua item success
        if (isset($statusCounts['success']) && $statusCounts['success'] == $totalItems) {
            return 'success';
        }

        // Jika ada yang failed/cancelled/refunded
        if (isset($statusCounts['cancelled']) || isset($statusCounts['failed']) || isset($statusCounts['refunded'])) {
            $failedCount = ($statusCounts['cancelled'] ?? 0) + ($statusCounts['failed'] ?? 0) + ($statusCounts['refunded'] ?? 0);
            if ($failedCount == $totalItems) {
                return 'failed';
            }
            return 'partial_success';
        }

        // Jika ada yang success/paid tapi tidak semua
        if ((isset($statusCounts['success']) && $statusCounts['success'] > 0) || 
            (isset($statusCounts['paid']) && $statusCounts['paid'] > 0)) {
            return 'partial_success';
        }

        // Jika ada yang delivery
        if (isset($statusCounts['delivery']) && $statusCounts['delivery'] > 0) {
            return 'delivery';
        }

        // Default masih pending
        return 'pending';
    }


    /**
     * Tentukan status keseluruhan berdasarkan status item-item (untuk selling)
     */
    private function determineOverallStatus($statusCounts, $totalItems)
    {
        // Jika semua item success
        if (isset($statusCounts['success']) && $statusCounts['success'] == $totalItems) {
            return 'success';
        }

        // Jika ada yang failed/cancelled
        if (isset($statusCounts['cancelled']) || isset($statusCounts['failed'])) {
            $failedCount = ($statusCounts['cancelled'] ?? 0) + ($statusCounts['failed'] ?? 0);
            if ($failedCount == $totalItems) {
                return 'failed';
            }
            return 'partial_success';
        }

        // Jika ada yang success tapi tidak semua
        if (isset($statusCounts['success']) && $statusCounts['success'] > 0) {
            return 'partial_success';
        }

        // Default masih pending
        return 'pending';
    }

    /**
     * Map status payment ke status progress
     */
    private function mapToProgressStatus($paymentStatus)
    {
        $mapping = [
            'pending' => TokoPaymentProgress::STATUS_PENDING,
            'paid' => TokoPaymentProgress::STATUS_PAID,
            'partial_success' => TokoPaymentProgress::STATUS_PARTIAL_SUCCESS,
            'delivery' => TokoPaymentProgress::STATUS_DELIVERY,
            'success' => TokoPaymentProgress::STATUS_SUCCESS,
            'failed' => TokoPaymentProgress::STATUS_FAILED,
            'cancelled' => TokoPaymentProgress::STATUS_CANCELLED,
            'refund_requested' => TokoPaymentProgress::STATUS_REFUND_REQUESTED,
            'refunded' => TokoPaymentProgress::STATUS_REFUNDED,
            'unknown' => TokoPaymentProgress::STATUS_UNKNOWN,
        ];

        return $mapping[$paymentStatus] ?? TokoPaymentProgress::STATUS_UNKNOWN;
    }


    /**
     * Mark semua item dalam transaksi payment sebagai success
     */
    public function markAllPaymentItemsAsSuccess($paymentId, $notes = null)
    {
        return DB::transaction(function () use ($paymentId, $notes) {
            $items = TokoPesanan::where('payment_id', $paymentId)
                ->whereNotIn('status', ['success', 'cancelled'])
                ->get();

            foreach ($items as $item) {
                $item->status = 'success';
                $item->actual_delivery = now();
                if ($notes) {
                    $item->notes = $notes;
                }
                $item->save();
            }

            // Update status keseluruhan
            $this->updatePaymentOverallStatus($paymentId);

            // Log mark all as success
            $this->logPaymentProgress($paymentId, TokoPaymentProgress::STATUS_SUCCESS, $notes ?: 'All items marked as success');

            return $items->count();
        });
    }

    /**
     * Mark semua item dalam transaksi selling sebagai success
     */
    public function markAllSellingItemsAsSuccess($transactionId, $notes = null)
    {
        return DB::transaction(function () use ($transactionId, $notes) {
            $items = TokoSellingDetail::where('transaction_id', $transactionId)
                ->whereNotIn('item_status', ['success', 'cancelled'])
                ->get();

            foreach ($items as $item) {
                $item->item_status = 'success';
                $item->actual_delivery = now();
                if ($notes) {
                    $item->notes = $notes;
                }
                $item->save();
            }

            // Update status keseluruhan
            $this->updateSellingOverallStatus($transactionId);

            return $items->count();
        });
    }

    /**
     * Get summary status transaksi payment
     */
    public function getPaymentTransactionSummary($paymentId)
    {
        $payment = TokoPayment::with(['pesanan.barangki.barang', 'user', 'toko'])->find($paymentId);

        if (!$payment) {
            throw new \Exception('Payment tidak ditemukan');
        }

        $items = $payment->pesanan;
        $statusCounts = $items->groupBy('status')->map->count();
        $totalItems = $items->count();

        // Get progress history
        $progressHistory = TokoPaymentProgress::where('payment_id', $paymentId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'payment' => $payment,
            'total_items' => $totalItems,
            'status_breakdown' => $statusCounts,
            'items' => $items,
            'completion_percentage' => $totalItems > 0 ? round(($statusCounts['success'] ?? 0) / $totalItems * 100, 2) : 0,
            'progress_history' => $progressHistory,
            'has_problems' => $statusCounts['problem'] ?? 0 > 0,
        ];
    }

    /**
     * Get summary status transaksi selling
     */
    public function getSellingTransactionSummary($transactionId)
    {
        $selling = TokoSelling::with(['details.barang', 'user', 'toko'])
            ->where('increment_id', $transactionId)
            ->first();

        if (!$selling) {
            throw new \Exception('Selling transaction tidak ditemukan');
        }

        $items = $selling->details;
        $statusCounts = $items->groupBy('item_status')->map->count();
        $totalItems = $items->count();

        return [
            'selling' => $selling,
            'total_items' => $totalItems,
            'status_breakdown' => $statusCounts,
            'items' => $items,
            'completion_percentage' => $totalItems > 0 ? round(($statusCounts['success'] ?? 0) / $totalItems * 100, 2) : 0,
            'has_problems' => $statusCounts['problem'] ?? 0 > 0 || $statusCounts['delayed'] ?? 0 > 0,
        ];
    }

    /**
     * Log payment progress
     */
    private function logPaymentProgress($paymentId, $status, $keterangan = null)
    {
        TokoPaymentProgress::create([
            'payment_id' => $paymentId,
            'status' => $status,
            'keterangan' => $keterangan,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Get riwayat progress payment
     */
    public function getPaymentProgressHistory($paymentId)
    {
        return TokoPaymentProgress::where('payment_id', $paymentId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Update payment status dengan progress log
     */
    public function updatePaymentStatus($paymentId, $newStatus, $keterangan = null)
    {
        return DB::transaction(function () use ($paymentId, $newStatus, $keterangan) {
            $payment = TokoPayment::find($paymentId);

            if (!$payment) {
                throw new \Exception('Payment tidak ditemukan');
            }

            $oldStatus = $payment->status;
            $payment->status = $newStatus;
            $payment->save();

            // Log progress
            $progressStatus = $this->mapToProgressStatus($newStatus);
            $this->logPaymentProgress($paymentId, $progressStatus, $keterangan ?: "Payment status changed from {$oldStatus} to {$newStatus}");

            return $payment;
        });
    }

    /**
     * Batch update items dengan progress tracking
     */
    public function batchUpdatePaymentItems($paymentId, $updates)
    {
        return DB::transaction(function () use ($paymentId, $updates) {
            $results = [];

            foreach ($updates as $update) {
                $pesanan = $this->updatePaymentItemStatus(
                    $paymentId,
                    $update['item_id'],
                    $update['status'],
                    $update['notes'] ?? null,
                    isset($update['estimated_delivery']) ? Carbon::parse($update['estimated_delivery']) : null
                );

                $results[] = $pesanan;
            }

            // Log batch update
            $this->logPaymentProgress(
                $paymentId,
                TokoPaymentProgress::STATUS_PARTIAL_SUCCESS,
                'Batch update completed for ' . count($updates) . ' items'
            );

            return $results;
        });
    }
}
