<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auth\UserModel;
use App\Models\Barang\BarangKI;
use App\Models\Barang\BarangModel;
use App\Models\Infaq\InfaqHistory;
use App\Models\Infaq\InfaqList;
use App\Models\PakDul\PaylatterAccount;
use App\Models\PakDul\PaylatterTransaction;
use App\Models\Toko\TokoModel;
use App\Models\Toko\TokoPayment;
use App\Models\Toko\TokoPesanan;
use App\Models\Toko\TokoSellingDetail;
use App\Services\Barang\ConvertSatuanService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;

class DashboardController extends Controller
{
    public $convertService;

    public function __construct(ConvertSatuanService $convertService)
    {
        $this->convertService = $convertService;
    }

    public function index()
    {
        // === TOKO STATISTICS ===
        $pendingToko = TokoModel::where('status', 'pending')->count();
        $lastPendingTokoTime = TokoModel::where('status', 'pending')->latest()->first();
        $activeToko = TokoModel::where('status', 'active')->count();
        $totalToko = TokoModel::count();

        // Toko by type
        $tokoByType = TokoModel::where('status', 'active')
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // === PAYMENT/ORDER STATISTICS ===
        $paidPesanan = TokoPayment::where('status', 'paid')->count();
        $lastPaidPesananTime = TokoPayment::where('status', 'paid')->latest()->first();

        $deliverPesanan = TokoPayment::where('status', 'delivery')->count();
        $lastDeliverPesananTime = TokoPayment::where('status', 'delivery')->latest()->first();

        // Revenue this month
        $revenueThisMonth = TokoPayment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'success')
            ->sum('total');

        $revenueLastMonth = TokoPayment::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->where('status', 'success')
            ->sum('total');

        $revenuePercentage = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : ($revenueThisMonth > 0 ? 100 : 0);

        // Total transactions
        $totalTransactionsThisMonth = TokoPayment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'success')
            ->count();

        $totalTransactionsLastMonth = TokoPayment::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->where('status', 'success')
            ->count();

        $transactionPercentage = $totalTransactionsLastMonth > 0
            ? round((($totalTransactionsThisMonth - $totalTransactionsLastMonth) / $totalTransactionsLastMonth) * 100, 1)
            : ($totalTransactionsThisMonth > 0 ? 100 : 0);

        // === BARANG KI TERJUAL ===
        $barangTerjualThisMonth = TokoPesanan::whereMonth('created_at', now()->month)
            ->where('status', 'success')
            ->with('barangKI')
            ->get();
        $totalTerjualBulanIni = 0;
        foreach ($barangTerjualThisMonth as $data) {
            $converTosmall = $this->convertService->convertToSmallestUnit($data->barangki, $data->quantity);
            if ($converTosmall['success'] == true) {
                $totalTerjualBulanIni += $converTosmall['converted_amount'];
            }
        }

        $barangTerjualBulanLalu = TokoPesanan::whereMonth('created_at', now()->subMonth()->month)
            ->where('status', 'success')
            ->with('barangKI')
            ->get();
        $totalTerjualBulanLalu = 0;
        foreach ($barangTerjualBulanLalu as $data) {
            $converTosmall = $this->convertService->convertToSmallestUnit($data->barangki, $data->quantity);
            if ($converTosmall['success'] == true) {
                $totalTerjualBulanLalu += $converTosmall['converted_amount'];
            }
        }
        $totalterjualBulanPercentage = $totalTerjualBulanLalu > 0
            ? min(100, floor((($totalTerjualBulanIni - $totalTerjualBulanLalu) / $totalTerjualBulanLalu) * 100))
            : 100;


        // === BARANG TOKO TERJUAL ===
        $barangTokoTerjualBulanIni = TokoSellingDetail::whereMonth('created_at', now()->month)
            ->whereHas('toko_selling', function ($query) {
                $query->where('status', 'success');
            })
            ->with('barangKI')
            ->get();
        $totalBarangTokoTerjualBulanIni = 0;
        foreach ($barangTokoTerjualBulanIni as $data) {
            $converTosmall = $this->convertService->convertToSmallestUnit($data->barangki, $data->jumlah);
            if ($converTosmall['success'] == true) {
                $totalBarangTokoTerjualBulanIni += $converTosmall['converted_amount'];
            }
        }

        $barangTokoTerjualBulanLalu = TokoSellingDetail::whereMonth('created_at', now()->subMonth()->month)
            ->whereHas('toko_selling', function ($query) {
                $query->where('status', 'success');
            })
            ->with('barangKI')
            ->get();

        $totalBarangTokoTerjualBulanLalu = 0;
        foreach ($barangTokoTerjualBulanLalu as $data) {
            $converTosmall = $this->convertService->convertToSmallestUnit($data->barangki, $data->jumlah);
            if ($converTosmall['success'] == true) {
                $totalBarangTokoTerjualBulanLalu += $converTosmall['converted_amount'];
            }
        }
        $totalBarangTokoTerjualBulanPercentage = $totalBarangTokoTerjualBulanLalu > 0
            ? min(100, floor((($totalBarangTokoTerjualBulanIni - $totalBarangTokoTerjualBulanLalu) / $totalBarangTokoTerjualBulanLalu) * 100))
            : 100;

        // === INVENTORY STATISTICS ===
        $totalProducts = BarangModel::where('status', 'active')->count();

        // Low stock (items with less than 20% remaining)
        $lowStockCount = BarangKI::where('status', 'active')
            ->whereRaw('quantity - sold_quantity < quantity * 0.2')
            ->where('quantity', '>', 0)
            ->count();

        // Expiring soon (within 30 days)
        $expiringSoonCount = BarangKI::where('status', 'active')
            ->where('expired_time', '<=', now()->addDays(30))
            ->where('expired_time', '>', now())
            ->count();

        // Already expired
        $expiredCount = BarangKI::where('status', 'active')
            ->where('expired_time', '<=', now())
            ->count();

        // === USER STATISTICS ===
        $totalUsers = UserModel::count();
        $ktpVerifiedUsers = UserModel::where('ktp_verified', true)->count();
        $newUsersThisMonth = UserModel::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // === PAYLATTER (PAKDUL) STATISTICS ===
        $activePaylatterAccounts = 0;
        $totalCreditUsed = 0;
        $overdueTransactions = 0;

        try {
            $activePaylatterAccounts = PaylatterAccount::where('status', 'active')->count();
            $totalCreditUsed = PaylatterAccount::where('status', 'active')->sum('used_credit');
            $overdueTransactions = PaylatterTransaction::where('status', 'overdue')->count();
        } catch (\Exception $e) {
            // Tables might not exist
        }

        // === INFAQ STATISTICS ===
        $infaqThisMonth = 0;
        $activeInfaqCampaigns = 0;

        try {
            $infaqThisMonth = InfaqHistory::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('status', 'success')
                ->sum('amount');
            $activeInfaqCampaigns = InfaqList::where('is_active', true)->count();
        } catch (\Exception $e) {
            // Tables might not exist
        }

        // === CHART DATA ===
        // Revenue last 7 days
        $revenueLast7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = TokoPayment::whereDate('created_at', $date->toDateString())
                ->where('status', 'success')
                ->sum('total');
            $revenueLast7Days->push([
                'date' => $date->format('d M'),
                'revenue' => $revenue
            ]);
        }

        // Order status distribution
        $orderStatusDistribution = TokoPayment::select('status', DB::raw('count(*) as count'))
            ->whereMonth('created_at', now()->month)
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Top 5 products this month
        $topProducts = TokoPesanan::select(
            'barangki_id',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(total) as total_sales')
        )
            ->whereMonth('created_at', now()->month)
            ->where('status', 'success')
            ->groupBy('barangki_id')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->with('barangKI.barang')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => optional(optional($item->barangKI)->barang)->name ?? 'Unknown',
                    'qty' => $item->total_qty,
                    'sales' => $item->total_sales
                ];
            });

        return view('login.dashboard', compact(
            // Original variables
            'pendingToko',
            'lastPendingTokoTime',
            'paidPesanan',
            'lastPaidPesananTime',
            'deliverPesanan',
            'lastDeliverPesananTime',
            'totalTerjualBulanIni',
            'totalTerjualBulanLalu',
            'totalterjualBulanPercentage',
            'totalBarangTokoTerjualBulanIni',
            'totalBarangTokoTerjualBulanLalu',
            'totalBarangTokoTerjualBulanPercentage',

            // New Toko stats
            'activeToko',
            'totalToko',
            'tokoByType',

            // Revenue stats
            'revenueThisMonth',
            'revenueLastMonth',
            'revenuePercentage',
            'totalTransactionsThisMonth',
            'transactionPercentage',

            // Inventory stats
            'totalProducts',
            'lowStockCount',
            'expiringSoonCount',
            'expiredCount',

            // User stats
            'totalUsers',
            'ktpVerifiedUsers',
            'newUsersThisMonth',

            // Paylatter stats
            'activePaylatterAccounts',
            'totalCreditUsed',
            'overdueTransactions',

            // Infaq stats
            'infaqThisMonth',
            'activeInfaqCampaigns',

            // Chart data
            'revenueLast7Days',
            'orderStatusDistribution',
            'topProducts'
        ));
    }

    /**
     * API endpoint for dashboard overview data
     */
    public function apiOverview()
    {
        $lowStockProducts = BarangKI::where('status', 'active')
            ->whereRaw('quantity - sold_quantity < quantity * 0.2')
            ->where('quantity', '>', 0)
            ->with('barang', 'satuan')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $remaining = $item->quantity - $item->sold_quantity;
                return [
                    'id' => $item->id,
                    'name' => optional($item->barang)->name ?? 'Unknown',
                    'sku' => optional($item->barang)->sku ?? 'N/A',
                    'remaining' => $remaining,
                    'unit' => optional($item->satuan)->name ?? '',
                    'percentage' => $item->quantity > 0 ? round(($remaining / $item->quantity) * 100) : 0
                ];
            });

        $expiringProducts = BarangKI::where('status', 'active')
            ->where('expired_time', '<=', now()->addDays(30))
            ->where('expired_time', '>', now())
            ->with('barang', 'satuan')
            ->orderBy('expired_time')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => optional($item->barang)->name ?? 'Unknown',
                    'sku' => optional($item->barang)->sku ?? 'N/A',
                    'expired_time' => Carbon::parse($item->expired_time)->format('d M Y'),
                    'days_left' => now()->diffInDays($item->expired_time),
                    'unit' => optional($item->satuan)->name ?? ''
                ];
            });

        return response()->json([
            'low_stock_products' => $lowStockProducts,
            'expiring_products' => $expiringProducts
        ]);
    }

    /**
     * Format phone number with proper country code in parentheses
     * 
     * @param string $phoneNumber
     * @return string
     */
    private function formatPhoneNumber($phoneNumber)
    {
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();

            // Bersihkan semua karakter kecuali angka dan +
            $phoneNumber = preg_replace('/[^\d+]/', '', $phoneNumber);

            // Jika mulai dari 0, hapus 0
            if (substr($phoneNumber, 0, 1) === '0') {
                $phoneNumber = substr($phoneNumber, 1);
            }

            // Jika tidak diawali '+', tambah default +62
            if (substr($phoneNumber, 0, 1) !== '+') {
                $phoneNumber = '+62' . $phoneNumber;
            }

            $defaultRegion = 'ID';
            $parsedNumber = $phoneUtil->parse($phoneNumber, $defaultRegion);

            $countryCode = $parsedNumber->getCountryCode();
            $nationalNumber = $parsedNumber->getNationalNumber(); // <-- ini ambil raw number tanpa leading 0

            // Format manual biar fleksibel
            $formatted = '(+' . $countryCode . ') ' . $this->splitPhoneNumber($nationalNumber);

            return $formatted;
        } catch (NumberParseException $e) {
            return $phoneNumber;
        } catch (\Exception $e) {
            return $phoneNumber;
        }
    }

    // Fungsi bantu untuk kasih strip-stripnya
    private function splitPhoneNumber($number)
    {
        // Contoh sederhana: 3-4-4 digit split (812-3456-7891)
        return preg_replace("/(\d{3})(\d{4})(\d{4})/", "$1-$2-$3", $number);
    }
}
