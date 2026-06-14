@extends('layouts.admin')
@section('nav_title', 'Data Barang KI')

@section('page_title', 'Data Barang KI')
@push('styles')
    <link rel="stylesheet" href="{{ asset('datatables/datatables.css') }}">
    <style>
        #detailBarangTable_filter::before {
            left: 3rem !important;
        }

        /* Custom scrollbar styling */
        #subcategoryDropdown .max-h-64::-webkit-scrollbar {
            width: 6px;
        }

        #subcategoryDropdown .max-h-64::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        #subcategoryDropdown .max-h-64::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        #subcategoryDropdown .max-h-64::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Smooth transitions */
        .subcategory-item {
            transition: all 0.15s ease-in-out;
        }

        /* Search input focus effect */
        #subcategorySearch:focus {
            box-shadow: 0 0 0 2px rgb(238, 44, 44);
        }

        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        /* Focus styles */
        #otherDropdown input:focus {
            box-shadow: 0 0 0 2px rgb(238, 44, 44);
        }

        /* Button styles */
        #otherDropdown button {
            transition: all 0.15s ease-in-out;
        }

        #otherDropdown button:hover {
            transform: translateY(-1px);
        }
    </style>
@endpush
@section('content')
    <div class="mx-auto py-6">
        <div class="container mx-auto py-6">
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
                    <span class="inline-flex items-center py-2 text-center text-sm font-medium text-gray-500">Data
                        Perhalaman</span>
                </div>
                <div class="flex space-x-2">
                    <!-- Dropdown menu -->
                    <div id="dropdownDivider"
                        class="z-10 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow-sm">
                        <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownDividerButton">
                            <li><a data-length="10" class="block px-4 py-2 hover:bg-gray-100">10</a></li>
                            <li><a data-length="25" class="block px-4 py-2 hover:bg-gray-100">25</a></li>
                            <li><a data-length="50" class="block px-4 py-2 hover:bg-gray-100">50</a></li>
                            <li><a data-length="100" class="block px-4 py-2 hover:bg-gray-100">100</a></li>
                        </ul>
                    </div>
                    @if (request()->query('filter'))
                        <div class="relative">
                            <a href="{{ route('barang.ki.index') }}"
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
                        <!-- Dropdown menu -->
                        <div id="filterDropdown"
                            class="z-30 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow">
                            <ul class="py-2 text-sm text-gray-700" aria-labelledby="filterDropdownButton">
                                <li>
                                    <button id="statusDropdownButton" data-dropdown-toggle="statusDropdown"
                                        data-dropdown-placement="left-start" type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100"><i
                                            class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Filter by status</span>
                                    </button>
                                    <div id="statusDropdown"
                                        class="z-10 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow-sm">
                                        <ul class="py-2 text-sm text-gray-700" aria-labelledby="doubleDropdownButton">
                                            <li>
                                                <a href="{{ route('barang.ki.index', ['filter' => ['status' => 'active']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Aktif</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('barang.ki.index', ['filter' => ['status' => 'nonactive']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Inaktif</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('barang.ki.index', ['filter' => ['status' => 'deleted']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Deleted</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <button id="expiredDropdownButton" data-dropdown-toggle="expiredDropdown"
                                        data-dropdown-placement="left-start" type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100"><i
                                            class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Expired</span>
                                    </button>
                                    <div id="expiredDropdown"
                                        class="z-10 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow-sm">
                                        <ul class="py-2 text-sm text-gray-700" aria-labelledby="doubleDropdownButton">
                                            <li>
                                                <a href="{{ route('barang.ki.index', ['filter' => ['expired' => 'no_expiry']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">No Expiry</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('barang.ki.index', ['filter' => ['expired' => 'fresh']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Baru</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('barang.ki.index', ['filter' => ['expired' => 'early_expiry']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Mendekati Expiry</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('barang.ki.index', ['filter' => ['expired' => 'mid_expiry']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Tengah Expiry</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('barang.ki.index', ['filter' => ['expired' => 'late_expiry']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Akan Expired</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('barang.ki.index', ['filter' => ['expired' => 'expired']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Sudah Expired</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <button id="discountDropdownButton" data-dropdown-toggle="discountDropdown"
                                        data-dropdown-placement="left-start" type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100"><i
                                            class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Discount</span>
                                    </button>
                                    <div id="discountDropdown"
                                        class="z-10 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow-sm">
                                        <ul class="py-2 text-sm text-gray-700" aria-labelledby="doubleDropdownButton">
                                            <li>
                                                <a href="{{ route('barang.ki.index', ['filter' => ['discount' => 'ongoing']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Berlangsung</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('barang.ki.index', ['filter' => ['discount' => 'coming']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Akan Datang</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('barang.ki.index', ['filter' => ['discount' => 'expired_discount']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Selesai</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('barang.ki.index', ['filter' => ['discount' => 'no_discount']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Tidak Ada</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <button id="stockDropdownButton" data-dropdown-toggle="stockDropdown"
                                        data-dropdown-placement="left-start" type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100"><i
                                            class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Stock</span>
                                    </button>
                                    <div id="stockDropdown"
                                        class="z-10 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow-sm">
                                        <ul class="py-2 text-sm text-gray-700" aria-labelledby="doubleDropdownButton">
                                            <li>
                                                <a href="{{ route('barang.ki.index', ['filter' => ['stock' => 'available']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Tersedia</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('barang.ki.index', ['filter' => ['stock' => 'out_of_stock']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Habis</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('barang.ki.index', ['filter' => ['stock' => 'low_stock']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Hampir Habis</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <button id="subcategoryDropdownButton" data-dropdown-toggle="subcategoryDropdown"
                                        data-dropdown-placement="left-start" type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100">
                                        <i class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Subcategory</span>
                                    </button>
                                    <div id="subcategoryDropdown"
                                        class="z-10 hidden w-60 divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white shadow-lg">
                                        <!-- Search Input -->
                                        <div class="border-b border-gray-100 p-3">
                                            <div class="relative">
                                                <input type="text" id="subcategorySearch"
                                                    placeholder="Search subcategory..."
                                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <i
                                                    class="fa-solid fa-search absolute right-3 top-1/2 -translate-y-1/2 transform text-sm text-gray-400"></i>
                                            </div>
                                        </div>

                                        <!-- Scrollable List -->
                                        <div class="max-h-64 overflow-y-auto">
                                            <ul id="subcategoryList" class="py-2 text-sm text-gray-700"
                                                aria-labelledby="subcategoryDropdownButton">
                                                @foreach ($subcategories as $subcategory)
                                                    <li class="subcategory-item"
                                                        data-name="{{ strtolower($subcategory->name) }}">
                                                        <a href="{{ route('barang.ki.index', ['filter' => ['subcategory' => $subcategory->id]]) }}"
                                                            class="block px-4 py-2 transition-colors duration-150 hover:bg-gray-100">
                                                            {{ $subcategory->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <!-- No Results Message -->
                                        <div id="noResults" class="hidden px-4 py-3 text-center text-sm text-gray-500">
                                            No subcategories found
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <button id="otherDropdownButton" data-dropdown-toggle="otherDropdown"
                                        data-dropdown-placement="left-start" type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100">
                                        <i class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Lainnya</span>
                                    </button>
                                    <div id="otherDropdown"
                                        class="z-10 hidden w-80 divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white shadow-lg">
                                        <div class="p-4">
                                            <!-- Price Range Filter -->
                                            <div class="mb-4">
                                                <h4 class="mb-3 text-sm font-medium text-gray-700">Filter Harga</h4>
                                                <form id="priceFilterForm" method="GET"
                                                    action="{{ route('barang.ki.index') }}">
                                                    <!-- Preserve existing filters -->
                                                    @if (request('filter'))
                                                        @foreach (request('filter') as $key => $value)
                                                            @if (!in_array($key, ['price_min', 'price_max']))
                                                                <input type="hidden" name="filter[{{ $key }}]"
                                                                    value="{{ $value }}">
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    <div class="mb-3 flex items-center space-x-2">
                                                        <div class="flex-1">
                                                            <label class="mb-1 block text-xs text-gray-500">Harga
                                                                Min</label>
                                                            <input type="number" name="filter[price_min]" id="priceMin"
                                                                placeholder="0" value="{{ request('filter.price_min') }}"
                                                                min="0"
                                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        </div>
                                                        <span class="mt-5 text-gray-400">-</span>
                                                        <div class="flex-1">
                                                            <label class="mb-1 block text-xs text-gray-500">Harga
                                                                Max</label>
                                                            <input type="number" name="filter[price_max]" id="priceMax"
                                                                placeholder="Tidak terbatas"
                                                                value="{{ request('filter.price_max') }}" min="0"
                                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        </div>
                                                    </div>

                                                    <div class="flex space-x-2">
                                                        <button type="submit"
                                                            class="flex-1 rounded-lg bg-red-600 px-4 py-2 text-sm text-white transition-colors duration-150 hover:bg-blue-700">
                                                            <i class="fa-solid fa-filter mr-2"></i>Terapkan
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- Quick Price Filters -->
                                            <div class="border-t border-gray-200 pt-3">
                                                <h4 class="mb-3 text-sm font-medium text-gray-700">Filter Cepat</h4>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <a href="{{ route('barang.ki.index') }}?{{ http_build_query(array_merge(request('filter', []), ['price_max' => 100000])) }}"
                                                        class="rounded-lg bg-gray-50 px-3 py-2 text-center text-sm transition-colors duration-150 hover:bg-gray-100">
                                                        < 100K </a>
                                                            <a href="{{ route('barang.ki.index') }}?{{ http_build_query(['filter' => array_merge(request('filter', []), ['price_min' => 100000, 'price_max' => 500000])]) }}"
                                                                class="rounded-lg bg-gray-50 px-3 py-2 text-center text-sm transition-colors duration-150 hover:bg-gray-100">
                                                                100K - 500K
                                                            </a>
                                                            <a href="{{ route('barang.ki.index') }}?{{ http_build_query(['filter' => array_merge(request('filter', []), ['price_min' => 500000, 'price_max' => 1000000])]) }}"
                                                                class="rounded-lg bg-gray-50 px-3 py-2 text-center text-sm transition-colors duration-150 hover:bg-gray-100">
                                                                500K - 1M
                                                            </a>
                                                            <a href="{{ route('barang.ki.index') }}?{{ http_build_query(['filter' => array_merge(request('filter', []), ['price_min' => 1000000])]) }}"
                                                                class="rounded-lg bg-gray-50 px-3 py-2 text-center text-sm transition-colors duration-150 hover:bg-gray-100">
                                                                > 1M
                                                            </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @can('create.barang.ki')
                        <div class="relative">
                            <a href="{{ route('barang.ki.tambah-barang') }}"
                                class="inline-flex items-center rounded-lg border border-gray-300 bg-red-600 px-4 py-2 text-center text-sm font-medium text-white hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-200">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Barang
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <div class="overflow-x-auto">
                    {!! $dataTable->table(['class' => 'w-full text-sm text-left text-gray-500']) !!}
                </div>
            </div>
        </div>
    </div>

    <div id="message-modal"
        class="z-60 fixed inset-0 hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50">
        <div class="relative w-full max-w-md">
            <div id="message-modal-container"
                class="animate-jump-in w-full max-w-md overflow-hidden rounded-lg bg-white shadow-xl transition-all duration-300">
                <div class="p-6">
                    <div class="text-center">
                        <!-- Icon Container -->
                        <div id="iconContainer" class="animate__animated animate__rotateIn my-6 flex justify-center">
                            <i id="modalIcon" class="fas fa-circle-exclamation text-6xl text-red-500"></i>
                        </div>

                        <!-- Message -->
                        <h3 id="modalTitle" class="mb-2 text-2xl font-bold text-gray-800">Something went wrong</h3>
                        <p id="modalMessage" class="mb-6 text-gray-600">
                            Please try again later.
                        </p>
                        <input type="hidden" action="" id="barcode">
                        <!-- Buttons -->
                        <div id="actionButtons" class="flex justify-center space-x-4">
                            <button id="actionBtnMessage"
                                class="cursor-pointer rounded-lg bg-red-500 px-6 py-2 font-bold text-white transition duration-200 hover:bg-red-600">
                                Delete
                            </button>
                            <button id="closeBtnMessage"
                                class="cursor-pointer rounded-lg bg-gray-300 px-6 py-2 font-bold text-gray-800 transition duration-200 hover:bg-gray-400">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalDetailBarang"
        class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50">
        <div class="relative mx-4 w-full max-w-6xl">
            <div id="modalDetailBarangContainer"
                class="animate-jump-in w-full overflow-hidden rounded-lg bg-white shadow-xl transition-all duration-300">
                <!-- Header Modal -->
                <div class="flex items-center justify-between border-b border-gray-200 p-6">
                    <div class="flex items-center space-x-2">
                        <h4 id="modalDetailBarangTitle" class="text-xl font-bold text-gray-800">Detail Barang</h4>
                        <div id="expiredTimeBadgeDetail"
                            class="hidden rounded bg-red-100 px-2 py-1 text-xs font-medium text-red-800">Expired</div>
                    </div>
                    <button id="closeModalBtn" type="button"
                        class="text-gray-400 transition-colors duration-200 hover:text-gray-600 focus:text-gray-600 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Content Modal -->
                <div class="p-6">
                    <!-- Summary Cards -->
                    <div id="summaryCards" class="mb-6 grid hidden grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                            <h5 class="mb-1 text-sm font-medium text-blue-800">Total Stock</h5>
                            <p id="totalStock" class="text-2xl font-bold text-blue-900">0</p>
                        </div>
                        <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                            <div class="flex items-center justify-between">
                                <h5 class="mb-1 text-sm font-medium text-green-800">Total Tersedia</h5>
                                <span id="totalAvailableBadge"
                                    class="rounded bg-red-100 px-2 py-1 text-xs font-medium text-red-800">Expired</span>
                            </div>
                            <p id="totalAvailable" class="text-2xl font-bold text-green-900">0</p>
                        </div>
                        <div class="rounded-lg border border-orange-200 bg-orange-50 p-4">
                            <h5 class="mb-1 text-sm font-medium text-orange-800">Total Terjual</h5>
                            <p id="totalSold" class="text-2xl font-bold text-orange-900">0</p>
                        </div>
                    </div>
                    <p id="expiredTime" class="mb-6 text-gray-600">Expired Time: <span id="expiredTimeValue"></span>
                        <span id="expiredTimeBadge"></span>
                    </p>
                    <!-- DataTable -->
                    <div class="overflow-x-auto">
                        <table id="detailBarangTable" class="display w-full" style="width:100%">
                            <thead>
                                <tr>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="flex justify-end border-t border-gray-200 bg-gray-50 px-6 py-4">
                    <button id="closeModalBtnFooter" type="button"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div id="edit-barang-modal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-black/50">
        <div class="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-lg bg-white shadow-xl">
            <div class="flex items-center justify-between rounded-t-lg bg-red-600 p-4 text-white">
                <h2 class="text-xl font-bold">Edit Barang</h2>
                <button id="close-edit-modal" class="text-white hover:text-red-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="edit-barangForm" class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2" method="POST"
                enctype="multipart/form-data">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Barang</label>
                        <div class="flex items-center gap-3">
                            <div class="relative w-full">
                                <select id="barang-select" name="barang" required
                                    data-hs-select='{
                                        "hasSearch": true,
                                        "isSearchDirectMatch": false,
                                        "searchPlaceholder": "Cari barang...",
                                        "searchClasses": "block w-full cari-barang-input text-sm border-gray-200 rounded-lg focus:border-red-500 focus:ring-red-500 py-2 px-3",
                                        "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0 border-b border-gray-100",
                                        "placeholder": "Pilih Barang...",
                                        "toggleTag": "<button type=\"button\" aria-expanded=\"false\"><span class=\"me-2\" data-icon></span><span class=\"text-gray-800\" data-title></span></button>",
                                        "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-10 flex items-center gap-x-2 w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm text-gray-800 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200",
                                        "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 ",
                                        "optionClasses": "py-2.5 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100 transition-colors duration-150",
                                        "optionTemplate": "<div class=\"flex items-center justify-between w-full\"><div class=\"flex items-center gap-3\"><div class=\"hs-selected:text-red-600 hs-selected:font-semibold text-sm text-gray-800\" data-icon></div><div class=\"hs-selected:text-red-600 hs-selected:font-semibold text-sm text-gray-800\" data-title></div></div><div class=\"ms-auto\"><span class=\"hidden hs-selected:block\"><svg class=\"w-4 h-4 text-red-600\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z\" clip-rule=\"evenodd\"/></svg></span></div></div>",
                                        "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2 pointer-events-none\"><svg class=\"w-4 h-4 text-gray-500 hs-select-open:rotate-180 transition-transform duration-200\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 9l-7 7-7-7\"/></svg></div>"
                                    }'
                                    class="hidden">
                                    <option value="">Pilih Barang...</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Barcode -->
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Barcode</label>
                        <div class="relative">
                            <input type="text" id="barcodeEdit" name="barcode"
                                class="w-full rounded-md border border-gray-300 px-3 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-500">
                            <button class="absolute right-2 top-1/2 -translate-y-1/2 transform text-red-600">
                                <i class="fas fa-barcode"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Satuan Barang -->
                    <div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Satuan</label>
                            <div class="flex items-center gap-3">
                                <div class="relative w-full">
                                    <select id="satuan-select" name="satuan" required
                                        data-hs-select='{
                                        "hasSearch": true,
                                        "isSearchDirectMatch": false,
                                        "searchPlaceholder": "Search...",
                                        "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-red-500 focus:ring-red-500 before:absolute before:inset-0 before:z-1 py-1.5 sm:py-2 px-3",
                                        "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0",
                                        "placeholder": "Select Satuan...",
                                        "toggleTag": "<button type=\"button\" aria-expanded=\"false\"></button>",
                                        "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-hidden focus:ring-2 focus:ring-red-500",
                                        "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300",
                                        "optionClasses": "py-2.5 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-hidden focus:bg-gray-100",
                                        "optionTemplate": "<div class=\"flex items-center\"><div class=\"truncate\"><div class=\"hs-selected:text-red-600 hs-selected:font-semibold text-sm text-gray-800\" data-title></div></div><div class=\"ms-auto\"><span class=\"block hs-selected:hidden\" data-icon></span><div class=\"hidden hs-selected:block\"><i class=\"fas fa-check text-red-600 text-xs\"></i></div></div>",
                                        "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><i class=\"fas fa-chevron-down text-gray-500 text-xs\"></i></div>"
                                    }'
                                        class="hidden">
                                        <option value="">Pilih Satuan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Quantity</label>
                        <input id="quantity" name="quantity" type="number"
                            class="w-full rounded-md border border-gray-300 px-3 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Price Inputs -->
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Harga Beli</label>
                        <input type="text" name="price_buy" id="price_buy"
                            class="w-full rounded-md border border-gray-300 px-3 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-500"
                            onkeyup="this.value=currencyFormat(this.value)">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Harga Jual</label>
                        <input type="text" name="price_sell" id="price_sell"
                            class="w-full rounded-md border border-gray-300 px-3 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-500"
                            onkeyup="this.value=currencyFormat(this.value)">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Markup (%)</label>
                        <input type="number" name="price_up" id="price_up"
                            class="w-full rounded-md border border-gray-300 px-3 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-500">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Expired Time</label>
                        <div class="flex gap-2">
                            <input type="date" name="expired_time_date" id="expired_time_date"
                                class="w-full rounded-md border border-gray-300 px-3 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-500">
                            <input type="time" name="expired_time_time" id="expired_time_time"
                                class="w-full rounded-md border border-gray-300 px-3 py-2.5 focus:border-red-500 focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                </div>

                <!-- Discount Section -->
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 md:col-span-2">
                    <h3 class="mb-4 text-lg font-medium text-red-600">Pengaturan Diskon</h3>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2" id="discount-section">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Start Date</label>
                            <input id="discount_start" name="discount_start" type="datetime-local"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-red-500 focus:ring-2 focus:ring-red-500">
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">End Date</label>
                            <input id="discount_end" name="discount_end" type="datetime-local"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-red-500 focus:ring-2 focus:ring-red-500">
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Tipe Diskon</label>
                            <div class="flex gap-4">
                                <label class="inline-flex items-center">
                                    <input id="discount_type_amount" type="radio" name="discount_type" value="amount"
                                        class="text-red-600 focus:ring-red-500">
                                    <span class="mx-2">Amount</span>
                                    <i data-tooltip-target="tooltip-amount" data-tooltip-placement="top" type="button"
                                        class="fa-solid fa-circle-info text-xs text-red-500"></i>

                                    <div id="tooltip-amount" role="tooltip"
                                        class="shadow-xs tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 transition-opacity duration-300 ">
                                        Diskon dalam amount berati diskon langsung dalam harga, <br> misal harga 10000 dan
                                        diskon 9000 maka harga jual akan menjadi 9000
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                </label>
                                <label class="inline-flex items-center">
                                    <input id="discount_type_percentage" type="radio" name="discount_type"
                                        value="percentage" class="text-red-600 focus:ring-red-500">
                                    <span class="mx-2">Percentage</span>
                                    <i data-tooltip-target="tooltip-percentage" data-tooltip-placement="top"
                                        type="button" class="fa-solid fa-circle-info text-xs text-red-500"></i>

                                    <div id="tooltip-percentage" role="tooltip"
                                        class="shadow-xs tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 transition-opacity duration-300 ">
                                        Diskon dalam percentage berati diskon dalam persen, <br> misal harga 10000 dan
                                        diskon 10% maka harga jual akan menjadi 9000
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Diskon</label>
                            <input name="discount" id="discount" type="number"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-red-500 focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Status Barang</label>
                    <div class="flex gap-6">
                        <label class="inline-flex items-center">
                            <input type="radio" name="status" value="active" class="text-red-600 focus:ring-red-500"
                                id="status_active">
                            <span class="ml-2">Aktif</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="status" value="nonactive"
                                class="text-red-600 focus:ring-red-500" id="status_nonactive">
                            <span class="ml-2">Non Aktif</span>
                        </label>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3 border-t border-gray-200 pt-4 md:col-span-2">
                    <button id="close-edit-modal-footer" type="button"
                        class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="button" id="submit-edit-modal"
                        class="rounded-md bg-red-600 px-4 py-2 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Import Excel -->
    <div id="importModal-excel" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="mx-4 max-h-screen w-full max-w-2xl overflow-y-auto rounded-lg bg-white shadow-xl">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-gray-200 p-6">
                <h5 class="flex items-center text-xl font-semibold text-gray-800">
                    <i class="fas fa-upload mr-2 text-blue-600"></i>
                    Import Data Barang KI
                </h5>
                <button type="button" class="text-2xl text-gray-400 hover:text-gray-600" onclick="closeImportModal()">
                    <span>&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6">
                <!-- Alert Info -->
                <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle mr-3 mt-0.5 text-blue-600"></i>
                        <div>
                            <strong class="text-blue-800">Petunjuk:</strong>
                            <p class="mt-1 text-blue-700">Unggah file Excel untuk mengimpor data Barang KI. Pastikan format
                                file sesuai dengan template yang telah disediakan.</p>
                        </div>
                    </div>
                </div>

                <!-- Download Template Button -->
                <div class="mb-6 text-center">
                    <a href="#"
                        onclick="window.barangKiImporter?.handleDownloadTemplate(event, '{{ route('barang.ki.download-template') }}')"
                        class="inline-flex items-center rounded-lg bg-green-600 px-6 py-3 font-semibold text-white transition-colors hover:bg-green-700">
                        <i class="fas fa-download mr-2"></i>
                        Download Template Excel
                    </a>
                </div>

                <hr class="mb-6 border-gray-200">

                <!-- Upload Form -->
                <form id="importForm" action="{{ route('barang.ki.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-6">
                        <label for="file" class="mb-2 block text-sm font-semibold text-gray-700">
                            <i class="fas fa-file-excel mr-1 text-green-600"></i>
                            Pilih File Excel
                        </label>

                        <div class="relative">
                            <input type="file"
                                class="block w-full cursor-pointer rounded-lg border border-gray-300 text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                                id="file" name="file" accept=".xlsx,.xls" required>
                        </div>

                        <p class="mt-2 flex items-center text-sm text-gray-600">
                            <i class="fas fa-exclamation-triangle mr-2 text-yellow-500"></i>
                            Format yang didukung: .xlsx atau .xls (maksimal 5MB)
                        </p>
                    </div>

                    <!-- Progress Container -->
                    <div id="progressContainer" class="mb-6 hidden">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Progress Import</span>
                            <span id="progressText" class="text-sm text-gray-500">0%</span>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-gray-200">
                            <div id="importProgress"
                                class="relative h-2.5 overflow-hidden rounded-full bg-blue-600 transition-all duration-300"
                                style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                <!-- Animated stripes -->
                                <div
                                    class="absolute inset-0 animate-pulse bg-gradient-to-r from-transparent via-white to-transparent opacity-20">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feedback Area -->
                    <div id="importFeedback" class="mb-4 hidden"></div>

                </form>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end space-x-3 rounded-b-lg border-t border-gray-200 bg-gray-50 p-6">
                <button type="button"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 font-medium text-gray-700 transition-colors hover:bg-gray-50"
                    onclick="closeImportModal()">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </button>
                <button type="submit" form="importForm" id="importButton"
                    class="rounded-lg bg-blue-600 px-6 py-2 font-medium text-white transition-colors hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50">
                    <i class="fas fa-upload mr-2"></i>
                    <span>Import Data</span>
                </button>
            </div>
        </div>
    </div>


    <div id="loadingOverlay"
        class="z-60 fixed inset-0 hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50">
        <div class="flex min-h-screen items-center justify-center">
            <div class="rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-center space-x-3">
                    <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-red-600"></div>
                    <span class="font-medium text-gray-700">Memproses...</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}

    <!-- Scanner Manager Script -->
    <script src="{{ asset('js/datatable-scanner-manager.js') }}"></script>
    <script src="{{ asset('js/validasiMinMax.js') }}"></script>
    <script src="{{ asset('js/barang/customSearch.js') }}"></script>
    <script src="{{ asset('js/barang/barangkiDatatable.js') }}"></script>
    <script src="{{ asset('js/barang/barangkiImport.js') }}"></script>
    <script>
        $(function() {
            let table = $('#barang-table-ki').DataTable();
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

            // Initialize other managers
            setTimeout(() => {
                new DataTableManager();
                new FormDetailManager();
            }, 100);

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

        function handleExport(tableId, format) {
            const exporter = new ExportData('barang/ki', format);
            exporter.export();
        }
    </script>
@endpush
