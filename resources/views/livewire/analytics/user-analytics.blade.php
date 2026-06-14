<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-4 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Users</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalUsers) }}</p>
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
                    <p class="text-sm text-gray-500">User Baru</p>
                    <p class="text-2xl font-bold text-green-500">+{{ number_format($newUsersThisMonth) }}</p>
                    <p class="text-xs text-gray-400">bulan ini</p>
                </div>
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-green-400 to-emerald-500">
                    <i class="fas fa-user-plus text-white"></i>
                </div>
            </div>
        </div>
        <div class="cursor-pointer rounded-xl bg-white p-4 shadow-md transition-all hover:shadow-lg"
            wire:click="$set('verificationFilter', 'ktp_verified')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">KTP Verified</p>
                    <p class="text-2xl font-bold text-purple-500">{{ number_format($ktpVerified) }}</p>
                    <p class="text-xs text-gray-400">
                        {{ $totalUsers > 0 ? round(($ktpVerified / $totalUsers) * 100) : 0 }}% dari total</p>
                </div>
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-purple-400 to-pink-500">
                    <i class="fas fa-id-card text-white"></i>
                </div>
            </div>
        </div>
        <div class="cursor-pointer rounded-xl bg-white p-4 shadow-md transition-all hover:shadow-lg"
            wire:click="$set('verificationFilter', 'phone_verified')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Phone Verified</p>
                    <p class="text-2xl font-bold text-cyan-500">{{ number_format($phoneVerified) }}</p>
                    <p class="text-xs text-gray-400">
                        {{ $totalUsers > 0 ? round(($phoneVerified / $totalUsers) * 100) : 0 }}% dari total</p>
                </div>
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-cyan-400 to-blue-500">
                    <i class="fas fa-phone-alt text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- User Growth Chart -->
        <div class="lg:col-span-2 rounded-2xl bg-white p-6 shadow-lg">
            <div class="mb-4 flex items-center justify-between">
                <h4 class="text-lg font-bold text-gray-800">Pertumbuhan User</h4>
                <select wire:model.live="period" class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <option value="week">7 Hari</option>
                    <option value="month">30 Hari</option>
                    <option value="quarter">3 Bulan</option>
                </select>
            </div>
            <div class="h-64" wire:ignore>
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        <!-- Gender Distribution -->
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <h4 class="mb-4 text-lg font-bold text-gray-800">Gender Distribution</h4>
            <div class="flex items-center justify-center">
                <div class="h-48 w-48" wire:ignore>
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
            <div class="mt-4 space-y-2">
                @foreach ($genderDistribution as $gender => $count)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <div
                                class="h-3 w-3 rounded-full {{ $gender === 'male' ? 'bg-blue-500' : ($gender === 'female' ? 'bg-pink-500' : 'bg-gray-400') }}">
                            </div>
                            <span class="text-gray-600">{{ ucfirst($gender) }}</span>
                        </div>
                        <span class="font-medium text-gray-800">{{ number_format($count) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="rounded-2xl bg-white p-6 shadow-lg">
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h4 class="text-lg font-bold text-gray-800">Daftar User</h4>
            <div class="flex flex-wrap gap-3">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari user..."
                    class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200 sm:w-auto">
                <select wire:model.live="verificationFilter"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <option value="all">Semua</option>
                    <option value="ktp_verified">KTP Verified</option>
                    <option value="ktp_pending">KTP Pending</option>
                    <option value="phone_verified">Phone Verified</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-sm text-gray-500">
                        <th class="pb-3">User</th>
                        <th class="pb-3">Email</th>
                        <th class="pb-3">Phone</th>
                        <th class="pb-3">KTP</th>
                        <th class="pb-3">Registered</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-red-400 to-pink-500 text-sm font-bold text-white">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ '@' . $user->username }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="py-3">
                                @if ($user->phone_number_verified_at)
                                    <span class="inline-flex items-center text-sm text-green-600">
                                        <i class="fas fa-check-circle mr-1"></i> {{ $user->phone_number }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">{{ $user->phone_number ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if ($user->ktp_verified)
                                    <span
                                        class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">Verified</span>
                                @elseif ($user->ktp_number)
                                    <span
                                        class="rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-700">Pending</span>
                                @else
                                    <span
                                        class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-500">Not
                                        Submitted</span>
                                @endif
                            </td>
                            <td class="py-3 text-sm text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">Tidak ada user ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>

@script
    <script>
        let userGrowthChart = null;
        let genderChart = null;

        function initCharts() {
            const growthData = @json($userGrowthData);
            const genderData = @json($genderDistribution);

            renderGrowthChart(growthData);
            renderGenderChart(genderData);
        }

        function renderGrowthChart(data) {
            const ctx = document.getElementById('userGrowthChart');
            if (!ctx) return;

            if (userGrowthChart) {
                userGrowthChart.destroy();
            }

            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 256);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

            userGrowthChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(d => d.date),
                    datasets: [{
                        label: 'New Users',
                        data: data.map(d => d.count),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
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
                            },
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        function renderGenderChart(data) {
            const ctx = document.getElementById('genderChart');
            if (!ctx) return;

            if (genderChart) {
                genderChart.destroy();
            }

            // Handle array or object data for gender
            const labels = Array.isArray(data) ? data.map(d => d.gender) : Object.keys(data);
            const values = Array.isArray(data) ? data.map(d => d.count) : Object.values(data);

            // Format labels
            const formattedLabels = labels.map(l => l.charAt(0).toUpperCase() + l.slice(1));

            genderChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: formattedLabels,
                    datasets: [{
                        data: values,
                        backgroundColor: ['rgb(59, 130, 246)', 'rgb(236, 72, 153)', 'rgb(156, 163, 175)'],
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

        document.addEventListener('livewire:initialized', initCharts);

        $wire.on('statsUpdated', (data) => {
            // Livewire 3 dispatch with named arguments passes an object
            // data.growthData and data.genderData should be available
            // Or if passed as array, access by index.
            // Let's handle both for robustness.

            let growth = [];
            let gender = [];

            if (data.growthData) {
                growth = data.growthData;
                gender = data.genderData;
            } else if (Array.isArray(data)) {
                // If passed as array arguments
                growth = data[0]; // growthData
                gender = data[1]; // genderData (if passed)
            } else {
                // Fallback or single argument
                growth = data;
            }

            if (growth) renderGrowthChart(growth);
            if (gender) renderGenderChart(gender);
        });
    </script>
@endscript
