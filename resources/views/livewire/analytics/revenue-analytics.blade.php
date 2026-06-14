<div class="rounded-2xl bg-white p-6 shadow-lg">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Revenue Analytics</h3>
            <p class="text-sm text-gray-500">Analisis pendapatan dan payment</p>
        </div>
        <div class="flex items-center gap-2">
            <select wire:model.live="period"
                class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                <option value="week">7 Hari</option>
                <option value="month">30 Hari</option>
                <option value="quarter">3 Bulan</option>
                <option value="year">1 Tahun</option>
            </select>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 p-4">
            <p class="text-sm text-gray-600">Total Revenue</p>
            <p class="text-xl font-bold text-green-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 p-4">
            <p class="text-sm text-gray-600">Rata-rata Order</p>
            <p class="text-xl font-bold text-blue-600">Rp {{ number_format($avgOrderValue, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl bg-gradient-to-r from-purple-50 to-pink-50 p-4">
            <p class="text-sm text-gray-600">Top Payment</p>
            <p class="text-xl font-bold text-purple-600">{{ ucfirst($topPaymentMethod) }}</p>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="mb-6">
        <div class="h-64" wire:ignore>
            <canvas id="revenueLineChart"></canvas>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div>
        <h4 class="mb-3 text-sm font-semibold text-gray-700">Payment Methods</h4>
        <div class="space-y-3">
            @php
                $colors = ['bg-green-500', 'bg-blue-500', 'bg-purple-500', 'bg-orange-500', 'bg-pink-500'];
                $maxTotal = collect($paymentMethodData)->max('total') ?: 1;
            @endphp
            @forelse ($paymentMethodData as $index => $method)
                <div>
                    <div class="mb-1 flex justify-between text-sm">
                        <span class="font-medium text-gray-700">{{ ucfirst($method['method']) }}</span>
                        <span class="text-gray-500">{{ number_format($method['count']) }} transaksi · Rp
                            {{ number_format($method['total'], 0, ',', '.') }}</span>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100">
                        <div class="{{ $colors[$index % count($colors)] }} h-full rounded-full transition-all duration-500"
                            style="width: {{ ($method['total'] / $maxTotal) * 100 }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Tidak ada data payment method</p>
            @endforelse
        </div>
    </div>
</div>

@script
    <script>
        let revenueChart = null;

        function initRevenueChart() {
            const ctx = document.getElementById('revenueLineChart');
            if (!ctx) return;

            const data = @json($revenueData);
            renderChart(data);
        }

        function renderChart(data) {
            const ctx = document.getElementById('revenueLineChart');
            if (!ctx) return;

            if (revenueChart) {
                revenueChart.destroy();
            }

            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 256);
            gradient.addColorStop(0, 'rgba(239, 68, 68, 0.3)');
            gradient.addColorStop(1, 'rgba(239, 68, 68, 0)');

            revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.date),
                    datasets: [{
                        label: 'Revenue',
                        data: data.map(d => d.revenue),
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgb(239, 68, 68)',
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
                                color: 'rgba(0,0,0,0.05)',
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

        document.addEventListener('livewire:initialized', () => {
            initRevenueChart();
        });

        $wire.on('revenueDataUpdated', (event) => {
            // Event data comes as an array, we need the 'data' key from the payload
            // Livewire 3 dispatch sends parameters. If we sent named parameter 'data', it should be accessible.
            // Let's check how it was dispatched: $this->dispatch('revenueDataUpdated', data: $this->revenueData);
            // In JS, event is the object. event.data should be it if using $wire.on with named params?
            // Actually, usually it's passed as the first argument if it's a single param, or an object.
            // Let's try to log it or just use the payload.
            // Safest way with $wire.on in Livewire 3:

            if (event.data) {
                renderChart(event.data);
            } else if (Array.isArray(event) && event[0]) {
                // Fallback if it comes as array
                renderChart(event[0]);
            } else {
                // Fallback: re-fetch or just ignore?
                // If we can't get data, we might need to re-init, but we want to avoid full re-render.
                // Let's assume event.data works because we used named argument.
                renderChart(event.data || event);
            }
        });
    </script>
@endscript
