<?php

namespace App\Services\Toko;

use App\Models\Toko\TokoPayment;
use App\Models\Toko\TokoPaymentProgress;
use App\Models\Toko\TokoPesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TokoPaymentProgressService
{
    /**
     * Create a new payment progress entry
     *
     * @param int $paymentId
     * @param string $status
     * @param string|null $keterangan
     * @param int|null $userId
     * @return TokoPaymentProgress
     * @throws \Exception
     */
    public function create(int $paymentId, string $status, ?string $keterangan = null, ?int $userId = null): TokoPaymentProgress
    {
        try {
            DB::beginTransaction();

            // Verify payment exists and get current status
            $payment = TokoPayment::findOrFail($paymentId);
            $oldStatus = $payment->status;

            $userId = $userId ?? Auth::id();

            // Create progress entry
            $progress = TokoPaymentProgress::create([
                'payment_id' => $paymentId,
                'status' => $status,
                'keterangan' => $this->getDefaultKeterangan($status),
                'user_id' => $userId,
            ]);

            // Update payment status
            $payment->update(['status' => $status]);

            // Update all related pesanan status
            $this->updateRelatedPesananStatus($paymentId, $status, $keterangan, $userId);

            DB::commit();
            return $progress;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Failed to create payment progress: " . $e->getMessage());
        }
    }

    /**
     * Update an existing payment progress entry
     *
     * @param int $progressId
     * @param array $data
     * @return TokoPaymentProgress
     * @throws \Exception
     */
    public function update(int $progressId, array $data): TokoPaymentProgress
    {
        try {
            DB::beginTransaction();

            $progress = TokoPaymentProgress::findOrFail($progressId);
            $payment = TokoPayment::findOrFail($progress->payment_id);
            $oldStatus = $payment->status;

            // Only update allowed fields
            $allowedFields = ['status', 'keterangan', 'user_id'];
            $updateData = array_intersect_key($data, array_flip($allowedFields));

            $progress->update($updateData);

            // If status is being updated
            if (isset($updateData['status']) && $updateData['status'] !== $oldStatus) {
                $newStatus = $updateData['status'];

                // Update payment status
                $payment->update(['status' => $newStatus]);

                // Update all related pesanan status
                $this->updateRelatedPesananStatus($progress->payment_id, $newStatus, $updateData['keterangan'] ?? null, $updateData['user_id'] ?? Auth::id());
            }

            DB::commit();
            return $progress->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Failed to update payment progress: " . $e->getMessage());
        }
    }

    /**
     * Update payment status and create progress entry
     *
     * @param int $paymentId
     * @param string $status
     * @param string|null $keterangan
     * @param int|null $userId
     * @return TokoPaymentProgress
     * @throws \Exception
     */
    public function updatePaymentStatus(int $paymentId, string $status, ?string $keterangan = null, ?int $userId = null): TokoPaymentProgress
    {
        try {
            DB::beginTransaction();

            // Get payment and old status
            $payment = TokoPayment::findOrFail($paymentId);
            $oldStatus = $payment->status;

            // Validate status transition
            if (!$this->isValidStatusTransition($oldStatus, $status)) {
                throw new \Exception("Invalid status transition from {$oldStatus} to {$status}");
            }

            // Update payment status
            $payment->update(['status' => $status]);

            // Create progress entry
            $progress = TokoPaymentProgress::create([
                'payment_id' => $paymentId,
                'status' => $status,
                'keterangan' => $this->getDefaultKeterangan($status),
                'user_id' => $userId ?? Auth::id(),
            ]);

            // Update all related pesanan status
            $this->updateRelatedPesananStatus($paymentId, $status, $keterangan, $userId ?? Auth::id());

            DB::commit();
            return $progress;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Failed to update payment status: " . $e->getMessage());
        }
    }

    /**
     * Update related pesanan status based on payment status
     *
     * @param int $paymentId
     * @param string $paymentStatus
     * @param string $keterangan
     * @param int $userId
     * @return void
     */
    private function updateRelatedPesananStatus(int $paymentId, string $paymentStatus, ?string $keterangan = null, int $userId): void
    {
        $pesananStatus = $this->mapPaymentStatusToPesananStatus($paymentStatus);

        if ($pesananStatus) {
            $pesananList = TokoPesanan::where('payment_id', $paymentId)->get();

            foreach ($pesananList as $pesanan) {
                $oldPesananStatus = $pesanan->status;

                // Update pesanan status
                $pesanan->update(['status' => $pesananStatus]);
                $pesanan->update(['admin_note' => $keterangan]);
                // Update timestamps based on status
                if ($pesananStatus === 'success') {
                    $pesanan->update(['actual_delivery' => now()]);
                }
            }
        }
    }

    /**
     * Map payment status to pesanan status
     *
     * @param string $paymentStatus
     * @return string|null
     */
    private function mapPaymentStatusToPesananStatus(string $paymentStatus): ?string
    {
        return match ($paymentStatus) {
            TokoPaymentProgress::STATUS_PENDING => 'pending',
            TokoPaymentProgress::STATUS_PAID => 'pending',
            TokoPaymentProgress::STATUS_DELIVERY => 'delivery',
            TokoPaymentProgress::STATUS_SUCCESS => 'success',
            TokoPaymentProgress::STATUS_CANCELLED => 'cancelled',
            TokoPaymentProgress::STATUS_FAILED => 'cancelled',
            default => null,
        };
    }

    /**
     * Delete a payment progress entry
     *
     * @param int $progressId
     * @return bool
     * @throws \Exception
     */
    public function delete(int $progressId): bool
    {
        try {
            DB::beginTransaction();

            $progress = TokoPaymentProgress::findOrFail($progressId);
            $deleted = $progress->delete();

            DB::commit();
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Failed to delete payment progress: " . $e->getMessage());
        }
    }

    /**
     * Add multiple progress entries at once
     *
     * @param int $paymentId
     * @param array $progressData Array of ['status' => string, 'keterangan' => string]
     * @param int|null $userId
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    public function createBulk(int $paymentId, array $progressData, ?int $userId = null): \Illuminate\Support\Collection
    {
        try {
            DB::beginTransaction();

            // Verify payment exists
            $payment = TokoPayment::findOrFail($paymentId);
            $userId = $userId ?? Auth::id();
            $progressEntries = collect();

            foreach ($progressData as $data) {
                $progress = $this->create($paymentId, $data['status'], $data['keterangan'] ?? null, $userId);
                $progressEntries->push($progress);
            }

            DB::commit();
            return $progressEntries;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Failed to create bulk payment progress: " . $e->getMessage());
        }
    }

    /**
     * Get all progress entries for a payment
     *
     * @param int $paymentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByPayment(int $paymentId): \Illuminate\Database\Eloquent\Collection
    {
        return TokoPaymentProgress::where('payment_id', $paymentId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get the latest progress entry for a payment
     *
     * @param int $paymentId
     * @return TokoPaymentProgress|null
     */
    public function getLatestByPayment(int $paymentId): ?TokoPaymentProgress
    {
        return TokoPaymentProgress::where('payment_id', $paymentId)
            ->with('user')
            ->latest('updated_at')
            ->first();
    }

    /**
     * Get default keterangan based on status
     *
     * @param string $status
     * @return string
     */
    private function getDefaultKeterangan(string $status): string
    {
        return match ($status) {
            TokoPaymentProgress::STATUS_PAID => 'Pesanan telah dibayar',
            TokoPaymentProgress::STATUS_PENDING => 'Menunggu pembayaran',
            TokoPaymentProgress::STATUS_SUCCESS => 'Pembayaran telah selesai',
            TokoPaymentProgress::STATUS_DELIVERY => 'Pesanan sedang dikirim',
            TokoPaymentProgress::STATUS_CANCELLED => 'Pesanan dibatalkan',
            TokoPaymentProgress::STATUS_REFUND_REQUESTED => 'Permintaan pengembalian dana',
            TokoPaymentProgress::STATUS_REFUNDED => 'Dana telah dikembalikan',
            default => 'Status diperbarui',
        };
    }

    /**
     * Get all available statuses
     *
     * @return array
     */
    public function getAvailableStatuses(): array
    {
        return [
            TokoPaymentProgress::STATUS_PAID,
            TokoPaymentProgress::STATUS_PENDING,
            TokoPaymentProgress::STATUS_FAILED,
            TokoPaymentProgress::STATUS_UNKNOWN,
            TokoPaymentProgress::STATUS_SUCCESS,
            TokoPaymentProgress::STATUS_DELIVERY,
            TokoPaymentProgress::STATUS_CANCELLED,
            TokoPaymentProgress::STATUS_REFUND_REQUESTED,
            TokoPaymentProgress::STATUS_REFUNDED,
        ];
    }

    /**
     * Check if a status transition is valid
     *
     * @param string $currentStatus
     * @param string $newStatus
     * @return bool
     */
    public function isValidStatusTransition(string $currentStatus, string $newStatus): bool
    {
        $validTransitions = [
            TokoPaymentProgress::STATUS_PENDING => [
                TokoPaymentProgress::STATUS_PAID,
                TokoPaymentProgress::STATUS_FAILED,
                TokoPaymentProgress::STATUS_UNKNOWN,
                TokoPaymentProgress::STATUS_CANCELLED,
            ],
            TokoPaymentProgress::STATUS_PAID => [
                TokoPaymentProgress::STATUS_REFUND_REQUESTED,
                TokoPaymentProgress::STATUS_DELIVERY,
            ],
            TokoPaymentProgress::STATUS_REFUND_REQUESTED => [
                TokoPaymentProgress::STATUS_REFUNDED,
                TokoPaymentProgress::STATUS_PENDING,
            ],
            TokoPaymentProgress::STATUS_DELIVERY => [
                TokoPaymentProgress::STATUS_CANCELLED,
                TokoPaymentProgress::STATUS_SUCCESS,
            ],
            TokoPaymentProgress::STATUS_SUCCESS => [
                TokoPaymentProgress::STATUS_CANCELLED,
                TokoPaymentProgress::STATUS_REFUND_REQUESTED,
            ],
            TokoPaymentProgress::STATUS_REFUNDED => [],
            TokoPaymentProgress::STATUS_CANCELLED => [
                TokoPaymentProgress::STATUS_REFUND_REQUESTED,
            ],
        ];

        return in_array($newStatus, $validTransitions[$currentStatus] ?? []);
    }
}
