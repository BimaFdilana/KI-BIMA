<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-white/80">Total Terkumpul</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($totalInfaq, 0, ',', '.') }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                    <i class="fas fa-hand-holding-heart text-white"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Donatur</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalDonors) }}</p>
                </div>
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-indigo-500">
                    <i class="fas fa-users text-white"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Kampanye Aktif</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($activeCampaigns) }}</p>
                </div>
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-purple-400 to-pink-500">
                    <i class="fas fa-bullhorn text-white"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Rata-rata Donasi</p>
                    <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($avgDonation, 0, ',', '.') }}</p>
                </div>
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-orange-400 to-amber-500">
                    <i class="fas fa-coins text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Campaigns Row -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Infaq Trend -->
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <div class="mb-4 flex items-center justify-between">
                <h4 class="text-lg font-bold text-gray-800">Trend Donasi</h4>
                <select wire:model.live="period" class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <option value="week">7 Hari</option>
                    <option value="month">30 Hari</option>
                    <option value="quarter">3 Bulan</option>
                </select>
            </div>
            <div class="h-64" wire:ignore>
                <canvas id="infaqTrendChart"></canvas>
            </div>
        </div>

        <!-- Top Campaigns -->
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <h4 class="mb-4 text-lg font-bold text-gray-800">Top Kampanye</h4>
            <div class="space-y-4">
                @forelse ($topCampaigns as $campaign)
                    <div
                        class="rounded-lg border border-gray-100 p-4 transition-all hover:border-emerald-200 hover:shadow-md">
                        <div class="mb-2 flex items-start justify-between">
                            <div>
                                <p class="font-medium text-gray-800">{{ Str::limit($campaign['title'], 40) }}</p>
                                <p class="text-xs text-gray-500">{{ $campaign['donors'] }} donatur</p>
                            </div>
                            @if ($campaign['is_active'])
                                <span
                                    class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">Aktif</span>
                            @else
                                <span
                                    class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-500">Selesai</span>
                            @endif
                        </div>
                        <div class="mb-1 flex justify-between text-sm">
                            <span class="text-emerald-600">Rp
                                {{ number_format($campaign['collected'], 0, ',', '.') }}</span>
                            <span class="text-gray-500">Target: Rp
                                {{ number_format($campaign['target'], 0, ',', '.') }}</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-teal-500 transition-all duration-500"
                                style="width: {{ $campaign['progress'] }}%"></div>
                        </div>
                        <p class="mt-1 text-right text-xs text-gray-500">{{ $campaign['progress'] }}%</p>
                    </div>
                @empty
                    <p class="text-center text-gray-500">Tidak ada kampanye</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Donations -->
    <div class="rounded-2xl bg-white p-6 shadow-lg">
        <h4 class="mb-4 text-lg font-bold text-gray-800">Donasi Terbaru</h4>
        <div class="space-y-3">
            @forelse ($recentDonations as $donation)
                <div
                    class="flex items-center justify-between rounded-lg bg-gradient-to-r from-emerald-50 to-teal-50 p-4 transition-all hover:from-emerald-100 hover:to-teal-100">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-emerald-400 to-teal-500 text-sm font-bold text-white">
                            {{ strtoupper(substr($donation['user'], 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $donation['user'] }}</p>
                            <p class="text-sm text-gray-500">{{ Str::limit($donation['campaign'], 30) }} ·
                                {{ $donation['created_at'] }}</p>
                        </div>
                    </div>
                    <p class="font-semibold text-emerald-600">Rp {{ number_format($donation['amount'], 0, ',', '.') }}
                    </p>
                </div>
            @empty
                <p class="text-center text-gray-500">Tidak ada donasi terbaru</p>
            @endforelse
        </div>
    </div>
</div>

@script
    <script>
        let infaqChart = null;

        function initInfaqChart() {
            const ctx = document.getElementById('infaqTrendChart');
            if (!ctx) return;

            const data = @json($infaqTrend);

            if (infaqChart) infaqChart.destroy();

            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 256);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

            infaqChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.date),
                    datasets: [{
                        label: 'Donasi',
                        data: data.map(d => d.amount),
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgb(16, 185, 129)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                    if (value >= 1000) return (value / 1000).toFixed(0) + 'K';
                                    return value;
                                }
                            }
                        }
                    }
                }
            });
        }

        document.addEventListener('livewire:initialized', initInfaqChart);
        $wire.on('statsUpdated', initInfaqChart);
    </script>
@endscript
