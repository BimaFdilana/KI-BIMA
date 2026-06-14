<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-4 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Transaksi</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalTransactions) }}</p>
                </div>
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-indigo-500">
                    <i class="fas fa-receipt text-white"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Success Rate</p>
                    <p
                        class="text-2xl font-bold {{ $successRate >= 80 ? 'text-green-500' : ($successRate >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                        {{ $successRate }}%
                    </p>
                </div>
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-green-400 to-emerald-500">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
            </div>
        </div>
        @php
            $pendingCount = collect($statusDistribution)->where('status', 'pending')->first()['count'] ?? 0;
            $paidCount = collect($statusDistribution)->where('status', 'paid')->first()['count'] ?? 0;
        @endphp
        <div class="cursor-pointer rounded-xl bg-white p-4 shadow-md transition-all hover:shadow-lg"
            wire:click="$set('statusFilter', 'pending')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pending</p>
                    <p class="text-2xl font-bold text-yellow-500">{{ number_format($pendingCount) }}</p>
                </div>
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-yellow-400 to-orange-500">
                    <i class="fas fa-clock text-white"></i>
                </div>
            </div>
        </div>
        <div class="cursor-pointer rounded-xl bg-white p-4 shadow-md transition-all hover:shadow-lg"
            wire:click="$set('statusFilter', 'paid')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Paid (Belum Kirim)</p>
                    <p class="text-2xl font-bold text-blue-500">{{ number_format($paidCount) }}</p>
                </div>
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-cyan-500">
                    <i class="fas fa-truck text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Payment Trend -->
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <div class="mb-4 flex items-center justify-between">
                <h4 class="text-lg font-bold text-gray-800">Trend Transaksi</h4>
                <select wire:model.live="period" class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <option value="week">7 Hari</option>
                    <option value="month">30 Hari</option>
                    <option value="quarter">3 Bulan</option>
                </select>
            </div>
            <div class="h-64" wire:ignore>
                <canvas id="paymentTrendChart"></canvas>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <h4 class="mb-4 text-lg font-bold text-gray-800">Status Transaksi</h4>
            <div class="space-y-4">
                @php
                    $statusColors = [
                        'success' => 'bg-green-500',
                        'paid' => 'bg-blue-500',
                        'pending' => 'bg-yellow-500',
                        'failed' => 'bg-red-500',
                        'delivery' => 'bg-purple-500',
                        'cancelled' => 'bg-gray-500',
                    ];
                    $maxCount = collect($statusDistribution)->max('count') ?: 1;
                @endphp
                @foreach ($statusDistribution as $item)
                    <div>
                        <div class="mb-1 flex justify-between text-sm">
                            <span class="font-medium text-gray-700">{{ ucfirst($item['status']) }}</span>
                            <span class="text-gray-500">{{ number_format($item['count']) }} · Rp
                                {{ number_format($item['total'], 0, ',', '.') }}</span>
                        </div>
                        <div class="h-3 w-full overflow-hidden rounded-full bg-gray-100">
                            <div class="{{ $statusColors[$item['status']] ?? 'bg-gray-400' }} h-full rounded-full transition-all duration-500"
                                style="width: {{ ($item['count'] / $maxCount) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="rounded-2xl bg-white p-6 shadow-lg">
        <h4 class="mb-4 text-lg font-bold text-gray-800">Transaksi Terbaru</h4>
        <div class="space-y-3">
            @forelse ($recentTransactions as $tx)
                <div
                    class="flex items-center justify-between rounded-lg bg-gray-50 p-4 transition-all hover:bg-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="hidden sm:block">
                            @switch($tx['status'])
                                @case('success')
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100">
                                        <i class="fas fa-check text-green-500"></i>
                                    </div>
                                @break

                                @case('paid')
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100">
                                        <i class="fas fa-credit-card text-blue-500"></i>
                                    </div>
                                @break

                                @case('pending')
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100">
                                        <i class="fas fa-clock text-yellow-500"></i>
                                    </div>
                                @break

                                @default
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                                        <i class="fas fa-receipt text-gray-500"></i>
                                    </div>
                            @endswitch
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $tx['transaction_id'] }}</p>
                            <p class="text-sm text-gray-500">{{ $tx['toko'] }} · {{ $tx['created_at'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-800">Rp {{ number_format($tx['total'], 0, ',', '.') }}</p>
                        <span
                            class="inline-flex rounded-full px-2 py-1 text-xs font-medium
                            {{ $tx['status'] === 'success' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $tx['status'] === 'paid' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $tx['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $tx['status'] === 'failed' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ ucfirst($tx['status']) }}
                        </span>
                    </div>
                </div>
                @empty
                    <p class="text-center text-gray-500">Tidak ada transaksi terbaru</p>
                @endforelse
            </div>
        </div>

        <!-- Payments Table -->
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h4 class="text-lg font-bold text-gray-800">Semua Transaksi</h4>
                <div class="flex flex-wrap gap-3">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari transaksi..."
                        class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200 sm:w-auto">
                    <select wire:model.live="statusFilter" class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
                        <option value="all">Semua Status</option>
                        <option value="success">Success</option>
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                        <option value="delivery">Delivery</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-sm text-gray-500">
                            <th class="pb-3">Transaction ID</th>
                            <th class="pb-3">Toko</th>
                            <th class="pb-3">User</th>
                            <th class="pb-3">Total</th>
                            <th class="pb-3">Method</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 font-mono text-sm text-gray-800">{{ $payment->transaction_id }}</td>
                                <td class="py-3 text-sm text-gray-600">{{ optional($payment->toko)->name ?? '-' }}</td>
                                <td class="py-3 text-sm text-gray-600">{{ optional($payment->user)->name ?? '-' }}</td>
                                <td class="py-3 font-medium text-gray-800">Rp
                                    {{ number_format($payment->total, 0, ',', '.') }}</td>
                                <td class="py-3 text-sm text-gray-600">{{ ucfirst($payment->payment_method ?? '-') }}</td>
                                <td class="py-3">
                                    <span
                                        class="inline-flex rounded-full px-2 py-1 text-xs font-medium
                                    {{ $payment->status === 'success' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $payment->status === 'paid' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $payment->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $payment->status === 'delivery' ? 'bg-purple-100 text-purple-700' : '' }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="py-3 text-sm text-gray-500">{{ $payment->created_at->format('d M Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500">Tidak ada transaksi ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $payments->links() }}
            </div>
        </div>
    </div>

    @script
        <script>
            let paymentChart = null;

            function initPaymentChart() {
                const ctx = document.getElementById('paymentTrendChart');
                if (!ctx) return;

                const data = @json($paymentTrend);

                if (paymentChart) paymentChart.destroy();

                paymentChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(d => d.date),
                        datasets: [{
                            label: 'Transactions',
                            data: data.map(d => d.count),
                            backgroundColor: 'rgba(99, 102, 241, 0.8)',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            }
                        }
                    }
                });
            }

            document.addEventListener('livewire:initialized', initPaymentChart);
            $wire.on('statsUpdated', initPaymentChart);
        </script>
    @endscript
