<div class="min-h-screen  p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Analytics Dashboard</h1>
                <p class="text-gray-500">Real-time insights dan analisis bisnis</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-2 rounded-full bg-green-100 px-3 py-1 text-sm text-green-700">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-green-500"></span>
                    Real-time
                </span>
                <span class="text-sm text-gray-500">
                    Last updated: {{ now()->format('H:i:s') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="mb-6 overflow-x-auto">
        <div class="flex gap-2 border-b border-gray-200 pb-2">
            @php
                $tabs = [
                    'overview' => ['icon' => 'fas fa-chart-pie', 'label' => 'Overview'],
                    'revenue' => ['icon' => 'fas fa-coins', 'label' => 'Revenue'],
                    'toko' => ['icon' => 'fas fa-store', 'label' => 'Toko'],
                    'product' => ['icon' => 'fas fa-boxes', 'label' => 'Produk'],
                    'inventory' => ['icon' => 'fas fa-exchange-alt', 'label' => 'Inventory I/O'],
                    'user' => ['icon' => 'fas fa-users', 'label' => 'Users'],
                    'payment' => ['icon' => 'fas fa-credit-card', 'label' => 'Payment'],
                    'infaq' => ['icon' => 'fas fa-hand-holding-heart', 'label' => 'Infaq'],
                    'paylatter' => ['icon' => 'fas fa-wallet', 'label' => 'Paylatter'],
                ];
            @endphp
            @foreach ($tabs as $key => $tab)
                <button wire:click="setActiveTab('{{ $key }}')"
                    class="flex items-center gap-2 whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium transition-all
                        {{ $activeTab === $key
                            ? 'bg-red-500 text-white shadow-lg shadow-red-500/30'
                            : 'text-gray-600 hover:bg-gray-100' }}">
                    <i class="{{ $tab['icon'] }}"></i>
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Content Area -->
    <div class="transition-all duration-300">
        @switch($activeTab)
            @case('overview')
                <div class="space-y-6">
                    <livewire:analytics.analytics-overview />

                    <!-- Quick Charts Row -->
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div class="rounded-2xl bg-white p-6 shadow-lg">
                            <h3 class="mb-4 text-lg font-bold text-gray-800">Revenue Trend</h3>
                            <livewire:analytics.revenue-analytics />
                        </div>
                    </div>

                    <!-- Quick Access Cards -->
                    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                        <button wire:click="setActiveTab('toko')"
                            class="group rounded-xl bg-white p-4 shadow-md transition-all hover:-translate-y-1 hover:shadow-lg">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-purple-400 to-pink-500 shadow-lg">
                                    <i class="fas fa-store text-xl text-white"></i>
                                </div>
                                <div class="text-left">
                                    <p class="font-medium text-gray-800">Toko Analytics</p>
                                    <p class="text-sm text-gray-500">Lihat performa toko</p>
                                </div>
                            </div>
                        </button>
                        <button wire:click="setActiveTab('product')"
                            class="group rounded-xl bg-white p-4 shadow-md transition-all hover:-translate-y-1 hover:shadow-lg">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-orange-400 to-red-500 shadow-lg">
                                    <i class="fas fa-boxes text-xl text-white"></i>
                                </div>
                                <div class="text-left">
                                    <p class="font-medium text-gray-800">Produk Analytics</p>
                                    <p class="text-sm text-gray-500">Inventori & sales</p>
                                </div>
                            </div>
                        </button>
                        <button wire:click="setActiveTab('user')"
                            class="group rounded-xl bg-white p-4 shadow-md transition-all hover:-translate-y-1 hover:shadow-lg">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-400 to-indigo-500 shadow-lg">
                                    <i class="fas fa-users text-xl text-white"></i>
                                </div>
                                <div class="text-left">
                                    <p class="font-medium text-gray-800">User Analytics</p>
                                    <p class="text-sm text-gray-500">Pertumbuhan user</p>
                                </div>
                            </div>
                        </button>
                        <button wire:click="setActiveTab('payment')"
                            class="group rounded-xl bg-white p-4 shadow-md transition-all hover:-translate-y-1 hover:shadow-lg">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 shadow-lg">
                                    <i class="fas fa-credit-card text-xl text-white"></i>
                                </div>
                                <div class="text-left">
                                    <p class="font-medium text-gray-800">Payment Analytics</p>
                                    <p class="text-sm text-gray-500">Transaksi detail</p>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            @break

            @case('revenue')
                <livewire:analytics.revenue-analytics />
            @break

            @case('toko')
                <livewire:analytics.toko-analytics />
            @break

            @case('product')
                <livewire:analytics.product-analytics />
            @break

            @case('user')
                <livewire:analytics.user-analytics />
            @break

            @case('payment')
                <livewire:analytics.payment-analytics />
            @break

            @case('infaq')
                <livewire:analytics.infaq-analytics />
            @break

            @case('paylatter')
                <livewire:analytics.paylatter-analytics />
            @break

            @case('inventory')
                <livewire:analytics.barang-io-analytics />
            @break

            @default
                <livewire:analytics.analytics-overview />
        @endswitch
    </div>
</div>
