<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @php
            $statusColors = [
                'active' => 'from-green-400 to-emerald-500',
                'pending' => 'from-yellow-400 to-orange-500',
                'suspend' => 'from-red-400 to-rose-500',
                'rejected' => 'from-gray-400 to-gray-500',
            ];
            $typeColors = [
                'ki' => 'border-red-500 bg-red-50 text-red-600',
                'kmp' => 'border-purple-500 bg-purple-50 text-purple-600',
                'pro' => 'border-blue-500 bg-blue-50 text-blue-600',
            ];
        @endphp
        @foreach ($tokoByStatus as $status => $count)
            <div class="rounded-xl bg-white p-4 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">{{ ucfirst($status) }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($count) }}</p>
                    </div>
                    <div
                        class="h-10 w-10 rounded-full bg-gradient-to-br {{ $statusColors[$status] ?? 'from-gray-400 to-gray-500' }} flex items-center justify-center">
                        <i class="fas fa-store text-white"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Top Performers Row -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Top Buyers -->
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <h4 class="mb-4 flex items-center text-lg font-bold text-gray-800">
                <i class="fas fa-shopping-bag mr-2 text-green-500"></i> Top Buyers
            </h4>
            <div class="space-y-3">
                @forelse ($topBuyers as $index => $toko)
                    <div
                        class="flex items-center justify-between rounded-lg bg-gray-50 p-3 transition-all hover:bg-green-50">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-green-400 to-emerald-500 text-sm font-bold text-white">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $toko['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $toko['orders'] }} orders ·
                                    {{ strtoupper($toko['type']) }}</p>
                            </div>
                        </div>
                        <p class="font-semibold text-green-600">Rp {{ number_format($toko['revenue'], 0, ',', '.') }}
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Tidak ada data</p>
                @endforelse
            </div>
        </div>

        <!-- Top Sellers -->
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <h4 class="mb-4 flex items-center text-lg font-bold text-gray-800">
                <i class="fas fa-chart-line mr-2 text-purple-500"></i> Top Sellers
            </h4>
            <div class="space-y-3">
                @forelse ($topSellers as $index => $toko)
                    <div
                        class="flex items-center justify-between rounded-lg bg-gray-50 p-3 transition-all hover:bg-purple-50">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-purple-400 to-pink-500 text-sm font-bold text-white">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $toko['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $toko['orders'] }} sales ·
                                    {{ strtoupper($toko['type']) }}</p>
                            </div>
                        </div>
                        <p class="font-semibold text-purple-600">Rp {{ number_format($toko['revenue'], 0, ',', '.') }}
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Tidak ada data</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- All Stores Table -->
    <div class="rounded-2xl bg-white p-6 shadow-lg">
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h4 class="text-lg font-bold text-gray-800">Semua Toko</h4>
            <div class="flex flex-wrap gap-3">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari toko..."
                    class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200 sm:w-auto">
                <select wire:model.live="statusFilter" class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <option value="all">Semua Status</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="suspend">Suspend</option>
                </select>
                <select wire:model.live="typeFilter" class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <option value="all">Semua Tipe</option>
                    <option value="ki">KI</option>
                    <option value="kmp">KMP</option>
                    <option value="pro">PRO</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-sm text-gray-500">
                        <th class="pb-3">Nama Toko</th>
                        <th class="pb-3">Tipe</th>
                        <th class="pb-3">Status</th>
                        <th class="cursor-pointer pb-3" wire:click="sortByColumn('orders')">
                            Orders
                            @if ($sortBy === 'orders')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @endif
                        </th>
                        <th class="cursor-pointer pb-3" wire:click="sortByColumn('revenue')">
                            Revenue
                            @if ($sortBy === 'revenue')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tokos as $toko)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3">
                                <p class="font-medium text-gray-800">{{ $toko->name }}</p>
                                <p class="text-xs text-gray-500">{{ $toko->slug }}</p>
                            </td>
                            <td class="py-3">
                                <span
                                    class="rounded-full border px-2 py-1 text-xs font-medium {{ $typeColors[$toko->type] ?? 'border-gray-300 bg-gray-50 text-gray-600' }}">
                                    {{ strtoupper($toko->type) }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                    {{ $toko->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $toko->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $toko->status === 'suspend' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ ucfirst($toko->status) }}
                                </span>
                            </td>
                            <td class="py-3 text-gray-600">{{ number_format($toko->total_orders ?? 0) }}</td>
                            <td class="py-3 font-medium text-gray-800">Rp
                                {{ number_format($toko->total_revenue ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">Tidak ada toko ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tokos->links() }}
        </div>
    </div>
</div>
