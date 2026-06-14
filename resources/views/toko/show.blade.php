@extends('layouts.admin')

@section('title', 'Detail Toko - ' . $toko->name)

@section('breadcrumb')
    <x-admin.breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('dashboard')],
        ['label' => 'Toko', 'url' => route('toko.index')],
        ['label' => $toko->name, 'url' => null],
    ]" />
@endsection

@section('content')
    <div class="container mx-auto px-4 py-8" x-data="{ activeTab: 'overview' }">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    {{ $toko->name }}
                    <span
                        class="px-3 py-1 text-sm rounded-full 
                    @if ($toko->status === 'active') bg-green-100 text-green-800
                    @elseif($toko->status === 'pending') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($toko->status) }}
                    </span>
                </h1>
                <p class="text-gray-500 mt-1"><i class="fas fa-map-marker-alt mr-2"></i>{{ $toko->address }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('toko.edit', $toko->id) }}"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm font-semibold">
                    <i class="fas fa-edit mr-2"></i>Edit Toko
                </a>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="bg-white rounded-xl shadow-sm mb-6 border-b border-gray-200">
            <nav class="flex overflow-x-auto">
                <button @click="activeTab = 'overview'"
                    :class="activeTab === 'overview' ? 'border-indigo-600 text-indigo-600 bg-indigo-50' :
                        'border-transparent text-gray-600 hover:text-indigo-600 hover:bg-gray-50'"
                    class="px-6 py-4 border-b-2 font-semibold text-sm transition-all flex items-center gap-2 whitespace-nowrap">
                    <i class="fas fa-chart-line"></i> Ringkasan
                </button>
                <button @click="activeTab = 'sales'"
                    :class="activeTab === 'sales' ? 'border-indigo-600 text-indigo-600 bg-indigo-50' :
                        'border-transparent text-gray-600 hover:text-indigo-600 hover:bg-gray-50'"
                    class="px-6 py-4 border-b-2 font-semibold text-sm transition-all flex items-center gap-2 whitespace-nowrap">
                    <i class="fas fa-shopping-cart"></i> Riwayat Penjualan
                </button>
                <button @click="activeTab = 'purchases'"
                    :class="activeTab === 'purchases' ? 'border-indigo-600 text-indigo-600 bg-indigo-50' :
                        'border-transparent text-gray-600 hover:text-indigo-600 hover:bg-gray-50'"
                    class="px-6 py-4 border-b-2 font-semibold text-sm transition-all flex items-center gap-2 whitespace-nowrap">
                    <i class="fas fa-truck-loading"></i> Riwayat Pembelian
                </button>
                <button @click="activeTab = 'stock'"
                    :class="activeTab === 'stock' ? 'border-indigo-600 text-indigo-600 bg-indigo-50' :
                        'border-transparent text-gray-600 hover:text-indigo-600 hover:bg-gray-50'"
                    class="px-6 py-4 border-b-2 font-semibold text-sm transition-all flex items-center gap-2 whitespace-nowrap">
                    <i class="fas fa-box"></i> Stok Barang
                </button>
            </nav>
        </div>

        <!-- Tab: Overview -->
        <div x-show="activeTab === 'overview'" x-transition class="space-y-6">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Sales Revenue -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Pendapatan</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-1">Rp
                                {{ number_format($totalSalesRevenue, 0, ',', '.') }}</h3>
                        </div>
                        <div class="p-3 bg-green-100 rounded-lg text-green-600">
                            <i class="fas fa-money-bill-wave text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Sales Count -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalSalesCount) }}</h3>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg text-blue-600">
                            <i class="fas fa-shopping-bag text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Purchase Amount -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Pembelian (Restock)</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-1">Rp
                                {{ number_format($totalPurchaseAmount, 0, ',', '.') }}</h3>
                        </div>
                        <div class="p-3 bg-orange-100 rounded-lg text-orange-600">
                            <i class="fas fa-truck text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Products -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Produk</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $toko->barangs->count() }}</h3>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-lg text-purple-600">
                            <i class="fas fa-box-open text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Grafik Pendapatan & Pembelian (30 Hari Terakhir)</h3>
                <div id="revenueChart" class="w-full h-96"></div>
            </div>
        </div>

        <!-- Tab: Sales History -->
        <div x-show="activeTab === 'sales'" x-transition class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Riwayat Penjualan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-medium border-b">
                        <tr>
                            <th class="px-6 py-3">ID Transaksi</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Pelanggan</th>
                            <th class="px-6 py-3">Metode</th>
                            <th class="px-6 py-3">Total</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($sales as $sale)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">#{{ $sale->increment_id }}</td>
                                <td class="px-6 py-4">{{ $sale->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4">{{ $sale->user->name ?? 'Guest' }}</td>
                                <td class="px-6 py-4">{{ $sale->metode_pembayaran }}</td>
                                <td class="px-6 py-4 font-semibold">Rp {{ number_format($sale->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full 
                                    {{ $sale->status == 'success' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada data penjualan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab: Purchase History -->
        <div x-show="activeTab === 'purchases'" x-transition class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Riwayat Pembelian (Restock)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-medium border-b">
                        <tr>
                            <th class="px-6 py-3">ID Pembayaran</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Item</th>
                            <th class="px-6 py-3">Total</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($purchases as $purchase)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">#{{ $purchase->id }}</td>
                                <td class="px-6 py-4">{{ $purchase->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <ul class="list-disc list-inside text-xs text-gray-600">
                                        @foreach ($purchase->pesanan as $pesanan)
                                            <li>{{ $pesanan->barangKI->name ?? 'Unknown' }} (x{{ $pesanan->quantity }})
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="px-6 py-4 font-semibold">Rp {{ number_format($purchase->total, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full 
                                    @if ($purchase->status == 'success') bg-green-100 text-green-800
                                    @elseif($purchase->status == 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                        {{ $purchase->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data pembelian
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab: Stock -->
        <div x-show="activeTab === 'stock'" x-transition class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Stok Barang</h3>
                <a href="{{ route('toko.edit', $toko->id) }}#products"
                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    Kelola Stok <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-medium border-b">
                        <tr>
                            <th class="px-6 py-3">Barang</th>
                            <th class="px-6 py-3">Satuan</th>
                            <th class="px-6 py-3">Harga Jual</th>
                            <th class="px-6 py-3">Stok</th>
                            <th class="px-6 py-3">Expired</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($toko->barangs as $barang)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $barang->barang->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $barang->barcode }}</div>
                                </td>
                                <td class="px-6 py-4">{{ $barang->barang->satuan->name ?? '-' }}</td>
                                <td class="px-6 py-4">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full {{ $barang->jumlah_stock > 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $barang->jumlah_stock }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $barang->expired_date ? \Carbon\Carbon::parse($barang->expired_date)->format('d M Y') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada barang</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                series: [{
                    name: 'Pendapatan Penjualan',
                    data: @json($chartData['sales'])
                }, {
                    name: 'Pengeluaran Pembelian',
                    data: @json($chartData['purchases'])
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#10B981', '#F97316'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    categories: @json($chartData['categories']),
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                }
            };

            var chart = new ApexCharts(document.querySelector("#revenueChart"), options);
            chart.render();
        });
    </script>
@endpush
