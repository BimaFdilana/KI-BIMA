<?php

namespace App\Http\Controllers\Api\Toko;

use App\Http\Controllers\Controller;
use App\Models\Toko\BiayaOperasional;
use App\Models\Toko\TokoPesanan;
use App\Models\Toko\TokoSellingDetail;
use App\Models\Toko\TokoSelling;
use App\Services\Barang\ConvertSatuanService;
use App\Services\Toko\TokoService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TokoSalesReportController extends Controller
{
    protected $convertSatuanService;
    protected $tokoService;

    public function __construct(ConvertSatuanService $convertSatuanService, TokoService $tokoService)
    {
        $this->convertSatuanService = $convertSatuanService;
        $this->tokoService = $tokoService;
    }

    public function getLaporanPenjualan(Request $request)
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

        $tokoId = $toko->id;
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Set default tanggal jika tidak ada
        if (!$startDate) {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        if (!$endDate) {
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        // Parse tanggal
        $startDateParsed = Carbon::parse($startDate)->startOfDay();
        $endDateParsed = Carbon::parse($endDate)->endOfDay();

        // Query untuk mendapatkan data penjualan
        $penjualanQuery = TokoSelling::with(['details.barangki.barang', 'details.barangki.satuan', 'details.barangki.tokoPesanan'])
            ->where('toko_id', $tokoId)
            ->where('status', 'success')
            ->whereBetween('created_at', [$startDateParsed, $endDateParsed]);

        $penjualan = $penjualanQuery->get();

        // Hitung total pendapatan
        $totalPendapatan = $penjualan->sum('total_harga');

        // Hitung HPP dan detail barang terjual
        $barangTerjual = [];
        $totalHPP = 0;

        foreach ($penjualan as $transaksi) {
            foreach ($transaksi->details as $detail) {
                $barangKI = $detail->barangki;
                $barang = $barangKI->barang;

                // Hitung HPP untuk item ini
                // Prioritas: harga dari TokoPesanan (jika toko membeli dari sistem) atau price_buy dari BarangKI
                $hargaBeli = $this->getHargaBeliBarang($barangKI, $tokoId);
                $hppItem = $hargaBeli * $detail->jumlah;
                $totalHPP += $hppItem;

                // Kode barang (gunakan SKU dari barang atau ID barcode dari barangKI)
                $kodeBarang = $barang->sku ?? $barangKI->id_barcode ?? 'BRG' . $barangKI->id;

                // Cek apakah barang sudah ada di array
                if (isset($barangTerjual[$kodeBarang])) {
                    $barangTerjual[$kodeBarang]['jumlah_terjual'] += $detail->jumlah;
                    $barangTerjual[$kodeBarang]['total'] += $detail->subtotal;
                } else {
                    $barangTerjual[$kodeBarang] = [
                        'kode_barang' => $kodeBarang,
                        'nama_barang' => $barang->name,
                        'jumlah_terjual' => $detail->jumlah,
                        'satuan' => $barangKI->satuan->name,
                        'harga_satuan' => $detail->harga_satuan,
                        'total' => $detail->subtotal
                    ];
                }
            }
        }

        // Hitung laba kotor dan laba bersih
        $labaKotor = $totalPendapatan - $totalHPP;
        $labaBersih = $labaKotor; // Untuk saat ini sama dengan laba kotor, bisa ditambah biaya operasional nantinya

        // Format response
        $response = [
            'periode' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'laporan' => [
                'pendapatan' => (int) $totalPendapatan,
                'hpp' => (int) $totalHPP,
                'laba_kotor' => (int) $labaKotor,
                'laba_bersih' => (int) $labaBersih
            ],
            'barang_terjual' => array_values($barangTerjual)
        ];

        return response()->json($response);
    }

    /**
     * Helper method untuk mendapatkan harga beli barang
     * Prioritas: TokoPesanan.price (jika toko membeli dari sistem) atau BarangKI.price_buy
     */
    private function getHargaBeliBarang($barangKI, $tokoId)
    {
        // Cari pembelian terakhir dari TokoPesanan untuk toko ini
        $tokoPesanan = TokoPesanan::whereHas('payment', function ($query) use ($tokoId) {
            $query->where('toko_id', $tokoId)
                ->where('status', 'success');
        })
            ->where('barangki_id', $barangKI->id)
            ->orderBy('created_at', 'desc')
            ->first();

        // Jika ada pembelian dari sistem, gunakan harga dari TokoPesanan
        if ($tokoPesanan) {
            return $tokoPesanan->price;
        }

        // Jika tidak ada, gunakan price_buy dari BarangKI
        return $barangKI->price_buy;
    }

    /**
     * Alternative method dengan query yang lebih optimal
     */
    public function getLaporanPenjualanOptimized(Request $request)
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

        $tokoId = $toko->id;
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Set default tanggal jika tidak ada
        if (!$startDate) {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        if (!$endDate) {
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        // Parse tanggal
        $startDateParsed = Carbon::parse($startDate)->startOfDay();
        $endDateParsed = Carbon::parse($endDate)->endOfDay();

        // Query untuk total pendapatan
        $totalPendapatan = TokoSelling::where('toko_id', $tokoId)
            ->where('status', 'success')
            ->whereBetween('created_at', [$startDateParsed, $endDateParsed])
            ->sum('total_harga');

        // Query untuk detail barang terjual dengan HPP dari TokoPesanan atau BarangKI
        $barangTerjual = DB::table('toko_selling as ts')
            ->join('toko_selling_detail as tsd', 'ts.increment_id', '=', 'tsd.transaction_id')
            ->join('barang_ki as bki', 'tsd.barangki_id', '=', 'bki.id')
            ->join('barang as b', 'bki.barang_id', '=', 'b.id')
            ->join('satuan_items as si', 'bki.satuan_id', '=', 'si.id')
            ->leftJoin(DB::raw('(
                    SELECT
                        tp.barangki_id,
                        tp.price as harga_beli_pesanan,
                        ROW_NUMBER() OVER (PARTITION BY tp.barangki_id ORDER BY tp.created_at DESC) as rn
                    FROM toko_pesanan tp
                    JOIN toko_payment tpay ON tp.payment_id = tpay.id
                    WHERE tpay.toko_id = ' . $tokoId . ' AND tpay.status = "success"
                ) as latest_pesanan'), function ($join) {
                $join->on('bki.id', '=', 'latest_pesanan.barangki_id')
                    ->where('latest_pesanan.rn', '=', 1);
            })
            ->where('ts.toko_id', $tokoId)
            ->where('ts.status', 'success')
            ->whereBetween('ts.created_at', [$startDateParsed, $endDateParsed])
            ->select(
                DB::raw('COALESCE(b.sku, bki.id_barcode, CONCAT("BRG", bki.id)) as kode_barang'),
                'b.name as nama_barang',
                'si.name as satuan',
                DB::raw('SUM(tsd.jumlah) as jumlah_terjual'),
                'tsd.harga_satuan',
                DB::raw('SUM(tsd.subtotal) as total'),
                DB::raw('SUM(COALESCE(latest_pesanan.harga_beli_pesanan, bki.price_buy) * tsd.jumlah) as total_hpp')
            )
            ->groupBy('kode_barang', 'b.name', 'si.name', 'tsd.harga_satuan')
            ->get();

        // Hitung total HPP
        $totalHPP = $barangTerjual->sum('total_hpp');

        // Format barang terjual
        $barangTerjualFormatted = $barangTerjual->map(function ($item) {
            return [
                'kode_barang' => $item->kode_barang,
                'nama_barang' => $item->nama_barang,
                'jumlah_terjual' => (int) $item->jumlah_terjual,
                'satuan' => $item->satuan,
                'harga_satuan' => (int) $item->harga_satuan,
                'total' => (int) $item->total
            ];
        })->values();

        // Query untuk Biaya Operasional dalam periode yang sama
        $biayaOperasional = BiayaOperasional::where('toko_id', $tokoId)
            ->whereBetween('tanggal', [$startDateParsed, $endDateParsed])
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalBiayaOperasional = $biayaOperasional->sum('jumlah');

        // Format detail biaya operasional
        $detailBiayaOperasional = $biayaOperasional->map(function ($item) {
            return [
                'id' => $item->id,
                'kategori' => $item->kategori,
                'deskripsi' => $item->deskripsi,
                'jumlah' => (int) $item->jumlah,
                'tanggal' => $item->tanggal->format('Y-m-d')
            ];
        })->toArray();

        // Hitung laba kotor dan laba usaha
        $labaKotor = $totalPendapatan - $totalHPP;
        $labaUsaha = $labaKotor - $totalBiayaOperasional;

        // Format response
        $response = [
            'periode' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'laporan' => [
                'pendapatan' => (int) $totalPendapatan,
                'hpp' => (int) $totalHPP,
                'laba_kotor' => (int) $labaKotor,
                'biaya_operasional' => (int) $totalBiayaOperasional,
                'laba_usaha' => (int) $labaUsaha
            ],
            'barang_terjual' => $barangTerjualFormatted->toArray(),
            'detail_biaya_operasional' => $detailBiayaOperasional
        ];

        return response()->json($response);
    }

    /**
     * Menampilkan laporan penjualan per toko dengan time series
     */
    public function getSalesData(Request $request)
    {
        // Parameter untuk filter data
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', Carbon::now()->format('Y-m-d'));
        $groupBy = $request->query('group_by', 'daily');
        $user = auth()->user();

        // Cek apakah user ada di toko tertentu
        $toko = $this->tokoService->getTokoByUser($user);

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }

        $tokoId = $toko->id;
        // Dapatkan data penjualan
        $salesData = $this->getSalesTimeSeries($startDate, $endDate, $groupBy, $tokoId);

        return response()->json([
            'status' => 'success',
            'message' => 'Data penjualan berhasil diambil',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'data' => $salesData,
            'groupBy' => $groupBy,
        ]);
    }


    /**
     * Mendapatkan data penjualan dengan time series berdasarkan toko
     *
     * @param string $startDate Tanggal awal (format: Y-m-d)
     * @param string $endDate Tanggal akhir (format: Y-m-d)
     * @param string $groupBy Pengelompokan data (daily, weekly, monthly)
     * @param int|null $tokoId ID toko (opsional)
     * @return array
     */
    private function getSalesTimeSeries($startDate, $endDate, $groupBy = 'daily', $tokoId = null)
    {
        // Konversi string tanggal ke objek Carbon
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Query dasar untuk mendapatkan penjualan
        $query = TokoSelling::with(['details', 'details.barangki', 'details.barangki.barangToko'])
            ->where('status', 'success')
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Filter berdasarkan toko jika ada
        if ($tokoId) {
            $query->where('toko_id', $tokoId);
        }

        // Ambil data penjualan
        $sales = $query->get();

        // Inisialisasi data untuk time series
        $timeSeriesData = [];
        $tokoData = [];

        // Siapkan periode untuk time series
        $period = $this->generateTimePeriod($startDate, $endDate, $groupBy);

        // Inisialisasi data kosong untuk setiap toko dan periode
        foreach ($sales as $sale) {
            $tokoId = $sale->toko_id;
            if (!isset($tokoData[$tokoId])) {
                $tokoData[$tokoId] = [
                    'toko_id' => $tokoId,
                    'toko_name' => $sale->toko->name ?? "Toko #$tokoId",
                    'time_series' => []
                ];

                // Inisialisasi data kosong untuk setiap periode
                foreach ($period as $date => $label) {
                    $tokoData[$tokoId]['time_series'][$date] = [
                        'date' => $date,
                        'label' => $label,
                        'total_sales' => 0,
                        'total_transactions' => 0,
                        'margin' => 0,
                        'items_sold' => 0
                    ];
                }
            }
        }

        // Proses data penjualan untuk setiap toko
        foreach ($sales as $sale) {
            $tokoId = $sale->toko_id;
            $saleDate = $sale->created_at;
            $periodKey = $this->getPeriodKey($saleDate, $groupBy);

            // Pastikan periode tersedia dalam data toko
            if (isset($tokoData[$tokoId]['time_series'][$periodKey])) {
                // Update data penjualan
                $tokoData[$tokoId]['time_series'][$periodKey]['total_sales'] += $sale->total_harga;
                $tokoData[$tokoId]['time_series'][$periodKey]['total_transactions'] += 1;

                // Hitung margin dan jumlah item terjual
                foreach ($sale->details as $detail) {
                    $barangki = $detail->barangki;
                    if ($barangki && isset($barangki->barangToko) && count($barangki->barangToko) > 0) {
                        // Ambil harga beli dari barangToko yang sesuai dengan toko_id
                        $barangToko = $barangki->barangToko->where('toko_id', $tokoId)->first();
                        if ($barangToko) {
                            $priceBuy = $barangToko->price_buy;
                            $margin = $detail->subtotal - ($detail->jumlah * $priceBuy);
                            $tokoData[$tokoId]['time_series'][$periodKey]['margin'] += $margin;
                        }
                    }

                    // Konversi ke satuan terkecil untuk mendapatkan jumlah item sebenarnya
                    if ($barangki) {
                        $convertedQuantity = $this->convertSatuanService->convertToSmallestUnit($barangki, $detail->jumlah);
                        $tokoData[$tokoId]['time_series'][$periodKey]['items_sold'] += $convertedQuantity['converted_amount'];
                    }
                }
            }
        }

        // Konversi data ke format yang dibutuhkan untuk chart
        foreach ($tokoData as $tokoId => $toko) {
            $timeSeriesArray = [];
            foreach ($toko['time_series'] as $date => $data) {
                $timeSeriesArray[] = $data;
            }
            $tokoData[$tokoId]['time_series'] = $timeSeriesArray;
        }

        return array_values($tokoData);
    }

    /**
     * Mendapatkan data toko terbaik berdasarkan penjualan
     *
     * @param string $startDate Tanggal awal (format: Y-m-d)
     * @param string $endDate Tanggal akhir (format: Y-m-d)
     * @param int $limit Jumlah data yang diambil
     * @return array
     */
    private function getBestSellingToko($startDate, $endDate, $limit = 5)
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $sellingDetails = TokoSellingDetail::whereHas('toko_selling', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'success');
        })
            ->with(['toko_selling', 'barangki.barang', 'barangki.satuan', 'barangki.barangToko'])
            ->get();

        // Group by toko_id dan hitung total
        $tokoSelling = $sellingDetails->groupBy('toko_selling.toko_id')
            ->map(function ($details) {
                $tokoId = $details->first()->toko_selling->toko_id;
                $totalSales = $details->sum('subtotal');
                $totalSmallestQuantity = 0;
                $convertedQuantity = null;
                $margin = 0;

                foreach ($details as $detail) {
                    // Ambil harga beli yang sesuai dengan toko_id
                    $barangToko = $detail->barangki->barangToko->where('toko_id', $tokoId)->first();
                    if ($barangToko) {
                        $priceBuy = $barangToko->price_buy;
                        $margin += $detail->subtotal - ($detail->jumlah * $priceBuy);
                    }

                    $convertedQuantity = $this->convertSatuanService->convertToSmallestUnit($detail->barangki, $detail->jumlah);
                    $totalSmallestQuantity += $convertedQuantity['converted_amount'];
                }

                return [
                    'toko_id' => $tokoId,
                    'total_sales' => $totalSales,
                    'total_transactions' => $details->groupBy('toko_selling.id_transaction')->count(),
                    'total_smallest_quantity' => $totalSmallestQuantity,
                    'small_unit' => $convertedQuantity ? $convertedQuantity['converted_satuan'] : null,
                    'margin' => $margin,
                ];
            })
            ->sortByDesc('margin')
            ->take($limit)
            ->values()
            ->toArray();

        return $tokoSelling;
    }

    /**
     * Menghasilkan periode waktu berdasarkan range tanggal dan pengelompokan
     *
     * @param Carbon $startDate Tanggal awal
     * @param Carbon $endDate Tanggal akhir
     * @param string $groupBy Pengelompokan (daily, weekly, monthly)
     * @return array
     */
    private function generateTimePeriod($startDate, $endDate, $groupBy)
    {
        $period = [];
        $current = clone $startDate;

        while ($current <= $endDate) {
            $key = $this->getPeriodKey($current, $groupBy);

            switch ($groupBy) {
                case 'daily':
                    $period[$key] = $current->format('d M Y');
                    $current->addDay();
                    break;
                case 'weekly':
                    $period[$key] = 'Week ' . $current->weekOfYear . ' ' . $current->year;
                    $current->addWeek();
                    break;
                case 'monthly':
                    $period[$key] = $current->format('M Y');
                    $current->addMonth();
                    break;
            }
        }

        return $period;
    }

    /**
     * Mendapatkan kunci periode berdasarkan tanggal dan pengelompokan
     *
     * @param Carbon $date Tanggal
     * @param string $groupBy Pengelompokan (daily, weekly, monthly)
     * @return string
     */
    private function getPeriodKey($date, $groupBy)
    {
        if (!($date instanceof Carbon)) {
            $date = Carbon::parse($date);
        }

        switch ($groupBy) {
            case 'daily':
                return $date->format('Y-m-d');
            case 'weekly':
                return $date->year . '-W' . $date->weekOfYear;
            case 'monthly':
                return $date->format('Y-m');
            default:
                return $date->format('Y-m-d');
        }
    }
}
