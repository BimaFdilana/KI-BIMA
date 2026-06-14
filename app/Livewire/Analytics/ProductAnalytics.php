<?php

namespace App\Livewire\Analytics;

use App\Models\Barang\BarangKI;
use App\Models\Toko\TokoPesanan;
use App\Services\Barang\ConvertSatuanService;
use App\Services\Barang\BarangKIService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ProductAnalytics extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter = 'all';
    public string $sortBy = 'sold';
    public string $sortDirection = 'desc';

    public int $totalProducts = 0;
    public int $lowStockCount = 0;
    public int $expiringSoonCount = 0;
    public int $expiredCount = 0;
    public array $bestSelling = [];
    public array $worstSelling = [];
    public array $categoryPerformance = [];

    #[\Livewire\Attributes\Reactive]
    public $categoryPerformanceData = null;

    protected array $queryString = ['search', 'filter'];

    private const SATUAN_COLORS = [
        1 => 'bg-blue-100 text-blue-800',
        2 => 'bg-green-100 text-green-800',
        3 => 'bg-yellow-100 text-yellow-800',
        4 => 'bg-red-100 text-red-800',
        5 => 'bg-purple-100 text-purple-800',
    ];

    public function mount(): void
    {
        $this->loadStats();
    }

    public function loadStats(): void
    {
        $this->totalProducts = $this->calculateTotalProducts();
        $this->lowStockCount = $this->calculateLowStockCount();
        $this->expiringSoonCount = $this->calculateExpiringSoonCount();
        $this->expiredCount = $this->calculateExpiredCount();
        $this->bestSelling = $this->calculateBestSelling();
        $this->worstSelling = $this->calculateWorstSelling();
        $this->categoryPerformance = $this->calculateCategoryPerformance();
    }

    private function calculateTotalProducts(): int
    {
        return BarangKI::where('status', 'active')
            ->distinct('barang_id', 'satuan_id')
            ->count(DB::raw('DISTINCT barang_id, satuan_id'));
    }

    private function calculateLowStockCount(): int
    {
        return BarangKI::where('status', 'active')
            ->where('quantity', '>', 0)
            ->selectRaw('barang_id, satuan_id, SUM(quantity) as total_qty, SUM(sold_quantity) as total_sold')
            ->groupBy('barang_id', 'satuan_id')
            ->havingRaw('SUM(quantity) - SUM(sold_quantity) < SUM(quantity) * 0.2')
            ->count();
    }

    private function calculateExpiringSoonCount(): int
    {
        return BarangKI::where('status', 'active')
            ->whereRaw('expired_time IS NOT NULL 
                AND expired_time <= NOW() + INTERVAL 30 DAY 
                AND expired_time > NOW()')
            ->selectRaw('barang_id, satuan_id, MAX(expired_time) as latest_expired')
            ->groupBy('barang_id', 'satuan_id')
            ->count();
    }

    private function calculateExpiredCount(): int
    {
        return BarangKI::where('status', 'active')
            ->whereRaw('expired_time IS NOT NULL AND expired_time <= NOW()')
            ->selectRaw('barang_id, satuan_id, MAX(expired_time) as latest_expired')
            ->groupBy('barang_id', 'satuan_id')
            ->count();
    }

    private function calculateBestSelling(): array
    {
        return TokoPesanan::selectRaw('barangki_id, SUM(quantity) as total_qty, SUM(total) as total_sales')
            ->whereMonth('created_at', now()->month)
            ->where('status', 'success')
            ->groupBy('barangki_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('barangKI.barang')
            ->get()
            ->map(fn($item) => [
                'id' => $item->barangki_id,
                'name' => $item->barangKI?->barang?->name ?? 'Unknown',
                'qty' => $item->total_qty,
                'sales' => $item->total_sales
            ])
            ->toArray();
    }

    private function calculateWorstSelling(): array
    {
        return BarangKI::where('status', 'active')
            ->selectRaw('barang_id, satuan_id, SUM(sold_quantity) as total_sold, SUM(quantity) as total_qty')
            ->groupBy('barang_id', 'satuan_id')
            ->orderBy('total_sold', 'asc')
            ->limit(5)
            ->with('barang')
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->barang?->name ?? 'Unknown',
                'satuan' => $item->satuan?->name ?? 'Unknown',
                'sold' => $item->total_sold,
                'remaining' => $item->total_qty - $item->total_sold
            ])
            ->toArray();
    }

    private function calculateCategoryPerformance()
    {
        return TokoPesanan::selectRaw('barangki_id, SUM(total) as total')
            ->whereMonth('created_at', now()->month)
            ->where('status', 'success')
            ->groupBy('barangki_id')
            ->with('barangKI.barang.subcategory.category')
            ->get()
            ->toArray();
    }

    public function getProductsProperty()
    {
        return BarangKI::query()
            ->where('status', 'active')
            ->selectRaw('
                barang_id, 
                satuan_id, 
                SUM(quantity) as total_qty, 
                SUM(sold_quantity) as total_sold, 
                MAX(expired_time) as latest_expired
            ')
            ->when($this->search, fn($q) => $this->applySearchFilter($q))
            ->when($this->filter !== 'all', fn($q) => $this->applyStatusFilter($q))
            ->with('barang', 'satuan')
            ->groupBy('barang_id', 'satuan_id')
            ->orderBy(
                $this->sortBy === 'sold' ? 'total_sold' : 'total_qty',
                $this->sortDirection
            )
            ->paginate(10);
    }

    private function applySearchFilter($query)
    {
        return $query->whereHas('barang', function ($q) {
            $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('sku', 'like', "%{$this->search}%");
        });
    }

    private function applyStatusFilter($query)
    {
        return match ($this->filter) {
            'low_stock' => $query->havingRaw('SUM(quantity) - SUM(sold_quantity) < SUM(quantity) * 0.2'),
            'expiring' => $query->havingRaw('MAX(expired_time) <= NOW() + INTERVAL 30 DAY AND MAX(expired_time) > NOW()'),
            'expired' => $query->havingRaw('MAX(expired_time) <= NOW()'),
            default => $query,
        };
    }

    public function getQuantityProperty($barangKi): string
    {
        $barangKIs = $this->getBarangKIVariants($barangKi);

        if ($barangKIs->isEmpty()) {
            return $this->renderEmptyQuantity();
        }

        $convertSatuanService = app(ConvertSatuanService::class);
        $converted = $convertSatuanService->convertBarangKeTerkecilDatatables($barangKIs);
        $result = $convertSatuanService->convertStock($barangKi, $converted['total']);

        return $this->formatQuantityResult($result, $barangKi);
    }

    private function getBarangKIVariants($barangKi)
    {
        return BarangKI::withTrashed()
            ->where('barang_id', $barangKi->barang_id)
            ->where('expired_time', $barangKi->expired_time)
            ->whereNull('deleted_at')
            ->get();
    }

    private function formatQuantityResult($result, $barangKi): string
    {
        if (is_string($result)) {
            return $this->renderStringQuantity($result);
        }

        if (empty($result['formatted'] ?? [])) {
            return $this->renderEmptyQuantity();
        }

        return $this->buildQuantityButtons($result, $barangKi);
    }

    private function buildQuantityButtons(array $result, $barangKi): string
    {
        $buttons = [];

        foreach ($result['formatted'] as $formattedItem) {
            $satuanData = $this->findSatuanData($formattedItem, $result);

            if (!$satuanData) {
                continue;
            }

            $button = $this->createButton($formattedItem, $satuanData, $result, $barangKi);
            $buttons[] = $button;
        }

        return sprintf('<div class="flex flex-wrap">%s</div>', implode('', $buttons));
    }

    private function findSatuanData(string $formattedItem, array $result): ?array
    {
        foreach ($result['satuans'] as $id => $satuan) {
            if (str_contains($formattedItem, $satuan['name'])) {
                return [
                    'id' => $id,
                    'satuan' => $satuan,
                    'level' => $satuan['level'] ?? 0
                ];
            }
        }

        return null;
    }

    private function createButton(string $formattedItem, array $satuanData, array $result, $barangKi): string
    {
        $colorClass = $this->getColorClass($satuanData['level']);
        $tooltip = $this->buildTooltip($satuanData['id'], $result, $barangKi);

        return sprintf(
            '<button type="button" class="inline-flex items-center justify-center px-2 py-1 rounded-sm text-xs font-medium mr-1 mb-1 %s" title="%s">%s</button>',
            htmlspecialchars($colorClass),
            htmlspecialchars($tooltip),
            htmlspecialchars($formattedItem)
        );
    }

    private function getColorClass(int $level): string
    {
        return self::SATUAN_COLORS[$level] ?? 'bg-gray-100 text-gray-800';
    }

    private function buildTooltip(int $satuanId, array $result, $barangKi): string
    {
        $hargaBeli = $barangKi->price_buy ?? 0;
        $hargaJual = $barangKi->price_sell ?? 0;
        $diskonPersen = 0;
        $discountStatus = 'No';

        if (isset($result['prices'][$satuanId])) {
            $prices = $result['prices'][$satuanId];
            $hargaBeli = $prices['harga_beli'] ?? $hargaBeli;
            $hargaJual = $prices['harga_jual'] ?? $hargaJual;
            $diskonPersen = $prices['diskon_persen'] ?? 0;
            $discountStatus = $prices['diskon_status'] ?? 'No';
        }

        $formattedBeli = number_format($hargaBeli, 0, ',', '.');
        $formattedJual = number_format($hargaJual, 0, ',', '.');

        $tooltip = "Harga Jual: Rp {$formattedJual} | Harga Beli: Rp {$formattedBeli}";

        if ($discountStatus !== 'No' && $diskonPersen) {
            $tooltip .= " | Diskon: {$discountStatus}({$diskonPersen})";
        }

        return $tooltip;
    }

    private function renderStringQuantity(string $quantity): string
    {
        return sprintf(
            '<span class="inline-flex items-center px-2 py-1 rounded-sm text-xs font-medium bg-blue-100 text-blue-800 mr-2 mb-1">%s</span>',
            htmlspecialchars($quantity)
        );
    }

    private function renderEmptyQuantity(): string
    {
        return '<span class="inline-flex items-center px-2 py-1 rounded-sm text-xs font-medium bg-gray-100 text-gray-800">0</span>';
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function diskonStatus($barangKi)
    {
        $diskon = app(BarangKIService::class)->applyDiscountsToBarang($barangKi);
        $class = '';
        $classHidden = '';
        if ($diskon['discount_status'] === 'active') {
            $class = 'bg-green-500';
            $classHidden = '';
        } elseif ($diskon['discount_status'] === 'coming') {
            $class = 'bg-yellow-500';
            $classHidden = '';
        } else {
            $class = 'bg-red-700';
            $classHidden = 'hidden';
        }

        // Format persentase dengan menghilangkan trailing zeros
        $diskonPercentage = rtrim(rtrim(number_format($diskon['discount_percentage'], 2, '.', ''), '0'), '.');

        $html = '<button data-modal-target="timeline-modal' . $barangKi->id . '" data-modal-toggle="timeline-modal' . $barangKi->id . '" type="button" class="rounded px-2 py-0.5 text-xs font-medium text-white hover:opacity-70 capitalize focus:outline-none ' . $class . '">' . $diskon['discount_status'] . '<span class="' . $classHidden . '"> (' . $diskonPercentage . '%)</span>' .  '</button>';
        return $html;
    }

    public function getProductStatus($product): array
    {
        $remaining = $product->total_qty - $product->total_sold;
        $stockPercent = $product->total_qty > 0 ? ($remaining / $product->total_qty) * 100 : 0;

        $isExpired = $product->latest_expired && $product->latest_expired <= now();
        $isExpiring = $product->latest_expired &&
            $product->latest_expired <= now()->addDays(30) &&
            $product->latest_expired > now();
        $isLowStock = $stockPercent < 20 && $product->total_qty > 0;

        return [
            'isLowStock' => $isLowStock,
            'isExpiring' => $isExpiring,
            'isExpired' => $isExpired,
            'label' => match (true) {
                $isExpired => 'expired',
                $isExpiring => 'expiring',
                $isLowStock => 'low_stock',
                default => 'normal',
            },
            'type' => match (true) {
                $isExpired => 'expired',
                $isExpiring => 'expiring',
                $isLowStock => 'low_stock',
                default => 'normal',
            },
            'dateColor' => match (true) {
                $isExpired => 'text-red-500',
                $isExpiring => 'text-yellow-500',
                default => 'text-gray-600',
            },
        ];
    }

    public function render()
    {
        return view('livewire.analytics.product-analytics', [
            'products' => $this->products
        ]);
    }
}
