<?php

namespace App\Http\Controllers\Admin;

use App\Models\Toko\TokoModel;
use App\Models\Toko\TokoPayment;
use App\Models\Toko\TokoPaymentProgress;
use App\Services\Message\NotificationService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class AdminApprovalController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function getToko(): JsonResponse
    {
        $toko = TokoModel::where('status', 'active')
            ->select(['id', 'name', 'slug', 'address', 'latitude', 'longitude', 'image', 'description'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'address' => $item->address,
                    'latitude' => (float) ($item->latitude ?? 0),
                    'longitude' => (float) ($item->longitude ?? 0),
                    'image' => $item->image ? asset('storage/' . $item->image) : asset('images/default-logo.png'),
                    'description' => substr(($item->description ?? ''), 0, 100) . '...',
                ];
            });
        return response()->json([
            'success' => true,
            'data' => $toko,
            'count' => $toko->count(),
        ]);
    }

    // Menampilkan detail toko
    public function show($slug): View
    {
        $toko = TokoModel::where('slug', $slug)->firstOrFail();
        return view('toko.detail', compact('toko'));
    }

    public function index()
    {
        return view('login.dashboard-approval');
    }
    /**
     * Get all pending toko registrations
     */
    public function getPendingToko(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $perPage = $request->input('per_page', 20);
            $page = $request->input('page', 1);

            $query = TokoModel::where('status', 'pending')
                ->with(['owner', 'users'])
                ->orderBy('created_at', 'desc');

            if (!empty($search)) {
                $searchTerm = '%' . $search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                        ->orWhere('address', 'like', $searchTerm)
                        ->orWhereHas('owner', function ($subq) use ($searchTerm) {
                            $subq->where('name', 'like', $searchTerm)
                                ->orWhere('email', 'like', $searchTerm);
                        });
                });
            }

            $total = $query->count();
            $tokos = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'message' => 'Data toko pending berhasil diambil',
                'data' => $tokos->items(),
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $tokos->lastPage(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error getPendingToko: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data toko pending',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detail toko untuk review
     */
    public function getTokoDetail($tokoId)
    {
        try {
            $toko = TokoModel::with(['owner', 'users', 'barangs'])->findOrFail($tokoId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $toko->id,
                    'name' => $toko->name,
                    'slug' => $toko->slug,
                    'address' => $toko->address,
                    'description' => $toko->description,
                    'status' => $toko->status,
                    'latitude' => $toko->latitude,
                    'longitude' => $toko->longitude,
                    'token' => $toko->token,
                    'created_at' => $toko->created_at,
                    'owner' => [
                        'id' => $toko->owner->id ?? null,
                        'name' => $toko->owner->name ?? '-',
                        'email' => $toko->owner->email ?? '-',
                        'phone_number' => $toko->owner->phone_number ?? '-',
                    ],
                    'employees_count' => $toko->users->count(),
                    'products_count' => $toko->barangs->count(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getTokoDetail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Approve toko registration
     */
    public function approveToko(Request $request, $tokoId)
    {
        return DB::transaction(function () use ($request, $tokoId) {
            try {
                $toko = TokoModel::with('owner')->findOrFail($tokoId);

                if ($toko->status !== 'pending') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Status toko bukan pending'
                    ], 422);
                }

                // Update toko status
                $toko->update([
                    'status' => 'active',
                    'edited_by' => Auth::id()
                ]);

                // Update owner role to 'shop'
                if ($toko->owner) {
                    $toko->owner->syncRoles('shop');

                    // Send notification to owner
                    $this->notificationService->sendToUserFromSystem(
                        $toko->owner,
                        'toko_approved',
                        [
                            'message' => "Selamat! Toko '{$toko->name}' Anda telah disetujui dan sekarang aktif.",
                            'toko_name' => $toko->name,
                            'toko_id' => $toko->id
                        ]
                    );
                }

                // Log activity
                Log::info("Toko '{$toko->name}' (ID: {$toko->id}) approved by Admin ID: " . Auth::id());

                return response()->json([
                    'success' => true,
                    'message' => 'Toko berhasil disetujui',
                    'data' => [
                        'id' => $toko->id,
                        'name' => $toko->name,
                        'status' => $toko->status
                    ]
                ]);
            } catch (\Exception $e) {
                Log::error('Error approveToko: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyetujui toko',
                    'error' => $e->getMessage()
                ], 500);
            }
        });
    }

    /**
     * Reject toko registration
     */
    public function rejectToko(Request $request, $tokoId)
    {
        return DB::transaction(function () use ($request, $tokoId) {
            try {
                $validated = $request->validate([
                    'reason' => 'required|string|max:500'
                ]);

                $toko = TokoModel::with('owner')->findOrFail($tokoId);

                if ($toko->status !== 'pending') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Status toko bukan pending'
                    ], 422);
                }

                // Update toko status
                $toko->update([
                    'status' => 'suspend',
                    'edited_by' => Auth::id()
                ]);

                // Send notification to owner with reason
                if ($toko->owner) {
                    $this->notificationService->sendToUserFromSystem(
                        $toko->owner,
                        'toko_rejected',
                        [
                            'message' => "Pendaftaran toko '{$toko->name}' ditolak dengan alasan: {$validated['reason']}",
                            'toko_name' => $toko->name,
                            'reason' => $validated['reason']
                        ]
                    );
                }

                Log::info("Toko '{$toko->name}' (ID: {$toko->id}) rejected by Admin ID: " . Auth::id() . " - Reason: {$validated['reason']}");

                return response()->json([
                    'success' => true,
                    'message' => 'Toko berhasil ditolak',
                    'data' => [
                        'id' => $toko->id,
                        'name' => $toko->name,
                        'status' => $toko->status
                    ]
                ]);
            } catch (\Exception $e) {
                Log::error('Error rejectToko: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menolak toko',
                    'error' => $e->getMessage()
                ], 500);
            }
        });
    }

    // ============ PAYMENT VERIFICATION ============

    /**
     * Get pending payment verifications
     */
    public function getPendingPayments(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $paymentMethod = $request->input('payment_method', '');
            $perPage = $request->input('per_page', 20);
            $page = $request->input('page', 1);

            $query = TokoPayment::where('status', 'pending')
                ->with(['user', 'toko', 'progress' => function ($q) {
                    $q->latest()->limit(1);
                }])
                ->orderBy('created_at', 'desc');

            if (!empty($search)) {
                $searchTerm = '%' . $search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('transaction_id', 'like', $searchTerm)
                        ->orWhere('total', 'like', $searchTerm)
                        ->orWhereHas('user', function ($subq) use ($searchTerm) {
                            $subq->where('name', 'like', $searchTerm)
                                ->orWhere('email', 'like', $searchTerm);
                        })
                        ->orWhereHas('toko', function ($subq) use ($searchTerm) {
                            $subq->where('name', 'like', $searchTerm);
                        });
                });
            }

            if (!empty($paymentMethod)) {
                $query->where('payment_method', $paymentMethod);
            }

            $total = $query->count();
            $payments = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'message' => 'Data pembayaran pending berhasil diambil',
                'data' => $payments->items(),
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $payments->lastPage(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error getPendingPayments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pembayaran pending',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify/Confirm payment
     */
    public function confirmPayment(Request $request, $paymentId)
    {
        return DB::transaction(function () use ($request, $paymentId) {
            try {
                $validated = $request->validate([
                    'note' => 'nullable|string|max:500'
                ]);

                $payment = TokoPayment::with('user')->findOrFail($paymentId);

                if ($payment->status !== 'pending') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Status pembayaran bukan pending'
                    ], 422);
                }

                // Update payment status
                $payment->update(['status' => 'paid']);

                // Create progress record
                TokoPaymentProgress::create([
                    'payment_id' => $payment->id,
                    'status' => 'payment_confirmed',
                    'keterangan' => $validated['note'] ?? 'Pembayaran dikonfirmasi oleh admin',
                    'user_id' => Auth::id()
                ]);

                // Send notification to user
                $this->notificationService->sendToUserFromSystem(
                    $payment->user,
                    'payment_confirmed',
                    [
                        'message' => "Pembayaran Anda dengan ID Transaksi '{$payment->transaction_id}' telah dikonfirmasi.",
                        'transaction_id' => $payment->transaction_id,
                        'total' => $payment->total
                    ]
                );

                Log::info("Payment '{$payment->transaction_id}' confirmed by Admin ID: " . Auth::id());

                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran berhasil dikonfirmasi',
                    'data' => [
                        'id' => $payment->id,
                        'transaction_id' => $payment->transaction_id,
                        'status' => $payment->status
                    ]
                ]);
            } catch (\Exception $e) {
                Log::error('Error confirmPayment: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengkonfirmasi pembayaran',
                    'error' => $e->getMessage()
                ], 500);
            }
        });
    }

    /**
     * Reject/Cancel payment
     */
    public function rejectPayment(Request $request, $paymentId)
    {
        return DB::transaction(function () use ($request, $paymentId) {
            try {
                $validated = $request->validate([
                    'reason' => 'required|string|max:500'
                ]);

                $payment = TokoPayment::with('user')->findOrFail($paymentId);

                if ($payment->status !== 'pending') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Status pembayaran bukan pending'
                    ], 422);
                }

                // Update payment status
                $payment->update(['status' => 'failed']);

                // Create progress record
                TokoPaymentProgress::create([
                    'payment_id' => $payment->id,
                    'status' => 'payment_rejected',
                    'keterangan' => "Pembayaran ditolak: {$validated['reason']}",
                    'user_id' => Auth::id()
                ]);

                // Send notification to user
                $this->notificationService->sendToUserFromSystem(
                    $payment->user,
                    'payment_rejected',
                    [
                        'message' => "Pembayaran Anda dengan ID Transaksi '{$payment->transaction_id}' ditolak. Alasan: {$validated['reason']}",
                        'transaction_id' => $payment->transaction_id,
                        'reason' => $validated['reason']
                    ]
                );

                Log::info("Payment '{$payment->transaction_id}' rejected by Admin ID: " . Auth::id() . " - Reason: {$validated['reason']}");

                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran berhasil ditolak',
                    'data' => [
                        'id' => $payment->id,
                        'transaction_id' => $payment->transaction_id,
                        'status' => $payment->status
                    ]
                ]);
            } catch (\Exception $e) {
                Log::error('Error rejectPayment: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menolak pembayaran',
                    'error' => $e->getMessage()
                ], 500);
            }
        });
    }

    /**
     * Get approval dashboard summary
     */
    public function getApprovalSummary()
    {
        try {
            $pendingToko = TokoModel::where('status', 'pending')->count();
            $pendingPayments = TokoPayment::where('status', 'pending')->count();
            $activeToko = TokoModel::where('status', 'active')->count();
            $totalPayments = TokoPayment::count();

            $tokoTodayCount = TokoModel::where('status', 'pending')
                ->whereDate('created_at', Carbon::today())
                ->count();

            $paymentsTodayCount = TokoPayment::where('status', 'pending')
                ->whereDate('created_at', Carbon::today())
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'pending_toko' => $pendingToko,
                    'pending_payments' => $pendingPayments,
                    'active_toko' => $activeToko,
                    'total_payments' => $totalPayments,
                    'toko_today' => $tokoTodayCount,
                    'payments_today' => $paymentsTodayCount,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getApprovalSummary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan approval',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
