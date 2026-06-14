<?php

namespace App\Services\Toko;

use App\Models\Barang\BarangModel;
use App\Models\Barang\TypeItem;
use App\Models\Barang\BarangKI;
use App\Models\BelanjaCepat\Kriteria;
use App\Models\BelanjaCepat\Subkriteria;
use App\Models\Toko\BarangToko;
use App\Models\Toko\TokoSellingDetail;
use App\Models\Toko\TokoSelling;
use App\Services\Barang\BarangKIService;
use App\Services\Barang\ConvertSatuanService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BelanjaCepatService
{
    /**
     * Memeriksa kelayakan toko untuk fitur Belanja Cepat
     *
     * @param int $tokoId
     * @param string $requestId
     * @return array
     */

    public function __construct(ConvertSatuanService $convertSatuanService, BarangKIService $barangKIService,)
    {
        $this->convertSatuanService = $convertSatuanService;
        $this->barangKIService = $barangKIService;
    }

    private function logRequest($requestId, $message, $context = [])
    {
        Log::info("[$requestId] " . $message, $context);
    }

    private function logWarning($requestId, $message)
    {
        Log::warning("[$requestId] " . $message);
    }

    public function checkStoreEligibility(int $tokoId, string $requestId): array
    {
        $salesCount = TokoSelling::where('toko_id', $tokoId)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->where('status', 'success')
            ->count();

        // if ($salesCount < 5) {
        //     return [
        //         'eligible' => false,
        //         'reason' => 'Toko harus memiliki minimal 5 transaksi dalam 30 hari terakhir',
        //         'weekly_sales' => 0
        //     ];
        // }

        $weeklySales = TokoSelling::where('toko_id', $tokoId)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->where('status', 'success')
            ->sum('total_harga');

        return [
            'eligible' => true,
            'weekly_sales' => $weeklySales
        ];
    }

    /**
     * Menghasilkan daftar belanja komprehensif berdasarkan berbagai kriteria
     *
     * @param int $tokoId
     * @param float $budget
     * @param int $maxItems
     * @param bool $includeExpiring
     * @param bool $includePopularItems
     * @param string $requestId
     * @return array
     */
    public function generateComprehensiveShoppingList(int $tokoId, float $budget, int $maxItems, bool $includeExpiring, bool $includePopularItems, string $requestId): array
    {
        // Mendapatkan data stok dan penjualan
        $stockData = $this->getStockData($tokoId, $requestId);
        $depletedItems = $this->getDepletedItemsByType($tokoId, $requestId);

        $popularItemsFromOtherStores = [];
        $expiringItems = [];
        if ($includePopularItems) {
            $popularItemsFromOtherStores = $this->getPopularItemsFromOtherStores($tokoId, $requestId);
        }

        if ($includeExpiring) {
            $expiringItems = $this->getExpiringItems($tokoId, $requestId);
        }

        // Menggabungkan semua rekomendasi
        $allRecommendations = $this->combineRecommendations(
            $stockData,
            $depletedItems,
            $popularItemsFromOtherStores,
            $expiringItems,
            $requestId
        );

        // Mengurutkan dan memfilter rekomendasi berdasarkan prioritas
        $prioritizedItems = $this->prioritizeRecommendations($allRecommendations, $requestId);

        // Mengoptimalkan daftar belanja berdasarkan budget dan batas jumlah item
        $finalList = $this->optimizeBudget($prioritizedItems, $budget, $maxItems, $requestId);

        return $finalList;
    }

    /**
     * Mendapatkan data stok dan penjualan barang di toko
     *
     * @param int $tokoId
     * @param string $requestId
     * @return array
     */
    private function getStockData(int $tokoId, string $requestId): array
    {

        // Mendapatkan semua barang toko yang memiliki quantity > 0 atau pernah terjual
        $barangToko = BarangToko::where('toko_id', $tokoId)
            ->where(function ($query) {
                $query->where('quantity', '>', 0)
                    ->orWhere('sold', '>', 0);
            })
            ->with(['barangKi', 'barangKi.barang', 'barangKi.barang.type', 'barangKi.satuan'])
            ->get();
        $result = [];

        foreach ($barangToko as $item) {
            if (!$item->barangKi || !$item->barangKi->barang) {
                continue;
            }
            $barang = $item->barangKi->barang;
            $barangKi = $item->barangKi;

            // Mendapatkan data penjualan mingguan untuk barang ini
            $weeklySales = $this->calculateWeeklySales($tokoId, $barangKi->id, $requestId);
            $conversatuans = $this->convertSatuanService->convertToSmallestUnit($barangKi, $weeklySales);
            $weeklySales = $conversatuans['converted_amount'];
            // Hanya mempertimbangkan barang dengan penjualan > 0
            if ($weeklySales <= 0) {
                continue;
            }

            // Menghitung inventory turnover rate
            $turnoverRate = $item->quantity > 0 ? $weeklySales / $item->quantity : 999;

            // Menghitung poin penilaian berdasarkan kriteria
            $score = $this->calculateItemScore($weeklySales, $turnoverRate, $item->quantity);
            $largestbarangki = $this->convertSatuanService->findLargestUnit($barangKi);

            if (!$largestbarangki['success']) {
                continue;
            }
            $barangValid = $this->barangKIService->getBarangValid($largestbarangki['data']->barang_id, $largestbarangki['data']->satuan_id);

            $result[] = [
                'barang_id' => $barangValid->barang_id,
                'barangki_id' => $largestbarangki['data']->id,
                'name' => $largestbarangki['data']->barang->name,
                'type_id' => $barang->type_id,
                'type_name' => $barang->type ? $barang->type->name : 'Unknown',
                'current_stock' => $item->quantity,
                'weekly_sales' => $weeklySales,
                'is_discounted' => $barangValid->is_discounted,
                'price_per_unit' => $barangValid->final_price,
                "discount_info" => $barangValid->discount_info,
                'unit_name' => $barangValid->satuan->name,
                'unit_id' => $barangValid->satuan->id,
                'score' => $score,
                'recommended_quantity' => $this->calculateRecommendedQuantity($weeklySales, $item->quantity, $barang->type_id),
                'total_price' => 0, // Akan dihitung nanti
                'purchase_reason' => 'Berdasarkan penjualan mingguan dan stok saat ini',
                'type' => 'sales_based',
                'is_depleted' => false
            ];
        }

        return $result;
    }

    /**
     * Menghitung penjualan mingguan untuk barang tertentu
     *
     * @param int $tokoId
     * @param int $barangKiId
     * @param string $requestId
     * @return int
     */
    private function calculateWeeklySales(int $tokoId, int $barangKiId, string $requestId): int
    {
        // Mendapatkan semua penjualan barang dalam 7 hari terakhir
        $weeklyTransactions = DB::table('toko_selling_detail')
            ->join('toko_selling', 'toko_selling.increment_id', '=', 'toko_selling_detail.transaction_id')
            ->where('toko_selling.toko_id', $tokoId)
            ->where('toko_selling_detail.barangki_id', $barangKiId)
            ->where('toko_selling.created_at', '>=', Carbon::now()->subDays(7))
            ->where('toko_selling.status', 'success')
            ->sum('toko_selling_detail.jumlah');


        return (int) $weeklyTransactions;
    }

    /**
     * Menghitung skor barang berdasarkan kriteria penilaian
     *
     * @param int $weeklySales
     * @param float $turnoverRate
     * @param int $currentStock
     * @return float
     */
    private function calculateItemScore(int $weeklySales, float $turnoverRate, int $currentStock): float
    {
        // Mendapatkan kriteria dan bobotnya dari database
        $kriteria = Kriteria::with('subkriteria')->get();
        $score = 0;

        foreach ($kriteria as $k) {
            switch ($k->nama) {
                case 'Penjualan':
                    // Semakin tinggi penjualan, semakin tinggi skor
                    $salesScore = $this->getScoreFromSubcriteria($k, $weeklySales);
                    $score += $salesScore * $k->bobot;
                    break;

                case 'Perputaran Stok':
                    // Semakin tinggi turnover rate, semakin tinggi skor
                    $turnoverScore = $this->getScoreFromSubcriteria($k, $turnoverRate);
                    $score += $turnoverScore * $k->bobot;
                    break;

                case 'Ketersediaan Stok':
                    // Semakin rendah stok, semakin tinggi skor
                    $stockScore = $this->getScoreFromSubcriteria($k, $currentStock);
                    $score += $stockScore * $k->bobot;
                    break;
            }
        }

        return $score;
    }

    /**
     * Mendapatkan skor dari subkriteria
     *
     * @param Kriteria $kriteria
     * @param float $value
     * @return float
     */
    private function getScoreFromSubcriteria(Kriteria $kriteria, float $value): float
    {
        // Jika tidak ada subkriteria, gunakan nilai default
        if ($kriteria->subkriteria->isEmpty()) {
            return 1.0;
        }

        // Cari subkriteria yang sesuai
        foreach ($kriteria->subkriteria as $sub) {
            // Implementasi logika untuk menentukan subkriteria yang sesuai
            // Contoh: untuk kriteria Penjualan, jika nilai > 10, gunakan subkriteria "Tinggi"

            // Logika ini harus disesuaikan dengan struktur subkriteria yang ada
            // Ini hanya contoh sederhana
            switch ($kriteria->nama) {
                case 'Penjualan':
                    if (($sub->nama == 'Tinggi' && $value > 10) ||
                        ($sub->nama == 'Sedang' && $value >= 5 && $value <= 10) ||
                        ($sub->nama == 'Rendah' && $value < 5)
                    ) {
                        return $sub->bobot;
                    }
                    break;

                case 'Perputaran Stok':
                    if (($sub->nama == 'Cepat' && $value > 1.5) ||
                        ($sub->nama == 'Normal' && $value >= 0.5 && $value <= 1.5) ||
                        ($sub->nama == 'Lambat' && $value < 0.5)
                    ) {
                        return $sub->bobot;
                    }
                    break;

                case 'Ketersediaan Stok':
                    if (($sub->nama == 'Rendah' && $value < 5) ||
                        ($sub->nama == 'Sedang' && $value >= 5 && $value <= 20) ||
                        ($sub->nama == 'Tinggi' && $value > 20)
                    ) {
                        return $sub->bobot;
                    }
                    break;
            }
        }

        // Nilai default jika tidak ada subkriteria yang cocok
        return 0.5;
    }

    /**
     * Menghitung jumlah yang direkomendasikan untuk dibeli
     *
     * @param int $weeklySales
     * @param int $currentStock
     * @param int $typeId
     * @return int
     */
    private function calculateRecommendedQuantity(int $weeklySales, int $currentStock, int $typeId): int
    {
        // Mendapatkan tipe barang
        $typeBarang = TypeItem::find($typeId);

        if (!$typeBarang) {
            // Default ke tipe Mingguan jika tidak ditemukan
            $typeFactor = 2;
        } else {
            // Faktor pengali berdasarkan tipe barang
            switch ($typeBarang->name) {
                case 'Harian':
                    $typeFactor = 7; // Stok untuk 7 hari
                    break;
                case 'Mingguan':
                    $typeFactor = 2; // Stok untuk 2 minggu
                    break;
                case 'Bulanan':
                    $typeFactor = 1; // Stok untuk 1 bulan
                    break;
                default:
                    $typeFactor = 2;
            }
        }

        // Hitung kebutuhan stok berdasarkan penjualan dan tipe
        $neededStock = $weeklySales * $typeFactor;

        // Jumlah yang direkomendasikan adalah selisih antara kebutuhan dan stok saat ini
        $recommendedQuantity = max(0, $neededStock - $currentStock);

        // Minimal beli 1 jika direkomendasikan
        return $recommendedQuantity > 0 ? $recommendedQuantity : 0;
    }

    /**
     * Mendapatkan barang yang hampir habis berdasarkan tipe barang
     *
     * @param int $tokoId
     * @param string $requestId
     * @return array
     */
    private function getDepletedItemsByType(int $tokoId, string $requestId): array
    {
        $result = [];

        // Mendapatkan semua barang di toko
        $barangToko = BarangToko::where('toko_id', $tokoId)
            ->with(['barangKi', 'barangKi.barang', 'barangKi.barang.type', 'barangKi.satuan'])
            ->get();

        foreach ($barangToko as $item) {
            if (!$item->barangKi || !$item->barangKi->barang || !$item->barangKi->barang->type) {
                continue;
            }

            $barang = $item->barangKi->barang;
            $barangKi = $item->barangKi;
            $type = $barang->type;

            // Mendapatkan data penjualan mingguan
            $weeklySales = $this->calculateWeeklySales($tokoId, $barangKi->id, $requestId);
            $conversatuans = $this->convertSatuanService->convertToSmallestUnit($barangKi, $weeklySales);

            $weeklySales = $conversatuans['converted_amount'];
            // Hanya pertimbangkan barang yang pernah terjual
            if ($weeklySales <= 0 && $item->sold <= 0) {
                continue;
            }

            // Menentukan threshold stok berdasarkan tipe barang
            $stockThreshold = $this->getStockThresholdByType($type->name, $weeklySales);

            // Jika stok di bawah threshold, rekomendasikan pembelian
            if ($item->quantity < $stockThreshold) {
                // Menghitung jumlah yang direkomendasikan
                $recommendedQuantity = $this->calculateRecommendedQuantity($weeklySales > 0 ? $weeklySales : 1, $item->quantity, $barang->type_id);

                if ($recommendedQuantity <= 0) {
                    continue;
                }
                $largestbarangki = $this->convertSatuanService->findLargestUnit($barangKi);
                $barangValid = $this->barangKIService->getBarangValid($largestbarangki['data']->barang_id, $largestbarangki['data']->satuan_id);

                $result[] = [
                    'barang_id' => $barangValid->barang_id,
                    'barangki_id' => $barangValid->id,
                    'name' => $barangValid->barang->name,
                    'type_id' => $barang->type_id,
                    'type_name' => $type->name,
                    'current_stock' => $item->quantity,
                    'weekly_sales' => $weeklySales,
                    'is_discounted' => $barangValid->is_discounted,
                    'price_per_unit' => $barangValid->final_price,
                    "discount_info" => $barangValid->discount_info,
                    'unit_name' => $barangValid->satuan->name,
                    'unit_id' => $barangValid->satuan->id,
                    'score' => 80 + (($stockThreshold - $item->quantity) / $stockThreshold) * 20, // Skor tinggi untuk barang yang hampir habis
                    'recommended_quantity' => $recommendedQuantity,
                    'total_price' => 0, // Akan dihitung nanti
                    'purchase_reason' => 'Stok hampir habis untuk barang tipe ' . $type->name,
                    'type' => 'depleted',
                    'is_depleted' => true
                ];
            }
        }

        return $result;
    }

    /**
     * Mendapatkan threshold stok berdasarkan tipe barang
     *
     * @param string $typeName
     * @param int $weeklySales
     * @return int
     */
    private function getStockThresholdByType(string $typeName, int $weeklySales): int
    {
        $minThreshold = 3; // Minimum threshold untuk barang apapun

        switch ($typeName) {
            case 'Harian':
                // Untuk barang harian, minimal stok adalah 2 hari penjualan
                return max($minThreshold, $weeklySales * 2 / 7);

            case 'Mingguan':
                // Untuk barang mingguan, minimal stok adalah 3 hari penjualan
                return max($minThreshold, $weeklySales * 3 / 7);

            case 'Bulanan':
                // Untuk barang bulanan, minimal stok adalah 5 hari penjualan
                return max($minThreshold, $weeklySales * 5 / 7);

            default:
                return $minThreshold;
        }
    }

    /**
     * Mendapatkan barang populer dari toko lain yang belum dimiliki toko ini
     *
     * @param int $tokoId
     * @param string $requestId
     * @return array
     */
    private function getPopularItemsFromOtherStores(int $tokoId, string $requestId): array
    {
        // Mendapatkan barang yang dimiliki toko saat ini
        $currentStoreItems = BarangToko::where('toko_id', $tokoId)
            ->pluck('barangki_id')
            ->toArray();

        // Mencari barang yang populer di toko lain dalam 30 hari terakhir
        $popularItems = DB::table('toko_selling_detail')
            ->join('toko_selling', 'toko_selling.increment_id', '=', 'toko_selling_detail.transaction_id')
            ->join('barang_ki', 'barang_ki.id', '=', 'toko_selling_detail.barangki_id')
            ->join('barang', 'barang.id', '=', 'barang_ki.barang_id')
            ->leftJoin('type_barang', 'type_barang.id', '=', 'barang.type_id')
            ->leftJoin('satuan_items', 'satuan_items.id', '=', 'barang_ki.satuan_id')
            ->where('toko_selling.toko_id', '!=', $tokoId)
            ->where('toko_selling.created_at', '>=', Carbon::now()->subDays(30))
            ->whereNotIn('toko_selling_detail.barangki_id', $currentStoreItems)
            ->select(
                'barang_ki.id as barangki_id',
                'barang.id as barang_id',
                'barang.name',
                'barang.type_id',
                'type_barang.name as type_name',
                'barang_ki.price_buy',
                'satuan_items.name as unit_name',
                DB::raw('SUM(toko_selling_detail.jumlah) as total_sold'),
                DB::raw('COUNT(DISTINCT toko_selling.toko_id) as store_count')
            )
            ->groupBy('barang_ki.id', 'barang.id', 'barang.name', 'barang.type_id', 'type_barang.name', 'barang_ki.price_buy', 'satuan_items.name')
            ->orderByDesc('total_sold')
            ->limit(20)
            ->get();

        $result = [];

        foreach ($popularItems as $item) {
            // Hanya pertimbangkan barang yang terjual di minimal 3 toko lain
            if ($item->store_count < 1) {
                continue;
            }

            // Hitung rata-rata penjualan per toko per minggu
            $avgWeeklySales = $item->total_sold / $item->store_count / 4; // Dari 30 hari ke 7 hari

            // Hitung jumlah yang direkomendasikan berdasarkan rata-rata penjualan
            $recommendedQuantity = $this->calculateInitialQuantityForNewItem($avgWeeklySales, $item->type_id);
            $largestbarangki = $this->convertSatuanService->findLargestUnit($item);

            $barangValid = $this->barangKIService->getBarangValid($largestbarangki['data']->barang_id, $largestbarangki['data']->satuan_id);
            $cekdiscount = $this->barangKIService->applyDiscountsToBarang($barangValid);

            $result[] = [
                'barang_id' => $barangValid->barang_id,
                'barangki_id' => $barangValid->id,
                'name' => $barangValid->barang->name,
                'type_id' => $item->type_id,
                'type_name' => $item->type_name ?? 'Unknown',
                'current_stock' => 0,
                'weekly_sales' => $avgWeeklySales,
                'price_per_unit' => $cekdiscount->final_price,
                'is_discounted' => $cekdiscount->is_discounted,
                "discount_info" => $cekdiscount->discount_info,
                'unit_name' => $barangValid->satuan->name,
                'unit_id' => $barangValid->satuan->id,
                'score' => 60 + ($item->store_count * 5), // Skor berdasarkan popularitas di toko lain
                'recommended_quantity' => $recommendedQuantity,
                'total_price' => 0,
                'purchase_reason' => 'Barang populer di toko lain',
                'type' => 'popular_elsewhere',
                'is_depleted' => false
            ];
        }

        return $result;
    }

    /**
     * Menghitung jumlah awal yang direkomendasikan untuk barang baru
     *
     * @param float $avgWeeklySales
     * @param int $typeId
     * @return int
     */
    private function calculateInitialQuantityForNewItem(float $avgWeeklySales, int $typeId): int
    {
        // Mendapatkan tipe barang
        $typeBarang = TypeItem::find($typeId);

        if (!$typeBarang) {
            // Default ke tipe Mingguan jika tidak ditemukan
            return max(5, ceil($avgWeeklySales * 1.5));
        }

        // Faktor pengali berdasarkan tipe barang
        switch ($typeBarang->name) {
            case 'Harian':
                return max(7, ceil($avgWeeklySales * 2)); // Stok untuk 2 minggu

            case 'Mingguan':
                return max(5, ceil($avgWeeklySales * 1.5)); // Stok untuk 1.5 minggu

            case 'Bulanan':
                return max(3, ceil($avgWeeklySales)); // Stok untuk 1 minggu

            default:
                return max(5, ceil($avgWeeklySales * 1.5));
        }
    }

    /**
     * Mendapatkan barang yang hampir kadaluarsa
     *
     * @param int $tokoId
     * @param string $requestId
     * @return array
     */
    private function getExpiringItems(int $tokoId, string $requestId): array
    {

        $result = [];

        // Mendapatkan semua barang di toko yang memiliki tanggal kadaluarsa
        $expiringItems = DB::table('barang_toko')
            ->join('barang_ki', 'barang_ki.id', '=', 'barang_toko.barangki_id')
            ->join('barang', 'barang.id', '=', 'barang_ki.barang_id')
            ->leftJoin('type_barang', 'type_barang.id', '=', 'barang.type_id')
            ->leftJoin('satuan_items', 'satuan_items.id', '=', 'barang_ki.satuan_id')
            ->where('barang_toko.toko_id', $tokoId)
            ->where('barang_toko.quantity', '>', 0)
            ->whereNotNull('barang_ki.expired_time')
            ->where('barang_ki.expired_time', '<=', Carbon::now()->addDays(15))
            ->select(
                'barang_ki.id as barangki_id',
                'barang.id as barang_id',
                'barang.name',
                'barang.type_id',
                'type_barang.name as type_name',
                'barang_ki.price_buy',
                'satuan_items.name as unit_name',
                'barang_toko.quantity',
                'barang_ki.expired_time',
                'barang_toko.sold'
            )
            ->get();

        foreach ($expiringItems as $item) {
            // Mendapatkan data penjualan mingguan
            $weeklySales = $this->calculateWeeklySales($tokoId, $item->barangki_id, $requestId);
            $conversatuans = $this->convertSatuanService->convertToSmallestUnit($item, $weeklySales);

            $weeklySales = $conversatuans['converted_amount'];
            // Hitung sisa hari sebelum kadaluarsa
            $daysUntilExpiry = Carbon::now()->diffInDays(Carbon::parse($item->expired_time), false);

            // Hanya pertimbangkan barang yang memiliki penjualan atau pernah terjual
            if ($weeklySales <= 0 && $item->sold <= 0) {
                continue;
            }

            // Menghitung jumlah yang direkomendasikan untuk penggantian
            $recommendedQuantity = $this->calculateExpiringReplacementQuantity($weeklySales > 0 ? $weeklySales : 1, $item->type_id);
            $largestbarangki = $this->convertSatuanService->findLargestUnit($item);
            $barangValid = $this->barangKIService->getBarangValid($largestbarangki['data']->barang_id, $largestbarangki['data']->satuan_id);


            $result[] = [
                'barang_id' => $barangValid->barang_id,
                'barangki_id' => $barangValid->id,
                'name' => $largestbarangki['data']->barang->name,
                'type_id' => $item->type_id,
                'type_name' => $item->type_name ?? 'Unknown',
                'current_stock' => $item->quantity,
                'weekly_sales' => $weeklySales,
                'is_discounted' => $barangValid->is_discounted,
                'price_per_unit' => $barangValid->final_price,
                "discount_info" => $barangValid->discount_info,
                'unit_name' => $barangValid->satuan->name,
                'unit_id' => $barangValid->satuan->id,
                'score' => 90 - $daysUntilExpiry * 2, // Skor tinggi untuk barang yang hampir kadaluarsa
                'recommended_quantity' => $recommendedQuantity,
                'total_price' => 0, // Akan dihitung nanti
                'purchase_reason' => 'Penggantian barang yang akan kadaluarsa dalam ' . $daysUntilExpiry . ' hari',
                'type' => 'expiring',
                'is_depleted' => false
            ];
        }

        return $result;
    }

    /**
     * Menghitung jumlah penggantian untuk barang yang hampir kadaluarsa
     *
     * @param float $weeklySales
     * @param int $typeId
     * @return int
     */
    private function calculateExpiringReplacementQuantity(float $weeklySales, int $typeId): int
    {
        // Mendapatkan tipe barang
        $typeBarang = TypeItem::find($typeId);

        if (!$typeBarang) {
            // Default ke tipe Mingguan jika tidak ditemukan
            return max(5, ceil($weeklySales * 2));
        }

        // Faktor pengali berdasarkan tipe barang
        switch ($typeBarang->name) {
            case 'Harian':
                return max(7, ceil($weeklySales * 2.5)); // Stok untuk 2.5 minggu

            case 'Mingguan':
                return max(5, ceil($weeklySales * 2)); // Stok untuk 2 minggu

            case 'Bulanan':
                return max(3, ceil($weeklySales * 1.5)); // Stok untuk 1.5 minggu

            default:
                return max(5, ceil($weeklySales * 2));
        }
    }

    /**
     * Menggabungkan semua rekomendasi dari berbagai sumber
     *
     * @param array $stockData
     * @param array $depletedItems
     * @param array $popularItemsFromOtherStores
     * @param array $expiringItems
     * @param string $requestId
     * @return array
     */
    private function combineRecommendations(
        array $stockData,
        array $depletedItems,
        array $popularItemsFromOtherStores,
        array $expiringItems,
        string $requestId
    ): array {

        // Buat indeks barang berdasarkan barangki_id
        $combinedItems = [];

        // Tambahkan barang yang hampir kadaluarsa terlebih dahulu (prioritas tertinggi)
        foreach ($expiringItems as $item) {
            $combinedItems[$item['barangki_id']] = $item;
        }

        // Tambahkan barang yang hampir habis
        foreach ($depletedItems as $item) {
            if (array_key_exists($item['barangki_id'], $combinedItems)) {
                // Jika barang sudah ada dalam daftar, pertahankan yang memiliki skor lebih tinggi
                if ($item['score'] > $combinedItems[$item['barangki_id']]['score']) {
                    $combinedItems[$item['barangki_id']] = $item;
                }
            } else {
                $combinedItems[$item['barangki_id']] = $item;
            }
        }

        // Tambahkan barang berdasarkan data penjualan
        foreach ($stockData as $item) {
            if (array_key_exists($item['barangki_id'], $combinedItems)) {
                // Jika barang sudah ada dalam daftar, pertahankan yang memiliki skor lebih tinggi
                if ($item['score'] > $combinedItems[$item['barangki_id']]['score']) {
                    $combinedItems[$item['barangki_id']] = $item;
                }
            } else {
                $combinedItems[$item['barangki_id']] = $item;
            }
        }

        // Tambahkan barang populer dari toko lain
        foreach ($popularItemsFromOtherStores as $item) {
            if (!array_key_exists($item['barangki_id'], $combinedItems)) {
                $combinedItems[$item['barangki_id']] = $item;
            }
        }

        // Kembalikan sebagai array biasa (bukan associative)
        return array_values($combinedItems);
    }

    /**
     * Mengurutkan dan memfilter rekomendasi berdasarkan prioritas
     *
     * @param array $recommendations
     * @param string $requestId
     * @return array
     */
    private function prioritizeRecommendations(array $recommendations, string $requestId): array
    {

        // Hitung total price dan bersihkan item dengan jumlah rekomendasi 0
        $filteredItems = [];
        foreach ($recommendations as $item) {
            if ($item['recommended_quantity'] <= 0) {
                continue;
            }

            // Hitung total harga
            $item['total_price'] = $item['price_per_unit'] * $item['recommended_quantity'];

            $filteredItems[] = $item;
        }

        // Urutkan berdasarkan skor dan type
        usort($filteredItems, function ($a, $b) {
            // Prioritaskan barang yang hampir habis atau hampir kadaluarsa
            if ($a['type'] === 'expiring' && $b['type'] !== 'expiring') {
                return -1;
            }
            if ($a['type'] !== 'expiring' && $b['type'] === 'expiring') {
                return 1;
            }
            if ($a['type'] === 'depleted' && $b['type'] !== 'depleted' && $b['type'] !== 'expiring') {
                return -1;
            }
            if ($a['type'] !== 'depleted' && $a['type'] !== 'expiring' && $b['type'] === 'depleted') {
                return 1;
            }

            // Jika tipe sama, urutkan berdasarkan skor
            return $b['score'] <=> $a['score'];
        });

        // Kelompokkan berdasarkan tipe barang
        $groupedItems = [];
        foreach ($filteredItems as $item) {
            $typeId = $item['type_id'];
            if (!isset($groupedItems[$typeId])) {
                $groupedItems[$typeId] = [
                    'type_id' => $typeId,
                    'type_name' => $item['type_name'],
                    'items' => []
                ];
            }
            $groupedItems[$typeId]['items'][] = $item;
        }

        // Mengembalikan item yang sudah dikelompokkan dan diurutkan
        return array_values($groupedItems);
    }

    /**
     * Mengoptimalkan daftar belanja berdasarkan budget dan batas jumlah item
     *
     * @param array $prioritizedGroupedItems
     * @param float $budget
     * @param int $maxItems
     * @param string $requestId
     * @return array
     */
    /**
     * Mengoptimalkan daftar belanja berdasarkan budget dan batas jumlah item
     *
     * @param array $prioritizedGroupedItems
     * @param float $budget
     * @param int $maxItems
     * @param string $requestId
     * @return array
     */
    private function optimizeBudget(array $prioritizedGroupedItems, float $budget, int $maxItems, string $requestId): array
    {
        // Flatkan daftar item dari grup
        $allItems = [];
        foreach ($prioritizedGroupedItems as $group) {
            foreach ($group['items'] as $item) {
                $allItems[] = $item;
            }
        }

        // Urutkan semua item berdasarkan prioritas
        usort($allItems, function ($a, $b) {
            // Prioritaskan barang yang hampir habis atau hampir kadaluarsa
            if ($a['type'] === 'expiring' && $b['type'] !== 'expiring') {
                return -1;
            }
            if ($a['type'] !== 'expiring' && $b['type'] === 'expiring') {
                return 1;
            }
            if ($a['type'] === 'depleted' && $b['type'] !== 'depleted' && $b['type'] !== 'expiring') {
                return -1;
            }
            if ($a['type'] !== 'depleted' && $a['type'] !== 'expiring' && $b['type'] === 'depleted') {
                return 1;
            }

            // Jika tipe sama, urutkan berdasarkan skor
            return $b['score'] <=> $a['score'];
        });

        // PERBAIKAN: Tentukan batas budget maksimal per item berdasarkan persentase dari total budget
        $maxBudgetPerItem = $budget * 0.3; // Maksimal 30% dari total budget untuk 1 item

        // Alokasikan budget dan batasi jumlah item
        $selectedItems = [];
        $totalCost = 0;
        $itemCount = 0;
        $remainingItems = []; // Untuk menyimpan item yang belum terpilih
        // Langkah 1: Alokasikan budget untuk item-item prioritas tinggi dengan batas maksimal per item
        foreach ($allItems as $item) {
            $itemCost = $item['price_per_unit'] * $item['recommended_quantity'];

            // Batasi biaya maksimal per item
            if ($itemCost > $maxBudgetPerItem) {
                // Kurangi jumlah yang direkomendasikan
                $maxQuantity = floor($maxBudgetPerItem / $item['price_per_unit']);
                if ($maxQuantity >= 1) {
                    $originalQuantity = $item['recommended_quantity'];
                    $item['recommended_quantity'] = $maxQuantity;
                    $item['total_price'] = $item['price_per_unit'] * $maxQuantity;
                    $itemCost = $item['total_price'];

                    // Simpan sisa quantity yang belum teralokasi
                    if ($originalQuantity > $maxQuantity) {
                        $remainingItem = $item;
                        $remainingItem['recommended_quantity'] = $originalQuantity - $maxQuantity;
                        $remainingItem['total_price'] = $remainingItem['price_per_unit'] * $remainingItem['recommended_quantity'];
                        $remainingItems[] = $remainingItem;
                    }
                }
            }

            // Jika menambahkan item ini akan melebihi budget atau maxItems
            if ($totalCost + $itemCost > $budget || $itemCount >= $maxItems) {
                // Simpan item untuk dipertimbangkan di tahap berikutnya
                $remainingItems[] = $item;
                continue;
            }

            $totalCost += $itemCost;
            $selectedItems[] = $item;
            $itemCount++;
        }

        // Langkah 2: Pastikan kita memiliki diversitas tipe item
        // Ambil tipe barang yang sudah terpilih
        $selectedTypeIds = [];
        foreach ($selectedItems as $item) {
            $selectedTypeIds[$item['type_id']] = true;
        }

        // Alokasikan budget untuk memastikan diversitas tipe barang
        foreach ($prioritizedGroupedItems as $group) {
            // Lewati tipe yang sudah ada dalam daftar terpilih
            if (isset($selectedTypeIds[$group['type_id']])) {
                continue;
            }

            // Cari item dengan skor tertinggi dari tipe ini
            usort($group['items'], function ($a, $b) {
                return $b['score'] <=> $a['score'];
            });

            if (!empty($group['items'])) {
                $topItem = $group['items'][0];
                $itemCost = $topItem['price_per_unit'] * $topItem['recommended_quantity'];

                // Jika terlalu mahal, kurangi jumlahnya
                if ($totalCost + $itemCost > $budget) {
                    $maxQuantity = floor(($budget - $totalCost) / $topItem['price_per_unit']);
                    if ($maxQuantity >= 1) {
                        $topItem['recommended_quantity'] = $maxQuantity;
                        $topItem['total_price'] = $topItem['price_per_unit'] * $maxQuantity;
                        $itemCost = $topItem['total_price'];
                    } else {
                        continue;
                    }
                }

                // Tambahkan item jika masih dalam budget dan belum mencapai maxItems
                if ($totalCost + $itemCost <= $budget && $itemCount < $maxItems) {
                    $totalCost += $itemCost;
                    $selectedItems[] = $topItem;
                    $itemCount++;
                    $selectedTypeIds[$group['type_id']] = true;
                }
            }
        }

        // Langkah 3: Gunakan sisa budget untuk item-item lain yang belum terpilih
        usort($remainingItems, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        foreach ($remainingItems as $item) {
            // Hitung berapa banyak yang bisa dibeli dengan sisa budget
            $maxQuantity = floor(($budget - $totalCost) / $item['price_per_unit']);

            if ($maxQuantity >= 1 && $itemCount < $maxItems) {
                $item['recommended_quantity'] = min($item['recommended_quantity'], $maxQuantity);
                $item['total_price'] = $item['price_per_unit'] * $item['recommended_quantity'];
                $totalCost += $item['total_price'];
                $selectedItems[] = $item;
                $itemCount++;
            }

            // Berhenti jika sudah mencapai maxItems
            if ($itemCount >= $maxItems) {
                break;
            }
        }

        // Kelompokkan item yang terpilih berdasarkan tipe barang
        $groupedSelectedItems = [];
        foreach ($selectedItems as $item) {
            $typeId = $item['type_id'];
            if (!isset($groupedSelectedItems[$typeId])) {
                $groupedSelectedItems[$typeId] = [
                    'type_id' => $typeId,
                    'type_name' => $item['type_name'],
                    'items' => []
                ];
            }
            $groupedSelectedItems[$typeId]['items'][] = $item;
        }

        // Kembalikan item yang sudah dikelompokkan berdasarkan tipe
        return [
            'items' => $selectedItems,
            'grouped_items' => array_values($groupedSelectedItems),
            'total_cost' => $totalCost
        ];
    }
}
