<?php

namespace App\Http\Controllers\Api\Toko;

use App\Models\Barang\SatuanConversion;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Toko\TokoPayment;
use App\Models\Toko\TokoPesanan;
use App\Models\Toko\TokoSelling;
use App\Models\Toko\TokoSellingDetail;
use App\Models\Toko\TokoUserModel;
use App\Services\Barang\BarangKIService;
use App\Services\Toko\TokoService;
use Carbon\Carbon;

class TokoDataController extends BaseController
{
    protected $tokoService;
    protected $barangKIService;
    protected $keranjangTokoService;

    public function __construct(TokoService $tokoService, BarangKIService $barangKIService)
    {
        $this->tokoService = $tokoService;
        $this->barangKIService = $barangKIService;
    }

    public function tokoKaryawan()
    {
        $user = auth()->user();

        // Cek apakah user ada di toko tertentu
        $toko = $this->tokoService->getTokoByUser($user);

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }

        // Ambil data karyawan berdasarkan toko_id dengan eager loading yang lebih efisien
        $karyawan = TokoUserModel::where('toko_id', $toko->id)
            ->with([
                'user:id,name,email,phone_number',
                'jabatan:id,name,level,can_invite_users,can_manage_inventory,can_view_reports,can_manage_orders',
            ])
            ->get()
            ->map(function ($karyawanItem) {
                $joinDate = $karyawanItem->created_at ? $karyawanItem->created_at->format('Y-m-d') : null;
                $joinFormatted = $karyawanItem->created_at ? $karyawanItem->created_at->isoFormat('D MMMM YYYY, HH:mm:ss') : 'Tidak ada data';

                return [
                    'user_id' => $karyawanItem->user_id,
                    'nama' => $karyawanItem->user->name,
                    'email' => $karyawanItem->user->email,
                    'phone_number' => $karyawanItem->user->phone_number,
                    'jabatan' => [
                        'id' => $karyawanItem->jabatan->id,
                        'name' => $karyawanItem->jabatan->name,
                        'level' => $karyawanItem->jabatan->level,
                    ],
                    'access' => [
                        'can_invite_users' => $karyawanItem->jabatan->can_invite_users,
                        'can_manage_inventory' => $karyawanItem->jabatan->can_manage_inventory,
                        'can_view_reports' => $karyawanItem->jabatan->can_view_reports,
                        'can_manage_orders' => $karyawanItem->jabatan->can_manage_orders,
                    ],
                    'status' => $karyawanItem->status,
                    'join_date' => $joinDate,
                    'join' => $joinFormatted,
                ];
            })
            ->sortByDesc(function ($item) {
                return $item['status'] === 'active' ? 1 : 0;
            })
            ->sortByDesc('level')
            ->sortBy('join_date')
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'toko' => [
                'id' => $toko->id,
                'owner_id' => $toko->owner_id,
                'name' => $toko->name,
                'latitude' => $toko->latitude,
                'longitude' => $toko->longitude,
                'address' => $toko->address,
                'status' => $toko->status,
                'created_at' => $toko->created_at->isoFormat('D MMMM YYYY, HH:mm:ss'),
                'updated_at' => $toko->updated_at->isoFormat('D MMMM YYYY, HH:mm:ss'),
                'data' => $karyawan,
            ],
        ], 200, ['Content-Type' => 'application/json']);
    }

    public function conversion()
    {
        $conversions = SatuanConversion::with(['barang', 'conversionFrom', 'conversionTo'])
            ->get()
            ->map(function ($conversion) {
                return [
                    'id' => $conversion->id,
                    'name' => $conversion->barang->name,
                    'from' => $conversion->conversionFrom->name,
                    'to' => $conversion->conversionTo->name,
                    'factor' => $conversion->conversion_factor,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $conversions,
        ], 200, ['Content-Type' => 'application/json']);
    }
    public function tokoBarang()
    {
        $user = auth()->user();

        // Cek apakah user ada di toko tertentu
        $toko = $this->tokoService->getTokoByUser($user);

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }

        $data = $this->tokoService->getBarangSimple($toko->id);


        return response()->json([
            'success' => true,
            'toko' => [
                'id' => $toko->id,
                'name' => $toko->name,
                'data' => $data,
            ],
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Get detailed information about a specific payment transaction
     *
     * @param string $transactionId The transaction ID to fetch details for
     * @return \Illuminate\Http\JsonResponse
     */
    public function tokoPaymentDetail($transactionId)
    {
        $user = auth()->user();

        // Cek apakah user ada di toko tertentu
        $toko = $this->tokoService->getTokoByUser($user);
        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }

        // Find the payment record
        $payment = TokoPayment::where('toko_id', $toko->id)
            ->where('transaction_id', $transactionId)
            ->with('progress')
            ->with('user:id,name,email,phone_number')
            ->first();
        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak ditemukan.',
            ], 404);
        }

        // Get all items from this payment
        $paymentItems = TokoPesanan::where('payment_id', $payment->id)
            ->with(['barangKI.barang', 'barangKI.satuan'])
            ->get()
            ->map(function ($item) {
                $barang = $item->barangKI->barang;
                $satuan = $item->barangKI->satuan;

                return [
                    'id' => $item->id,
                    'barang_id' => $item->barangki_id,
                    'nama_barang' => $barang->name ?? 'Barang tidak ditemukan',
                    'kode_barang' => $barang->sku ?? '-',
                    'satuan' => $satuan->name ?? 'Pcs',
                    'harga_satuan' => $item->price,
                    'quantity' => $item->quantity,
                    'total_harga' => $item->total,
                    'status' => $item->status,
                    'barcode' => $item->barangKI->id_barcode ?? '-',
                ];
            });

        // Format payment data
        $paymentData = [
            'id' => $payment->id,
            'transaction_id' => $payment->transaction_id,
            'customer' => [
                'id' => $payment->user_id,
                'name' => $payment->user->name ?? 'Unknown',
                'email' => $payment->user->email ?? '-',
                'phone' => $payment->user->phone_number ?? '-',
            ],
            'payment_info' => [
                'total' => $payment->total,
                'payment_type' => $payment->payment_type,
                'payment_method' => $payment->payment_method ?? $payment->payment_methode,
                'status' => $payment->status,
                'snap_token' => $payment->snap_token,
            ],
            'progress' => $payment->progress,
            'created_at' => $payment->created_at ? $payment->created_at->isoFormat('D MMMM YYYY, HH:mm:ss') : null,
            'updated_at' => $payment->updated_at ? $payment->updated_at->isoFormat('D MMMM YYYY, HH:mm:ss') : null,
            'item_count' => $paymentItems->count(),
            'items' => $paymentItems,
        ];

        return response()->json([
            'success' => true,
            'toko' => [
                'id' => $toko->id,
                'name' => $toko->name,
            ],
            'payment' => $paymentData,
        ], 200, ['Content-Type' => 'application/json']);
    }

    public function tokoPayment()
    {
        $user = auth()->user();

        // Cek apakah user ada di toko tertentu
        $toko = $this->tokoService->getTokoByUser($user);

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }
        $paymentData = TokoPayment::where('toko_id', $toko->id)
            ->where('status', '!=', 'unknown')
            ->where(function ($query) {
                $query->where('status', '!=', 'failed')
                    ->orWhere(function ($query) {
                        $query->where('status', 'failed')
                            ->where('created_at', '>=', Carbon::now()->subWeek());
                    });
            })
            ->with('user:id,name')
            ->get()
            ->map(function ($paymentItem) {
                $pesananData = TokoPesanan::where('payment_id', $paymentItem->id)->count();

                // Hitung jumlah barang yang belum dikonfirmasi (status bukan 'success')
                $unconfirmedItemCount = TokoPesanan::where('payment_id', $paymentItem->id)
                    ->where('status', '!=', 'success')
                    ->count();

                return [
                    'id_transaksi' => $paymentItem->transaction_id,
                    'pemesanan' => $paymentItem->user->name,
                    'status' => $paymentItem->status,
                    'total' => round($paymentItem->total),
                    'payment_methode' => $paymentItem->payment_methode,
                    'payment_type' => $paymentItem->payment_type,
                    'item' => $pesananData,
                    'created_at' => $paymentItem->created_at->isoFormat('D MMMM YYYY, HH:mm:ss'),
                    'snap_token' => $paymentItem->snap_token, // Untuk resume payment
                    'unconfirmed_item_count' => $unconfirmedItemCount, // Jumlah barang belum dikonfirmasi (bukan success)
                ];
            })
            ->sortByDesc(fn($karyawan) => $karyawan['created_at']);

        return response()->json([
            'success' => true,
            'toko' => [
                'id' => $toko->id,
                'name' => $toko->name,
                'data' => array_values($paymentData->toArray()),
            ],
        ], 200, ['Content-Type' => 'application/json']);
    }

    public function tokoSelling()
    {
        $user = auth()->user();

        // Cek apakah user ada di toko tertentu
        $toko = $this->tokoService->getTokoByUser($user);

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }
        $pesananData = TokoSelling::where('toko_id', $toko->id)
            ->where('status', '!=', 'unknown')
            ->where(function ($query) {
                $query->where('status', '!=', 'failed')
                    ->orWhere(function ($query) {
                        $query->where('status', 'failed')
                            ->where('created_at', '>=', Carbon::now()->subWeek());
                    });
            })
            ->with('user:id,name')
            ->get()
            ->map(function ($pesananItem) {
                return [
                    'id_transaksi' => $pesananItem->id_transaction,
                    'kasir' => $pesananItem->user->name,
                    'status' => $pesananItem->status,
                    'total' => round($pesananItem->total_harga),
                    'payment_methode' => $pesananItem->methode_pembayaran,
                    'item' =>
                    $pesananItem->details->map(function ($detail) {
                        return [
                            'barang_id' => $detail->barangki_id,
                            'nama_barang' => $detail->barangki->barang->name,
                            'kode_barang' => $detail->barangki->barang->sku,
                            'satuan' => $detail->barangki->satuan->name,
                            'harga_satuan' => $detail->harga_satuan,
                            'quantity' => $detail->jumlah,
                            'total_harga' => $detail->subtotal,
                        ];
                    }),
                    'created_at' => $pesananItem->created_at->isoFormat('D MMMM YYYY, HH:mm:ss'),
                ];
            })
            ->sortByDesc(fn($karyawan) => $karyawan['created_at']);

        return response()->json([
            'success' => true,
            'toko' => [
                'id' => $toko->id,
                'name' => $toko->name,
                'data' => array_values($pesananData->toArray()),
            ],
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Get management dashboard data
     */
    public function managementDashboard()
    {
        $user = auth()->user();

        // Get user's toko
        $toko = $this->tokoService->getTokoByUser($user);

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }

        // Check if user has can_invite_users permission
        $tokoUser = TokoUserModel::where('toko_id', $toko->id)
            ->where('user_id', $user->id)
            ->with('jabatan')
            ->first();

        if (!$tokoUser || !$tokoUser->jabatan->can_invite_users) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengakses manajemen.',
            ], 403);
        }

        // Count team members
        $teamCount = TokoUserModel::where('toko_id', $toko->id)->count();

        // Count pending invitations
        $pendingInvitations = \App\Models\Toko\TokoInvitation::where('toko_id', $toko->id)
            ->where('status', 'pending')
            ->count();

        // Get recent actions (staff added/removed in last 30 days)
        $recentActions = TokoUserModel::where('toko_id', $toko->id)
            ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))
            ->with(['user:id,name,email', 'jabatan:id,name'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'user_id' => $item->user_id,
                    'name' => $item->user->name,
                    'email' => $item->user->email,
                    'position' => $item->jabatan->name,
                    'action' => 'added',
                    'date' => $item->created_at->isoFormat('D MMMM YYYY, HH:mm:ss'),
                    'date_short' => $item->created_at->format('Y-m-d'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'team_count' => $teamCount,
                'pending_invitations' => $pendingInvitations,
                'recent_actions' => $recentActions,
                'stats' => [
                    'total_active' => TokoUserModel::where('toko_id', $toko->id)
                        ->where('status', 'active')
                        ->count(),
                ]
            ]
        ]);
    }
}
