<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-white/80">Total Limit</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($totalCreditLimit, 0, ',', '.') }}</p>
                    <p class="text-xs text-white/60">{{ number_format($totalAccounts) }} akun</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                    <i class="fas fa-wallet text-white"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Credit Used</p>
                    <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalCreditUsed, 0, ',', '.') }}
                    </p>
                    @if ($totalCreditLimit > 0)
                        <p class="text-xs text-gray-400">{{ round(($totalCreditUsed / $totalCreditLimit) * 100) }}%
                            terpakai</p>
                    @endif
                </div>
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-indigo-500">
                    <i class="fas fa-credit-card text-white"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Akun Aktif</p>
                    <p class="text-2xl font-bold text-green-500">{{ number_format($activeAccounts) }}</p>
                    @if ($totalAccounts > 0)
                        <p class="text-xs text-gray-400">{{ round(($activeAccounts / $totalAccounts) * 100) }}% dari
                            total</p>
                    @endif
                </div>
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-green-400 to-emerald-500">
                    <i class="fas fa-user-check text-white"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Overdue</p>
                    <p class="text-2xl font-bold {{ $overdueCount > 0 ? 'text-red-500' : 'text-gray-800' }}">
                        {{ number_format($overdueCount) }}
                    </p>
                    <p class="text-xs {{ $repaymentRate >= 90 ? 'text-green-500' : 'text-yellow-500' }}">
                        {{ $repaymentRate }}% repayment rate
                    </p>
                </div>
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-red-400 to-rose-500">
                    <i class="fas fa-exclamation-circle text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Credit Utilization -->
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <h4 class="mb-4 text-lg font-bold text-gray-800">Credit Utilization</h4>
            <div class="flex items-center justify-center">
                <div class="h-48 w-48" wire:ignore>
                    <canvas id="utilizationChart"></canvas>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2">
                @php
                    $colors = ['bg-green-500', 'bg-blue-500', 'bg-yellow-500', 'bg-red-500'];
                @endphp
                @foreach ($creditUtilization as $range => $count)
                    <div class="flex items-center gap-2 text-sm">
                        <div class="h-3 w-3 rounded-full {{ $colors[$loop->index % 4] }}"></div>
                        <span class="text-gray-600">{{ $range }}</span>
                        <span class="ml-auto font-medium text-gray-800">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Account Status -->
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <h4 class="mb-4 text-lg font-bold text-gray-800">Status Akun</h4>
            <div class="space-y-4">
                @php
                    $statusColors = [
                        'active' => 'bg-green-500',
                        'pending' => 'bg-yellow-500',
                        'suspended' => 'bg-red-500',
                        'closed' => 'bg-gray-500',
                    ];
                    $maxCount = count($accountStats) > 0 ? max($accountStats) : 1;
                @endphp
                @forelse ($accountStats as $status => $count)
                    <div>
                        <div class="mb-1 flex justify-between text-sm">
                            <span class="font-medium text-gray-700">{{ ucfirst($status) }}</span>
                            <span class="text-gray-500">{{ number_format($count) }}</span>
                        </div>
                        <div class="h-3 w-full overflow-hidden rounded-full bg-gray-100">
                            <div class="{{ $statusColors[$status] ?? 'bg-gray-400' }} h-full rounded-full transition-all duration-500"
                                style="width: {{ ($count / $maxCount) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500">Tidak ada data akun</p>
                @endforelse

                <!-- Credit Overview -->
                <div class="mt-6 rounded-lg border border-indigo-100 bg-gradient-to-r from-indigo-50 to-purple-50 p-4">
                    <h5 class="mb-2 text-sm font-semibold text-gray-700">Credit Overview</h5>
                    <div class="mb-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Used / Total</span>
                            <span class="font-medium text-gray-800">
                                Rp {{ number_format($totalCreditUsed, 0, ',', '.') }} / Rp
                                {{ number_format($totalCreditLimit, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    <div class="h-4 w-full overflow-hidden rounded-full bg-gray-200">
                        @php
                            $usedPercent =
                                $totalCreditLimit > 0 ? min(100, ($totalCreditUsed / $totalCreditLimit) * 100) : 0;
                        @endphp
                        <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-500"
                            style="width: {{ $usedPercent }}%"></div>
                    </div>
                    <p class="mt-1 text-right text-xs text-gray-500">{{ round($usedPercent) }}% utilized</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="rounded-2xl bg-white p-6 shadow-lg">
        <h4 class="mb-4 text-lg font-bold text-gray-800">Transaksi Terbaru</h4>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-sm text-gray-500">
                        <th class="pb-3">User / Toko</th>
                        <th class="pb-3">Amount</th>
                        <th class="pb-3">Due Date</th>
                        <th class="pb-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentTransactions as $tx)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3">
                                <p class="font-medium text-gray-800">{{ $tx['user'] }}</p>
                                <p class="text-xs text-gray-500">{{ $tx['toko'] }}</p>
                            </td>
                            <td class="py-3 font-medium text-gray-800">Rp
                                {{ number_format($tx['amount'], 0, ',', '.') }}</td>
                            <td class="py-3 text-sm {{ $tx['is_overdue'] ? 'text-red-500' : 'text-gray-600' }}">
                                {{ $tx['due_date'] }}
                                @if ($tx['is_overdue'])
                                    <i class="fas fa-exclamation-triangle ml-1 text-red-500"></i>
                                @endif
                            </td>
                            <td class="py-3">
                                <span
                                    class="inline-flex rounded-full px-2 py-1 text-xs font-medium
                                    {{ $tx['status'] === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $tx['status'] === 'pending' ? ($tx['is_overdue'] ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') : '' }}
                                    {{ $tx['status'] === 'overdue' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ $tx['is_overdue'] ? 'Overdue' : ucfirst($tx['status']) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500">Tidak ada transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@script
    <script>
        let utilizationChart = null;

        function initUtilizationChart() {
            const ctx = document.getElementById('utilizationChart');
            if (!ctx) return;

            const data = @json($creditUtilization);

            if (utilizationChart) utilizationChart.destroy();

            utilizationChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(data),
                    datasets: [{
                        data: Object.values(data),
                        backgroundColor: ['rgb(34, 197, 94)', 'rgb(59, 130, 246)', 'rgb(234, 179, 8)',
                            'rgb(239, 68, 68)'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        document.addEventListener('livewire:initialized', initUtilizationChart);
    </script>
@endscript
