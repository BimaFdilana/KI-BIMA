@extends('layouts.admin')

@push('styles')
    <style>
        .gallery-thumbnail {
            transition: all 0.3s ease;
        }

        .gallery-thumbnail:hover {
            transform: scale(1.05);
        }

        .status-badge {
            animation: pulse 2s infinite;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        }

        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hover-scale {
            transition: transform 0.2s ease;
        }

        .hover-scale:hover {
            transform: scale(1.02);
        }
    </style>
@endpush

@section('content')
    <div class="container mx-auto px-6 py-8">
        <!-- Product Gallery & Info -->
        <div class="mb-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Gallery -->
            <div class="fade-in space-y-4">
                <!-- Main Image -->
                @if ($mainImage)
                    <div class="hover-scale relative overflow-hidden rounded-2xl bg-white shadow-xl">
                        <img id="mainImage" src="{{ asset('storage/' . $mainImage->url) }}" alt="Samsung Galaxy S24" class="h-96 w-full object-cover">
                        <div class="absolute right-4 top-4">
                            <span class="rounded-full bg-red-600 px-3 py-1 text-sm font-semibold text-white">Terlaris</span>
                        </div>
                    </div>
                @else
                    <div class="hover-scale relative overflow-hidden rounded-2xl bg-white shadow-xl">
                        <div class="flex h-96 w-full items-center justify-center bg-gray-200"><i class="fas fa-image text-5xl"></i></div>
                        <div class="absolute right-4 top-4">
                            <span class="rounded-full bg-red-600 px-3 py-1 text-sm font-semibold text-white">Terlaris</span>
                        </div>
                    </div>
                @endif
                @if ($barangKi->barang->images->count() > 0)
                    <!-- Thumbnail Gallery -->
                    <div class="flex space-x-3 overflow-x-auto pb-2">
                        @foreach ($barangKi->barang->images->take(5) as $image)
                            <img src="{{ asset('storage/' . $image->url) }}" class="gallery-thumbnail h-20 w-20 cursor-pointer rounded-lg border-2 border-gray-200 object-cover shadow-md" onclick="changeMainImage(this.src)">
                        @endforeach
                        @if ($barangKi->barang->images->where('is_main', false)->count() > 5)
                            <div class="relative">
                                <img src="{{ asset('storage/' . $barangKi->barang->images->where('is_main', false)->skip(5)->first()->url) }}" class="gallery-thumbnail h-20 w-20 cursor-pointer rounded-lg border-2 border-gray-200 object-cover opacity-75 shadow-md" onclick="showMorePhotos()">
                                <div class="absolute inset-0 flex cursor-pointer items-center justify-center rounded-lg bg-black/50" onclick="showMorePhotos()">
                                    <span class="text-sm font-bold text-white">+{{ $barangKi->barang->images->where('is_main', false)->count() - 5 }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="fade-in space-y-6">
                <div class="rounded-2xl bg-white p-8 shadow-xl">
                    <!-- Product Header -->
                    <div class="mb-6">
                        <h2 class="mb-2 pb-2 text-3xl font-bold text-gray-900">{{ $barangKi->barang->name }}</h2>
                        <div class="mb-4 flex items-center space-x-4">
                            <span class="rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-800">{{ $barangKi->barang->brand->name }}</span>
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-sm text-gray-800">SKU: {{ $barangKi->barang->sku }}</span>
                            <span class="status-badge @if ($barangKi->barang->status == 'active') bg-green-100 @else bg-red-100 @endif rounded-full px-3 py-1 text-sm font-semibold capitalize text-green-800">{{ $barangKi->barang->status }}</span>
                            <span class="@if ($discount['is_discounted']) bg-red-600 text-white @else bg-gray-100 text-gray-800 @endif rounded-full px-3 py-1 text-sm capitalize">{{ $discount['message'] }}</span>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="mb-6 rounded-xl bg-red-50 p-4">
                        @if ($discount['is_discounted'])
                            <div class="mb-2 flex items-center justify-between">
                                <span class="text-sm text-gray-600">Harga Jual</span>
                                <span class="text-lg text-gray-500 line-through">Rp {{ $barangKi->price_sell }}</span>
                            </div>
                        @endif
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-xl font-bold text-red-600">
                                @if ($discount['is_discounted'])
                                    Harga Diskon
                                @else
                                    Harga Jual
                                @endif
                            </span>
                            <span class="text-3xl font-bold text-red-600">Rp @if ($discount['is_discounted'])
                                    {{ $discount['discounted_price'] }}
                                @else
                                    {{ $barangKi->price_sell }}
                                @endif
                            </span>
                        </div>
                        @if ($discount['is_discounted'])
                            <div class="flex items-center justify-between">
                                <span class="rounded-full bg-red-600 px-3 py-1 text-sm font-bold text-white">Hemat {{ $discount['discount_percentage'] }}%</span>
                                <span class="text-sm text-gray-600">Berlaku hingga {{ $discount['time_remaining'] }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Stock Info -->
                    <div class="mb-6 grid grid-cols-2 gap-4">
                        <div class="rounded-xl bg-gray-50 p-4">
                            <div class="mb-1 text-sm text-gray-600">Stok Tersedia</div>
                            <div class="text-2xl font-bold text-green-600">{{ $barangKi->quantity }} {{ $barangKi->satuan->name }}</div>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-4">
                            <div class="mb-1 text-sm text-gray-600">Terjual</div>
                            <div class="text-2xl font-bold text-blue-600">{{ $barangKi->sold_quantity }} {{ $barangKi->satuan->name }}</div>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kategori</span>
                                <span class="font-semibold">{{ $barangKi->barang->subcategory->category->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Sub Kategori</span>
                                <span class="font-semibold">{{ $barangKi->barang->subcategory->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tipe</span>
                                <span class="font-semibold">{{ $barangKi->barang->type->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Margin</span>
                                <span class="font-semibold">{{ $formattedMargin }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">SKU</span>
                                <span class="font-semibold">{{ $barangKi->barang->sku }}</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Harga Beli</span>
                                <span class="font-semibold">Rp {{ number_format($barangKi->price_buy, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Harga Jual</span>
                                <span class="font-semibold">Rp {{ number_format($barangKi->price_sell, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Diskon</span>
                                <span class="font-semibold">Rp {{ number_format($barangKi->discount_amount, 0, ',', '.') }} @if ($barangKi->discount_percentage)
                                        ({{ $barangKi->discount_percentage }}%)
                                    @endif
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Periode Diskon</span>
                                <span class="font-semibold">
                                    @if ($discount['is_discounted'])
                                        {{ $discountRange }}
                                    @else
                                        Tidak ada diskon
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Barcode</span>
                                <span class="font-mono font-semibold">{{ $barangKi->id_barcode }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-sm">
                            <span class="text-gray-600">Status: </span>
                            <span class="status-badge @if ($barangKi->status == 'active') bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif rounded-full px-3 py-1 text-sm font-semibold capitalize">{{ $barangKi->status }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="text-gray-600">Expired: </span>
                            <span class="font-semibold text-green-600">{{ $expiredTime }}</span>
                            <span class="ml-2 rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800">{{ $barangKi->expired_time->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Product Description -->
        <div class="rounded-2xl bg-white p-8 shadow-xl">
            <h3 class="mb-4 text-xl font-bold text-gray-900">Deskripsi Produk</h3>
            <div class="prose max-w-none text-gray-700">
                <p class="mb-4">{{ $barangKi->barang->description }}</p>
            </div>
        </div>
        <!-- Sales Data Summary -->
        <div class="mb-8 grid grid-cols-1 gap-6 pt-6 md:grid-cols-4">
            <div class="hover-scale rounded-xl bg-white p-6 shadow-lg">
                <div class="mb-2 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-600">Total Penjualan</h3>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                        <span class="text-sm text-blue-600">💰</span>
                    </div>
                </div>
                <div class="text-2xl font-bold text-blue-600">Rp {{ $totalHargaKeluar }}</div>
                <div class="text-sm text-green-600">+{{ $persentaseHargaKeluar }}% dari bulan lalu</div>
            </div>
            <div class="hover-scale rounded-xl bg-white p-6 shadow-lg">
                <div class="mb-2 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-600">Unit Terjual</h3>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-100">
                        <span class="text-sm text-green-600">📦</span>
                    </div>
                </div>
                @if ($smallestSold && $smallestSold['success'] == true)
                    <div class="text-2xl font-bold text-green-600">{{ $smallestSoldFormatted }}</div>
                    <div class="text-sm text-green-600">{{ $smallestSold['converted_satuan'] }}</div>
                @endif
            </div>
            <div class="hover-scale rounded-xl bg-white p-6 shadow-lg">
                <div class="mb-2 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-600">Unit Tersedia</h3>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-100">
                        <span class="text-sm text-yellow-600">📥</span>
                    </div>
                </div>
                @if ($smallestAvailable && $smallestAvailable['success'] == true)
                    <div class="text-2xl font-bold text-yellow-600">{{ $smallestAvailableFormatted }}</div>
                    <div class="text-sm text-yellow-600">{{ $smallestAvailable['converted_satuan'] }}</div>
                @endif
            </div>
            <div class="hover-scale rounded-xl bg-white p-6 shadow-lg">
                <div class="mb-2 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-600">Stok Masuk</h3>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100">
                        <span class="text-sm text-red-600">📤</span>
                    </div>
                </div>
                <div class="text-2xl font-bold text-red-600">{{ $totalMasuk }}</div>
                <div class="text-sm text-gray-600">Bulan ini</div>
            </div>
        </div>

        <!-- Tabbed Data Section -->
        <div class="mb-8 overflow-hidden rounded-2xl bg-white shadow-xl">
            <!-- Tab Navigation -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 p-6 text-white">
                <h3 class="mb-4 text-xl font-bold">Data Penjualan & Stok</h3>
                <div class="flex space-x-2">
                    <button class="tab-button rounded-lg bg-red-600 px-4 py-2 font-medium text-white transition-all duration-200" onclick="showTab('sales-data')">
                        📊 Data Penjualan
                    </button>
                    <button class="tab-button rounded-lg bg-gray-100 px-4 py-2 font-medium text-gray-600 transition-all duration-200" onclick="showTab('stock-movement')">
                        📦 Input & Output Stok
                    </button>
                </div>
            </div>

            <div id="sales-data" class="tab-section">
                <div class="border-b bg-red-50 p-6">
                    <div class="flex items-center space-x-6 text-sm">
                        <div class="flex items-center space-x-2">
                            <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                            <span>Total: <span class="font-bold text-blue-600">155 unit</span></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="h-3 w-3 rounded-full bg-green-500"></div>
                            <span>Nilai: <span class="font-bold text-green-600">Rp 2.014.845.000</span></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="h-3 w-3 rounded-full bg-yellow-500"></div>
                            <span>Rata-rata: <span class="font-bold text-yellow-600">5.2 unit/hari</span></span>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">04 Jun 2025</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">3</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">Rp 12.999.000</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-green-600">Rp 38.997.000</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">Budi Santoso</td>
                                <td class="whitespace-nowrap px-6 py-4"><span class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-800">Selesai</span></td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">03 Jun 2025</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">2</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">Rp 12.999.000</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-green-600">Rp 25.998.000</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">Siti Nurhaliza</td>
                                <td class="whitespace-nowrap px-6 py-4"><span class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-800">Selesai</span></td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">02 Jun 2025</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">1</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">Rp 12.999.000</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-green-600">Rp 12.999.000</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">Ahmad Rizki</td>
                                <td class="whitespace-nowrap px-6 py-4"><span class="rounded-full bg-yellow-100 px-2 py-1 text-xs text-yellow-800">Proses</span></td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">01 Jun 2025</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">4</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">Rp 12.999.000</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-green-600">Rp 51.996.000</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">PT. Teknologi Maju</td>
                                <td class="whitespace-nowrap px-6 py-4"><span class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-800">Selesai</span></td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">31 Mei 2025</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">6</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">Rp 12.999.000</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-green-600">Rp 77.994.000</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">CV. Digital Store</td>
                                <td class="whitespace-nowrap px-6 py-4"><span class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-800">Selesai</span></td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">30 Mei 2025</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">2</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">Rp 12.999.000</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-green-600">Rp 25.998.000</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">Indah Permata</td>
                                <td class="whitespace-nowrap px-6 py-4"><span class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-800">Selesai</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Stock Movement Tab -->
            <div id="stock-movement" class="tab-section hidden">
                <div class="border-b bg-red-50 p-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div class="rounded-lg bg-white p-4 shadow">
                            <div class="flex items-center space-x-3">
                                <div class="h-3 w-3 rounded-full bg-green-500"></div>
                                <div>
                                    <div class="text-sm text-gray-600">Total Masuk</div>
                                    <div class="text-xl font-bold text-green-600">200 unit</div>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow">
                            <div class="flex items-center space-x-3">
                                <div class="h-3 w-3 rounded-full bg-red-500"></div>
                                <div>
                                    <div class="text-sm text-gray-600">Total Keluar</div>
                                    <div class="text-xl font-bold text-red-600">155 unit</div>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow">
                            <div class="flex items-center space-x-3">
                                <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                                <div>
                                    <div class="text-sm text-gray-600">Saldo Akhir</div>
                                    <div class="text-xl font-bold text-blue-600">45 unit</div>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow">
                            <div class="flex items-center space-x-3">
                                <div class="h-3 w-3 rounded-full bg-yellow-500"></div>
                                <div>
                                    <div class="text-sm text-gray-600">Avg. Per Bulan</div>
                                    <div class="text-xl font-bold text-yellow-600">80 unit</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Keterangan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm">04 Jun 2025</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800">KELUAR</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-red-600">-3</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">Penjualan Online #INV-20250604-001</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">Budi Santoso</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">45</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm">03 Jun 2025</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800">KELUAR</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-red-600">-2</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">Penjualan Toko #INV-20250603-004</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">Siti Nurhaliza</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">48</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm">01 Jun 2025</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">MASUK</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-green-600">+50</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">Pembelian dari Supplier #PO-20250601-003</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">PT. Samsung Mobile</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">50</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Product Detail Page JavaScript Functions

        // Gallery Functions
        function changeMainImage(src) {
            const mainImage = document.getElementById('mainImage');
            mainImage.src = src;

            // Update border for active thumbnail
            const thumbnails = document.querySelectorAll('.gallery-thumbnail');
            thumbnails.forEach(thumb => {
                thumb.classList.remove('border-red-500');
                thumb.classList.add('border-gray-200');
            });

            // Add active border to clicked thumbnail
            event.target.classList.add('border-red-500');
            event.target.classList.remove('border-gray-200');

            // Add fade effect
            mainImage.style.opacity = '0';
            setTimeout(() => {
                mainImage.style.opacity = '1';
            }, 150);
        }

        function showMorePhotos() {
            const additionalPhotos = [
                'https://images.unsplash.com/photo-1512499617640-c74ae3a79d37?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=600&h=600&fit=crop'
            ];

            let photoIndex = 0;
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
            modal.innerHTML = `
        <div class="relative max-w-4xl max-h-full p-4">
            <img id="modalImage" src="${additionalPhotos[photoIndex]}" class="max-w-full max-h-full rounded-lg">
            <button onclick="this.parentElement.parentElement.remove()" class="absolute top-4 right-4 text-white text-2xl bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center">×</button>
            <button id="prevPhoto" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white text-2xl bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center">‹</button>
            <button id="nextPhoto" class="absolute right-16 top-1/2 transform -translate-y-1/2 text-white text-2xl bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center">›</button>
        </div>
    `;

            document.body.appendChild(modal);

            // Navigation functions
            document.getElementById('prevPhoto').onclick = () => {
                photoIndex = photoIndex > 0 ? photoIndex - 1 : additionalPhotos.length - 1;
                document.getElementById('modalImage').src = additionalPhotos[photoIndex];
            };

            document.getElementById('nextPhoto').onclick = () => {
                photoIndex = photoIndex < additionalPhotos.length - 1 ? photoIndex + 1 : 0;
                document.getElementById('modalImage').src = additionalPhotos[photoIndex];
            };
        }

        // Tab functionality
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-section');
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab-button');
            tabs.forEach(tab => {
                tab.classList.remove('bg-red-600', 'text-white');
                tab.classList.add('bg-gray-100', 'text-gray-600');
            });

            // Show selected tab content with animation
            const selectedTab = document.getElementById(tabName);
            selectedTab.classList.remove('hidden');
            selectedTab.style.opacity = '0';
            setTimeout(() => {
                selectedTab.style.opacity = '1';
            }, 100);

            // Add active class to selected tab
            event.target.classList.remove('bg-gray-100', 'text-gray-600');
            event.target.classList.add('bg-red-600', 'text-white');
        }

        // Stock Management Functions
        function updateStock(action, quantity = 1) {
            const stockElement = document.querySelector('.text-green-600:contains("45")');
            let currentStock = parseInt(stockElement.textContent.split(' ')[0]);

            if (action === 'add') {
                currentStock += quantity;
            } else if (action === 'subtract' && currentStock > 0) {
                currentStock -= quantity;
            }

            stockElement.textContent = `${currentStock} Unit`;

            // Update stock status color
            if (currentStock < 10) {
                stockElement.className = 'text-2xl font-bold text-red-600';
            } else if (currentStock < 25) {
                stockElement.className = 'text-2xl font-bold text-yellow-600';
            } else {
                stockElement.className = 'text-2xl font-bold text-green-600';
            }
        }

        // Price Calculator
        function calculateDiscountPrice(originalPrice, discountPercent) {
            const discount = (originalPrice * discountPercent) / 100;
            return originalPrice - discount;
        }

        function updatePricing() {
            const originalPrice = 15999000;
            const discountPercent = 19;
            const discountedPrice = calculateDiscountPrice(originalPrice, discountPercent);

            // Format numbers to Indonesian currency
            const formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            });

            return {
                original: formatter.format(originalPrice),
                discounted: formatter.format(discountedPrice),
                savings: formatter.format(originalPrice - discountedPrice)
            };
        }

        // Search and Filter Functions
        function searchSalesData(searchTerm) {
            const rows = document.querySelectorAll('#sales-data tbody tr');
            const term = searchTerm.toLowerCase();

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(term)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function filterByDateRange(startDate, endDate) {
            const rows = document.querySelectorAll('#sales-data tbody tr');
            const start = new Date(startDate);
            const end = new Date(endDate);

            rows.forEach(row => {
                const dateCell = row.cells[0].textContent;
                const rowDate = new Date(dateCell.split(' ').reverse().join('-'));

                if (rowDate >= start && rowDate <= end) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Statistics Calculation
        function calculateSalesStats() {
            const salesRows = document.querySelectorAll('#sales-data tbody tr');
            let totalQty = 0;
            let totalValue = 0;
            let completedSales = 0;

            salesRows.forEach(row => {
                if (row.style.display !== 'none') {
                    const qty = parseInt(row.cells[1].textContent);
                    const total = parseInt(row.cells[3].textContent.replace(/[^\d]/g, ''));
                    const status = row.cells[5].textContent.trim();

                    totalQty += qty;
                    totalValue += total;

                    if (status.includes('Selesai')) {
                        completedSales++;
                    }
                }
            });

            return {
                totalQuantity: totalQty,
                totalValue: totalValue,
                completedSales: completedSales,
                avgOrderValue: totalValue / (salesRows.length || 1)
            };
        }

        // Export Functions
        function exportSalesData(format = 'csv') {
            const stats = calculateSalesStats();
            const rows = document.querySelectorAll('#sales-data tbody tr');
            let data = [];

            // Header
            data.push(['Tanggal', 'Qty', 'Harga', 'Total', 'Customer', 'Status']);

            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const rowData = [];
                    for (let i = 0; i < row.cells.length; i++) {
                        rowData.push(row.cells[i].textContent.trim());
                    }
                    data.push(rowData);
                }
            });

            if (format === 'csv') {
                const csvContent = data.map(row => row.join(',')).join('\n');
                downloadFile(csvContent, 'sales-data.csv', 'text/csv');
            } else if (format === 'json') {
                const jsonData = {
                    summary: stats,
                    data: data.slice(1).map(row => ({
                        tanggal: row[0],
                        qty: parseInt(row[1]),
                        harga: row[2],
                        total: row[3],
                        customer: row[4],
                        status: row[5]
                    }))
                };
                downloadFile(JSON.stringify(jsonData, null, 2), 'sales-data.json', 'application/json');
            }
        }

        function downloadFile(content, filename, mimeType) {
            const blob = new Blob([content], {
                type: mimeType
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        // Notification System
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        type === 'warning' ? 'bg-yellow-500 text-black' :
        'bg-blue-500 text-white'
    }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Product Actions
        function addToCart() {
            showNotification('Produk berhasil ditambahkan ke keranjang!', 'success');
            updateStock('subtract', 1);
        }

        function addToWishlist() {
            showNotification('Produk berhasil ditambahkan ke wishlist!', 'info');
        }

        function shareProduct() {
            if (navigator.share) {
                navigator.share({
                    title: 'Samsung Galaxy S24',
                    text: 'Smartphone flagship dengan teknologi AI terdepan',
                    url: window.location.href
                });
            } else {
                // Fallback for browsers that don't support Web Share API
                navigator.clipboard.writeText(window.location.href);
                showNotification('Link produk berhasil disalin!', 'info');
            }
        }

        // Real-time Updates
        function startRealTimeUpdates() {
            setInterval(() => {
                // Simulate real-time stock updates
                const randomChange = Math.random();
                if (randomChange > 0.95) { // 5% chance
                    const change = Math.floor(Math.random() * 3) + 1;
                    if (randomChange > 0.97) {
                        updateStock('add', change);
                        showNotification(`Stok bertambah +${change} unit`, 'info');
                    } else {
                        updateStock('subtract', change);
                        showNotification(`Stok berkurang -${change} unit`, 'warning');
                    }
                }
            }, 30000); // Check every 30 seconds
        }

        // Keyboard Shortcuts
        function setupKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                if (e.ctrlKey || e.metaKey) {
                    switch (e.key) {
                        case '1':
                            e.preventDefault();
                            document.querySelector('[onclick="showTab(\'sales-data\')"]').click();
                            break;
                        case '2':
                            e.preventDefault();
                            document.querySelector('[onclick="showTab(\'stock-movement\')"]').click();
                            break;
                        case '3':
                            e.preventDefault();
                            document.querySelector('[onclick="showTab(\'product-analysis\')"]').click();
                            break;
                        case 'e':
                            e.preventDefault();
                            exportSalesData('csv');
                            break;
                    }
                }
            });
        }

        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize first tab as active
            const firstTab = document.querySelector('.tab-button');
            if (firstTab) {
                firstTab.click();
            }

            // Setup keyboard shortcuts
            setupKeyboardShortcuts();

            // Start real-time updates
            startRealTimeUpdates();

            // Add smooth scrolling
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Add loading animation to images
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                img.addEventListener('load', function() {
                    this.style.opacity = '1';
                });

                img.style.transition = 'opacity 0.3s ease';
            });

            // Initialize tooltips for interactive elements
            const interactiveElements = document.querySelectorAll('.hover-scale, .gallery-thumbnail');
            interactiveElements.forEach(element => {
                element.addEventListener('mouseenter', function() {
                    this.style.cursor = 'pointer';
                });
            });

            console.log('Product Detail Page initialized successfully!');
            showNotification('Halaman detail produk siap digunakan!', 'success');
        });
    </script>
@endpush
