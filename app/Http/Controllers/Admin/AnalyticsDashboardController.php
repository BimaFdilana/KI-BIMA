<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang\BarangKI;
use App\Models\Toko\TokoModel;
use App\Models\Toko\TokoPayment;
use App\Models\Toko\TokoPesanan;
use App\Models\Toko\TokoSelling;
use App\Models\Toko\TokoSellingDetail;
use App\Services\Barang\ConvertSatuanService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class AnalyticsDashboardController extends Controller
{
    protected $convertSatuanService;

    public function __construct(ConvertSatuanService $convertSatuanService)
    {
        $this->convertSatuanService = $convertSatuanService;
    }

    public function index(Request $request)
    {
        $period = $request->query('period', 'weekly');
        $customStart = $request->query('start_date');
        $customEnd = $request->query('end_date');

        $dates = $this->getDateRange($period, $customStart, $customEnd);
        $startDate = $dates['start_date'];
        $endDate = $dates['end_date'];

        return view('login.dashboard-analystic', compact(
            'period',
            'startDate',
            'endDate'
        ));
    }

    
    public function apiIndex(Request $request): JsonResponse
    {
        $period = $request->query('period', 'weekly');
        $customStart = $request->query('start_date');
        $customEnd = $request->query('end_date');

        $dates = $this->getDateRange($period, $customStart, $customEnd);
        $startDate = $dates['start_date'];
        $endDate = $dates['end_date'];
   
        return response()->json([
            'total_sales' => $this->getTotalSales($startDate, $endDate),
            'sales_by_day' => $this->getSalesByPeriod($startDate, $endDate),
            'best_selling_products' => $this->getBestSellingProducts($startDate, $endDate),
            'worst_selling_products' => $this->getWorstSellingProducts($startDate, $endDate),
            'sales_growth' => $this->getSalesGrowth($period),
            'revenue_projection' => $this->getRevenueProjection($period),
            'inventory_status' => $this->getProductInventoryStatus(),
            'meta' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'period' => $period
            ]
        ]);
    }

    
    public function getBestBuyer(Request $request): JsonResponse
    {
        $period = $request->query('period', 'weekly');
        $customStart = $request->query('start_date');
        $customEnd = $request->query('end_date');

        $dates = $this->getDateRange($period, $customStart, $customEnd);
        $startDate = $dates['start_date'];
        $endDate = $dates['end_date'];
       
        return response()->json([
            'best_buyer' => $this->getBestBuyerData($startDate, $endDate, $request->query('limit', 5))
        ]);
    }

    public function getBestMasterData(Request $request): JsonResponse
    {
        $period = $request->query('period', 'weekly');
        $customStart = $request->query('start_date');
        $customEnd = $request->query('end_date');

        $dates = $this->getDateRange($period, $customStart, $customEnd);
        $startDate = $dates['start_date'];
        $endDate = $dates['end_date'];

        return response()->json([
            'brand' => $this->getPopulerBrand($startDate, $endDate),
            'category' => $this->getPopulerCategory($startDate, $endDate)
        ]);
    }

    public function getMargin(Request $request): JsonResponse
    {
        $period = $request->query('period', 'weekly');
        $customStart = $request->query('start_date');
        $customEnd = $request->query('end_date');

        $dates = $this->getDateRange($period, $customStart, $customEnd);
        $startDate = $dates['start_date'];
        $endDate = $dates['end_date'];

        return response()->json([
            'margin' => $this->getMarginData($startDate, $endDate, $request->query('limit', 5)),
        ]);
    }
   
    public function getBestTokoBuyAndSell(Request $request): JsonResponse
    {
        $period = $request->query('period', 'weekly');
        $customStart = $request->query('start_date');
        $customEnd = $request->query('end_date');

        $dates = $this->getDateRange($period, $customStart, $customEnd);
        $startDate = $dates['start_date'];
        $endDate = $dates['end_date'];

        return response()->json([
            'time' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'period' => $period
            ],
            'best_buy' => $this->getBestBuyerData($startDate, $endDate, $request->query('limit', 5)),
            'best_sell' => $this->getBestSellingData($startDate, $endDate, $request->query('limit', 5))
        ]);
    }

    private function getBestBuyerData($startDate, $endDate, $limit = 5)
    {
        // Make sure we have Carbon dates
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Get the date labels for the time series based on difference between start and end date
        $dateLabels = $this->getDateLabels($startDate, $endDate);
        
        // Get top toko IDs by total sales in the period
        $topTokos = TokoPayment::select(
            'toko_id',
            DB::raw("(SELECT name FROM toko WHERE id = toko_payment.toko_id) as toko_name"),
            DB::raw('SUM(toko_pesanan.total) as total_sales'),
            DB::raw('COUNT(toko_pesanan.id) as total_transactions')
        )
        ->join('toko_pesanan', 'toko_payment.id', '=', 'toko_pesanan.payment_id')
        ->whereBetween('toko_payment.created_at', [$startDate, $endDate])
        ->where('toko_payment.status', 'success')
        ->groupBy('toko_payment.toko_id')
        ->orderBy('total_sales', 'desc')
        ->limit($limit)
        ->get();

        $tokoData = [];
        
        // Initialize toko data with zero values for all date periods
        foreach ($topTokos as $toko) {
            $tokoId = $toko->toko_id;
            $tokoData[$tokoId] = [
                'toko_id' => $tokoId,
                'toko_name' => $toko->toko_name,
                'total_sales' => $toko->total_sales,
                'total_transactions' => $toko->total_transactions,
                'time_series' => []
            ];
            
            // Initialize time series with zero values
            foreach ($dateLabels as $date => $label) {
                $tokoData[$tokoId]['time_series'][$date] = [
                    'date' => $date,
                    'label' => $label,
                    'total_sales' => 0,
                    'total_transactions' => 0
                ];
            }
        }
        
        // Get daily stats for each top toko
        foreach ($topTokos as $toko) {
            $tokoId = $toko->toko_id;
            
            // Query to get daily sales for this toko
            $dailySales = TokoPayment::select(
                DB::raw('DATE(toko_payment.created_at) as sale_date'),
                DB::raw('SUM(toko_pesanan.total) as daily_total'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->join('toko_pesanan', 'toko_payment.id', '=', 'toko_pesanan.payment_id')
            ->where('toko_payment.toko_id', $tokoId)
            ->where('toko_payment.status', 'success')
            ->whereBetween('toko_payment.created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(toko_payment.created_at)'))
            ->get();
            
            // Populate time series with actual data
            foreach ($dailySales as $daySale) {
                $date = $daySale->sale_date;
                if (isset($tokoData[$tokoId]['time_series'][$date])) {
                    $tokoData[$tokoId]['time_series'][$date]['total_sales'] = $daySale->daily_total;
                    $tokoData[$tokoId]['time_series'][$date]['total_transactions'] = $daySale->transaction_count;
                }
            }
            
            // Convert assoc array to indexed array for time_series
            $tokoData[$tokoId]['time_series'] = array_values($tokoData[$tokoId]['time_series']);
        }
        
        return array_values($tokoData);
    }

    private function getBestSellingData($startDate, $endDate, $limit = 5)
    {
        // Make sure we have Carbon dates
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        // Get the date labels for the time series
        $dateLabels = $this->getDateLabels($startDate, $endDate);
        
        // Get top selling tokos
        $topTokos = TokoSelling::select(
            'toko_id',
            DB::raw("(SELECT name FROM toko WHERE id = toko_selling.toko_id) as toko_name"),
            DB::raw('SUM(total_harga) as total_sales'),
            DB::raw('COUNT(*) as total_transactions')
        )
        ->where('status', 'success')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('toko_id')
        ->orderBy('total_sales', 'desc')
        ->limit($limit)
        ->get();
        
        $tokoData = [];
        
        // Initialize toko data with zero values for all date periods
        foreach ($topTokos as $toko) {
            $tokoId = $toko->toko_id;
            $tokoData[$tokoId] = [
                'toko_id' => $tokoId,
                'toko_name' => $toko->toko_name,
                'total_sales' => $toko->total_sales,
                'total_transactions' => $toko->total_transactions,
                'time_series' => []
            ];
            
            // Initialize time series with zero values
            foreach ($dateLabels as $date => $label) {
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
        
        // Process data for each top toko
        foreach ($topTokos as $toko) {
            $tokoId = $toko->toko_id;
            
            // Get all sales with their details for this toko in the date range
            $sales = TokoSelling::with(['details', 'details.barangki', 'details.barangki.barangToko' => function($query) use ($tokoId) {
                $query->where('toko_id', $tokoId);
            }])
            ->where('toko_id', $tokoId)
            ->where('status', 'success')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
            
            // Process daily data
            foreach ($sales as $sale) {
                $saleDate = $sale->created_at->format('Y-m-d');
                
                // Skip if date not in our range (shouldn't happen but just in case)
                if (!isset($tokoData[$tokoId]['time_series'][$saleDate])) {
                    continue;
                }
                
                // Update sales and transaction count
                $tokoData[$tokoId]['time_series'][$saleDate]['total_sales'] += $sale->total_harga;
                $tokoData[$tokoId]['time_series'][$saleDate]['total_transactions']++;
                
                // Calculate margin and items sold from details
                foreach ($sale->details as $detail) {
                    $barangki = $detail->barangki;
                    if ($barangki) {
                        // Get the barangToko for this specific toko
                        $barangToko = $barangki->barangToko->where('toko_id', $tokoId)->first();
                        
                        if ($barangToko) {
                            // Calculate margin = selling price - (quantity * cost price)
                            $margin = $detail->subtotal - ($detail->jumlah * $barangToko->price_buy);
                            $tokoData[$tokoId]['time_series'][$saleDate]['margin'] += $margin;
                        }
                        
                        // Count items sold
                        if (isset($this->convertSatuanService)) {
                            // If you have a conversion service to handle different units
                            $convertedQuantity = $this->convertSatuanService->convertToSmallestUnit($barangki, $detail->jumlah);
                            $tokoData[$tokoId]['time_series'][$saleDate]['items_sold'] += $convertedQuantity['converted_amount'];
                        } else {
                            // If no conversion service, just use the raw quantity
                            $tokoData[$tokoId]['time_series'][$saleDate]['items_sold'] += $detail->jumlah;
                        }
                    }
                }
            }
            
            // Convert assoc array to indexed array for time_series
            $tokoData[$tokoId]['time_series'] = array_values($tokoData[$tokoId]['time_series']);
        }
        
        return array_values($tokoData);
    }

    /**
     * Generate date labels for the time series based on date range
     */
    private function getDateLabels(Carbon $startDate, Carbon $endDate)
    {
        $labels = [];
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            $date = $current->format('Y-m-d');
            $labels[$date] = $current->format('d M');
            $current->addDay();
        }
        
        return $labels;
    }

    private function getDateRange($period, $customStart = null, $customEnd = null)
    {
        $now = now();
        if ($period === 'custom' && $customStart && $customEnd) {
            return [
                'start_date' => Carbon::parse($customStart)->startOfDay(),
                'end_date' => Carbon::parse($customEnd)->endOfDay()
            ];
        }

        switch ($period) {
            case 'daily':
                $startDate = $now->copy()->startOfDay();
                break;
            case 'weekly':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                break;
            case 'monthly':
                $startDate = $now->copy()->startOfMonth();
                break;
            case 'yearly':
                $startDate = $now->copy()->startOfYear();
                break;
            default:
                $startDate = $now->copy()->subDays(6)->startOfDay();
        }

        return [
            'start_date' => $startDate,
            'end_date' => $now->endOfDay()
        ];
    }
    private function getTotalSales($startDate, $endDate)
    {
        return TokoPayment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'success')
            ->sum('total');
    }

    private function getSalesByPeriod($startDate, $endDate)
    {
        $days = $endDate->diffInDays($startDate);

        // Tentukan format grup
        $groupFormat = 'Y-m-d';
        if ($days > 90 && $days <= 365) {
            $groupFormat = 'Y-W'; // Weekly
        } elseif ($days > 365) {
            $groupFormat = 'Y-m'; // Monthly
        }

        return TokoPayment::select(
                DB::raw("DATE_FORMAT(created_at, '" . $this->getDbDateFormat($groupFormat) . "') as date"),
                DB::raw('SUM(total) as total_sales')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'success')
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '" . $this->getDbDateFormat($groupFormat) . "')"))
            ->orderBy('date')
            ->get()
            ->map(function ($item) use ($groupFormat) {
                try {
                    $originalDate = Carbon::createFromFormat('Y-m-d', $item->date);
                } catch (\Exception $e) {
                    $originalDate = Carbon::now(); // fallback
                }

                return [
                    'date' => match ($groupFormat) {
                        'Y-W' => 'Week ' . $originalDate->weekOfYear,
                        'Y-m' => $originalDate->format('F Y'),
                        default => $originalDate->format('Y-m-d'),
                    },
                    'total_sales' => (float)$item->total_sales
                ];
            });
    }

    

    private function getMarginData($startDate, $endDate, $limit = 5)
    {
        $margin = BarangKI::whereHas('tokoPesanan', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'success');
        })
        ->with('tokoPesanan')
        ->get()
        ->map(function ($barangKI) {
            $totalSales = $barangKI->tokoPesanan->sum('total');
            $totalQuantity = $barangKI->tokoPesanan->sum('quantity');
            $margin = $totalSales - ($totalQuantity * $barangKI->price_buy);

            $totalSmallestQuantity = 0;
            $convertedQuantity = null;
            foreach ($barangKI->tokoPesanan as $pesanan) {
                $convertedQuantity = $this->convertSatuanService->convertToSmallestUnit($barangKI, $pesanan->quantity);
                $totalSmallestQuantity += $convertedQuantity['converted_amount'];
            }
            return [
                'barang_id' => $barangKI->barang_id,
                'barang_name' => $barangKI->barang->name,
                'unit' => $barangKI->satuan->name,
                'total_sales' => $totalSales,
                'total_quantity' => $totalQuantity,
                'total_smallest_quantity' => $totalSmallestQuantity,
                'small_unit' => $convertedQuantity['converted_satuan'],
                'margin' => $margin,
            ];
        })
        ->groupBy('barang_id')
        ->map(function ($item) {
            return $item->sortByDesc('margin')->first();
        })
        ->sortByDesc('margin')
        ->take($limit)
        ->values()
        ->toArray();

        return $margin;
    }

    
    private function getPopulerBrand($startDate, $endDate)
    {
        $brandData = BarangKI::whereHas('tokoPesanan', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'success');
        })
        ->with('barang')
        ->get()
        ->groupBy('barang.brand_id')
        ->map(function ($item) {
            $brand = $item->first()->barang->brand;
            $totalSmallestQuantity = 0;
            foreach ($item as $barangKI) {
                foreach ($barangKI->tokoPesanan as $pesanan) {
                    $convertedQuantity = $this->convertSatuanService->convertToSmallestUnit($barangKI, $pesanan->quantity);
                    $totalSmallestQuantity += $convertedQuantity['converted_amount'];
                }
            }
            return [
                'brand_id' => $brand->id,
                'brand_name' => $brand->name,
                'total_quantity' => $totalSmallestQuantity,
                'unit' => $convertedQuantity['converted_satuan'],
            ];
        })
        ->sortByDesc('total_quantity')
        ->values()
        ->toArray();

        return $brandData;
    }


    private function getPopulerCategory($startDate, $endDate)
    {
        $brandData = BarangKI::whereHas('tokoPesanan', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'success');
        })
        ->with('barang')
        ->get()
        ->groupBy('barang.subcategory_id')
        ->map(function ($item) {
            $category = $item->first()->barang->subcategory->category;
            $totalSmallestQuantity = 0;
            foreach ($item as $barangKI) {
                foreach ($barangKI->tokoPesanan as $pesanan) {
                    $convertedQuantity = $this->convertSatuanService->convertToSmallestUnit($barangKI, $pesanan->quantity);
                    $totalSmallestQuantity += $convertedQuantity['converted_amount'];
                }
            }
            return [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'total_quantity' => $totalSmallestQuantity,
                'unit' => $convertedQuantity['converted_satuan'],
            ];
        })
        ->sortByDesc('total_quantity')
        ->values()
        ->toArray();

        return $brandData;
    }


    private function getDbDateFormat($groupFormat)
    {
        return match ($groupFormat) {
            'Y-W' => '%Y-%U',
            'Y-m' => '%Y-%m',
            default => '%Y-%m-%d'
        };
    }

    private function getBestSellingProducts($startDate, $endDate, $limit = 10)
    {
        // Get all sales data within the date range
        $salesData = TokoPesanan::select(
            'toko_pesanan.barangki_id',
            'toko_payment.created_at as payment_date',
            'toko_pesanan.quantity',
            'toko_pesanan.total',
            'barang_ki.barang_id',
            'barang_ki.satuan_id',
            'barang_ki.quantity as stock_quantity',
            'barang_ki.sold_quantity'
        )
        ->join('toko_payment', 'toko_pesanan.payment_id', '=', 'toko_payment.id')
        ->join('barang_ki', 'toko_pesanan.barangki_id', '=', 'barang_ki.id')
        ->whereBetween('toko_payment.created_at', [$startDate, $endDate])
        ->where('toko_payment.status', 'success')
        ->with(['barangKI.barang', 'barangKI.satuan'])
        ->get()
        ->map(function ($item) {
            $converted = $this->convertSatuanService->convertToSmallestUnit($item->barangKI, $item->quantity);
            return [
                'barang_id' => $item->barangKI->barang_id,
                'date' => $item->payment_date,
                'product_name' => optional($item->barangKI->barang)->name ?? 'Unknown',
                'sku' => optional($item->barangKI->barang)->sku ?? 'N/A',
                'unit' => $converted['converted_satuan'],
                'quantity' => $converted['converted_amount'],
                'total_sales' => (float)$item->total,
                'stock_quantity' => $item->stock_quantity,
                'sold_quantity' => $item->sold_quantity
            ];
        });

        if ($salesData->isEmpty()) return [];

        // Calculate time period duration
        $days = $endDate->diffInDays($startDate);
        $weeks = ceil($days / 7);
        $months = ceil($days / 30);
        $years = ceil($days / 365);

        // Determine grouping strategy based on time period
        $groupStrategy = match (true) {
            $days <= 7 => 'daily', // Daily grouping for up to 7 days
            $weeks <= 10 => 'weekly', // Weekly grouping for up to 10 weeks
            $months <= 12 => 'monthly', // Monthly grouping for up to 12 months
            default => 'yearly' // Yearly grouping for longer periods
        };

        // Group data by product and time period
        $groupedData = $salesData->groupBy('barang_id')
            ->map(function ($items) use ($groupStrategy, $startDate, $endDate) {
                $productData = [
                    'name' => $items->first()['product_name'],
                    'sku' => $items->first()['sku'],
                    'unit' => $items->first()['unit'],
                    'total_quantity' => $items->sum('quantity'),
                    'total_sales' => $items->sum('total_sales'),
                    'time_series' => []
                ];

                // Create time series data based on strategy
                $currentDate = $startDate->copy();
                while ($currentDate <= $endDate) {
                    $endPeriod = match ($groupStrategy) {
                        'daily' => $currentDate->copy()->endOfDay(),
                        'weekly' => $currentDate->copy()->endOfWeek(),
                        'monthly' => $currentDate->copy()->endOfMonth(),
                        'yearly' => $currentDate->copy()->endOfYear()
                    };

                    $periodSales = $items->filter(function ($sale) use ($currentDate, $endPeriod) {
                        $saleDate = Carbon::parse($sale['date']);
                        return $saleDate->between($currentDate, $endPeriod);
                    });
                    if ($periodSales->isNotEmpty()) {
                        $productData['time_series'][] = [
                            'period' => match ($groupStrategy) {
                                'daily' => $currentDate->format('Y-m-d'),
                                'weekly' => 'Week ' . $currentDate->weekOfYear,
                                'monthly' => $currentDate->format('F Y'),
                                'yearly' => $currentDate->format('Y')
                            },
                            'quantity' => $periodSales->sum('quantity'),
                            'sales' => $periodSales->sum('total_sales')
                        ];
                    }

                    $currentDate = match ($groupStrategy) {
                        'daily' => $currentDate->addDay(),
                        'weekly' => $currentDate->addWeek(),
                        'monthly' => $currentDate->addMonth(),
                        'yearly' => $currentDate->addYear()
                    };
                }

                return $productData;
            });

        // Sort by total quantity and limit results
        return $groupedData
            ->sortByDesc('total_quantity')
            ->take($limit)
            ->values()
            ->toArray();
    }

    private function getWorstSellingProducts($startDate, $endDate, $limit = 10)
    {
        $products = $this->getBestSellingProducts($startDate, $endDate);
        usort($products, fn($a, $b) => $a['total_quantity'] <=> $b['total_quantity']);
        return array_slice($products, 0, $limit);
    }

    private function getSalesGrowth($period)
    {
        $dates = $this->getDateRange($period);
        $current = $this->getTotalSales($dates['start_date'], $dates['end_date']);

        $length = $dates['end_date']->diffInDays($dates['start_date']) + 1;
        $prevEnd = $dates['start_date']->copy()->subDay();
        $prevStart = $prevEnd->copy()->subDays($length - 1);
        $previous = $this->getTotalSales($prevStart, $prevEnd);

        $growth = $previous ? (($current - $previous) / $previous) * 100 : ($current ? 100 : 0);

        return [
            'current' => round($current, 2),
            'previous' => round($previous, 2),
            'growth_percentage' => round($growth, 2),
            'is_positive' => $growth >= 0
        ];
    }

    private function getRevenueProjection($period)
    {
        $dates = $this->getDateRange($period);
        $length = $dates['end_date']->diffInDays($dates['start_date']) + 1;

        $historical = [];
        $endDate = $dates['end_date'];

        for ($i = 0; $i < 3; $i++) {
            $startDate = $endDate->copy()->subDays($length);
            $sales = TokoPayment::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'success')
                ->sum('total');
            $historical[] = [
                'period' => 3 - $i,
                'sales' => (float)$sales
            ];
            $endDate = $startDate->copy()->subDay();
        }

        $n = count($historical);
        $sumX = array_sum(array_column($historical, 'period'));
        $sumY = array_sum(array_column($historical, 'sales'));
        $sumXY = array_reduce($historical, fn($acc, $x) => $acc + ($x['period'] * $x['sales']), 0);
        $sumX2 = array_reduce($historical, fn($acc, $x) => $acc + ($x['period'] ** 2), 0);

        if ($n * $sumX2 - $sumX ** 2 == 0) {
            $projected = end($historical)['sales'];
        } else {
            $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX ** 2);
            $intercept = ($sumY - $slope * $sumX) / $n;
            $projected = max(0, $intercept + $slope * 0);
        }

        return [
            'projected_sales' => round($projected, 2),
            'start' => $dates['end_date']->copy()->addDay()->format('Y-m-d'),
            'end' => $dates['end_date']->copy()->addDays($length)->format('Y-m-d')
        ];
    }

    private function getProductInventoryStatus()
    {
        return BarangKI::select('id', 'barang_id', 'quantity', 'sold_quantity', 'expired_time')
            ->where('status', 'active')
            ->where('expired_time', '>', now())
            ->with(['barang', 'satuan'])
            ->withCount('tokoPesanan')
            ->get()
            ->map(function ($item) {
                // Konversi ke unit terkecil
                $converted = $this->convertSatuanService->convertToSmallestUnit($item, $item->quantity);
                $convertedSold = $this->convertSatuanService->convertToSmallestUnit($item, $item->tokoPesanan->sum('quantity'));
                
                $remaining = $converted['converted_amount'] - $convertedSold['converted_amount'];
                $stockLevel = 'Low';
    
                // Hitung persentase berdasarkan unit terkecil
                $percentage = ($converted['converted_amount'] > 0) 
                    ? ($remaining / $converted['converted_amount']) 
                    : 0;
    
                if ($percentage > 0.5) {
                    $stockLevel = 'High';
                } elseif ($percentage > 0.2) {
                    $stockLevel = 'Medium';
                }
    
                return [
                    'barang_id' => $item->barang_id,
                    'product_name' => optional($item->barang)->name ?? 'Unknown',
                    'product_sku' => optional($item->barang)->sku ?? 'N/A',
                    'initial_quantity' => (int)$converted['converted_amount'],
                    'sold_quantity' => (int)$convertedSold['converted_amount'],
                    'remaining_quantity' => (int)$remaining,
                    'stock_level' => $stockLevel,
                    'unit' => $converted['converted_satuan']
                ];
            })
            ->groupBy('barang_id')
            ->map(fn($items) => [
                'product_name' => $items->first()['product_name'],
                'product_sku' => $items->first()['product_sku'],
                'initial_quantity' => $items->sum('initial_quantity'),
                'sold_quantity' => $items->sum('sold_quantity'),
                'remaining_quantity' => $items->sum('remaining_quantity'),
                'stock_level' => $items->first()['stock_level'],
                'unit' => $items->first()['unit']
            ])
            ->values()
            ->toArray();
    }
}