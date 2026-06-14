<!-- resources/views/components/analytics.blade.php -->

@php
    // Product Card Component
    $productCard = function ($title, $icon, $iconColor, $slot) {
        echo "
        <div class=\"rounded-2xl bg-white p-6 shadow-lg\">
            <h4 class=\"mb-4 flex items-center text-lg font-bold text-gray-800\">
                <i class=\"fas {$icon} mr-2 {$iconColor}\"></i>
                {$title}
            </h4>
            {$slot}
        </div>
        ";
    };

    // Product List Item Component
    $productListItem = function ($index, $name, $subtitle, $value, $bgColor, $badgeBg, $valueColor) {
        return "
        <div class=\"flex items-center justify-between rounded-lg bg-gradient-to-r {$bgColor} p-3\">
            <div class=\"flex items-center gap-3\">
                <div class=\"flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r {$badgeBg} text-sm font-bold text-white\">
                    " .
            ($index + 1) .
            "
                </div>
                <div class=\"min-w-0 flex-1\">
                    <p class=\"truncate font-medium text-gray-800\">" .
            Str::limit($name, 30) .
            "</p>
                    <p class=\"text-xs text-gray-500\">{$subtitle}</p>
                </div>
            </div>
            <p class=\"ml-2 whitespace-nowrap font-semibold {$valueColor}\">{$value}</p>
        </div>
        ";
    };

    // Stock Bar Component
    $stockBar = function ($percent, $remaining, $isLowStock) {
        $colorClass = $isLowStock ? 'bg-orange-500' : 'bg-green-500';
        $textColor = $isLowStock ? 'text-orange-500' : 'text-gray-600';

        return "
        <div class=\"flex items-center gap-2\">
            <div class=\"h-2 w-16 overflow-hidden rounded-full bg-gray-200\">
                <div class=\"h-full rounded-full {$colorClass}\" style=\"width: {$percent}%\"></div>
            </div>
            <span class=\"text-sm {$textColor}\">" .
            number_format($remaining) .
            "</span>
        </div>
        ";
    };

    // Status Badge Component
    $statusBadge = function ($type) {
        $badgeStyles = [
            'expired' => 'bg-red-100 text-red-700',
            'expiring' => 'bg-yellow-100 text-yellow-700',
            'low_stock' => 'bg-orange-100 text-orange-700',
            'normal' => 'bg-green-100 text-green-700',
        ];

        $badgeLabels = [
            'expired' => 'Expired',
            'expiring' => 'Expiring',
            'low_stock' => 'Low Stock',
            'normal' => 'Normal',
        ];

        $style = $badgeStyles[$type] ?? $badgeStyles['normal'];
        $label = $badgeLabels[$type] ?? 'Unknown';

        return "<span class=\"inline-block rounded-full px-2 py-1 text-xs font-medium {$style}\">{$label}</span>";
    };
@endphp

<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @php
            $stats = [
                ['label' => 'Total Produk', 'value' => $totalProducts, 'color' => 'blue', 'icon' => 'fa-boxes'],
                [
                    'label' => 'Low Stock',
                    'value' => $lowStockCount,
                    'color' => 'orange',
                    'icon' => 'fa-box-open',
                    'filter' => 'low_stock',
                ],
                [
                    'label' => 'Akan Expired',
                    'value' => $expiringSoonCount,
                    'color' => 'yellow',
                    'icon' => 'fa-clock',
                    'filter' => 'expiring',
                ],
                [
                    'label' => 'Expired',
                    'value' => $expiredCount,
                    'color' => 'red',
                    'icon' => 'fa-exclamation-triangle',
                    'filter' => 'expired',
                ],
            ];

            $colorMap = [
                'blue' => 'from-blue-400 to-indigo-500',
                'orange' => 'from-orange-400 to-amber-500',
                'yellow' => 'from-yellow-400 to-orange-500',
                'red' => 'from-red-400 to-rose-500',
            ];
        @endphp

        @foreach ($stats as $stat)
            <div class="rounded-xl bg-white p-4 shadow-md {{ isset($stat['filter']) ? 'cursor-pointer transition-all hover:shadow-lg' : '' }}"
                {{ isset($stat['filter']) ? "wire:click=\"\$set('filter', '{$stat['filter']}')" : '' }}>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">{{ $stat['label'] }}</p>
                        <p
                            class="text-2xl font-bold {{ $stat['color'] === 'blue' ? 'text-gray-800' : "text-{$stat['color']}-500" }}">
                            {{ number_format($stat['value']) }}
                        </p>
                    </div>
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br {{ $colorMap[$stat['color']] }}">
                        <i class="fas {{ $stat['icon'] }} text-white"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Best & Worst Selling -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Best Selling -->
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <h4 class="mb-4 flex items-center text-lg font-bold text-gray-800">
                <i class="fas fa-fire mr-2 text-red-500"></i> Produk Terlaris
            </h4>
            <div class="space-y-3">
                @forelse ($bestSelling as $index => $product)
                    {!! $productListItem(
                        $index,
                        $product['name'],
                        "{$product['qty']} terjual",
                        'Rp ' . number_format($product['sales'], 0, ',', '.'),
                        'from-green-50 to-emerald-50',
                        'from-green-400 to-emerald-500',
                        'text-green-600',
                    ) !!}
                @empty
                    <p class="text-sm text-gray-500">Tidak ada data penjualan</p>
                @endforelse
            </div>
        </div>

        <!-- Worst Selling -->
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <h4 class="mb-4 flex items-center text-lg font-bold text-gray-800">
                <i class="fas fa-chart-line mr-2 text-gray-400"></i> Perlu Perhatian
            </h4>
            <div class="space-y-3">
                @forelse ($worstSelling as $index => $product)
                    {!! $productListItem(
                        $index,
                        $product['name'],
                        "{$product['satuan']} | {$product['sold']} terjual",
                        "{$product['remaining']} sisa",
                        'from-gray-50 to-slate-50',
                        'from-gray-400 to-slate-500',
                        'text-gray-600',
                    ) !!}
                @empty
                    <p class="text-sm text-gray-500">Tidak ada data</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="rounded-2xl bg-white p-6 shadow-lg">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-bold text-gray-800">Daftar Produk</h3>
            <div class="flex flex-col gap-3 sm:flex-row">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari produk..."
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                <select wire:model.live="filter"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                    <option value="all">Semua</option>
                    <option value="low_stock">Low Stock</option>
                    <option value="expiring">Akan Expired</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-gray-500">
                        <th class="pb-3 font-semibold">Nama Produk</th>
                        <th class="pb-3 font-semibold">Satuan</th>
                        <th class="pb-3 font-semibold">Stok</th>
                        <th class="pb-3 font-semibold">Diskon</th>
                        <th class="pb-3 font-semibold">Expired</th>
                        <th class="pb-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        @php
                            $remaining = $product->total_qty - $product->total_sold;
                            $stockPercent = $product->total_qty > 0 ? ($remaining / $product->total_qty) * 100 : 0;
                            $status = $this->getProductStatus($product);
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="py-3">
                                <p class="font-medium text-gray-800">
                                    {{ Str::limit(optional($product->barang)->name, 40) }}</p>
                            </td>
                            <td class="py-3 text-gray-600">{{ $product->satuan->name ?? '-' }}</td>
                            <td class="py-3">
                                {!! $stockBar(min($stockPercent, 100), $remaining, $status['isLowStock']) !!}
                            </td>
                            <td class="py-3 text-gray-600">{!! $this->diskonStatus($product) !!}</td>
                            <td class="py-3">
                                @if ($product->latest_expired)
                                    <span class="{{ $status['dateColor'] }}">
                                        {{ \Carbon\Carbon::parse($product->latest_expired)->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-600">-</span>
                                @endif
                            </td>
                            <td class="py-3">
                                {!! $statusBadge($status['type']) !!}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                Tidak ada produk ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $products->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>
