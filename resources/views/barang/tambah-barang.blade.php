@extends('layouts.admin')
@section('page_title', 'Tambah Data Barang')
@section('nav_title', 'Tambah Data Barang')
@push('styles')
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
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

        .dropdown-search {
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
@endpush
@section('content')
    <div class="mx-auto py-6">
        <form id="barangForm" class="container mx-auto space-y-8" enctype="multipart/form-data">
            @csrf
            <div class="mb-6 flex items-center">
                <div class="mr-4 rounded-full bg-red-600">
                    <i class="fas fa-info-circle p-3 text-xl text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-red-800">Informasi Dasar</h2>
            </div>

            <div class="animate-fade-in rounded-2xl border-gray-200 bg-white p-8 shadow-2xl">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block font-semibold">Nama Barang <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" required
                            class="w-full rounded-lg border-2 border-gray-200 px-4 py-3 transition-colors focus:border-red-500 focus:outline-none focus:ring-red-500">
                    </div>

                    <div>
                        <label class="mb-2 block font-semibold">SKU</label>
                        <input type="text" id="sku" name="sku" readonly
                            class="w-full rounded-lg border-2 border-gray-200 bg-gray-100 px-4 py-3 text-gray-600 focus:ring-red-500">
                        <small class="text-sm text-red-500">SKU akan digenerate otomatis</small>
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-2 block font-semibold">Deskripsi</label>
                        <textarea name="description" rows="4"
                            class="w-full rounded-lg border-2 border-gray-200 px-4 py-3 transition-colors focus:border-red-500 focus:outline-none focus:ring-red-500"
                            placeholder="Masukkan deskripsi barang..."></textarea>
                    </div>
                </div>
            </div>

            <div class="mb-6 flex items-center">
                <div class="mr-4 rounded-full bg-red-600">
                    <i class="fas fa-tags p-3 text-xl text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-red-800">Informasi Kategori</h2>
            </div>

            <!-- Section 2: Kategori & Brand -->
            <div class="animate-fade-in rounded-2xl border-gray-200 bg-white p-8 shadow-2xl">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <!-- Subcategory Dropdown -->
                    <div class="relative">
                        <label class="mb-2 block font-semibold text-gray-700">Subkategori <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <select id="subcategory" name="subcategory" required
                                data-hs-select='{
                                "hasSearch": true,
                                "isSearchDirectMatch": false,
                                "searchPlaceholder": "Search...",
                                "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-red-500 focus:ring-red-500 before:absolute before:inset-0 before:z-1 py-1.5 sm:py-2 px-3",
                                "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0",
                                "placeholder": "Select Subcategory...",
                                "toggleTag": "<button type=\"button\" aria-expanded=\"false\"></button>",
                                "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-hidden focus:ring-2 focus:ring-red-500",
                                "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300",
                                "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-hidden focus:bg-gray-100",
                                "optionTemplate": "<div class=\"flex items-center\"><div class=\"truncate\"><div class=\"hs-selected:text-red-600 hs-selected:font-semibold text-sm text-gray-800\" data-title></div></div><div class=\"ms-auto\"><span class=\"block hs-selected:hidden\" data-icon></span><div class=\"hidden hs-selected:block\"><i class=\"fas fa-check text-red-600 text-xs\"></i></div></div>",
                                "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><i class=\"fas fa-chevron-down text-gray-500 text-xs\"></i></div>"
                            }'
                                class="hidden">
                                <option value="">Choose</option>
                                @foreach ($subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}"
                                        data-hs-select-option='{
                                    "icon": "<div class=\"shrink-0 size-5 text-xs text-gray-500\">{{ round($subcategory->margin) }}%</div>"}'>
                                        {{ $subcategory->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Brand Dropdown -->
                    <div class="relative">
                        <label class="mb-2 block font-semibold text-gray-700">Brand <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <select id="brand" name="brand" required
                                data-hs-select='{
                            "hasSearch": true,
                            "isSearchDirectMatch": false,
                            "searchPlaceholder": "Search...",
                            "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-red-500 focus:ring-red-500 before:absolute before:inset-0 before:z-1 py-1.5 sm:py-2 px-3",
                            "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0",
                            "placeholder": "Select Brand...",
                            "toggleTag": "<button type=\"button\" aria-expanded=\"false\"></button>",
                            "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-hidden focus:ring-2 focus:ring-red-500",
                            "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300",
                            "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-hidden focus:bg-gray-100",
                            "optionTemplate": "<div class=\"flex items-center\"><div><div class=\"hs-selected:text-red-600 hs-selected:font-semibold text-sm text-gray-800\" data-title></div></div><div class=\"ms-auto\"><span class=\"hidden hs-selected:block\"><i class=\"fas fa-check text-red-600 text-xs\"></i></span></div></div>",
                            "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><i class=\"fas fa-chevron-down text-gray-500 text-xs\"></i></div>"
                            }'
                                class="hidden">
                                <option value="">Choose</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Type Dropdown -->
                    <div class="relative">
                        <label id="kebutuhanLabel" class="mb-2 block font-semibold text-gray-700">Kebutuhan <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <select id="kebutuhan" name="kebutuhan" required
                                data-hs-select='{
                            "hasSearch": true,
                            "isSearchDirectMatch": false,
                            "searchPlaceholder": "Search...",
                            "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-red-500 focus:ring-red-500 before:absolute before:inset-0 before:z-1 py-1.5 sm:py-2 px-3",
                            "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0",
                            "placeholder": "Pilih Kebutuhan...",
                            "toggleTag": "<button type=\"button\" aria-expanded=\"false\"></button>",
                            "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-hidden focus:ring-2 focus:ring-red-500",
                            "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300",
                            "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-hidden focus:bg-gray-100",
                            "optionTemplate": "<div class=\"flex items-center\"><div><div class=\"hs-selected:text-red-600 hs-selected:font-semibold text-sm text-gray-800\" data-title></div></div><div class=\"ms-auto\"><span class=\"hidden hs-selected:block\"><i class=\"fas fa-check text-red-600 text-xs\"></i></span></div></div>",
                            "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><i class=\"fas fa-chevron-down text-gray-500 text-xs\"></i></div>"
                            }'
                                class="hidden">
                                <option value="">Choose</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6 flex items-center">
                <div class="mr-4 rounded-full bg-red-600">
                    <i class="fas fa-calendar-times p-3 text-xl text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-red-800">Pengaturan Expiry</h2>
            </div>
            <!-- Section 3: Pengaturan Expiry -->
            <div class="animate-fade-in rounded-2xl border-gray-200 bg-white p-8 shadow-2xl">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div>
                        <div class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2">
                            <label class="mb-2 block font-semibold text-gray-700">Early Expiry (Hari) <span
                                    class="text-red-500">*</span></label>
                            <div class="flex w-full items-center justify-between gap-x-3" data-hs-input-number="">
                                <div>
                                    <input id="early_expiry_days" name="early_expiry_days"
                                        class="border-0 bg-transparent p-0 text-gray-800 focus:ring-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                        style="-moz-appearance: textfield;" type="text"
                                        aria-roledescription="Number field" value="1" data-hs-input-number-input="">
                                </div>
                                <div class="flex items-center justify-end gap-x-1.5">
                                    <button type="button"
                                        class="shadow-2xs focus:outline-hidden inline-flex size-6 items-center justify-center gap-x-2 rounded-full border border-gray-200 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50 focus:bg-gray-50 disabled:pointer-events-none disabled:opacity-50"
                                        tabindex="-1" aria-label="Decrease" data-hs-input-number-decrement="">
                                        <svg class="size-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14"></path>
                                        </svg>
                                    </button>
                                    <button type="button"
                                        class="shadow-2xs focus:outline-hidden inline-flex size-6 items-center justify-center gap-x-2 rounded-full border border-gray-200 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50 focus:bg-gray-50 disabled:pointer-events-none disabled:opacity-50"
                                        tabindex="-1" aria-label="Increase" data-hs-input-number-increment="">
                                        <svg class="size-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14"></path>
                                            <path d="M12 5v14"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2">
                            <label id="midExpiryDaysLabel" class="mb-2 block font-semibold text-gray-700">Mid Expiry
                                (Hari) <span class="text-red-500">*</span></label>
                            <div class="flex w-full items-center justify-between gap-x-3" data-hs-input-number="">
                                <div>
                                    <input id="mid_expiry_days" name="mid_expiry_days"
                                        class="border-0 bg-transparent p-0 text-gray-800 focus:ring-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                        style="-moz-appearance: textfield;" type="text"
                                        aria-roledescription="Number field" value="1" data-hs-input-number-input="">
                                </div>
                                <div class="flex items-center justify-end gap-x-1.5">
                                    <button type="button"
                                        class="shadow-2xs focus:outline-hidden inline-flex size-6 items-center justify-center gap-x-2 rounded-full border border-gray-200 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50 focus:bg-gray-50 disabled:pointer-events-none disabled:opacity-50"
                                        tabindex="-1" aria-label="Decrease" data-hs-input-number-decrement="">
                                        <svg class="size-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14"></path>
                                        </svg>
                                    </button>
                                    <button type="button"
                                        class="shadow-2xs focus:outline-hidden inline-flex size-6 items-center justify-center gap-x-2 rounded-full border border-gray-200 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50 focus:bg-gray-50 disabled:pointer-events-none disabled:opacity-50"
                                        tabindex="-1" aria-label="Increase" data-hs-input-number-increment="">
                                        <svg class="size-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14"></path>
                                            <path d="M12 5v14"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2">
                            <label id="lateExpiryDaysLabel" class="mb-2 block font-semibold text-gray-700">Late Expiry
                                (Hari) <span class="text-red-500">*</span></label>
                            <div class="flex w-full items-center justify-between gap-x-3" data-hs-input-number="">
                                <div>
                                    <input id="late_expiry_days" name="late_expiry_days"
                                        class="border-0 bg-transparent p-0 text-gray-800 focus:ring-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                        style="-moz-appearance: textfield;" type="text"
                                        aria-roledescription="Number field" value="1" data-hs-input-number-input="">
                                </div>
                                <div class="flex items-center justify-end gap-x-1.5">
                                    <button type="button"
                                        class="shadow-2xs focus:outline-hidden inline-flex size-6 items-center justify-center gap-x-2 rounded-full border border-gray-200 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50 focus:bg-gray-50 disabled:pointer-events-none disabled:opacity-50"
                                        tabindex="-1" aria-label="Decrease" data-hs-input-number-decrement="">
                                        <svg class="size-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14"></path>
                                        </svg>
                                    </button>
                                    <button type="button"
                                        class="shadow-2xs focus:outline-hidden inline-flex size-6 items-center justify-center gap-x-2 rounded-full border border-gray-200 bg-white text-sm font-medium text-gray-800 hover:bg-gray-50 focus:bg-gray-50 disabled:pointer-events-none disabled:opacity-50"
                                        tabindex="-1" aria-label="Increase" data-hs-input-number-increment="">
                                        <svg class="size-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14"></path>
                                            <path d="M12 5v14"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center">
                <div class="mr-4 rounded-full bg-red-600">
                    <i class="fas fa-exchange-alt p-3 text-xl text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-red-800">Konversi Satuan</h2>
            </div>
            <!-- Section 4: Konversi Satuan -->
            <div class="animate-fade-in rounded-2xl border-gray-200 bg-white p-8 shadow-2xl">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <label for="select_tipe_unit" class="mr-2 text-gray-700">Pilih Tipe Barang</label>
                        <select id="select_tipe_unit"
                            class="mt-2 block w-full rounded-lg border-gray-200 bg-white px-4 py-2 capitalize text-gray-800 transition-colors hover:bg-gray-50 focus:border-red-500 focus:bg-gray-50 focus:ring-red-500">
                            <option selected disabled value="">Pilih Tipe</option>
                            @foreach ($tipe_unit as $item)
                                <option value="{{ $item }}" class="capitalize">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" id="addConversion"
                        class="hidden cursor-pointer rounded-lg bg-red-600 px-4 py-2 text-white transition-colors hover:bg-red-700">
                        <i class="fas fa-plus mr-2"></i>Tambah Konversi
                    </button>
                </div>

                <div id="conversionsContainer" class="hidden">
                    <!-- Template tersembunyi - JANGAN HAPUS -->
                    <div id="conversion-template" class="conversion-template hidden">
                        <div class="conversion-row relative mb-4 rounded-lg border-2 border-gray-200 bg-white p-6">
                            <div class="grid grid-cols-1 items-end gap-4 md:grid-cols-3">
                                <!-- From Satuan -->
                                <div class="relative">
                                    <label class="mb-2 block font-semibold text-gray-700">Dari Satuan <span
                                            class="text-red-500">*</span></label>
                                    <select name="from_unit[]"
                                        class="from-unit-select w-full rounded-lg border-2 border-gray-200 px-4 py-3 transition-colors focus:border-red-500 focus:outline-none focus:ring-red-500">
                                        <option value="">Pilih Satuan</option>

                                    </select>
                                </div>

                                <!-- To Satuan -->
                                <div class="relative">
                                    <label class="mb-2 block font-semibold text-gray-700">Ke Satuan <span
                                            class="text-red-500">*</span></label>
                                    <select name="to_unit[]"
                                        class="to-unit-select w-full rounded-lg border-2 border-gray-200 px-4 py-3 transition-colors focus:border-red-500 focus:outline-none focus:ring-red-500">
                                        <option value="">Pilih Satuan</option>
                                    </select>
                                </div>

                                <!-- Faktor Konversi -->
                                <div class="relative">
                                    <label class="mb-2 block font-semibold text-gray-700">Faktor Konversi <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" name="conversion_factor[]" step="0.001" min="0.001"
                                        class="conversion-factor-input w-full rounded-lg border-2 border-gray-200 px-4 py-3 transition-colors focus:border-red-500 focus:outline-none focus:ring-red-500"
                                        placeholder="Contoh: 1000">
                                </div>
                            </div>

                            <!-- Delete Button -->
                            <button type="button" tooltip title="Hapus Konversi"
                                class="delete-conversion absolute right-2 top-2 cursor-pointer text-red-500 transition-colors hover:scale-110 hover:text-red-600">
                                <i class="fas fa-trash text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Container untuk konversi aktual -->
                    <div id="actual-conversions">
                        <!-- Konversi pertama (required) -->
                        <div class="conversion-row relative mb-4 rounded-lg border-2 border-gray-200 bg-white p-6"
                            data-conversion-index="0">
                            <div class="grid grid-cols-1 items-end gap-4 md:grid-cols-3">
                                <!-- From Satuan -->
                                <div class="relative">
                                    <label class="mb-2 block font-semibold text-gray-700">Dari Satuan <span
                                            class="text-red-500">*</span></label>
                                    <select name="from_unit[]"
                                        class="from-unit-select w-full rounded-lg border-2 border-gray-200 px-4 py-3 transition-colors focus:border-red-500 focus:outline-none focus:ring-red-500">
                                        <option value="">Pilih Satuan</option>
                                    </select>
                                </div>

                                <!-- To Satuan -->
                                <div class="relative">
                                    <label class="mb-2 block font-semibold text-gray-700">Ke Satuan <span
                                            class="text-red-500">*</span></label>
                                    <select name="to_unit[]"
                                        class="to-unit-select w-full rounded-lg border-2 border-gray-200 px-4 py-3 transition-colors focus:border-red-500 focus:outline-none focus:ring-red-500">
                                        <option value="">Pilih Satuan</option>
                                    </select>
                                </div>

                                <!-- Faktor Konversi -->
                                <div class="relative">
                                    <label class="mb-2 block font-semibold text-gray-700">Faktor Konversi <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" name="conversion_factor[]" step="0.001" min="0.001"
                                        class="arrow-hide w-full rounded-lg border-2 border-gray-200 px-4 py-3 transition-colors focus:border-red-500 focus:outline-none focus:ring-red-500"
                                        placeholder="Contoh: 1000">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-2 rounded-lg bg-red-100 p-3 text-sm text-red-600 md:grid-cols-4">
                        <div class="col-span-2">
                            <i class="fas fa-info-circle mr-2"></i>
                            Minimal harus memasukan conversion lengkap contoh
                        </div>
                        <div class="col-span-1">
                            <div class="flex items-center">
                                <div class="mr-2">
                                    <i class="fas fa-weight text-sm"></i>
                                </div>
                                <div>
                                    <p>1 Kg = 1000 Gram</p>
                                </div>
                            </div>
                            Maka Anda juga harus memasukan
                            <div class="flex items-center">
                                <div class="mr-2">
                                    <i class="fas fa-weight text-sm"></i>
                                </div>
                                <div>
                                    <p>1000 Gram = 1 Kg</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-1">
                            <div class="flex items-center">
                                <div class="mr-2">
                                    <i class="fas fa-weight text-sm"></i>
                                </div>
                                <div>
                                    <p>1 Liter = 1000 Mililiter</p>
                                </div>
                            </div>
                            Maka Anda juga harus memasukan
                            <div class="flex items-center">
                                <div class="mr-2">
                                    <i class="fas fa-weight text-sm"></i>
                                </div>
                                <div>
                                    <p>1000 Mililiter = 1 Liter</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6 flex items-center">
                <div class="mr-4 rounded-full bg-red-600">
                    <i class="fas fa-image p-3 text-xl text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-red-800">Upload Gambar Produk</h2>
            </div>
            <!-- Image Upload Section -->
            <div class="animate-fade-in rounded-2xl border-gray-200 bg-white p-8 shadow-2xl">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <!-- Main Image Upload -->
                    <div>
                        <label id="mainImageLabel" class="mb-2 block font-semibold text-gray-700">Gambar Utama <span
                                class="text-red-500">*</span></label>
                        <div class="upload-area relative">
                            <input id="mainImage" name="main_image" required type="file" accept="image/*"
                                class="hidden">
                            <div id="mainImageDropzone"
                                class="flex cursor-pointer justify-center rounded-xl border-2 border-dashed border-red-300 bg-red-50 p-12 transition-colors hover:border-red-500 hover:bg-red-100">
                                <div class="text-center">
                                    <div id="mainImageIcon" class="mx-auto mb-4">
                                        <svg class="mx-auto w-16 text-red-400" width="70" height="46"
                                            viewBox="0 0 70 46" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="17.0656" y="1.62305" width="35.8689" height="42.7541" rx="5"
                                                fill="currentColor" stroke="currentColor" stroke-width="2"
                                                class="fill-white stroke-red-400"></rect>
                                            <path
                                                d="M47.9344 44.3772H22.0655C19.3041 44.3772 17.0656 42.1386 17.0656 39.3772L17.0656 35.9161L29.4724 22.7682L38.9825 33.7121C39.7832 34.6335 41.2154 34.629 42.0102 33.7025L47.2456 27.5996L52.9344 33.7209V39.3772C52.9344 42.1386 50.6958 44.3772 47.9344 44.3772Z"
                                                stroke="currentColor" stroke-width="2" class="stroke-red-400"></path>
                                            <circle cx="39.5902" cy="14.9672" r="4.16393" stroke="currentColor"
                                                stroke-width="2" class="stroke-red-400"></circle>
                                        </svg>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium text-gray-800">Klik untuk upload</span>
                                        <span class="text-gray-500"> atau drag & drop gambar di sini</span>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-400">
                                        PNG, JPG, JPEG hingga 5MB
                                    </p>
                                </div>
                            </div>
                            <!-- Main Image Preview -->
                            <div id="mainImagePreview" class="mt-4 hidden">
                                <div class="relative inline-block rounded-lg border-2 border-gray-200 p-2">
                                    <img id="mainImageDisplay" src="" alt="Preview"
                                        class="image-preview h-32 w-32 rounded-lg object-cover">
                                    <button type="button" id="removeMainImage"
                                        class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                    <div class="mt-2 text-center">
                                        <span class="text-xs text-gray-600" id="mainImageName"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Progress -->
                        <div id="uploadProgress" class="hidden pt-3">
                            <div class="mb-2 flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Memproses gambar...</span>
                                <span class="text-sm text-gray-500" id="progressText">0%</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-gray-200">
                                <div id="progressBar" class="h-full bg-red-600 transition-all duration-300 ease-out"
                                    style="width: 0%"></div>
                            </div>
                        </div>

                    </div>

                    <!-- Additional Images Upload -->
                    <div>
                        <label class="mb-2 block font-semibold text-gray-700">Gambar Tambahan <span
                                class="text-gray-500">(Opsional, maks 4 gambar)</span></label>
                        <div class="upload-area relative">
                            <input type="file" id="additionalImages" name="additional_images[]" accept="image/*"
                                multiple class="hidden">
                            <div id="additionalImagesDropzone"
                                class="flex cursor-pointer justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-8 transition-colors hover:border-gray-500 hover:bg-gray-100">
                                <div class="text-center">
                                    <div class="mx-auto mb-3">
                                        <i class="fas fa-images text-3xl text-gray-400"></i>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium text-gray-800">Upload gambar tambahan</span>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-400">
                                        Pilih hingga 4 gambar (PNG, JPG, JPEG)
                                    </p>
                                </div>
                            </div>
                            <!-- Additional Images Preview -->
                            <div id="additionalImagesPreview" class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4"></div>
                        </div>
                    </div>

                    <!-- Info Alert -->
                    <div class="max-h-auto">
                        <div class="flex rounded-lg bg-red-50 p-4">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Tips Upload Gambar:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc space-y-1 pl-5">
                                        <li>Gunakan gambar berkualitas tinggi untuk hasil terbaik</li>
                                        <li>Rasio aspek 1:1 (persegi) direkomendasikan untuk gambar utama</li>
                                        <li>Format yang didukung: PNG, JPG, JPEG</li>
                                        <li>Ukuran maksimal per file: 5MB</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Submit Buttons -->
            <div class="animate-fade-in flex justify-end space-x-4">
                <button type="button" id="cancelBtn"
                    class="cursor-pointer rounded-lg bg-gray-500 px-8 py-4 font-semibold text-white transition-colors hover:bg-gray-600">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
                <button id="submitBtn" type="button"
                    class="cursor-pointer rounded-lg bg-red-600 px-8 py-4 font-semibold text-white shadow-lg transition-colors hover:bg-red-700">
                    <i class="fas fa-save mr-2"></i>Simpan Barang
                </button>
            </div>
        </form>
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
                        <!-- Buttons -->
                        <div id="actionButtons" class="flex justify-center space-x-4">
                            <button id="confirmBtn" onclick="confirmAction()"
                                class="cursor-pointer rounded-lg bg-red-500 px-6 py-2 font-bold text-white transition duration-200 hover:bg-red-600">
                                Delete
                            </button>
                            <button id="closeMessageModalBtn" onclick="closeModal()"
                                class="cursor-pointer rounded-lg bg-gray-300 px-6 py-2 font-bold text-gray-800 transition duration-200 hover:bg-gray-400">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="loadingOverlay" class="z-60 fixed inset-0 hidden bg-black/50 backdrop-blur-sm">
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
    <script>
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const modalIcon = document.getElementById('modalIcon');
        const actionButtons = document.getElementById('actionButtons');
        const confirmButton = document.getElementById('confirmBtn');
        const cancelButton = document.getElementById('closeMessageModalBtn');
        const messageModal = document.getElementById('message-modal');
        const messageModalContainer = document.getElementById('message-modal-container');
        const addconvbutton = document.getElementById('addConversion');

        const selectTipeUnit = document.getElementById('select_tipe_unit');
        const conversionsContainer = document.getElementById('conversionsContainer');
        const actualConversion = document.getElementById('actual-conversions');
        const conversionTemplate = document.getElementById('conversion-template');


        class FormManager {
            constructor() {
                this.skuTimeout = null;
                this.conversionCount = 1;
                this.select_tipe_unit = null;
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.initializeValidation();
                this.initializeImageUploader();
            }

            setupEventListeners() {
                // SKU Auto-generation
                this.setupSKUGeneration();

                // Form submission
                this.setupFormSubmission();

                // Conversion management
                this.setupConversionManager();

                // Modal handlers
                this.setupModalHandlers();

                // Select tipe unit
                this.setupSelectTipeUnit();

            }

            setupSKUGeneration() {
                const nameInput = document.querySelector('#name');
                if (nameInput) {
                    nameInput.addEventListener('input', (e) => {
                        const name = e.target.value;

                        if (this.skuTimeout) {
                            clearTimeout(this.skuTimeout);
                        }

                        this.skuTimeout = setTimeout(() => {
                            if (name.trim()) {
                                this.generateSKU(name);
                            }
                        }, 1000);
                    });
                }
            }

            async generateSKU(name) {
                try {
                    const response = await $.ajax({
                        url: '/barang/getsku',
                        type: 'GET',
                        data: {
                            name
                        }
                    });

                    const skuInput = document.querySelector('#sku');
                    if (skuInput && response.sku) {
                        skuInput.value = response.sku;
                    }
                } catch (error) {
                    console.error('Error generating SKU:', error);
                }
            }

            setupFormSubmission() {
                const submitBtn = document.getElementById('submitBtn');
                const cancelBtn = document.getElementById('cancelBtn');

                if (submitBtn) {
                    submitBtn.addEventListener('click', () => this.handleFormSubmit());
                }

                if (cancelBtn) {
                    cancelBtn.addEventListener('click', () => this.resetForm());
                }
            }

            async handleFormSubmit() {
                try {
                    this.setButtonState(true);

                    if (!this.validateForm()) {
                        this.scrollToTop();
                        return;
                    }

                    this.showLoading();

                    const formData = $('#barangForm').serialize();
                    const response = await $.ajax({
                        url: "/barang/store",
                        method: "POST",
                        data: formData
                    });

                    this.showMessage(response.message, 'success');
                    this.resetForm(); // Reset form after successful submission

                } catch (xhr) {
                    this.handleSubmitError(xhr);
                } finally {
                    this.hideLoading();
                    this.setButtonState(false);
                }
            }

            handleSubmitError(xhr) {
                const errors = xhr.responseJSON?.errors;
                const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data';

                if (errors && errors.length > 0) {
                    const errorMessages = errors.map(error => error.message).join('<br>');
                    this.showMessage(errorMessages, 'error', 0);
                } else {
                    this.showMessage(message, 'error');
                }
            }

            setButtonState(loading) {
                const submitBtn = document.getElementById('submitBtn');
                const cancelBtn = document.getElementById('cancelBtn');

                if (submitBtn && cancelBtn) {
                    submitBtn.disabled = loading;
                    cancelBtn.disabled = loading;
                    submitBtn.innerHTML = loading ?
                        '<i class="fas fa-spinner animate-spin mr-2"></i>Menyimpan...' :
                        '<i class="fas fa-save mr-2"></i>Simpan Barang';
                }
            }

            setupConversionManager() {
                const addBtn = document.getElementById('addConversion');
                if (addBtn) {
                    addBtn.addEventListener('click', () => this.addConversion());
                }

                // Event delegation for delete buttons
                document.addEventListener('click', (e) => {
                    if (e.target.closest('.delete-conversion')) {
                        this.removeConversion(e.target.closest('.conversion-row'));
                    }
                });
            }

            addConversion() {
                const template = document.getElementById('conversion-template');
                const container = document.getElementById('actual-conversions');

                if (!template || !container) return;

                const newConversion = template.cloneNode(true);
                newConversion.classList.remove('conversion-template', 'hidden');
                newConversion.classList.add('conversion-row');
                newConversion.setAttribute('data-conversion-index', this.conversionCount);
                newConversion.id = `conversion-${this.conversionCount}`;

                container.appendChild(newConversion);
                this.conversionCount++;
                this.updateConversionIndices();
            }

            removeConversion(conversionRow) {
                const container = document.getElementById('actual-conversions');

                if (container.children.length > 1) {
                    conversionRow.remove();
                    this.conversionCount--;
                    this.updateConversionIndices();
                } else {
                    this.showMessage('Minimal harus ada 1 konversi satuan!', 'error');
                }
            }

            updateConversionIndices() {
                const conversions = document.querySelectorAll('#actual-conversions .conversion-row');
                conversions.forEach((conversion, index) => {
                    conversion.setAttribute('data-conversion-index', index);
                });
            }

            setupModalHandlers() {
                const closeBtn = document.getElementById('closeMessageModalBtn');
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => this.closeModal());
                }
            }

            // Validation Methods
            initializeValidation() {
                // Real-time validation
                document.addEventListener('change', (e) => {
                    if (e.target.matches('.from-unit, .to-unit, .conversion-factor')) {
                        setTimeout(() => {
                            this.clearAllErrors();
                            this.validateConversions();
                        }, 100);
                    }
                });
            }

            validateForm() {
                const requiredFields = [{
                        id: 'name',
                        name: 'Nama Barang'
                    },
                    {
                        id: 'subcategory',
                        name: 'Subkategori'
                    },
                    {
                        id: 'brand',
                        name: 'Brand'
                    },
                    {
                        id: 'kebutuhan',
                        name: 'Kebutuhan'
                    },
                    {
                        id: 'mainImage',
                        name: 'Gambar Utama'
                    }
                ];

                let isValid = true;
                const missingFields = [];

                // Validate required fields
                requiredFields.forEach(field => {
                    const element = document.getElementById(field.id);
                    if (!element || !element.value || element.value.trim() === '') {
                        isValid = false;
                        missingFields.push(field.name);
                        if (element) {
                            element.classList.add('border-red-500');
                        }
                    } else if (element) {
                        element.classList.remove('border-red-500');
                    }
                });

                // Validate conversions
                const conversionValidation = this.validateConversions();
                if (!conversionValidation.isValid) {
                    isValid = false;
                    missingFields.push(...conversionValidation.errors);
                }

                if (!isValid) {
                    this.showMessage(missingFields.join(', '), 'error', 4000);
                }

                return isValid;
            }

            validateConversions() {
                const rows = document.querySelectorAll('#actual-conversions .conversion-row');
                const errors = [];
                const seenCombinations = [];
                let hasValidConversion = false;

                rows.forEach((row, index) => {
                    const fromUnit = row.querySelector('select[name="from_unit[]"]')?.value;
                    const toUnit = row.querySelector('select[name="to_unit[]"]')?.value;
                    const factor = row.querySelector('input[name="conversion_factor[]"]')?.value;

                    this.clearRowErrors(row);

                    // Skip empty rows
                    if (!fromUnit && !toUnit && !factor) {
                        return;
                    }

                    // Check if all fields are filled
                    if (!fromUnit || !toUnit || !factor) {
                        this.addRowError(row, `Semua field harus diisi pada baris ${index}`);
                        errors.push(`Baris ${index}: Semua field harus diisi`);
                        return;
                    }

                    // Check if from_unit and to_unit are the same
                    if (index > 0 && fromUnit === toUnit) {
                        this.addRowError(row, 'Unit asal dan tujuan tidak boleh sama');
                        errors.push(`Baris ${index}: Unit asal dan tujuan tidak boleh sama`);
                        return;
                    }

                    // Check conversion factor validity
                    const factorNum = parseFloat(factor);
                    if (isNaN(factorNum) || factorNum <= 0) {
                        this.addRowError(row, 'Faktor konversi harus berupa angka positif');
                        errors.push(`Baris ${index}: Faktor konversi harus berupa angka positif`);
                        return;
                    }

                    // Check for duplicates
                    const forwardKey = `${fromUnit}-${toUnit}`;
                    const reverseKey = `${toUnit}-${fromUnit}`;


                    seenCombinations.push(forwardKey, reverseKey);
                    hasValidConversion = true;
                });

                if (!hasValidConversion) {
                    errors.push('Minimal satu unit conversion harus diisi dengan benar');
                }

                return {
                    isValid: errors.length === 0,
                    errors
                };
            }

            addRowError(row, message) {
                row.classList.add('error-row');

                let errorDiv = row.querySelector('.error-message');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message text-red-500 text-sm mt-1';
                    row.appendChild(errorDiv);
                }
                errorDiv.textContent = message;
            }

            clearRowErrors(row) {
                row.classList.remove('error-row');
                const errorDiv = row.querySelector('.error-message');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }

            clearAllErrors() {
                const rows = document.querySelectorAll('#actual-conversions .conversion-row');
                rows.forEach(row => this.clearRowErrors(row));

                const generalError = document.getElementById('generalError');
                if (generalError) {
                    generalError.remove();
                }
            }

            // UI Helper Methods
            showLoading() {
                const overlay = document.getElementById('loadingOverlay');
                if (overlay) {
                    overlay.classList.remove('hidden');
                }
            }

            hideLoading() {
                const overlay = document.getElementById('loadingOverlay');
                if (overlay) {
                    overlay.classList.add('hidden');
                }
            }

            showMessage(message, type, duration = 3000) {
                const modal = document.getElementById('message-modal');
                const title = document.getElementById('modalTitle');
                const messageEl = document.getElementById('modalMessage');
                const icon = document.getElementById('modalIcon');
                const actionButtons = document.getElementById('actionButtons');

                if (!modal || !title || !messageEl || !icon) return;

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                actionButtons.classList.add('hidden');

                title.textContent = type === 'success' ? 'Success' : 'Error';
                messageEl.textContent = message;

                const iconClass = type === 'success' ?
                    'fas fa-check-circle animate__animated animate__rotateIn text-6xl text-green-500' :
                    'fas fa-circle-exclamation animate__animated animate__rotateIn text-6xl text-red-500';

                icon.className = iconClass;

                if (duration > 0) {
                    setTimeout(() => this.closeModal(), duration);
                }
            }

            closeModal() {
                const modal = document.getElementById('message-modal');
                if (modal) {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                }
            }

            scrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            initializeImageUploader() {
                this.imageUploader = new ImageUploader();
            }

            // Form Reset Method
            resetForm() {
                const form = document.getElementById('barangForm');
                if (!form) return;

                // Reset all form inputs
                form.reset();

                // Clear SKU field specifically
                const skuInput = document.getElementById('sku');
                if (skuInput) {
                    skuInput.value = '';
                }

                // Reset conversion rows to only one
                this.resetConversions();

                // Reset image uploads
                this.resetImages();

                // Clear all validation errors
                this.clearAllErrors();
                this.clearFieldErrors();

                // Reset counter
                this.conversionCount = 1;
            }

            resetConversions() {
                const container = document.getElementById('actual-conversions');
                if (!container) return;

                // Remove all conversion rows except the first one
                const rows = container.querySelectorAll('.conversion-row');
                rows.forEach((row, index) => {
                    if (index > 0) {
                        row.remove();
                    } else {
                        // Reset the first row
                        const selects = row.querySelectorAll('select');
                        const inputs = row.querySelectorAll('input');

                        selects.forEach(select => select.selectedIndex = 0);
                        inputs.forEach(input => input.value = '');
                    }
                });

                this.updateConversionIndices();
            }


            setupSelectTipeUnit() {
                const selectTipeUnit = document.getElementById('select_tipe_unit');
                selectTipeUnit.addEventListener('change', () => {
                    this.select_tipe_unit = selectTipeUnit.value;
                    this.showLoading();

                    $.ajax({
                        url: "{{ route('barang.get-satuan-by-type') }}",
                        type: "GET",
                        data: {
                            type: this.select_tipe_unit
                        },
                        success: (response) => {
                            this.hideLoading();
                            if (response.satuan.length > 0) {
                                this.updateConversionSelects(response);
                                if (addconvbutton.classList.contains('hidden')) {
                                    addconvbutton.classList.remove('hidden');
                                    conversionsContainer.classList.remove('hidden');
                                }
                            }
                        },
                        error: (error) => {
                            this.hideLoading();
                            this.showMessage('Error fetching units: ' + error, 'error');
                        }
                    });
                });
            }

            updateConversionSelects(response) {
                const conversionTemplate = document.getElementById('conversion-template');
                const fromUnitSelects = conversionTemplate.querySelectorAll('.from-unit-select');
                const toUnitSelects = conversionTemplate.querySelectorAll('.to-unit-select');
                const container = document.getElementById('actual-conversions');
                const actionFromUnitSelects = container.querySelectorAll('.from-unit-select');
                const actionToUnitSelects = container.querySelectorAll('.to-unit-select');
                const clonedConversionTemplate = conversionTemplate.cloneNode(true);

                this.resetConversions();

                fromUnitSelects.forEach(select => {
                    select.innerHTML = '<option value="">Pilih Satuan</option>';
                    response.satuan.forEach(item => {
                        select.innerHTML +=
                            `<option value="${item.id}" data-type="${item.type}">${item.name}</option>`;
                    });
                });
                toUnitSelects.forEach(select => {
                    select.innerHTML = '<option value="">Pilih Satuan</option>';
                    response.satuan.forEach(item => {
                        select.innerHTML +=
                            `<option value="${item.id}" data-type="${item.type}">${item.name}</option>`;
                    });
                });

                actionFromUnitSelects.forEach(select => {
                    select.innerHTML = '<option value="">Pilih Satuan</option>';
                    response.satuan.forEach(item => {
                        select.innerHTML +=
                            `<option value="${item.id}" data-type="${item.type}">${item.name}</option>`;
                    });
                });
                actionToUnitSelects.forEach(select => {
                    select.innerHTML = '<option value="">Pilih Satuan</option>';
                    response.satuan.forEach(item => {
                        select.innerHTML +=
                            `<option value="${item.id}" data-type="${item.type}">${item.name}</option>`;
                    });
                });



            }


            resetImages() {
                // Reset main image
                const mainImageInput = document.getElementById('mainImage');
                const mainImagePreview = document.getElementById('mainImagePreview');
                const mainImageDropzone = document.getElementById('mainImageDropzone');

                if (mainImageInput) mainImageInput.value = '';
                if (mainImagePreview) mainImagePreview.classList.add('hidden');
                if (mainImageDropzone) mainImageDropzone.classList.remove('hidden');

                // Reset additional images
                const additionalImagesInput = document.getElementById('additionalImages');
                const additionalImagesPreview = document.getElementById('additionalImagesPreview');

                if (additionalImagesInput) additionalImagesInput.value = '';
                if (additionalImagesPreview) additionalImagesPreview.innerHTML = '';

                // Hide progress bar
                const uploadProgress = document.getElementById('uploadProgress');
                if (uploadProgress) uploadProgress.classList.add('hidden');
            }

            clearFieldErrors() {
                // Remove error borders from all inputs
                const inputs = document.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.classList.remove('border-red-500');
                });
            }
        }

        class ImageUploader {
            constructor() {
                this.maxAdditionalImages = 4;
                this.maxFileSize = 5 * 1024 * 1024; // 5MB
                this.allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                this.init();
            }

            init() {
                this.setupEventListeners();
            }

            setupEventListeners() {
                // Main image upload
                this.setupMainImageUpload();

                // Additional images upload
                this.setupAdditionalImagesUpload();
            }

            setupMainImageUpload() {
                const input = document.getElementById('mainImage');
                const dropzone = document.getElementById('mainImageDropzone');
                const removeBtn = document.getElementById('removeMainImage');

                if (dropzone) {
                    dropzone.addEventListener('click', () => input?.click());
                    this.setupDragAndDrop(dropzone, input);
                }

                if (input) {
                    input.addEventListener('change', (e) => this.handleMainImageUpload(e));
                }

                if (removeBtn) {
                    removeBtn.addEventListener('click', () => this.removeMainImage());
                }
            }

            setupAdditionalImagesUpload() {
                const input = document.getElementById('additionalImages');
                const dropzone = document.getElementById('additionalImagesDropzone');

                if (dropzone) {
                    dropzone.addEventListener('click', () => input?.click());
                    this.setupDragAndDrop(dropzone, input);
                }

                if (input) {
                    input.addEventListener('change', (e) => this.handleAdditionalImagesUpload(e));
                }
            }

            setupDragAndDrop(dropzone, input) {
                if (!dropzone || !input) return;

                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, this.preventDefaults, false);
                });

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, () => dropzone.classList.add('drag-over'), false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, () => dropzone.classList.remove('drag-over'), false);
                });

                dropzone.addEventListener('drop', (e) => {
                    const files = e.dataTransfer.files;
                    if (input.id === 'mainImage') {
                        this.handleMainImageUpload({
                            target: {
                                files: [files[0]]
                            }
                        });
                    } else {
                        this.handleAdditionalImagesUpload({
                            target: {
                                files
                            }
                        });
                    }
                }, false);
            }

            preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            handleMainImageUpload(event) {
                const file = event.target.files[0];
                if (!file) return;

                if (!this.validateFile(file)) {
                    event.target.value = '';
                    return;
                }

                this.showProgress();
                setTimeout(() => {
                    this.displayMainImage(file);
                    this.hideProgress();
                }, 800);
            }

            handleAdditionalImagesUpload(event) {
                const files = Array.from(event.target.files);
                const currentImages = document.querySelectorAll('#additionalImagesPreview .image-item').length;

                if (currentImages + files.length > this.maxAdditionalImages) {
                    this.showMessage(`Maksimal ${this.maxAdditionalImages} gambar tambahan diperbolehkan`, 'error');
                    return;
                }

                files.forEach(file => {
                    if (this.validateFile(file)) {
                        this.displayAdditionalImage(file);
                    }
                });
            }

            validateFile(file) {
                if (!this.allowedTypes.includes(file.type)) {
                    this.showMessage('Format file tidak didukung. Gunakan PNG, JPG, atau JPEG.', 'error');
                    return false;
                }

                if (file.size > this.maxFileSize) {
                    this.showMessage('Ukuran file terlalu besar. Maksimal 5MB.', 'error');
                    return false;
                }

                return true;
            }

            displayMainImage(file) {
                const preview = document.getElementById('mainImagePreview');
                const display = document.getElementById('mainImageDisplay');
                const nameSpan = document.getElementById('mainImageName');
                const dropzone = document.getElementById('mainImageDropzone');

                if (!preview || !display || !nameSpan || !dropzone) return;

                const reader = new FileReader();
                reader.onload = (e) => {
                    display.src = e.target.result;
                    nameSpan.textContent = file.name;
                    preview.classList.remove('hidden');
                    dropzone.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }

            displayAdditionalImage(file) {
                const preview = document.getElementById('additionalImagesPreview');
                if (!preview) return;

                const reader = new FileReader();
                reader.onload = (e) => {
                    const imageItem = this.createImageItem(e.target.result, file.name);
                    preview.appendChild(imageItem);
                };
                reader.readAsDataURL(file);
            }

            createImageItem(src, fileName) {
                const imageItem = document.createElement('div');
                imageItem.className = 'image-item relative';
                imageItem.innerHTML = `
            <div class="relative rounded-lg border-2 border-gray-200 p-2">
                <img src="${src}" alt="Preview" class="image-preview h-24 w-full rounded-lg object-cover">
                <button type="button" class="remove-additional-image absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600">
                    <i class="fas fa-times text-xs"></i>
                </button>
                <div class="mt-1 text-center">
                    <span class="text-xs text-gray-600 truncate block">${fileName}</span>
                </div>
            </div>
        `;

                const removeBtn = imageItem.querySelector('.remove-additional-image');
                if (removeBtn) {
                    removeBtn.addEventListener('click', () => imageItem.remove());
                }

                return imageItem;
            }

            removeMainImage() {
                const preview = document.getElementById('mainImagePreview');
                const dropzone = document.getElementById('mainImageDropzone');
                const input = document.getElementById('mainImage');

                if (preview) preview.classList.add('hidden');
                if (dropzone) dropzone.classList.remove('hidden');
                if (input) input.value = '';
            }

            showProgress() {
                const progressContainer = document.getElementById('uploadProgress');
                const progressBar = document.getElementById('progressBar');
                const progressText = document.getElementById('progressText');

                if (!progressContainer || !progressBar || !progressText) return;

                progressContainer.classList.remove('hidden');

                let progress = 0;
                const interval = setInterval(() => {
                    progress += Math.random() * 30;
                    if (progress > 100) progress = 100;

                    progressBar.style.width = progress + '%';
                    progressText.textContent = Math.round(progress) + '%';

                    if (progress >= 100) {
                        clearInterval(interval);
                    }
                }, 100);
            }

            hideProgress() {
                setTimeout(() => {
                    const progressContainer = document.getElementById('uploadProgress');
                    if (progressContainer) {
                        progressContainer.classList.add('hidden');
                    }
                }, 500);
            }

            showMessage(message, type) {
                // Use the main form manager's showMessage method
                if (window.formManager) {
                    window.formManager.showMessage(message, type);
                }
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            window.formManager = new FormManager();
        });
    </script>
@endpush
