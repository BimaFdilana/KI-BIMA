<?php

namespace App\Http\Controllers\Api\Infaq;

use App\Http\Controllers\Controller;
use App\Models\Infaq\InfaqHistory;
use App\Models\Infaq\InfaqList;
use App\Services\Message\NotificationService;
use App\Services\Toko\TokoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InfaqController extends Controller
{
    protected $tokoService;
    protected $notificationService;

    public function __construct(TokoService $tokoService, NotificationService $notificationService)
    {
        $this->tokoService = $tokoService;
        $this->notificationService = $notificationService;
    }

    /**
     * Mendapatkan daftar pos infaq yang aktif
     */
    public function infaqList(Request $request)
    {
        try {
            // Get query parameters
            $category = $request->get('category');
            $search = $request->get('search');
            $perPage = $request->get('per_page', 10);
            $defaultImage = asset('images/default-infaq.jpg'); // Make sure this image exists in your public/images directory

            // Build query
            $query = InfaqList::active()
                ->with(['infaqHistories' => function ($q) {
                    $q->completed();
                }])
                ->with(['infaqImages' => function ($q) {
                    $q->orderBy('is_main', 'desc'); // Sort by is_main (true first)
                }]);

            // Filter by category
            if ($category) {
                $query->byCategory($category);
            }

            // Search by name or description
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            $infaqList = $query->paginate($perPage);

            // Add calculated fields
            $infaqList->getCollection()->transform(function ($item) use ($defaultImage) {
                // Process images - use main image first, then others
                $images = $item->infaqImages->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'image_path' => asset($image->image_path),
                        'is_main' => (bool)$image->is_main
                    ];
                })->toArray();

                // If no images, use default
                if (empty($images)) {
                    $images[] = [
                        'id' => 0,
                        'image_path' => $defaultImage,
                        'is_main' => true
                    ];
                }

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'category' => $item->category,
                    'category_label' => $item->category_label,
                    'is_active' => $item->is_active,
                    'total_donations' => $item->total_donations,
                    'dana_dibutuhkan' => $item->dana_dibutuhkan,
                    'dana_kurang' => $item->dana_dibutuhkan - $item->total_donations,
                    'donors_count' => $item->donors_count,
                    'images' => $images,
                    'main_image' => !empty($images) ? $images[0]['image_path'] : $defaultImage, // For backward compatibility
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Daftar pos infaq berhasil diambil',
                'data' => $infaqList,
                'categories' => InfaqList::getCategories()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar infaq',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Membuat donasi infaq
     */
    public function createDonation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'infaq_list_id' => 'required|exists:infaq_lists,id',
            'amount' => 'required|numeric|min:1000', // Minimum Rp 1.000
            'donor_name' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cash,transfer,digital_wallet,qris',
            'selling_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $toko = $this->tokoService->getTokoByUser($user);

            if (!$toko) {
                return response()->json([
                    'success' => false,
                    'message' => 'Toko tidak ditemukan'
                ], 404);
            }

            // Cek apakah infaq list masih aktif
            $infaqList = InfaqList::active()->find($request->infaq_list_id);
            if (!$infaqList) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pos infaq tidak ditemukan atau tidak aktif'
                ], 404);
            }

            DB::beginTransaction();

            // Create infaq history
            $infaqHistory = InfaqHistory::create([
                'toko_id' => $toko->id,
                'user_id' => $user->id,
                'infaq_list_id' => $request->infaq_list_id,
                'amount' => $request->amount,
                'status' => InfaqHistory::STATUS_PENDING,
                'donor_name' => $request->donor_name ?: 'Hamba Allah',
                'note' => $request->note,
                'payment_method' => $request->payment_method,
                'selling_id' => $request->selling_id,
            ]);

            // Auto-complete untuk pembayaran cash
            if ($request->payment_method === 'cash') {
                $infaqHistory->markAsCompleted();
            }

            DB::commit();

            // Send notification
            try {
                $this->notificationService->sendInfaqNotification($infaqHistory);
            } catch (\Exception $e) {
                // Log notification error but don't fail the transaction
                \Log::error('Failed to send infaq notification: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Donasi infaq berhasil dibuat',
                'data' => [
                    'id' => $infaqHistory->id,
                    'infaq_list' => $infaqList->name,
                    'amount' => $infaqHistory->formatted_amount,
                    'donor_name' => $infaqHistory->donor_name,
                    'status' => $infaqHistory->status_label,
                    'payment_method' => $infaqHistory->payment_method_label,
                    'created_at' => $infaqHistory->created_at
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat donasi infaq',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan history donasi
     */
    public function donationHistory(Request $request)
    {
        try {
            $user = Auth::user();
            $toko = $this->tokoService->getTokoByUser($user);

            if (!$toko) {
                return response()->json([
                    'success' => false,
                    'message' => 'Toko tidak ditemukan'
                ], 404);
            }

            $query = InfaqHistory::with(['infaqList', 'user'])
                ->byToko($toko->id);

            // Filter by status
            if ($request->has('status')) {
                $query->byStatus($request->status);
            }

            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->byDateRange($request->start_date, $request->end_date);
            }

            // Filter by month and year
            if ($request->has('month') && $request->has('year')) {
                $query->byMonth($request->month, $request->year);
            }

            // Search by donor name
            if ($request->has('search')) {
                $query->where('donor_name', 'LIKE', "%{$request->search}%");
            }

            $perPage = $request->get('per_page', 15);
            $donations = $query->latest()->paginate($perPage);

            // Transform data
            $totalPendingDonation = 0;
            $totalCompletedDonation = 0;
            $totalFailedDonation = 0;
            $totalCancelledDonation = 0;
            $donations->getCollection()->transform(function ($item) use (&$totalPendingDonation, &$totalCompletedDonation, &$totalFailedDonation, &$totalCancelledDonation) {
                $donationAmount = (float)$item->amount;
                switch ($item->status) {
                    case InfaqHistory::STATUS_PENDING:
                        $totalPendingDonation += $donationAmount;
                        break;
                    case InfaqHistory::STATUS_COMPLETED:
                        $totalCompletedDonation += $donationAmount;
                        break;
                    case InfaqHistory::STATUS_FAILED:
                        $totalFailedDonation += $donationAmount;
                        break;
                    case InfaqHistory::STATUS_CANCELLED:
                        $totalCancelledDonation += $donationAmount;
                        break;
                }
                return [
                    'id' => $item->id,
                    'infaq_list' => [
                        'id' => $item->infaqList->id,
                        'name' => $item->infaqList->name,
                        'category' => $item->infaqList->category_label
                    ],
                    'amount' => $item->formatted_amount,
                    'donor_name' => $item->donor_name,
                    'status' => $item->status,
                    'status_label' => $item->status_label,
                    'payment_method' => $item->payment_method,
                    'payment_method_label' => $item->payment_method_label,
                    'note' => $item->note,
                    'selling_id' => $item->selling_id,
                    'created_at' => $item->created_at,
                ];
            });
            $pendingDonation = $donations->where('status', 'pending')->count();
            $completedDonation = $donations->where('status', 'completed')->count();
            $failedDonation = $donations->where('status', 'failed')->count();
            $cancelledDonation = $donations->where('status', 'cancelled')->count();
            return response()->json([
                'success' => true,
                'message' => 'History donasi berhasil diambil',
                'pending_donation' => $pendingDonation,
                'total_pending_donation' => 'Rp' . number_format($totalPendingDonation, 0, ',', '.'),
                'completed_donation' => $completedDonation,
                'total_completed_donation' => 'Rp' . number_format($totalCompletedDonation, 0, ',', '.'),
                'failed_donation' => $failedDonation,
                'total_failed_donation' => 'Rp' . number_format($totalFailedDonation, 0, ',', '.'),
                'cancelled_donation' => $cancelledDonation,
                'total_cancelled_donation' => 'Rp' . number_format($totalCancelledDonation, 0, ',', '.'),
                'data' => $donations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil history donasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update status donasi
     */
    public function updateDonationStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:completed,failed,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $toko = $this->tokoService->getTokoByUser($user);

            $donation = InfaqHistory::byToko($toko->id)->find($id);

            if (!$donation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Donasi tidak ditemukan'
                ], 404);
            }

            if (!$donation->canChangeStatus()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status donasi tidak dapat diubah'
                ], 400);
            }

            // Update status
            switch ($request->status) {
                case 'completed':
                    $donation->markAsCompleted();
                    break;
                case 'failed':
                    $donation->markAsFailed();
                    break;
                case 'cancelled':
                    $donation->markAsCancelled();
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Status donasi berhasil diupdate',
                'data' => [
                    'id' => $donation->id,
                    'status' => $donation->status,
                    'status_label' => $donation->status_label,
                    'updated_at' => $donation->updated_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update status donasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Statistik infaq
     */
    public function statistics(Request $request)
    {
        try {
            $user = Auth::user();
            $toko = $this->tokoService->getTokoByUser($user);

            if (!$toko) {
                return response()->json([
                    'success' => false,
                    'message' => 'Toko tidak ditemukan'
                ], 404);
            }

            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $lastMonth = Carbon::now()->subMonth()->month;
            $lastMonthYear = Carbon::now()->subMonth()->year;

            // Total statistics
            $totalCompleted = InfaqHistory::byToko($toko->id)
                ->completed()
                ->sum('amount');

            $totalDonations = InfaqHistory::byToko($toko->id)
                ->completed()
                ->count();

            $uniqueDonors = InfaqHistory::byToko($toko->id)
                ->completed()
                ->distinct('user_id')
                ->count('user_id');

            // Monthly statistics
            $currentMonthTotal = InfaqHistory::byToko($toko->id)
                ->completed()
                ->byMonth($currentMonth, $currentYear)
                ->sum('amount');

            $lastMonthTotal = InfaqHistory::byToko($toko->id)
                ->completed()
                ->byMonth($lastMonth, $lastMonthYear)
                ->sum('amount');

            // Category statistics
            $categoryStats = InfaqHistory::byToko($toko->id)
                ->completed()
                ->with('infaqList')
                ->get()
                ->groupBy('infaqList.category')
                ->map(function ($group) {
                    return [
                        'total_amount' => $group->sum('amount'),
                        'total_donations' => $group->count(),
                        'category_label' => $group->first()->infaqList->category_label
                    ];
                });

            // Top infaq lists
            $topInfaqLists = InfaqList::withCount(['infaqHistories as total_donations' => function ($q) use ($toko) {
                $q->byToko($toko->id)->completed();
            }])
                ->with(['infaqHistories' => function ($q) use ($toko) {
                    $q->byToko($toko->id)->completed();
                }])
                ->get()
                ->map(function ($item) {
                    $totalAmount = $item->infaqHistories->sum('amount');
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'category' => $item->category_label,
                        'total_amount' => $totalAmount,
                        'formatted_amount' => 'Rp' . number_format($totalAmount, 0, ',', '.'),
                        'total_donations' => $item->total_donations,
                        'donors_count' => $item->donors_count
                    ];
                })
                ->sortByDesc('total_amount')
                ->take(5)
                ->values();

            return response()->json([
                'success' => true,
                'message' => 'Statistik infaq berhasil diambil',
                'data' => [
                    'summary' => [
                        'total_amount' => $totalCompleted,
                        'formatted_total_amount' => 'Rp' . number_format($totalCompleted, 0, ',', '.'),
                        'total_donations' => $totalDonations,
                        'unique_donors' => $uniqueDonors,
                    ],
                    'monthly' => [
                        'current_month' => [
                            'amount' => $currentMonthTotal,
                            'formatted_amount' => 'Rp' . number_format($currentMonthTotal, 0, ',', '.'),
                            'month' => Carbon::now()->format('F Y')
                        ],
                        'last_month' => [
                            'amount' => $lastMonthTotal,
                            'formatted_amount' => 'Rp' . number_format($lastMonthTotal, 0, ',', '.'),
                            'month' => Carbon::now()->subMonth()->format('F Y')
                        ],
                        'growth_percentage' => $lastMonthTotal > 0
                            ? round((($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100, 2)
                            : 0
                    ],
                    'categories' => $categoryStats,
                    'top_infaq_lists' => $topInfaqLists
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik infaq',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detail donasi
     */
    public function donationDetail($id)
    {
        try {
            $user = Auth::user();
            $toko = $this->tokoService->getTokoByUser($user);

            $donation = InfaqHistory::with(['infaqList', 'user', 'selling'])
                ->byToko($toko->id)
                ->find($id);

            if (!$donation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Donasi tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail donasi berhasil diambil',
                'data' => [
                    'id' => $donation->id,
                    'infaq_list' => [
                        'id' => $donation->infaqList->id,
                        'name' => $donation->infaqList->name,
                        'description' => $donation->infaqList->description,
                        'category' => $donation->infaqList->category,
                        'category_label' => $donation->infaqList->category_label,
                    ],
                    'amount' => $donation->amount,
                    'formatted_amount' => $donation->formatted_amount,
                    'donor_name' => $donation->donor_name,
                    'status' => $donation->status,
                    'status_label' => $donation->status_label,
                    'payment_method' => $donation->payment_method,
                    'payment_method_label' => $donation->payment_method_label,
                    'note' => $donation->note,
                    'selling_id' => $donation->selling_id,
                    'can_change_status' => $donation->canChangeStatus(),
                    'created_at' => $donation->created_at,
                    'updated_at' => $donation->updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail donasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
