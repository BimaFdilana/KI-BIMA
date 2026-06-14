<div wire:poll.10s="loadData">
    <!-- Main KPI Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Revenue Card -->
        <div
            class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
            <div
                class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-gradient-to-br from-green-400/20 to-emerald-500/20 blur-2xl transition-all group-hover:scale-150">
            </div>
            <div class="relative">
                <div
                    class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-green-400 to-emerald-500 shadow-lg shadow-green-500/30">
                    <i class="fas fa-coins text-2xl text-white"></i>
                </div>
                <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                <h3 class="mt-1 text-2xl font-bold text-gray-800">
                    Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}
                </h3>
                @if ($revenueGrowth >= 0)
                    <p class="mt-2 flex items-center text-sm text-green-500">
                        <i class="bi bi-graph-up-arrow mr-1"></i>
                        <span>+{{ $revenueGrowth }}% dari bulan lalu</span>
                    </p>
                @else
                    <p class="mt-2 flex items-center text-sm text-red-500">
                        <i class="bi bi-graph-down-arrow mr-1"></i>
                        <span>{{ $revenueGrowth }}% dari bulan lalu</span>
                    </p>
                @endif
            </div>
        </div>

        <!-- Orders Card -->
        <div
            class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
            <div
                class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-gradient-to-br from-blue-400/20 to-indigo-500/20 blur-2xl transition-all group-hover:scale-150">
            </div>
            <div class="relative">
                <div
                    class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-400 to-indigo-500 shadow-lg shadow-blue-500/30">
                    <i class="fas fa-shopping-cart text-2xl text-white"></i>
                </div>
                <p class="text-sm font-medium text-gray-500">Total Orders</p>
                <h3 class="mt-1 text-2xl font-bold text-gray-800">{{ number_format($totalOrders) }}</h3>
                @if ($orderGrowth >= 0)
                    <p class="mt-2 flex items-center text-sm text-green-500">
                        <i class="bi bi-graph-up-arrow mr-1"></i>
                        <span>+{{ $orderGrowth }}% dari bulan lalu</span>
                    </p>
                @else
                    <p class="mt-2 flex items-center text-sm text-red-500">
                        <i class="bi bi-graph-down-arrow mr-1"></i>
                        <span>{{ $orderGrowth }}% dari bulan lalu</span>
                    </p>
                @endif
            </div>
        </div>

        <!-- Active Stores Card -->
        <div
            class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
            <div
                class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-gradient-to-br from-purple-400/20 to-pink-500/20 blur-2xl transition-all group-hover:scale-150">
            </div>
            <div class="relative">
                <div
                    class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-400 to-pink-500 shadow-lg shadow-purple-500/30">
                    <i class="fas fa-store text-2xl text-white"></i>
                </div>
                <p class="text-sm font-medium text-gray-500">Toko Aktif</p>
                <h3 class="mt-1 text-2xl font-bold text-gray-800">{{ number_format($activeStores) }}</h3>
                <p class="mt-2 text-sm text-gray-500">
                    <span class="font-medium text-blue-500">{{ number_format($totalUsers) }}</span> total users
                </p>
            </div>
        </div>

        <!-- Alerts Card -->
        <div
            class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
            <div
                class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-gradient-to-br from-orange-400/20 to-red-500/20 blur-2xl transition-all group-hover:scale-150">
            </div>
            <div class="relative">
                <div
                    class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-400 to-red-500 shadow-lg shadow-orange-500/30">
                    <i class="fas fa-bell text-2xl text-white"></i>
                </div>
                <p class="text-sm font-medium text-gray-500">Perlu Perhatian</p>
                <h3 class="mt-1 text-2xl font-bold text-gray-800">{{ $pendingApprovals }}</h3>
                <p class="mt-2 text-sm">
                    <span class="text-orange-500">{{ $lowStockCount }} low stock</span> ·
                    <span class="text-red-500">{{ $expiringSoonCount }} expiring</span>
                </p>
            </div>
        </div>
    </div>
</div>
