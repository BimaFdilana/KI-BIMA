<!-- resources/views/barang/barangki/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Barang Toko')

@section('page_title', 'Barang Toko')

@section('content')
    <div class="mx-auto py-6">
        <div class="container mx-auto py-6">
            <div class="mb-5 grid grid-cols-4 gap-4">
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Active Barang</p>
                            <h3 class="text-2xl font-bold text-gray-800">0</h3>
                            <p class="mt-1 flex items-center text-xs text-yellow-500">
                                10% dari minggu lalu
                            </p>
                        </div>
                        <div class="flex items-center justify-center rounded-full bg-teal-100 p-3">
                            <i class="fa-solid fa-circle-check text-xl text-teal-600"></i>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Active Barang</p>
                            <h3 class="text-2xl font-bold text-gray-800">0</h3>
                            <p class="mt-1 flex items-center text-xs text-yellow-500">
                                10% dari minggu lalu
                            </p>
                        </div>
                        <div class="flex items-center justify-center rounded-full bg-teal-100 p-3">
                            <i class="fa-solid fa-circle-check text-xl text-teal-600"></i>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Active Barang</p>
                            <h3 class="text-2xl font-bold text-gray-800">0</h3>
                            <p class="mt-1 flex items-center text-xs text-yellow-500">
                                10% dari minggu lalu
                            </p>
                        </div>
                        <div class="flex items-center justify-center rounded-full bg-teal-100 p-3">
                            <i class="fa-solid fa-circle-check text-xl text-teal-600"></i>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Active Barang</p>
                            <h3 class="text-2xl font-bold text-gray-800">0</h3>
                            <p class="mt-1 flex items-center text-xs text-yellow-500">
                                10% dari minggu lalu
                            </p>
                        </div>
                        <div class="flex items-center justify-center rounded-full bg-teal-100 p-3">
                            <i class="fa-solid fa-circle-check text-xl text-teal-600"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-6 flex items-center justify-between">
                <div class="flex space-x-2">
                    <span
                        class="inline-flex items-center py-2 text-center text-sm font-medium text-gray-500">Menampilkan</span>
                    <button id="dropdownDividerButton" data-dropdown-toggle="dropdownDivider"
                        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200"
                        type="button">
                        <span id="current-length"></span>
                        <svg class="ml-2.5 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <span class="inline-flex items-center py-2 text-center text-sm font-medium text-gray-500">Barang
                        Perhalaman</span>

                </div>
                <div class="flex space-x-2">
                    <!-- Dropdown menu -->
                    <div id="dropdownDivider"
                        class="z-10 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow-sm">
                        <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownDividerButton">
                            <li>
                                <a data-length="10" class="block px-4 py-2 hover:bg-gray-100">10</a>
                            </li>
                            <li>
                                <a data-length="25" class="block px-4 py-2 hover:bg-gray-100">25</a>
                            </li>
                            <li>
                                <a data-length="50" class="block px-4 py-2 hover:bg-gray-100">50</a>
                            </li>
                            <li>
                                <a data-length="100" class="block px-4 py-2 hover:bg-gray-100">100</a>
                            </li>
                        </ul>
                    </div>
                    @if (request()->query('filter'))
                        <div class="relative">
                            <a href="{{ route('toko.index') }}"
                                class="inline-flex items-center rounded-lg border border-gray-300 bg-yellow-50 px-4 py-2 text-center text-sm font-medium text-yellow-600 hover:bg-yellow-100 focus:outline-none focus:ring-4 focus:ring-yellow-200">
                                <i class="fa-solid fa-rotate mr-2"></i>
                                Reset Filter
                            </a>
                        </div>
                    @endif
                    <div class="relative">
                        <button id="filterDropdownButton" data-dropdown-toggle="filterDropdown"
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200"
                            type="button">
                            <svg class="mr-2 h-4 w-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 20 18">
                                <path
                                    d="M18.85 1.1A1.99 1.99 0 0 0 17.063 0H2.937a2 2 0 0 0-1.566 3.242L6.99 9.868 7 14a1 1 0 0 0 .4.8l4 3A1 1 0 0 0 13 17v-7.132l5.63-6.626a1.99 1.99 0 0 0 .22-2.142Z" />
                            </svg>
                            Filter
                            <svg class="ml-2.5 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 4 4 4-4" />
                            </svg>
                        </button>

                        <!-- Main Dropdown menu -->
                        <div id="filterDropdown"
                            class="z-30 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow">
                            <ul class="py-2 text-sm text-gray-700" aria-labelledby="filterDropdownButton">

                                <!-- Status Filter -->
                                <li>
                                    <button id="statusDropdownButton" data-dropdown-toggle="statusDropdown"
                                        data-dropdown-placement="left-start" type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100">
                                        <i class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Status</span>
                                    </button>
                                    <div id="statusDropdown"
                                        class="z-10 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow-sm">
                                        <ul class="py-2 text-sm text-gray-700" aria-labelledby="statusDropdownButton">
                                            <li>
                                                <a href="{{ route('toko.index', ['filter' => ['status' => 'active']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Active</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('toko.index', ['filter' => ['status' => 'inactive']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Inactive</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('toko.index', ['filter' => ['status' => 'deleted']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Deleted</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- Stock Status Filter -->
                                <li>
                                    <button id="stockStatusDropdownButton" data-dropdown-toggle="stockStatusDropdown"
                                        data-dropdown-placement="left-start" type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100">
                                        <i class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Status Stok</span>
                                    </button>
                                    <div id="stockStatusDropdown"
                                        class="z-10 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow-sm">
                                        <ul class="py-2 text-sm text-gray-700"
                                            aria-labelledby="stockStatusDropdownButton">
                                            <li>
                                                <a href="{{ route('toko.index', ['filter' => ['stock_status' => 'available']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Tersedia</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('toko.index', ['filter' => ['stock_status' => 'low_stock']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Stok Rendah</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('toko.index', ['filter' => ['stock_status' => 'out_of_stock']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Habis</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('toko.index', ['filter' => ['stock_status' => 'has_sales']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Ada Penjualan</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('toko.index', ['filter' => ['stock_status' => 'no_sales']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Tidak Ada Penjualan</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- Expired Status Filter -->
                                <li>
                                    <button id="expiredDropdownButton" data-dropdown-toggle="expiredDropdown"
                                        data-dropdown-placement="left-start" type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100">
                                        <i class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Status Expired</span>
                                    </button>
                                    <div id="expiredDropdown"
                                        class="z-10 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow-sm">
                                        <ul class="py-2 text-sm text-gray-700" aria-labelledby="expiredDropdownButton">
                                            <li>
                                                <a href="{{ route('toko.index', ['filter' => ['expired' => 'no_expiry']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Tidak Ada Expired</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('toko.index', ['filter' => ['expired' => 'early_expiry']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Mendekati Expired</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('toko.index', ['filter' => ['expired' => 'mid_expiry']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Menengah Expired</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('toko.index', ['filter' => ['expired' => 'late_expiry']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Akan Expired</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('toko.index', ['filter' => ['expired' => 'expired']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Sudah Expired</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- Category Filter -->
                                <li>
                                    <button id="categoryDropdownButton" data-dropdown-toggle="categoryDropdown"
                                        data-dropdown-placement="left-start" type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100">
                                        <i class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Kategori</span>
                                    </button>
                                    <div id="categoryDropdown"
                                        class="z-10 hidden w-60 divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white shadow-lg">
                                        <div class="border-b border-gray-100 p-3">
                                            <div class="relative">
                                                <input type="text" id="categorySearch" placeholder="Cari kategori..."
                                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <i
                                                    class="fa-solid fa-search absolute right-3 top-1/2 -translate-y-1/2 transform text-sm text-gray-400"></i>
                                            </div>
                                        </div>
                                        <div class="max-h-64 overflow-y-auto">
                                            <ul id="categoryList" class="py-2 text-sm text-gray-700"
                                                aria-labelledby="categoryDropdownButton">
                                                @foreach ($categories as $category)
                                                    <li class="category-item"
                                                        data-name="{{ strtolower($category->name) }}">
                                                        <a href="{{ route('toko.index', ['filter' => ['category_id' => $category->id]]) }}"
                                                            class="block px-4 py-2 transition-colors duration-150 hover:bg-gray-100">
                                                            {{ $category->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div id="noCategoryResults"
                                            class="hidden px-4 py-3 text-center text-sm text-gray-500">
                                            Tidak ada kategori ditemukan
                                        </div>
                                    </div>
                                </li>

                                <!-- Subcategory Filter -->
                                <li>
                                    <button id="subcategoryDropdownButton" data-dropdown-toggle="subcategoryDropdown"
                                        data-dropdown-placement="left-start" type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100">
                                        <i class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Subkategori</span>
                                    </button>
                                    <div id="subcategoryDropdown"
                                        class="z-10 hidden w-60 divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white shadow-lg">
                                        <div class="border-b border-gray-100 p-3">
                                            <div class="relative">
                                                <input type="text" id="subcategorySearch"
                                                    placeholder="Cari subkategori..."
                                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <i
                                                    class="fa-solid fa-search absolute right-3 top-1/2 -translate-y-1/2 transform text-sm text-gray-400"></i>
                                            </div>
                                        </div>
                                        <div class="max-h-64 overflow-y-auto">
                                            <ul id="subcategoryList" class="py-2 text-sm text-gray-700"
                                                aria-labelledby="subcategoryDropdownButton">
                                                @foreach ($subcategories as $subcategory)
                                                    <li class="subcategory-item"
                                                        data-name="{{ strtolower($subcategory->name) }}">
                                                        <a href="{{ route('toko.index', ['filter' => ['subcategory_id' => $subcategory->id]]) }}"
                                                            class="block px-4 py-2 transition-colors duration-150 hover:bg-gray-100">
                                                            {{ $subcategory->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div id="noSubcategoryResults"
                                            class="hidden px-4 py-3 text-center text-sm text-gray-500">
                                            Tidak ada subkategori ditemukan
                                        </div>
                                    </div>
                                </li>

                                <!-- Brand Filter -->
                                <li>
                                    <button id="brandDropdownButton" data-dropdown-toggle="brandDropdown"
                                        data-dropdown-placement="left-start" type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100">
                                        <i class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Brand</span>
                                    </button>
                                    <div id="brandDropdown"
                                        class="z-10 hidden w-60 divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white shadow-lg">
                                        <div class="border-b border-gray-100 p-3">
                                            <div class="relative">
                                                <input type="text" id="brandSearch" placeholder="Cari brand..."
                                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <i
                                                    class="fa-solid fa-search absolute right-3 top-1/2 -translate-y-1/2 transform text-sm text-gray-400"></i>
                                            </div>
                                        </div>
                                        <div class="max-h-64 overflow-y-auto">
                                            <ul id="brandList" class="py-2 text-sm text-gray-700"
                                                aria-labelledby="brandDropdownButton">
                                                @foreach ($brands as $brand)
                                                    <li class="brand-item" data-name="{{ strtolower($brand->name) }}">
                                                        <a href="{{ route('toko.index', ['filter' => ['brand_id' => $brand->id]]) }}"
                                                            class="block px-4 py-2 transition-colors duration-150 hover:bg-gray-100">
                                                            {{ $brand->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div id="noBrandResults"
                                            class="hidden px-4 py-3 text-center text-sm text-gray-500">
                                            Tidak ada brand ditemukan
                                        </div>
                                    </div>
                                </li>

                                <!-- Type Filter -->
                                <li>
                                    <button id="typeDropdownButton" data-dropdown-toggle="typeDropdown"
                                        data-dropdown-placement="left-start" type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100">
                                        <i class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Tipe</span>
                                    </button>
                                    <div id="typeDropdown"
                                        class="z-10 hidden w-60 divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white shadow-lg">
                                        <div class="border-b border-gray-100 p-3">
                                            <div class="relative">
                                                <input type="text" id="typeSearch" placeholder="Cari tipe..."
                                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <i
                                                    class="fa-solid fa-search absolute right-3 top-1/2 -translate-y-1/2 transform text-sm text-gray-400"></i>
                                            </div>
                                        </div>
                                        <div class="max-h-64 overflow-y-auto">
                                            <ul id="typeList" class="py-2 text-sm text-gray-700"
                                                aria-labelledby="typeDropdownButton">
                                                @foreach ($types as $type)
                                                    <li class="type-item" data-name="{{ strtolower($type->name) }}">
                                                        <a href="{{ route('toko.index', ['filter' => ['type_id' => $type->id]]) }}"
                                                            class="block px-4 py-2 transition-colors duration-150 hover:bg-gray-100">
                                                            {{ $type->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div id="noTypeResults"
                                            class="hidden px-4 py-3 text-center text-sm text-gray-500">
                                            Tidak ada tipe ditemukan
                                        </div>
                                    </div>
                                </li>

                                <!-- Toko Filter -->
                                <li>
                                    <button id="tokoDropdownButton" data-dropdown-toggle="tokoDropdown"
                                        data-dropdown-placement="left-start" type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100">
                                        <i class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Toko</span>
                                    </button>
                                    <div id="tokoDropdown"
                                        class="z-10 hidden w-60 divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white shadow-lg">
                                        <div class="border-b border-gray-100 p-3">
                                            <div class="relative">
                                                <input type="text" id="tokoSearch" placeholder="Cari toko..."
                                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <i
                                                    class="fa-solid fa-search absolute right-3 top-1/2 -translate-y-1/2 transform text-sm text-gray-400"></i>
                                            </div>
                                        </div>
                                        <div class="max-h-64 overflow-y-auto">
                                            <ul id="tokoList" class="py-2 text-sm text-gray-700"
                                                aria-labelledby="tokoDropdownButton">
                                                @foreach ($tokos as $toko)
                                                    <li class="toko-item" data-name="{{ strtolower($toko->name) }}">
                                                        <a href="{{ route('toko.index', ['filter' => ['toko_id' => $toko->id]]) }}"
                                                            class="block px-4 py-2 transition-colors duration-150 hover:bg-gray-100">
                                                            {{ $toko->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div id="noTokoResults"
                                            class="hidden px-4 py-3 text-center text-sm text-gray-500">
                                            Tidak ada toko ditemukan
                                        </div>
                                    </div>
                                </li>

                                <!-- Advanced Filters -->
                                <li>
                                    <button id="advancedDropdownButton" data-dropdown-toggle="advancedDropdown"
                                        data-dropdown-placement="left-start" type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100">
                                        <i class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Filter Lanjutan</span>
                                    </button>
                                    <div id="advancedDropdown"
                                        class="z-10 hidden w-96 divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white shadow-lg">
                                        <div class="p-4">
                                            <form id="advancedFilterForm" method="GET"
                                                action="{{ route('toko.index') }}">
                                                <!-- Preserve existing filters -->
                                                @if (request('filter'))
                                                    @foreach (request('filter') as $key => $value)
                                                        @if (
                                                            !in_array($key, [
                                                                'price_min',
                                                                'price_max',
                                                                'quantity_min',
                                                                'quantity_max',
                                                                'sold_min',
                                                                'sold_max',
                                                                'profit_margin_min',
                                                                'profit_margin_max',
                                                                'created_from',
                                                                'created_to',
                                                            ]))
                                                            <input type="hidden" name="filter[{{ $key }}]"
                                                                value="{{ $value }}">
                                                        @endif
                                                    @endforeach
                                                @endif

                                                <!-- Price Range -->
                                                <div class="mb-4">
                                                    <h4 class="mb-3 text-sm font-medium text-gray-700">Range Harga</h4>
                                                    <div class="flex items-center space-x-2">
                                                        <input type="number" name="filter[price_min]" placeholder="Min"
                                                            value="{{ request('filter.price_min') }}"
                                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        <span class="text-gray-400">-</span>
                                                        <input type="number" name="filter[price_max]" placeholder="Max"
                                                            value="{{ request('filter.price_max') }}"
                                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    </div>
                                                </div>

                                                <!-- Quantity Range -->
                                                <div class="mb-4">
                                                    <h4 class="mb-3 text-sm font-medium text-gray-700">Range Quantity</h4>
                                                    <div class="flex items-center space-x-2">
                                                        <input type="number" name="filter[quantity_min]"
                                                            placeholder="Min"
                                                            value="{{ request('filter.quantity_min') }}"
                                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        <span class="text-gray-400">-</span>
                                                        <input type="number" name="filter[quantity_max]"
                                                            placeholder="Max"
                                                            value="{{ request('filter.quantity_max') }}"
                                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    </div>
                                                </div>

                                                <!-- Sold Range -->
                                                <div class="mb-4">
                                                    <h4 class="mb-3 text-sm font-medium text-gray-700">Range Penjualan</h4>
                                                    <div class="flex items-center space-x-2">
                                                        <input type="number" name="filter[sold_min]" placeholder="Min"
                                                            value="{{ request('filter.sold_min') }}"
                                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        <span class="text-gray-400">-</span>
                                                        <input type="number" name="filter[sold_max]" placeholder="Max"
                                                            value="{{ request('filter.sold_max') }}"
                                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    </div>
                                                </div>

                                                <!-- Profit Margin Range -->
                                                <div class="mb-4">
                                                    <h4 class="mb-3 text-sm font-medium text-gray-700">Range Profit Margin
                                                        (%)</h4>
                                                    <div class="flex items-center space-x-2">
                                                        <input type="number" name="filter[profit_margin_min]"
                                                            placeholder="Min"
                                                            value="{{ request('filter.profit_margin_min') }}"
                                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        <span class="text-gray-400">-</span>
                                                        <input type="number" name="filter[profit_margin_max]"
                                                            placeholder="Max"
                                                            value="{{ request('filter.profit_margin_max') }}"
                                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    </div>
                                                </div>

                                                <!-- Date Range -->
                                                <div class="mb-4">
                                                    <h4 class="mb-3 text-sm font-medium text-gray-700">Range Tanggal</h4>
                                                    <div class="flex items-center space-x-2">
                                                        <input type="date" name="filter[created_from]"
                                                            value="{{ request('filter.created_from') }}"
                                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        <span class="text-gray-400">-</span>
                                                        <input type="date" name="filter[created_to]"
                                                            value="{{ request('filter.created_to') }}"
                                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    </div>
                                                </div>

                                                <!-- Show All Option -->
                                                <div class="mb-4">
                                                    <label class="flex items-center">
                                                        <input type="checkbox" name="filter[show_all]" value="1"
                                                            {{ request('filter.show_all') ? 'checked' : '' }}
                                                            class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                        <span class="text-sm text-gray-700">Tampilkan semua item (termasuk
                                                            tanpa stok/penjualan)</span>
                                                    </label>
                                                </div>

                                                <!-- Submit Button -->
                                                <div class="flex space-x-2">
                                                    <button type="submit"
                                                        class="flex-1 rounded-lg bg-blue-600 px-4 py-2 text-sm text-white transition-colors duration-150 hover:bg-blue-700">
                                                        <i class="fa-solid fa-filter mr-2"></i>Terapkan Filter
                                                    </button>
                                                    <a href="{{ route('toko.index') }}"
                                                        class="flex-1 rounded-lg bg-gray-600 px-4 py-2 text-center text-sm text-white transition-colors duration-150 hover:bg-gray-700">
                                                        <i class="fa-solid fa-times mr-2"></i>Reset
                                                    </a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="relative">
                        <button id="add-product-btn"
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-red-600 px-4 py-2 text-center text-sm font-medium text-white hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-200">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Product
                        </button>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <div class="overflow-x-auto">
                    {!! $dataTable->table(['class' => 'w-full text-sm text-left text-gray-500']) !!}
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    {{ $dataTable->scripts() }}

    <!-- Scanner Manager Script -->
    <script src="{{ asset('js/datatable-scanner-manager.js') }}"></script>
    <script>
        // Search functionality for dropdowns
        document.addEventListener('DOMContentLoaded', function() {
            // Category search
            const categorySearch = document.getElementById('categorySearch');
            const categoryItems = document.querySelectorAll('.category-item');
            const noCategoryResults = document.getElementById('noCategoryResults');

            if (categorySearch) {
                categorySearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    let visibleCount = 0;

                    categoryItems.forEach(item => {
                        const itemName = item.getAttribute('data-name');
                        if (itemName.includes(searchTerm)) {
                            item.style.display = 'block';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    if (visibleCount === 0) {
                        noCategoryResults.classList.remove('hidden');
                    } else {
                        noCategoryResults.classList.add('hidden');
                    }
                });
            }

            // Subcategory search
            const subcategorySearch = document.getElementById('subcategorySearch');
            const subcategoryItems = document.querySelectorAll('.subcategory-item');
            const noSubcategoryResults = document.getElementById('noSubcategoryResults');

            if (subcategorySearch) {
                subcategorySearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    let visibleCount = 0;

                    subcategoryItems.forEach(item => {
                        const itemName = item.getAttribute('data-name');
                        if (itemName.includes(searchTerm)) {
                            item.style.display = 'block';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    if (visibleCount === 0) {
                        noSubcategoryResults.classList.remove('hidden');
                    } else {
                        noSubcategoryResults.classList.add('hidden');
                    }
                });
            }

            // Brand search
            const brandSearch = document.getElementById('brandSearch');
            const brandItems = document.querySelectorAll('.brand-item');
            const noBrandResults = document.getElementById('noBrandResults');

            if (brandSearch) {
                brandSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    let visibleCount = 0;

                    brandItems.forEach(item => {
                        const itemName = item.getAttribute('data-name');
                        if (itemName.includes(searchTerm)) {
                            item.style.display = 'block';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    if (visibleCount === 0) {
                        noBrandResults.classList.remove('hidden');
                    } else {
                        noBrandResults.classList.add('hidden');
                    }
                });
            }

            // Type search
            const typeSearch = document.getElementById('typeSearch');
            const typeItems = document.querySelectorAll('.type-item');
            const noTypeResults = document.getElementById('noTypeResults');

            if (typeSearch) {
                typeSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    let visibleCount = 0;

                    typeItems.forEach(item => {
                        const itemName = item.getAttribute('data-name');
                        if (itemName.includes(searchTerm)) {
                            item.style.display = 'block';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    if (visibleCount === 0) {
                        noTypeResults.classList.remove('hidden');
                    } else {
                        noTypeResults.classList.add('hidden');
                    }
                });
            }

            // Toko search
            const tokoSearch = document.getElementById('tokoSearch');
            const tokoItems = document.querySelectorAll('.toko-item');
            const noTokoResults = document.getElementById('noTokoResults');

            if (tokoSearch) {
                tokoSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    let visibleCount = 0;

                    tokoItems.forEach(item => {
                        const itemName = item.getAttribute('data-name');
                        if (itemName.includes(searchTerm)) {
                            item.style.display = 'block';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    if (visibleCount === 0) {
                        noTokoResults.classList.remove('hidden');
                    } else {
                        noTokoResults.classList.add('hidden');
                    }
                });
            }

            // Clear search when dropdown is closed
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#categoryDropdown')) {
                    if (categorySearch) categorySearch.value = '';
                    categoryItems.forEach(item => item.style.display = 'block');
                    if (noCategoryResults) noCategoryResults.classList.add('hidden');
                }

                if (!e.target.closest('#subcategoryDropdown')) {
                    if (subcategorySearch) subcategorySearch.value = '';
                    subcategoryItems.forEach(item => item.style.display = 'block');
                    if (noSubcategoryResults) noSubcategoryResults.classList.add('hidden');
                }

                if (!e.target.closest('#brandDropdown')) {
                    if (brandSearch) brandSearch.value = '';
                    brandItems.forEach(item => item.style.display = 'block');
                    if (noBrandResults) noBrandResults.classList.add('hidden');
                }

                if (!e.target.closest('#typeDropdown')) {
                    if (typeSearch) typeSearch.value = '';
                    typeItems.forEach(item => item.style.display = 'block');
                    if (noTypeResults) noTypeResults.classList.add('hidden');
                }

                if (!e.target.closest('#tokoDropdown')) {
                    if (tokoSearch) tokoSearch.value = '';
                    tokoItems.forEach(item => item.style.display = 'block');
                    if (noTokoResults) noTokoResults.classList.add('hidden');
                }
            });
        });
    </script>
    <script>
        $(function() {
            let table = $('#barangtoko-table').DataTable();
            let scannerEnabled = true;

            function updateCurrentLength() {
                $('#current-length').text(table.page.len());
            }

            updateCurrentLength();

            $('#dropdownDivider li a').on('click', function(e) {
                e.preventDefault();
                let length = $(this).data('length');
                table.page.len(length).draw();
                updateCurrentLength();
            });

            table.on('length.dt', function() {
                updateCurrentLength();
            });


            // Update scanner status indicator
            setInterval(function() {
                if (window.scannerManager && scannerEnabled) {
                    const activeTables = window.scannerManager.getManagedTables();
                    const activeTable = window.scannerManager.currentFocusedTable;

                    $('#activeTableIndicator').text(
                        activeTable ? `Active: ${activeTable}` : `Tables: ${activeTables.length}`
                    );
                }
            }, 1000);

            // Manual test function (for development)
            window.testScan = function(barcode, tableId) {
                if (window.scannerManager) {
                    window.scannerManager.manualScan(barcode, tableId);
                }
            };

        });


        // Handler diperbaiki: buat instance baru tiap kali ekspor
        function handleExport(tableId, format) {
            const exporter = new ExportData('barang/toko', format);
            exporter.export();
        }
    </script>
@endpush
