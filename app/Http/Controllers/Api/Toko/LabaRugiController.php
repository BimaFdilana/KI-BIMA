<?php

namespace App\Http\Controllers\Api\Toko;

use App\Http\Controllers\Controller;
use App\Models\Toko\BiayaOperasional;
use App\Models\Toko\BarangToko;
use App\Models\Toko\TokoPayment;
use App\Models\Toko\TokoPesanan;
use App\Models\Toko\TokoSelling;
use App\Models\Toko\TokoSellingDetail;
use App\Services\Toko\TokoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabaRugiController extends Controller
{
    protected $tokoService;

    public function __construct(TokoService $tokoService)
    {
        $this->tokoService = $tokoService;
    }

    /**
     * Get Laporan Laba Rugi (Income Statement / Profit & Loss)
     *
     * Endpoint: GET /api/toko/laba-rugi
     * Query params:
     *   - start_date: tanggal awal (Y-m-d)
     *   - end_date: tanggal akhir (Y-m-d)
     *   - filter_type: 'hari_ini' | 'tanggal' | 'bulan' | 'tahun'
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $toko = $this->tokoService->getTokoByUser($user);

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }

        $tokoId = $toko->id;

        // Parse filter parameters
        $filterType = $request->query('filter_type', 'bulan');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Set default dates based on filter type
        $dates = $this->parseDateFilter($filterType, $startDate, $endDate);
        $startDateParsed = $dates['start'];
        $endDateParsed = $dates['end'];

        // ==========================================
        // PENDAPATAN (REVENUE)
        // ==========================================
        $pendapatanData = $this->calculatePendapatan($tokoId, $startDateParsed, $endDateParsed);

        // ==========================================
        // HARGA POKOK PENJUALAN (COST OF GOODS SOLD)
        // ==========================================
        $hppData = $this->calculateHPP($tokoId, $startDateParsed, $endDateParsed);

        // ==========================================
        // LABA KOTOR (GROSS PROFIT)
        // ==========================================
        $labaKotor = $pendapatanData['penjualan_bersih'] - $hppData['hpp'];

        // ==========================================
        // BIAYA OPERASIONAL (OPERATING EXPENSES)
        // ==========================================
        $biayaOperasionalData = $this->calculateBiayaOperasional($tokoId, $startDateParsed, $endDateParsed);

        // ==========================================
        // LABA USAHA (OPERATING INCOME)
        // ==========================================
        $labaUsaha = $labaKotor - $biayaOperasionalData['total'];

        // Format response
        $response = [
            'success' => true,
            'periode' => [
                'filter_type' => $filterType,
                'start_date' => $startDateParsed->format('Y-m-d'),
                'end_date' => $endDateParsed->format('Y-m-d'),
                'label' => $this->getPeriodeLabel($filterType, $startDateParsed, $endDateParsed)
            ],
            'pendapatan' => [
                'penjualan' => (int) $pendapatanData['penjualan'],
                'retur_penjualan' => (int) $pendapatanData['retur_penjualan'],
                'potongan_penjualan' => (int) $pendapatanData['potongan_penjualan'],
                'jumlah_retur_potongan' => (int) $pendapatanData['jumlah_retur_potongan'],
                'penjualan_bersih' => (int) $pendapatanData['penjualan_bersih'],
            ],
            'hpp' => [
                'persediaan_awal' => (int) $hppData['persediaan_awal'],
                'pembelian' => (int) $hppData['pembelian'],
                'retur_pembelian' => (int) $hppData['retur_pembelian'],
                'potongan_pembelian' => (int) $hppData['potongan_pembelian'],
                'jumlah_retur_potongan_pembelian' => (int) $hppData['jumlah_retur_potongan_pembelian'],
                'pembelian_bersih' => (int) $hppData['pembelian_bersih'],
                'barang_tersedia_dijual' => (int) $hppData['barang_tersedia_dijual'],
                'persediaan_akhir' => (int) $hppData['persediaan_akhir'],
                'hpp' => (int) $hppData['hpp'],
            ],
            'laba_kotor' => (int) $labaKotor,
            'biaya_operasional' => [
                'detail' => $biayaOperasionalData['detail'],
                'total' => (int) $biayaOperasionalData['total'],
            ],
            'laba_usaha' => (int) $labaUsaha,
        ];

        return response()->json($response);
    }

    /**
     * Parse date filter based on filter type
     */
    private function parseDateFilter($filterType, $startDate, $endDate)
    {
        $now = Carbon::now();

        switch ($filterType) {
            case 'hari_ini':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay()
                ];

            case 'tanggal':
                // Custom date range
                $start = $startDate ? Carbon::parse($startDate)->startOfDay() : $now->copy()->startOfMonth();
                $end = $endDate ? Carbon::parse($endDate)->endOfDay() : $now->copy()->endOfDay();
                return ['start' => $start, 'end' => $end];

            case 'bulan':
                // If specific date provided, use that month
                if ($startDate) {
                    $date = Carbon::parse($startDate);
                    return [
                        'start' => $date->copy()->startOfMonth(),
                        'end' => $date->copy()->endOfMonth()
                    ];
                }
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];

            case 'tahun':
                // If specific date provided, use that year
                if ($startDate) {
                    $date = Carbon::parse($startDate);
                    return [
                        'start' => $date->copy()->startOfYear(),
                        'end' => $date->copy()->endOfYear()
                    ];
                }
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear()
                ];

            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
        }
    }

    /**
     * Get human readable period label
     */
    private function getPeriodeLabel($filterType, $startDate, $endDate)
    {
        switch ($filterType) {
            case 'hari_ini':
                return $startDate->translatedFormat('d F Y');
            case 'tanggal':
                return $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y');
            case 'bulan':
                return $startDate->translatedFormat('F Y');
            case 'tahun':
                return $startDate->translatedFormat('Y');
            default:
                return $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y');
        }
    }

    /**
     * Calculate Pendapatan (Revenue) section
     */
    private function calculatePendapatan($tokoId, $startDate, $endDate)
    {
        // Get total penjualan from TokoSelling (successful transactions)
        $penjualan = TokoSelling::where('toko_id', $tokoId)
            ->where('status', 'success')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_harga');

        // TODO: When retur_penjualan table exists, calculate from there
        // For now, retur and potongan are calculated from cancelled/returned orders if available
        $returPenjualan = 0;
        $potonganPenjualan = 0;

        // Check if there are any failed/cancelled transactions that represent returns
        // This is a placeholder - adjust based on actual business logic
        $returPenjualan = TokoSelling::where('toko_id', $tokoId)
            ->where('status', 'refunded')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_harga');

        $jumlahReturPotongan = $returPenjualan + $potonganPenjualan;
        $penjualanBersih = $penjualan - $jumlahReturPotongan;

        return [
            'penjualan' => $penjualan,
            'retur_penjualan' => $returPenjualan,
            'potongan_penjualan' => $potonganPenjualan,
            'jumlah_retur_potongan' => $jumlahReturPotongan,
            'penjualan_bersih' => $penjualanBersih,
        ];
    }

    /**
     * Calculate HPP (Cost of Goods Sold) section
     */
    private function calculateHPP($tokoId, $startDate, $endDate)
    {
        // Persediaan Awal: Value of inventory at start of period
        // Calculate from BarangToko based on quantity * price_buy at period start
        $persediaanAwal = $this->calculateInventoryValue($tokoId, $startDate->copy()->subDay());

        // Pembelian: Total purchases during the period (from TokoPayment with success status)
        $pembelian = TokoPayment::where('toko_id', $tokoId)
            ->where('status', 'success')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        // TODO: When retur_pembelian table exists, calculate from there
        $returPembelian = 0;
        $potonganPembelian = 0;

        $jumlahReturPotonganPembelian = $returPembelian + $potonganPembelian;
        $pembelianBersih = $pembelian - $jumlahReturPotonganPembelian;

        // Barang Tersedia Untuk Dijual
        $barangTersediaDijual = $persediaanAwal + $pembelianBersih;

        // Persediaan Akhir: Current inventory value at end of period
        $persediaanAkhir = $this->calculateInventoryValue($tokoId, $endDate);

        // HPP = Persediaan Awal + Pembelian Bersih - Persediaan Akhir
        $hpp = $barangTersediaDijual - $persediaanAkhir;

        // Ensure HPP is not negative
        $hpp = max(0, $hpp);

        return [
            'persediaan_awal' => $persediaanAwal,
            'pembelian' => $pembelian,
            'retur_pembelian' => $returPembelian,
            'potongan_pembelian' => $potonganPembelian,
            'jumlah_retur_potongan_pembelian' => $jumlahReturPotonganPembelian,
            'pembelian_bersih' => $pembelianBersih,
            'barang_tersedia_dijual' => $barangTersediaDijual,
            'persediaan_akhir' => $persediaanAkhir,
            'hpp' => $hpp,
        ];
    }

    /**
     * Calculate inventory value at a specific date
     * This is a simplified calculation - in production, you might need inventory snapshots
     */
    private function calculateInventoryValue($tokoId, $date)
    {
        // Get current inventory and calculate backwards based on transactions
        // For simplicity, we use current inventory value
        // In production, you should implement inventory snapshots or ledger

        $currentInventory = BarangToko::where('toko_id', $tokoId)
            ->whereNull('deleted_at')
            ->get();

        $totalValue = 0;
        foreach ($currentInventory as $item) {
            $totalValue += ($item->quantity ?? 0) * ($item->price_buy ?? 0);
        }

        return $totalValue;
    }

    /**
     * Calculate Biaya Operasional (Operating Expenses)
     */
    private function calculateBiayaOperasional($tokoId, $startDate, $endDate)
    {
        $biayaOperasional = BiayaOperasional::where('toko_id', $tokoId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->get();

        // Group by category
        $groupedByCategory = $biayaOperasional->groupBy('kategori');

        $detail = [];
        foreach ($groupedByCategory as $kategori => $items) {
            $detail[] = [
                'nama' => $kategori,
                'jumlah' => (int) $items->sum('jumlah'),
            ];
        }

        $total = $biayaOperasional->sum('jumlah');

        return [
            'detail' => $detail,
            'total' => $total,
        ];
    }

    /**
     * Get summary of Laba Rugi for dashboard/quick view
     *
     * Endpoint: GET /api/toko/laba-rugi/summary
     */
    public function summary(Request $request)
    {
        $user = auth()->user();
        $toko = $this->tokoService->getTokoByUser($user);

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }

        $tokoId = $toko->id;
        $filterType = $request->query('filter_type', 'bulan');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $dates = $this->parseDateFilter($filterType, $startDate, $endDate);
        $startDateParsed = $dates['start'];
        $endDateParsed = $dates['end'];

        // Quick calculations
        $penjualanBersih = TokoSelling::where('toko_id', $tokoId)
            ->where('status', 'success')
            ->whereBetween('created_at', [$startDateParsed, $endDateParsed])
            ->sum('total_harga');

        // Calculate HPP from selling details
        $hpp = $this->calculateQuickHPP($tokoId, $startDateParsed, $endDateParsed);

        $labaKotor = $penjualanBersih - $hpp;

        $biayaOperasional = BiayaOperasional::where('toko_id', $tokoId)
            ->whereBetween('tanggal', [$startDateParsed, $endDateParsed])
            ->sum('jumlah');

        $labaUsaha = $labaKotor - $biayaOperasional;

        return response()->json([
            'success' => true,
            'periode' => [
                'filter_type' => $filterType,
                'start_date' => $startDateParsed->format('Y-m-d'),
                'end_date' => $endDateParsed->format('Y-m-d'),
                'label' => $this->getPeriodeLabel($filterType, $startDateParsed, $endDateParsed)
            ],
            'summary' => [
                'penjualan_bersih' => (int) $penjualanBersih,
                'hpp' => (int) $hpp,
                'laba_kotor' => (int) $labaKotor,
                'biaya_operasional' => (int) $biayaOperasional,
                'laba_usaha' => (int) $labaUsaha,
            ]
        ]);
    }

    /**
     * Quick HPP calculation based on items sold
     */
    private function calculateQuickHPP($tokoId, $startDate, $endDate)
    {
        // Get all successful selling transactions
        $sellingIds = TokoSelling::where('toko_id', $tokoId)
            ->where('status', 'success')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->pluck('increment_id');

        // Get details and calculate HPP
        $details = TokoSellingDetail::whereIn('transaction_id', $sellingIds)
            ->with(['barangki.barangToko' => function ($query) use ($tokoId) {
                $query->where('toko_id', $tokoId);
            }])
            ->get();

        $totalHPP = 0;
        foreach ($details as $detail) {
            $barangToko = $detail->barangki->barangToko->first();
            if ($barangToko) {
                $totalHPP += $detail->jumlah * $barangToko->price_buy;
            } else {
                // Fallback to barangki price_buy if available
                $totalHPP += $detail->jumlah * ($detail->barangki->price_buy ?? 0);
            }
        }

        return $totalHPP;
    }
}
