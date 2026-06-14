<?php

namespace App\Http\Controllers;

use App\Models\Infaq\InfaqHistory;
use App\Models\Infaq\InfaqList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InfaqManagementController extends Controller
{
    public function index(Request $request)
    {
        // Tentukan rentang tanggal saat ini berdasarkan input user
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfYear();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfYear();

        // Hitung durasi rentang waktu untuk perbandingan periode sebelumnya
        $periodDurationInDays = $endDate->diffInDays($startDate);
        // Pastikan durasi minimal 1 hari
        if ($periodDurationInDays == 0) {
            $periodDurationInDays = 1;
        }

        // Tentukan rentang tanggal periode sebelumnya
        $previousStartDate = (clone $startDate)->subDays($periodDurationInDays);
        $previousEndDate = (clone $endDate)->subDays($periodDurationInDays);

        // --- Ambil data untuk periode saat ini ---
        $currentPeriodDonations = InfaqHistory::completed()
            ->byDateRange($startDate, $endDate)
            ->get();

        $totalAmount = $currentPeriodDonations->sum('amount');
        $completedDonationsCount = $currentPeriodDonations->count();
        $averageAmount = $completedDonationsCount > 0 ? $currentPeriodDonations->avg('amount') : 0;

        // Cek jumlah infaq aktif secara total, tidak berdasarkan tanggal
        $activeInfaqCount = InfaqList::active()->count();


        // --- Ambil data untuk periode sebelumnya ---
        $previousPeriodDonations = InfaqHistory::completed()
            ->byDateRange($previousStartDate, $previousEndDate)
            ->get();

        $previousTotalAmount = $previousPeriodDonations->sum('amount');
        $previousCompletedDonationsCount = $previousPeriodDonations->count();
        $previousAverageAmount = $previousCompletedDonationsCount > 0 ? $previousPeriodDonations->avg('amount') : 0;


        // --- Hitung persentase perubahan ---
        $totalAmountPercentageChange = $this->calculatePercentageChange($totalAmount, $previousTotalAmount);
        $completedDonationsPercentageChange = $this->calculatePercentageChange($completedDonationsCount, $previousCompletedDonationsCount);
        $averageAmountPercentageChange = $this->calculatePercentageChange($averageAmount, $previousAverageAmount);


        // --- Ambil data untuk grafik dan progress infaq ---
        $donationsByCategory = InfaqList::active()
            ->with(['infaqHistories' => function ($query) use ($startDate, $endDate) {
                $query->completed()->byDateRange($startDate, $endDate);
            }])
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category_label,
                    'total' => $item->infaqHistories->sum('amount'),
                ];
            })->filter(function ($item) {
                return $item['total'] > 0;
            });

        $donationsByPaymentMethod = InfaqHistory::completed()
            ->byDateRange($startDate, $endDate)
            ->selectRaw('payment_method, count(*) as count, sum(amount) as total_amount')
            ->groupBy('payment_method')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->payment_method_label,
                    'count' => $item->count,
                    'total' => $item->total_amount,
                ];
            });

        // Bagian ini menghitung total donasi sepanjang waktu untuk setiap infaq
        $infaqProgress = InfaqList::active()
            ->with('infaqImages')
            ->withSum(['infaqHistories as total_donations_all_time' => function ($query) {
                $query->where('status', 'completed');
            }], 'amount')
            ->get()
            ->map(function ($infaq) {
                $totalDonations = $infaq->total_donations_all_time ?? 0;
                $danaDibutuhkan = $infaq->dana_dibutuhkan;
                $isTercapai = $danaDibutuhkan > 0 && $totalDonations >= $danaDibutuhkan;

                return [
                    'id' => $infaq->id,
                    'name' => $infaq->name,
                    'initials' => $infaq->initials,
                    'category' => $infaq->category,
                    'description' => $infaq->description,
                    'dana_dibutuhkan' => $danaDibutuhkan,
                    'total_donations_sum' => $totalDonations,
                    'progress_percentage' => $danaDibutuhkan > 0 ? min(100, ($totalDonations / $danaDibutuhkan) * 100) : 0,
                    'is_completed' => $isTercapai,
                    'image_url' => $infaq->infaqImages->first()->url ?? null,
                ];
            });

        // Hitung jumlah infaq yang sudah tercapai
        $infaqTercapai = $infaqProgress->where('is_completed', true)->count();
        $infaqBelumTercapai = $infaqProgress->where('is_completed', false)->count();

        // Kirim data ke view
        return view('infaq.dashboard', compact(
            'totalAmount',
            'totalAmountPercentageChange',
            'completedDonationsCount',
            'completedDonationsPercentageChange',
            'activeInfaqCount',
            'averageAmount',
            'averageAmountPercentageChange',
            'donationsByCategory',
            'donationsByPaymentMethod',
            'infaqProgress',
            'infaqTercapai',
            'infaqBelumTercapai',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Fungsi pembantu untuk menghitung persentase perubahan.
     * Mencegah pembagian dengan nol.
     */
    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            // Jika nilai sebelumnya nol, dan sekarang ada, berarti naik 100%
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }

    // Metode getInfaqDonationHistory tidak perlu diubah karena sudah benar
    public function getInfaqDonationHistory(Request $request, $infaqId)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfYear();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfYear();

        $donations = InfaqHistory::completed()
            ->where('infaq_list_id', $infaqId)
            ->byDateRange($startDate, $endDate)
            ->with('toko')
            ->latest()
            ->get();

        $formattedDonations = $donations->map(function ($donation) {
            return [
                'toko_name' => $donation->toko->name ?? 'Toko Tidak Ditemukan',
                'donor_name' => $donation->donor_name ?? 'Hamba Allah',
                'status' => $donation->status,
                'note' => $donation->note,
                'formatted_amount' => 'Rp ' . number_format($donation->amount, 0, ',', '.'),
                'payment_method_label' => $donation->payment_method_label,
                'date' => $donation->created_at->format('d M Y'),
            ];
        });

        return response()->json($formattedDonations);
    }
}
