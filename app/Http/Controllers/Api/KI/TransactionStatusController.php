<?php

namespace App\Http\Controllers\Api\KI;

use App\Http\Controllers\Controller;
use App\Models\Toko\TokoPaymentProgress;
use App\Services\Toko\TokoService;
use App\Services\Toko\TransactionStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TransactionStatusController extends Controller
{
    protected $transactionStatusService;
    protected $tokoService;
    public function __construct(TransactionStatusService $transactionStatusService, TokoService $tokoService)
    {
        $this->transactionStatusService = $transactionStatusService;
        $this->tokoService = $tokoService;
    }

    public function batchUpdatePaymentItems(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|integer|exists:toko_payment,id',
            'items' => 'required|array',
            'items.*.item_id' => 'required|integer',
            'items.*.status' => 'required|in:paid,pending,failed,unknown,partial_success,success,delivery,cancelled,refund_requested,refunded',
            'items.*.notes' => 'nullable|string|max:1000',
            'items.*.estimated_delivery' => 'nullable|date|after:now'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan validasi',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $results = $this->transactionStatusService->batchUpdatePaymentItems(
                $request->payment_id,
                $request->items
            );

            return response()->json([
                'success' => true,
                'message' => 'Batch update berhasil',
                'data' => $results,
                'updated_count' => count($results)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk action untuk menangani multiple items sekaligus
     */
    public function bulkItemAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|integer|exists:toko_payment,id',
            'action' => 'required|in:mark_success,mark_problem,mark_delayed,mark_ready',
            'item_ids' => 'required|array',
            'item_ids.*' => 'integer|exists:toko_pesanan,id',
            'notes' => 'nullable|string|max:1000',
            'estimated_delivery' => 'nullable|date|after:now'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan validasi',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $paymentId = $request->payment_id;
            $action = $request->action;
            $itemIds = $request->item_ids;
            $notes = $request->notes;
            $estimatedDelivery = $request->estimated_delivery ? Carbon::parse($request->estimated_delivery) : null;

            // Map action ke status
            $statusMapping = [
                'mark_success' => 'success',
                'mark_problem' => 'problem',
                'mark_delayed' => 'delayed',
                'mark_ready' => 'ready'
            ];

            $newStatus = $statusMapping[$action];
            $updates = [];

            foreach ($itemIds as $itemId) {
                $updates[] = [
                    'item_id' => $itemId,
                    'status' => $newStatus,
                    'notes' => $notes,
                    'estimated_delivery' => $estimatedDelivery?->format('Y-m-d H:i:s')
                ];
            }

            $results = $this->transactionStatusService->batchUpdatePaymentItems($paymentId, $updates);

            return response()->json([
                'success' => true,
                'message' => "Berhasil melakukan {$action} untuk " . count($itemIds) . " items",
                'data' => $results,
                'updated_count' => count($results)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
