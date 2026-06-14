@extends('layouts.admin')

@section('page_title', 'Detail ' . $barang->name)
@section('nav_title', 'Detail ' . $barang->name)

@section('content')
    <div class="min-h-screen py-8">
        <div class="mx-auto">
            <!-- Header Section with Glassmorphism -->
            <div class="mb-8 overflow-hidden rounded-2xl bg-white/80 shadow-xl ring-1 ring-gray-900/5 backdrop-blur-sm">
                <div class="bg-gradient-to-r from-red-500 to-pink-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                                <i class="fas fa-cube text-2xl text-white"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-white">Detail Barang</h1>
                                <p class="text-red-100">Informasi lengkap produk</p>
                            </div>
                        </div>
                        <a href="{{ route('barang.edit-barang', $barang->sku) }}"
                            class="group inline-flex items-center rounded-xl bg-white/20 px-4 py-2 text-sm font-medium text-white backdrop-blur-sm transition-all hover:scale-105 hover:bg-white/30">
                            <i class="fas fa-edit mr-2 transition-transform group-hover:rotate-12"></i>
                            Edit Barang
                        </a>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                        <!-- Enhanced Image Section -->
                        <div class="space-y-6">
                            <div class="group relative overflow-hidden rounded-2xl shadow-lg">
                                @if (count($barang->images) > 0)
                                    <div class="aspect-square bg-gradient-to-br from-gray-100 to-gray-200">
                                        <img src="{{ asset('storage/' . $barang->images->first()->url) }}"
                                            alt="{{ $barang->name }}"
                                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                                    </div>
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 transition-opacity group-hover:opacity-100">
                                    </div>
                                @else
                                    <div class="flex aspect-square items-center justify-center bg-gray-200 p-16">
                                        <i class="fas fa-image text-8xl text-gray-400"></i>
                                    </div>
                                @endif
                            </div>

                            @if (count($barang->images) > 1)
                                <div class="grid grid-cols-4 gap-3">
                                    @foreach ($barang->images->slice(1) as $image)
                                        <div class="group relative overflow-hidden rounded-lg shadow-md">
                                            <img src="{{ asset('storage/' . $image->url) }}" alt="{{ $barang->name }}"
                                                class="aspect-square w-full object-cover transition-transform duration-300 group-hover:scale-110">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Enhanced Info Section -->
                        <div class="space-y-6">
                            <div class="rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 p-6">
                                <div class="flex items-start space-x-4">
                                    <div
                                        class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 shadow-lg">
                                        <i class="fas fa-box text-xl text-white"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h2 class="text-2xl font-bold text-gray-900">{{ $barang->name }}</h2>
                                        <p class="mt-2 text-gray-600">{{ $barang->description }}</p>
                                        <div class="mt-3 flex items-center space-x-2">
                                            <span
                                                class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">
                                                SKU: {{ $barang->sku }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="rounded-xl bg-gradient-to-r from-purple-50 to-pink-50 p-4">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-purple-500 to-pink-500">
                                            <i class="fas fa-tags text-white"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Brand</p>
                                            <p class="text-sm text-gray-600">{{ $barang->brand->name }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 p-4">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-green-500 to-emerald-500">
                                            <i class="fas fa-layer-group text-white"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Kategori
                                                ({{ number_format($barang->subcategory->margin, 0) }}%)</p>
                                            <p class="text-sm text-gray-600">{{ $barang->subcategory->name }} </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl bg-gradient-to-r from-orange-50 to-red-50 p-4">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-orange-500 to-red-500">
                                        <i class="fas fa-clock text-white"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Expiry Settings</p>
                                        <div class="mt-2 grid grid-cols-3 gap-2 text-xs">
                                            <span class="rounded bg-yellow-100 px-2 py-1 text-yellow-800">Early:
                                                {{ $barang->early_expiry_days }}d</span>
                                            <span class="rounded bg-orange-100 px-2 py-1 text-orange-800">Mid:
                                                {{ $barang->mid_expiry_days }}d</span>
                                            <span class="rounded bg-red-100 px-2 py-1 text-red-800">Late:
                                                {{ $barang->late_expiry_days }}d</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl bg-gradient-to-r from-gray-50 to-slate-50 p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="{{ $barang->status === 'active' ? 'bg-gradient-to-r from-green-500 to-emerald-500' : 'bg-gradient-to-r from-red-500 to-pink-500' }} flex h-10 w-10 items-center justify-center rounded-full">
                                            <i class="fas fa-circle text-white"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Status</p>
                                        </div>
                                    </div>
                                    <span
                                        class="{{ $barang->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} inline-flex items-center rounded-full px-3 py-1 text-sm font-medium">
                                        {{ ucfirst($barang->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Conversion Table -->
            <div class="mb-8 overflow-hidden rounded-2xl bg-white/80 shadow-xl ring-1 ring-gray-900/5 backdrop-blur-sm">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                            <i class="fas fa-exchange-alt text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-white">Konversi Satuan</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="overflow-hidden rounded-xl shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                        Dari Satuan</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                        Ke Satuan</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                        Faktor Konversi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($conversions as $conversion)
                                    <tr class="transition-colors hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span
                                                class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">
                                                {{ $conversion->conversionFrom->name }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span
                                                class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">
                                                {{ $conversion->conversionTo->name }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span class="font-mono text-sm font-semibold text-gray-900">
                                                {{ number_format($conversion->conversion_factor, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Enhanced Stock Table -->
            <div class="overflow-hidden rounded-2xl bg-white/80 shadow-xl ring-1 ring-gray-900/5 backdrop-blur-sm">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                                <i class="fas fa-warehouse text-white"></i>
                            </div>
                            <h2 class="text-xl font-bold text-white">Stok Barang</h2>
                        </div>
                        <div class="flex space-x-3">
                            <div class="rounded-xl bg-white/20 px-4 py-2 backdrop-blur-sm">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-emerald-100">Total Stok</div>
                                    <div id="total-stock" class="text-lg font-bold text-white">-</div>
                                </div>
                            </div>
                            <div class="rounded-xl bg-white/20 px-4 py-2 backdrop-blur-sm">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-emerald-100">Tersedia</div>
                                    <div id="available-stock" class="text-lg font-bold text-white">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="overflow-hidden rounded-xl shadow-sm">
                        <div class="overflow-x-auto">
                            <table id="stock-table" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                            Satuan</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                            Jumlah Stok</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                            Tersedia</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                            Harga Beli</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                            Harga Jual</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                            Expired</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                            Status</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                            Diskon</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Custom Pagination -->
                        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                            <div class="flex flex-1 justify-between sm:hidden">
                                <button id="prev-page-mobile"
                                    class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Previous
                                </button>
                                <button id="next-page-mobile"
                                    class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Next
                                </button>
                            </div>
                            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing <span id="showing-from" class="font-medium">1</span> to <span
                                            id="showing-to" class="font-medium">10</span> of <span id="total-records"
                                            class="font-medium">0</span> results
                                    </p>
                                </div>
                                <div>
                                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                                        aria-label="Pagination">
                                        <button id="prev-page"
                                            class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <span id="current-page-info"
                                            class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                            Page 1 of 1
                                        </span>
                                        <button id="next-page"
                                            class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentPage = 1;
            let totalPages = 1;
            const itemsPerPage = 10;
            const barangId = "{{ $barang->sku }}";

            function loadStockData(page = 1) {
                fetch(`/barang/show/${barangId}?page=${page}`)
                    .then(response => response.json())
                    .then(data => {
                        const tbody = document.querySelector('#stock-table tbody');
                        tbody.innerHTML = '';

                        // Update summary
                        document.getElementById('total-stock').textContent = data.summary.total_stock;
                        document.getElementById('available-stock').textContent = data.summary.total_available;

                        // Populate table
                        data.barangki.data.forEach(item => {
                            const row = document.createElement('tr');
                            row.className = 'transition-colors hover:bg-gray-50';
                            row.innerHTML = `
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">
                                        ${item.satuan}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="text-sm font-semibold text-gray-900">${item.quantity}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="text-sm font-semibold text-emerald-600">${item.available_quantity}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="text-sm text-gray-900">${item.price_buy}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="text-sm font-semibold text-gray-900">${item.price_sell}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex flex-col space-y-1">
                                        <span class="text-sm text-gray-900">${item.expired_time}</span>
                                        ${item.expiry_status}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    ${item.status_badge}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    ${item.discount || '<span class="text-gray-400">-</span>'}
                                </td>
                            `;
                            tbody.appendChild(row);
                        });

                        // Update pagination
                        currentPage = data.barangki.current_page;
                        totalPages = data.barangki.last_page;

                        document.getElementById('current-page-info').textContent =
                            `Page ${currentPage} of ${totalPages}`;
                        document.getElementById('showing-from').textContent = ((currentPage - 1) *
                            itemsPerPage) + 1;
                        document.getElementById('showing-to').textContent = Math.min(currentPage * itemsPerPage,
                            data.barangki.total);
                        document.getElementById('total-records').textContent = data.barangki.total;

                        // Update button states
                        document.getElementById('prev-page').disabled = currentPage === 1;
                        document.getElementById('next-page').disabled = currentPage === totalPages;
                        document.getElementById('prev-page-mobile').disabled = currentPage === 1;
                        document.getElementById('next-page-mobile').disabled = currentPage === totalPages;
                    })
                    .catch(error => {
                        console.error('Error loading stock data:', error);
                    });
            }

            // Event listeners for pagination
            document.getElementById('prev-page').addEventListener('click', () => {
                if (currentPage > 1) {
                    loadStockData(currentPage - 1);
                }
            });

            document.getElementById('next-page').addEventListener('click', () => {
                if (currentPage < totalPages) {
                    loadStockData(currentPage + 1);
                }
            });

            document.getElementById('prev-page-mobile').addEventListener('click', () => {
                if (currentPage > 1) {
                    loadStockData(currentPage - 1);
                }
            });

            document.getElementById('next-page-mobile').addEventListener('click', () => {
                if (currentPage < totalPages) {
                    loadStockData(currentPage + 1);
                }
            });

            // Load initial data
            loadStockData();
        });
    </script>
@endpush
