<div class="space-y-6">
    {{-- Header with Period Selector --}}
    <div class="rounded-2xl bg-white p-6 shadow-lg">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-exchange-alt mr-2 text-red-500"></i>
                    Inventory In/Out Analytics
                </h2>
                <p class="mt-1 text-sm text-gray-500">Monitor inventory movements and trends</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <select wire:model.live="period"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                    <option value="daily">Hari Ini</option>
                    <option value="weekly">7 Hari Terakhir</option>
                    <option value="monthly">Bulan Ini</option>
                    <option value="yearly">Tahun Ini</option>
                    <option value="custom">Custom</option>
                </select>

                @if ($period === 'custom')
                    <input type="date" wire:model.live="startDate"
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                    <input type="date" wire:model.live="endDate"
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                @endif
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
        {{-- Total IN --}}
        <div class="rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Total IN</p>
                    <p class="mt-2 text-3xl font-bold">{{ number_format($totalIn) }}</p>
                    <p class="mt-1 text-xs opacity-75">Items</p>
                </div>
                <div class="rounded-full bg-white bg-opacity-20 p-3">
                    <i class="fas fa-arrow-down text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Total OUT --}}
        <div class="rounded-xl bg-gradient-to-br from-red-500 to-rose-600 p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Total OUT</p>
                    <p class="mt-2 text-3xl font-bold">{{ number_format($totalOut) }}</p>
                    <p class="mt-1 text-xs opacity-75">Items</p>
                </div>
                <div class="rounded-full bg-white bg-opacity-20 p-3">
                    <i class="fas fa-arrow-up text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Net Change --}}
        <div
            class="rounded-xl bg-gradient-to-br {{ $netChange >= 0 ? 'from-blue-500 to-indigo-600' : 'from-orange-500 to-amber-600' }} p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Net Change</p>
                    <p class="mt-2 text-3xl font-bold">{{ number_format($netChange) }}</p>
                    <p class="mt-1 text-xs opacity-75">Items</p>
                </div>
                <div class="rounded-full bg-white bg-opacity-20 p-3">
                    <i class="fas fa-balance-scale text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Value IN --}}
        <div class="rounded-xl bg-gradient-to-br from-purple-500 to-violet-600 p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Value IN</p>
                    <p class="mt-2 text-2xl font-bold">Rp {{ number_format($totalValueIn, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs opacity-75">Total</p>
                </div>
                <div class="rounded-full bg-white bg-opacity-20 p-3">
                    <i class="fas fa-coins text-2xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Value OUT --}}
        <div class="rounded-xl bg-gradient-to-br from-pink-500 to-rose-600 p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Value OUT</p>
                    <p class="mt-2 text-2xl font-bold">Rp {{ number_format($totalValueOut, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs opacity-75">Total</p>
                </div>
                <div class="rounded-full bg-white bg-opacity-20 p-3">
                    <i class="fas fa-money-bill-wave text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Flow Chart --}}
    <div class="rounded-2xl bg-white p-6 shadow-lg">
        <h3 class="mb-4 text-lg font-bold text-gray-800">
            <i class="fas fa-chart-line mr-2 text-blue-500"></i>
            Inventory Flow Trend
        </h3>
        <div class="h-80">
            <canvas id="flowChart"></canvas>
        </div>
    </div>

    {{-- Top Products & Users --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Top Products --}}
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <h4 class="mb-4 flex items-center text-lg font-bold text-gray-800">
                <i class="fas fa-trophy mr-2 text-yellow-500"></i>
                Top 10 Products by Movement
            </h4>
            <div class="space-y-3">
                @forelse ($topProducts as $index => $product)
                    <div
                        class="flex items-center justify-between rounded-lg bg-gradient-to-r from-gray-50 to-slate-50 p-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-red-400 to-rose-500 text-sm font-bold text-white">
                                {{ $index + 1 }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-gray-800">{{ Str::limit($product['name'], 30) }}</p>
                                <p class="text-xs text-gray-500">{{ $product['sku'] }} | {{ $product['satuan'] }}</p>
                            </div>
                        </div>
                        <div class="ml-2 text-right">
                            <p class="whitespace-nowrap font-semibold text-gray-800">
                                {{ number_format($product['total_movement']) }}</p>
                            <div class="flex gap-2 text-xs">
                                <span class="text-green-600">↓{{ number_format($product['total_in']) }}</span>
                                <span class="text-red-600">↑{{ number_format($product['total_out']) }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-sm text-gray-500">No data available</p>
                @endforelse
            </div>
        </div>

        {{-- Top Users --}}
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <h4 class="mb-4 flex items-center text-lg font-bold text-gray-800">
                <i class="fas fa-users mr-2 text-blue-500"></i>
                Top 10 Active Users
            </h4>
            <div class="space-y-3">
                @forelse ($topUsers as $index => $user)
                    <div
                        class="flex items-center justify-between rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50 p-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-blue-400 to-indigo-500 text-sm font-bold text-white">
                                {{ $index + 1 }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-gray-800">{{ $user['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $user['email'] }}</p>
                            </div>
                        </div>
                        <div class="ml-2 text-right">
                            <p class="whitespace-nowrap font-semibold text-gray-800">
                                {{ number_format($user['transaction_count']) }} txn</p>
                            <div class="flex gap-2 text-xs">
                                <span class="text-green-600">↓{{ number_format($user['total_in']) }}</span>
                                <span class="text-red-600">↑{{ number_format($user['total_out']) }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-sm text-gray-500">No data available</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="rounded-2xl bg-white p-6 shadow-lg">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-bold text-gray-800">Recent Transactions</h3>
            <div class="flex flex-col gap-3 sm:flex-row">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search..."
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                <select wire:model.live="filter"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                    <option value="all">All Types</option>
                    <option value="in">IN Only</option>
                    <option value="out">OUT Only</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-gray-500">
                        <th class="pb-3 font-semibold">Date</th>
                        <th class="pb-3 font-semibold">User</th>
                        <th class="pb-3 font-semibold">Product</th>
                        <th class="pb-3 font-semibold">Type</th>
                        <th class="pb-3 font-semibold">Quantity</th>
                        <th class="pb-3 font-semibold">Price</th>
                        <th class="pb-3 font-semibold">Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr class="border-b border-gray-100 transition-colors hover:bg-gray-50">
                            <td class="py-3 text-gray-600">
                                {{ $transaction->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="py-3 text-gray-800">
                                {{ $transaction->user?->name ?? '-' }}
                            </td>
                            <td class="py-3">
                                <p class="font-medium text-gray-800">
                                    {{ Str::limit($transaction->barangKI?->barang?->name ?? '-', 30) }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $transaction->barangKI?->barang?->sku ?? '-' }} |
                                    {{ $transaction->barangKI?->satuan?->name ?? '-' }}
                                </p>
                            </td>
                            <td class="py-3">
                                @if ($transaction->type === 'in')
                                    <span
                                        class="inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                        <i class="fas fa-arrow-down mr-1"></i>IN
                                    </span>
                                @else
                                    <span
                                        class="inline-block rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700">
                                        <i class="fas fa-arrow-up mr-1"></i>OUT
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 font-medium text-gray-800">
                                {{ number_format(abs($transaction->quantity)) }}
                            </td>
                            <td class="py-3 text-gray-600">
                                Rp {{ number_format($transaction->price, 0, ',', '.') }}
                            </td>
                            <td class="py-3 font-semibold text-gray-800">
                                Rp {{ number_format(abs($transaction->quantity) * $transaction->price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                No transactions found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $transactions->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const flowData = @json($flowData);

            const ctx = document.getElementById('flowChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: flowData.map(d => d.date),
                    datasets: [{
                            label: 'IN',
                            data: flowData.map(d => d.in),
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'OUT',
                            data: flowData.map(d => d.out),
                            borderColor: 'rgb(239, 68, 68)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Net',
                            data: flowData.map(d => d.net),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderDash: [5, 5]
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
