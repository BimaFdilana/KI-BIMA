@extends('layouts.admin')

@section('title', 'Dashboard')

@section('page_title', 'Dashboard')

@section('content')
    <!-- Welcome Header -->
    <div class="mb-8 pt-6">
        <div class="overflow-hidden rounded-2xl bg-gradient-to-r from-red-600 via-orange-600 to-pink-500 p-1 shadow-2xl">
            <div
                class="relative overflow-hidden rounded-xl bg-gradient-to-r from-red-600/90 via-orange-600/90 to-pink-500/90 p-8 backdrop-blur-sm">
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-white/10 blur-3xl"></div>
                <div class="relative z-10">
                    <h1 class="text-3xl font-bold text-white">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
                    <p class="mt-2 text-white/80">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                    <div class="mt-4 flex flex-wrap gap-4">
                        <div class="rounded-lg bg-white/20 px-4 py-2 backdrop-blur-sm">
                            <span class="text-sm text-white/80">Total Revenue Bulan Ini</span>
                            <p class="text-xl font-bold text-white">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-white/20 px-4 py-2 backdrop-blur-sm">
                            <span class="text-sm text-white/80">Total Transaksi</span>
                            <p class="text-xl font-bold text-white">{{ $totalTransactionsThisMonth }} Orders</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Stats Grid -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
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
                <h3 class="number-animation mt-1 text-2xl font-bold text-gray-800" data-target="{{ $revenueThisMonth }}"
                    data-original="Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}">
                    Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}
                </h3>
                @if ($revenuePercentage >= 0)
                    <p class="mt-2 flex items-center text-sm text-green-500">
                        <i class="bi bi-graph-up-arrow mr-1"></i>
                        <span>+{{ $revenuePercentage }}% dari bulan lalu</span>
                    </p>
                @else
                    <p class="mt-2 flex items-center text-sm text-red-500">
                        <i class="bi bi-graph-down-arrow mr-1"></i>
                        <span>{{ $revenuePercentage }}% dari bulan lalu</span>
                    </p>
                @endif
            </div>
        </div>

        <!-- Transactions Card -->
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
                <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                <h3 class="mt-1 text-2xl font-bold text-gray-800">{{ number_format($totalTransactionsThisMonth) }}</h3>
                @if ($transactionPercentage >= 0)
                    <p class="mt-2 flex items-center text-sm text-green-500">
                        <i class="bi bi-graph-up-arrow mr-1"></i>
                        <span>+{{ $transactionPercentage }}% dari bulan lalu</span>
                    </p>
                @else
                    <p class="mt-2 flex items-center text-sm text-red-500">
                        <i class="bi bi-graph-down-arrow mr-1"></i>
                        <span>{{ $transactionPercentage }}% dari bulan lalu</span>
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
                <h3 class="mt-1 text-2xl font-bold text-gray-800">{{ $activeToko }}</h3>
                <p class="mt-2 text-sm text-gray-500">
                    <span class="font-medium text-orange-500">{{ $pendingToko }}</span> menunggu approval
                </p>
            </div>
        </div>

        <!-- Inventory Alert Card -->
        <div
            class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
            <div
                class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-gradient-to-br from-orange-400/20 to-red-500/20 blur-2xl transition-all group-hover:scale-150">
            </div>
            <div class="relative">
                <div
                    class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-400 to-red-500 shadow-lg shadow-orange-500/30">
                    <i class="fas fa-exclamation-triangle text-2xl text-white"></i>
                </div>
                <p class="text-sm font-medium text-gray-500">Stock Alert</p>
                <h3 class="mt-1 text-2xl font-bold text-gray-800">{{ $lowStockCount + $expiringSoonCount }}</h3>
                <p class="mt-2 text-sm">
                    <span class="text-orange-500">{{ $lowStockCount }} low stock</span> ·
                    <span class="text-red-500">{{ $expiringSoonCount }} expiring</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Charts and Activity Row -->
    <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Revenue Chart -->
        <div class="lg:col-span-2 rounded-2xl bg-white p-6 shadow-lg">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Revenue Trend</h3>
                    <p class="text-sm text-gray-500">7 hari terakhir</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="h-3 w-3 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500"></span>
                    <span class="text-sm text-gray-600">Revenue</span>
                </div>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Activity Feed -->
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <h3 class="mb-4 text-lg font-bold text-gray-800">Aktivitas Terbaru</h3>
            <div class="space-y-4 max-h-64 overflow-y-auto">
                @if ($paidPesanan > 0)
                    <div class="flex items-start space-x-3 rounded-xl bg-green-50 p-3 transition-all hover:bg-green-100">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-500">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $paidPesanan }} pesanan terbayar</p>
                            <p class="text-xs text-gray-500">Menunggu pengiriman</p>
                        </div>
                    </div>
                @endif

                @if ($deliverPesanan > 0)
                    <div class="flex items-start space-x-3 rounded-xl bg-blue-50 p-3 transition-all hover:bg-blue-100">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-500">
                            <i class="fas fa-truck text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $deliverPesanan }} dalam pengiriman</p>
                            <p class="text-xs text-gray-500">Sedang dikirim ke toko</p>
                        </div>
                    </div>
                @endif

                @if ($pendingToko > 0)
                    <div class="flex items-start space-x-3 rounded-xl bg-orange-50 p-3 transition-all hover:bg-orange-100">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-500">
                            <i class="fas fa-store text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $pendingToko }} toko pending</p>
                            <p class="text-xs text-gray-500">Menunggu approval</p>
                        </div>
                    </div>
                @endif

                @if ($expiredCount > 0)
                    <div class="flex items-start space-x-3 rounded-xl bg-red-50 p-3 transition-all hover:bg-red-100">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-500">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $expiredCount }} produk expired</p>
                            <p class="text-xs text-gray-500">Perlu ditindaklanjuti</p>
                        </div>
                    </div>
                @endif

                @if ($lowStockCount > 0)
                    <div class="flex items-start space-x-3 rounded-xl bg-yellow-50 p-3 transition-all hover:bg-yellow-100">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-500">
                            <i class="fas fa-box-open text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $lowStockCount }} produk low stock</p>
                            <p class="text-xs text-gray-500">Stok menipis</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Secondary Stats Row -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- KI Items Sold -->
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition-all hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Barang KI Terjual</p>
                    <h4 class="number-animation mt-1 text-xl font-bold text-gray-800"
                        data-target="{{ $totalTerjualBulanIni }}"
                        data-original="{{ \App\Helpers\CurrencyHelper::formatStock($totalTerjualBulanIni) }}">
                        {{ \App\Helpers\CurrencyHelper::formatStock($totalTerjualBulanIni) }}
                    </h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100">
                    <i class="fas fa-box text-xl text-indigo-600"></i>
                </div>
            </div>
            @if ($totalterjualBulanPercentage > 0)
                <div class="mt-3 flex items-center text-xs text-green-500">
                    <i class="bi bi-arrow-up mr-1"></i> {{ $totalterjualBulanPercentage }}% dari bulan lalu
                </div>
            @elseif ($totalterjualBulanPercentage < 0)
                <div class="mt-3 flex items-center text-xs text-red-500">
                    <i class="bi bi-arrow-down mr-1"></i> {{ abs($totalterjualBulanPercentage) }}% dari bulan lalu
                </div>
            @else
                <div class="mt-3 flex items-center text-xs text-gray-500">
                    <i class="bi bi-dash mr-1"></i> Sama dengan bulan lalu
                </div>
            @endif
        </div>

        <!-- Toko Items Sold -->
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition-all hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Barang Toko Terjual</p>
                    <h4 class="number-animation mt-1 text-xl font-bold text-gray-800"
                        data-target="{{ $totalBarangTokoTerjualBulanIni }}"
                        data-original="{{ \App\Helpers\CurrencyHelper::formatStock($totalBarangTokoTerjualBulanIni) }}">
                        {{ \App\Helpers\CurrencyHelper::formatStock($totalBarangTokoTerjualBulanIni) }}
                    </h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100">
                    <i class="fas fa-shopping-bag text-xl text-purple-600"></i>
                </div>
            </div>
            @if ($totalBarangTokoTerjualBulanPercentage > 0)
                <div class="mt-3 flex items-center text-xs text-green-500">
                    <i class="bi bi-arrow-up mr-1"></i> {{ $totalBarangTokoTerjualBulanPercentage }}% dari bulan lalu
                </div>
            @elseif ($totalBarangTokoTerjualBulanPercentage < 0)
                <div class="mt-3 flex items-center text-xs text-red-500">
                    <i class="bi bi-arrow-down mr-1"></i> {{ abs($totalBarangTokoTerjualBulanPercentage) }}% dari bulan
                    lalu
                </div>
            @else
                <div class="mt-3 flex items-center text-xs text-gray-500">
                    <i class="bi bi-dash mr-1"></i> Sama dengan bulan lalu
                </div>
            @endif
        </div>

        <!-- Users -->
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition-all hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Users</p>
                    <h4 class="mt-1 text-xl font-bold text-gray-800">{{ number_format($totalUsers) }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-100">
                    <i class="fas fa-users text-xl text-cyan-600"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500">
                <span class="text-green-500">{{ $ktpVerifiedUsers }}</span> KTP verified · <span
                    class="text-blue-500">+{{ $newUsersThisMonth }}</span> bulan ini
            </div>
        </div>

        <!-- Products -->
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition-all hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Produk</p>
                    <h4 class="mt-1 text-xl font-bold text-gray-800">{{ number_format($totalProducts) }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100">
                    <i class="fas fa-cubes text-xl text-emerald-600"></i>
                </div>
            </div>
            <div class="mt-3 text-xs">
                <span class="text-red-500">{{ $expiredCount }} expired</span> · <span
                    class="text-orange-500">{{ $expiringSoonCount }} akan expired</span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Order Status Distribution -->
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <h3 class="mb-4 text-lg font-bold text-gray-800">Status Pesanan</h3>
            <div class="flex items-center justify-center">
                <div class="h-64 w-64">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                <div class="flex items-center">
                    <span class="mr-2 h-3 w-3 rounded-full bg-green-500"></span>
                    <span>Success: {{ $orderStatusDistribution['success'] ?? 0 }}</span>
                </div>
                <div class="flex items-center">
                    <span class="mr-2 h-3 w-3 rounded-full bg-yellow-500"></span>
                    <span>Pending: {{ $orderStatusDistribution['pending'] ?? 0 }}</span>
                </div>
                <div class="flex items-center">
                    <span class="mr-2 h-3 w-3 rounded-full bg-blue-500"></span>
                    <span>Paid: {{ $orderStatusDistribution['paid'] ?? 0 }}</span>
                </div>
                <div class="flex items-center">
                    <span class="mr-2 h-3 w-3 rounded-full bg-red-500"></span>
                    <span>Failed: {{ $orderStatusDistribution['failed'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Toko Type Distribution -->
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <h3 class="mb-4 text-lg font-bold text-gray-800">Toko Berdasarkan Tipe</h3>
            <div class="flex items-center justify-center">
                <div class="h-64 w-full">
                    <canvas id="tokoTypeChart"></canvas>
                </div>
            </div>
            <div class="mt-4 flex justify-center gap-6 text-sm">
                <div class="flex items-center">
                    <span class="mr-2 h-3 w-3 rounded-full bg-indigo-500"></span>
                    <span>KI: {{ $tokoByType['ki'] ?? 0 }}</span>
                </div>
                <div class="flex items-center">
                    <span class="mr-2 h-3 w-3 rounded-full bg-purple-500"></span>
                    <span>KMP: {{ $tokoByType['kmp'] ?? 0 }}</span>
                </div>
                <div class="flex items-center">
                    <span class="mr-2 h-3 w-3 rounded-full bg-pink-500"></span>
                    <span>PRO: {{ $tokoByType['pro'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Extra Stats Cards -->
    @if ($infaqThisMonth > 0 || $activePaylatterAccounts > 0)
        <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Infaq Card -->
            @if ($infaqThisMonth > 0 || $activeInfaqCampaigns > 0)
                <div
                    class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 p-6 text-white shadow-lg">
                    <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
                    <div class="relative">
                        <div class="mb-4 flex items-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20">
                                <i class="fas fa-hand-holding-heart text-2xl"></i>
                            </div>
                            <h3 class="ml-4 text-xl font-bold">Infaq</h3>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-white/80">Terkumpul Bulan Ini</p>
                                <p class="text-2xl font-bold">Rp {{ number_format($infaqThisMonth, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-white/80">Kampanye Aktif</p>
                                <p class="text-2xl font-bold">{{ $activeInfaqCampaigns }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Paylatter Card -->
            @if ($activePaylatterAccounts > 0)
                <div
                    class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-500 to-purple-600 p-6 text-white shadow-lg">
                    <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
                    <div class="relative">
                        <div class="mb-4 flex items-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20">
                                <i class="fas fa-credit-card text-2xl"></i>
                            </div>
                            <h3 class="ml-4 text-xl font-bold">PakDul (Paylatter)</h3>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-white/80">Akun Aktif</p>
                                <p class="text-xl font-bold">{{ $activePaylatterAccounts }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-white/80">Kredit Terpakai</p>
                                <p class="text-xl font-bold">Rp {{ number_format($totalCreditUsed, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-white/80">Overdue</p>
                                <p class="text-xl font-bold {{ $overdueTransactions > 0 ? 'text-yellow-300' : '' }}">
                                    {{ $overdueTransactions }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Best Buyer & Seller Tables -->
    <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div id="bestBuyComponent" class="rounded-2xl bg-white p-6 shadow-lg">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Best Buyer</h3>
                    <p class="text-sm text-gray-500">Toko dengan pembelian tertinggi</p>
                </div>
                <div class="loading-table-best-buy"><i class="fas fa-spinner fa-spin text-indigo-500"></i></div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="py-3 text-left text-sm font-medium text-gray-500">Toko</th>
                            <th class="py-3 text-center text-sm font-medium text-gray-500">Transaksi</th>
                            <th class="py-3 text-right text-sm font-medium text-gray-500">Revenue</th>
                            <th class="py-3 text-center text-sm font-medium text-gray-500">Trend</th>
                        </tr>
                    </thead>
                    <tbody id="bestBuyTable">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

        <div id="bestSellComponent" class="rounded-2xl bg-white p-6 shadow-lg">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Best Seller</h3>
                    <p class="text-sm text-gray-500">Toko dengan penjualan tertinggi</p>
                </div>
                <div class="loading-table-best-sell"><i class="fas fa-spinner fa-spin text-purple-500"></i></div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="py-3 text-left text-sm font-medium text-gray-500">Toko</th>
                            <th class="py-3 text-center text-sm font-medium text-gray-500">Transaksi</th>
                            <th class="py-3 text-right text-sm font-medium text-gray-500">Revenue</th>
                            <th class="py-3 text-center text-sm font-medium text-gray-500">Trend</th>
                        </tr>
                    </thead>
                    <tbody id="bestSellTable">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    @if (count($topProducts) > 0)
        <div class="mb-8 rounded-2xl bg-white p-6 shadow-lg">
            <h3 class="mb-4 text-lg font-bold text-gray-800">Produk Terlaris Bulan Ini</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ($topProducts as $index => $product)
                    <div
                        class="relative overflow-hidden rounded-xl border border-gray-100 p-4 transition-all hover:border-indigo-200 hover:shadow-md">
                        <div
                            class="absolute -right-2 -top-2 flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 text-xs font-bold text-white">
                            #{{ $index + 1 }}
                        </div>
                        <h4 class="mb-2 truncate pr-6 text-sm font-semibold text-gray-800"
                            title="{{ $product['name'] }}">{{ $product['name'] }}</h4>
                        <div class="text-xs text-gray-500">
                            <p>Qty: <span class="font-medium text-gray-700">{{ number_format($product['qty']) }}</span>
                            </p>
                            <p>Sales: <span class="font-medium text-green-600">Rp
                                    {{ number_format($product['sales'], 0, ',', '.') }}</span></p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Quick Access -->
    <div class="mb-8">
        <h3 class="mb-4 text-lg font-bold text-gray-800">Akses Cepat</h3>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
            <a href="{{ route('profile') }}"
                class="group flex flex-col items-center rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:border-purple-200 hover:bg-purple-50 hover:shadow-lg">
                <div
                    class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-400 to-purple-600 transition-transform group-hover:scale-110">
                    <i class="fas fa-user text-xl text-white"></i>
                </div>
                <span class="text-center text-sm font-medium text-gray-700">Profile</span>
            </a>

            <a href="{{ route('dashboard.analytics') }}"
                class="group flex flex-col items-center rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:border-indigo-200 hover:bg-indigo-50 hover:shadow-lg">
                <div
                    class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-400 to-indigo-600 transition-transform group-hover:scale-110">
                    <i class="fas fa-chart-line text-xl text-white"></i>
                </div>
                <span class="text-center text-sm font-medium text-gray-700">Analytics</span>
            </a>

            <a href="{{ route('dashboard.approval') }}"
                class="group flex flex-col items-center rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:border-green-200 hover:bg-green-50 hover:shadow-lg">
                <div
                    class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-green-400 to-green-600 transition-transform group-hover:scale-110">
                    <i class="fas fa-check-circle text-xl text-white"></i>
                </div>
                <span class="text-center text-sm font-medium text-gray-700">Approval</span>
            </a>

            <a href="{{ route('barang.index') }}"
                class="group flex flex-col items-center rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:border-blue-200 hover:bg-blue-50 hover:shadow-lg">
                <div
                    class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600 transition-transform group-hover:scale-110">
                    <i class="fas fa-boxes text-xl text-white"></i>
                </div>
                <span class="text-center text-sm font-medium text-gray-700">Barang</span>
            </a>

            <a href="{{ route('toko.index') }}"
                class="group flex flex-col items-center rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:border-pink-200 hover:bg-pink-50 hover:shadow-lg">
                <div
                    class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-pink-400 to-pink-600 transition-transform group-hover:scale-110">
                    <i class="fas fa-store text-xl text-white"></i>
                </div>
                <span class="text-center text-sm font-medium text-gray-700">Toko</span>
            </a>

            <a href="{{ route('user.index') }}"
                class="group flex flex-col items-center rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:border-cyan-200 hover:bg-cyan-50 hover:shadow-lg">
                <div
                    class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-400 to-cyan-600 transition-transform group-hover:scale-110">
                    <i class="fas fa-users text-xl text-white"></i>
                </div>
                <span class="text-center text-sm font-medium text-gray-700">Users</span>
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        // Chart.js Global Configuration
        Chart.defaults.font.family = "'Inter', 'Helvetica', 'Arial', sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = "#64748b";

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueData = @json($revenueLast7Days);

        const gradient = revenueCtx.createLinearGradient(0, 0, 0, 256);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.3)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueData.map(d => d.date),
                datasets: [{
                    label: 'Revenue',
                    data: revenueData.map(d => d.revenue),
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(99, 102, 241)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
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
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        padding: 12,
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(99, 102, 241, 0.3)',
                        borderWidth: 1,
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
                        },
                        ticks: {
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)'
                        },
                        ticks: {
                            color: '#94a3b8',
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

        // Order Status Doughnut Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusData = @json($orderStatusDistribution);

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Success', 'Pending', 'Paid', 'Failed', 'Delivery', 'Other'],
                datasets: [{
                    data: [
                        statusData.success || 0,
                        statusData.pending || 0,
                        statusData.paid || 0,
                        statusData.failed || 0,
                        statusData.delivery || 0,
                        (statusData.cancelled || 0) + (statusData.refunded || 0) + (statusData
                            .unknown || 0)
                    ],
                    backgroundColor: [
                        'rgb(34, 197, 94)',
                        'rgb(234, 179, 8)',
                        'rgb(59, 130, 246)',
                        'rgb(239, 68, 68)',
                        'rgb(168, 85, 247)',
                        'rgb(148, 163, 184)'
                    ],
                    borderWidth: 0,
                    hoverOffset: 10
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

        // Toko Type Bar Chart
        const tokoTypeCtx = document.getElementById('tokoTypeChart').getContext('2d');
        const tokoTypeData = @json($tokoByType);

        new Chart(tokoTypeCtx, {
            type: 'bar',
            data: {
                labels: ['KI', 'KMP', 'PRO'],
                datasets: [{
                    label: 'Jumlah Toko',
                    data: [tokoTypeData.ki || 0, tokoTypeData.kmp || 0, tokoTypeData.pro || 0],
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(236, 72, 153, 0.8)'
                    ],
                    borderRadius: 8,
                    borderSkipped: false,
                    barThickness: 50
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
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)'
                        },
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Number Animation
        document.addEventListener('DOMContentLoaded', function() {
            function animateNumber(element) {
                const target = parseInt(element.getAttribute('data-target'));
                if (isNaN(target)) return;

                let current = 0;
                const increment = target / 50;
                const duration = 1500;
                const stepTime = duration / 50;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        const originalContent = element.getAttribute('data-original') || '';
                        if (originalContent.startsWith('Rp ')) {
                            element.textContent = 'Rp ' + target.toLocaleString('id-ID');
                        } else {
                            if (target >= 1000000) {
                                element.textContent = (target / 1000000).toFixed(1) + 'M';
                            } else if (target >= 1000) {
                                element.textContent = (target / 1000).toFixed(1) + 'K';
                            } else {
                                element.textContent = target.toLocaleString('id-ID');
                            }
                        }
                        clearInterval(timer);
                    } else {
                        element.textContent = Math.floor(current).toLocaleString('id-ID');
                    }
                }, stepTime);
            }

            const animatedNumbers = document.querySelectorAll('.number-animation');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateNumber(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            });

            animatedNumbers.forEach(element => observer.observe(element));
        });

        // Toggle loading table
        function toggleLoadingTable(show) {
            const loadingBestBuy = document.querySelector('.loading-table-best-buy');
            const loadingBestSell = document.querySelector('.loading-table-best-sell');
            if (show) {
                loadingBestBuy?.classList.remove('hidden');
                loadingBestSell?.classList.remove('hidden');
            } else {
                loadingBestBuy?.classList.add('hidden');
                loadingBestSell?.classList.add('hidden');
            }
        }

        // Global Chart Instances for mini charts
        let productMiniCharts = {};

        $(document).ready(function() {
            toggleLoadingTable(true);
            $.ajax({
                url: '/dashboard/api/best-buy-and-sell?period=weekly',
                type: 'GET',
                success: function(data) {
                    toggleLoadingTable(false);
                    processAnalyticsData(data);
                },
                error: function(error) {
                    toggleLoadingTable(false);
                    console.error('Error fetching analytics data:', error);
                }
            });
        });

        function processAnalyticsData(data) {
            if (!data) return;

            if (data.best_buy && data.best_buy.length > 0) {
                updateBestBuy(data.best_buy);
                document.getElementById('bestBuyComponent').classList.remove('hidden');
            } else {
                document.getElementById('bestBuyComponent').classList.add('hidden');
            }

            if (data.best_sell && data.best_sell.length > 0) {
                updateBestSell(data.best_sell);
                document.getElementById('bestSellComponent').classList.remove('hidden');
            } else {
                document.getElementById('bestSellComponent').classList.add('hidden');
            }
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }

        function formatNumber(num) {
            return new Intl.NumberFormat().format(num);
        }

        function formatDate(dateStr) {
            return moment(dateStr).format('MMM D');
        }

        function createProductMiniChart(canvasId, timeSeriesData) {
            if (productMiniCharts[canvasId]) {
                productMiniCharts[canvasId].destroy();
            }

            const canvas = document.getElementById(canvasId);
            if (!canvas) return null;

            const ctx = canvas.getContext('2d');
            const sortedData = [...timeSeriesData].sort((a, b) => new Date(a.date) - new Date(b.date));
            const labels = sortedData.map(item => formatDate(item.date));
            const data = sortedData.map(item => parseFloat(item.total_sales));

            let color = 'rgba(59, 130, 246, 1)';
            if (data.length > 1) {
                color = data[data.length - 1] >= data[0] ? 'rgba(16, 185, 129, 1)' : 'rgba(239, 68, 68, 1)';
            }

            productMiniCharts[canvasId] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        borderColor: color,
                        backgroundColor: color.replace('1)', '0.1)'),
                        borderWidth: 2,
                        pointRadius: 1,
                        pointHoverRadius: 3,
                        tension: 0.3,
                        fill: true
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
                            enabled: true,
                            backgroundColor: 'rgba(17, 24, 39, 0.8)',
                            callbacks: {
                                label: function(context) {
                                    return formatCurrency(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false,
                            beginAtZero: true
                        }
                    }
                }
            });

            return productMiniCharts[canvasId];
        }

        function updateBestBuy(toko) {
            const tableBody = $('#bestBuyTable');
            tableBody.empty();

            toko.slice(0, 5).forEach((item, index) => {
                const chartId = `best-buy-chart-${index}`;
                tableBody.append(`
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-3 font-medium text-gray-800">${item.toko_name}</td>
                        <td class="py-3 text-center text-gray-600">${formatNumber(item.total_transactions)}</td>
                        <td class="py-3 text-right font-medium text-gray-800">${formatCurrency(item.total_sales)}</td>
                        <td class="py-3">
                            <div class="flex items-center justify-center">
                                <div class="h-12 w-36"><canvas id="${chartId}"></canvas></div>
                            </div>
                        </td>
                    </tr>
                `);

                setTimeout(() => {
                    if (document.getElementById(chartId) && item.time_series?.length > 0) {
                        createProductMiniChart(chartId, item.time_series);
                    }
                }, 100);
            });
        }

        function updateBestSell(toko) {
            const tableBody = $('#bestSellTable');
            tableBody.empty();

            toko.slice(0, 5).forEach((item, index) => {
                const chartId = `best-sell-chart-${index}`;
                tableBody.append(`
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-3 font-medium text-gray-800">${item.toko_name}</td>
                        <td class="py-3 text-center text-gray-600">${formatNumber(item.total_transactions)}</td>
                        <td class="py-3 text-right font-medium text-gray-800">${formatCurrency(item.total_sales)}</td>
                        <td class="py-3">
                            <div class="flex items-center justify-center">
                                <div class="h-12 w-36"><canvas id="${chartId}"></canvas></div>
                            </div>
                        </td>
                    </tr>
                `);

                setTimeout(() => {
                    if (document.getElementById(chartId) && item.time_series?.length > 0) {
                        createProductMiniChart(chartId, item.time_series);
                    }
                }, 100);
            });
        }
    </script>
@endpush
