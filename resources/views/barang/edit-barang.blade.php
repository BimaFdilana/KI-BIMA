@extends('layouts.admin')
@section('page_title', 'Edit ' . $barang->name)
@section('nav_title', 'Edit ' . $barang->name)
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
        <form id="barangForm" class="container mx-auto space-y-8" action="{{ route('barang.update', $barang->id) }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
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
                        <input type="text" id="name" value="{{ old('name', $barang->name) }}" name="name"
                            required
                            class="w-full rounded-lg border-2 border-gray-200 px-4 py-3 transition-colors focus:border-red-500 focus:outline-none focus:ring-red-500">
                    </div>

                    <div>
                        <label class="mb-2 block font-semibold">Status</label>
                        <select id="status" name="status"
                            class="w-full rounded-lg border-2 border-gray-200 px-4 py-3 text-gray-600 focus:ring-red-500">
                            <option value="active" {{ $barang->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="nonactive" {{ $barang->status == 'nonactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-2 block font-semibold">Deskripsi</label>
                        <textarea name="description" rows="4"
                            class="w-full rounded-lg border-2 border-gray-200 px-4 py-3 transition-colors focus:border-red-500 focus:outline-none focus:ring-red-500"
                            placeholder="Masukkan deskripsi barang...">{{ old('description', $barang->description) }}</textarea>
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
                                    "icon": "<div class=\"shrink-0 size-5 text-xs text-gray-500\">{{ round($subcategory->margin) }}%</div>"}'
                                        {{ $barang->subcategory_id == $subcategory->id ? 'selected' : '' }}>
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
                                "selectedOption": {{ $barang->brand_id }},
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
                                    <option value="{{ $brand->id }}"
                                        {{ $barang->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}
                                    </option>
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
                                    <option value="{{ $type->id }}"
                                        {{ $barang->type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
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
                                        aria-roledescription="Number field"
                                        value="{{ old('early_expiry_days', $barang->early_expiry_days) }}"
                                        data-hs-input-number-input="">
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
                                        aria-roledescription="Number field"
                                        value="{{ old('mid_expiry_days', $barang->mid_expiry_days) }}"
                                        data-hs-input-number-input="">
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
                                        aria-roledescription="Number field"
                                        value="{{ old('late_expiry_days', $barang->late_expiry_days) }}"
                                        data-hs-input-number-input="">
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
                        <select id="select_tipe_unit" name="select_tipe_unit"
                            class="mt-2 block w-full rounded-lg border-gray-200 bg-white px-4 py-2 capitalize text-gray-800 transition-colors hover:bg-gray-50 focus:border-red-500 focus:bg-gray-50 focus:ring-red-500">
                            <option selected disabled value="">Pilih Tipe</option>
                            @foreach ($tipe_unit as $item)
                                <option value="{{ $item }}" class="capitalize"
                                    {{ $barang->fromConversions->first()->conversionFrom->type == $item ? 'selected' : '' }}>
                                    {{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="button" id="resetButton"
                            class="hidden cursor-pointer rounded-lg bg-red-600 px-4 py-2 text-white transition-colors hover:bg-red-700">
                            Reset Kembali
                        </button>
                        <button type="button" id="addConversion"
                            class="cursor-pointer rounded-lg bg-red-600 px-4 py-2 text-white transition-colors hover:bg-red-700">
                            <i class="fas fa-plus mr-2"></i>Tambah Konversi
                        </button>
                    </div>
                </div>

                <div id="conversionsContainer">
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
                            <div>
                                <button type="button"
                                    class="remove-conversion absolute right-2 top-2 flex h-6 w-6 cursor-pointer items-center justify-center rounded-full bg-red-500 text-white hover:scale-110 hover:bg-red-600">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Container untuk konversi aktual -->
                    <div id="actual-conversions">
                        <!-- Konversi pertama (required) -->
                        @foreach ($barang->fromConversions as $conversion)
                            <div class="conversion-row relative mb-4 rounded-lg border-2 border-gray-200 bg-white p-6"
                                data-conversion-index="{{ $loop->index }}">
                                <div class="grid grid-cols-1 items-end gap-4 md:grid-cols-3">
                                    <!-- From Satuan -->
                                    <div class="relative">
                                        <label class="mb-2 block font-semibold text-gray-700">Dari Satuan <span
                                                class="text-red-500">*</span></label>
                                        <select name="from_unit[]"
                                            class="from-unit-select w-full rounded-lg border-2 border-gray-200 px-4 py-3 transition-colors focus:border-red-500 focus:outline-none focus:ring-red-500">
                                            <option value="">Pilih Satuan</option>
                                            @foreach ($satuans as $satuan)
                                                <option value="{{ $satuan->id }}"
                                                    class="{{ $satuan->type == $conversion->conversionFrom->type ? '' : 'hidden' }}"
                                                    {{ $conversion->conversionFrom->id == $satuan->id ? 'selected' : '' }}>
                                                    {{ $satuan->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- To Satuan -->
                                    <div class="relative">
                                        <label class="mb-2 block font-semibold text-gray-700">Ke Satuan <span
                                                class="text-red-500">*</span></label>
                                        <select name="to_unit[]"
                                            class="to-unit-select w-full rounded-lg border-2 border-gray-200 px-4 py-3 transition-colors focus:border-red-500 focus:outline-none focus:ring-red-500">
                                            <option value="">Pilih Satuan</option>
                                            @foreach ($satuans as $satuan)
                                                <option value="{{ $satuan->id }}"
                                                    class="{{ $satuan->type == $conversion->conversionTo->type ? '' : 'hidden' }}"
                                                    {{ $conversion->conversionTo->id == $satuan->id ? 'selected' : '' }}>
                                                    {{ $satuan->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Faktor Konversi -->
                                    <div class="relative">
                                        <label class="mb-2 block font-semibold text-gray-700">Faktor Konversi <span
                                                class="text-red-500">*</span></label>
                                        <input type="number" name="conversion_factor[]" step="0.001" min="0.001"
                                            class="conversion-factor-input w-full rounded-lg border-2 border-gray-200 px-4 py-3 transition-colors focus:border-red-500 focus:outline-none focus:ring-red-500"
                                            value="{{ $conversion->conversion_factor == (int) $conversion->conversion_factor ? number_format($conversion->conversion_factor, 0, '.', ',') : $conversion->conversion_factor }}"
                                            placeholder="Contoh: 1000">
                                    </div>
                                </div>
                                <div>
                                    <button type="button"
                                        class="remove-conversion absolute right-2 top-2 flex h-6 w-6 cursor-pointer items-center justify-center rounded-full bg-red-500 text-white hover:scale-110 hover:bg-red-600"
                                        data-conversion-id="{{ $conversion->id }}">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
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
                            <input id="mainImage" name="main_image" type="file" accept="image/*" class="hidden">
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
                            <div id="mainImagePreview" class="mt-4">
                                @if ($images->where('is_main', true)->first())
                                    <div class="relative inline-block rounded-lg border-2 border-gray-200 p-2">
                                        <img id="mainImageDisplay"
                                            src="{{ asset('storage/' . $images->where('is_main', true)->first()->url) }}"
                                            alt="Preview" class="image-preview h-32 w-32 rounded-lg object-cover">
                                        <button type="button" id="removeMainImage"
                                            class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                        <div class="mt-2 text-center">
                                            <span class="text-xs text-gray-600">Gambar Utama</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="relative inline-block rounded-lg border-2 border-gray-200 p-2"
                                        id="newMainImagePreview">
                                        <img id="newMainImageDisplay" src="" alt="Preview"
                                            class="image-preview h-32 w-32 rounded-lg object-cover">
                                        <button type="button" id="removeNewMainImage"
                                            class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                        <div class="mt-2 text-center">
                                            <span class="text-xs text-gray-600" id="newMainImageName"></span>
                                        </div>
                                    </div>
                                @endif
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
                            <div id="additionalImagesPreview" class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
                                @if ($images->where('is_main', false)->count() > 0)
                                    @foreach ($images->where('is_main', false) as $image)
                                        <div class="relative inline-block rounded-lg border-2 border-gray-200 p-2">
                                            <img src="{{ asset('storage/' . $image->url) }}" alt="Preview"
                                                class="image-preview h-32 w-32 rounded-lg object-cover">
                                            <button type="button"
                                                class="remove-image absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600"
                                                data-image-id="{{ $image->id }}">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="relative inline-block hidden rounded-lg border-2 border-gray-200 p-2"
                                        id="newAdditionalImagesPreview">
                                        <!-- New additional images will be added here dynamically -->
                                    </div>
                                @endif
                            </div>
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
        class FormManager {
            constructor() {
                this.init();
                this.setupEventListeners();
                this.saveInitialState();
            }

            init() {
                // DOM Elements
                this.elements = {
                    selectTipeUnit: document.getElementById('select_tipe_unit'),
                    resetButton: document.getElementById('resetButton'),
                    addConversionBtn: document.getElementById('addConversion'),
                    conversionsContainer: document.getElementById('conversionsContainer'),
                    actualConversions: document.getElementById('actual-conversions'),
                    conversionTemplate: document.getElementById('conversion-template'),

                    // Image elements
                    mainImage: document.getElementById('mainImage'),
                    mainImageDropzone: document.getElementById('mainImageDropzone'),
                    mainImagePreview: document.getElementById('mainImagePreview'),
                    additionalImages: document.getElementById('additionalImages'),
                    additionalImagesDropzone: document.getElementById('additionalImagesDropzone'),
                    additionalImagesPreview: document.getElementById('additionalImagesPreview'),

                    // Form elements
                    barangForm: document.getElementById('barangForm'),
                    submitBtn: document.getElementById('submitBtn'),
                    cancelBtn: document.getElementById('cancelBtn'),

                    // Modal elements
                    messageModal: document.getElementById('message-modal'),
                    loadingOverlay: document.getElementById('loadingOverlay')
                };

                this.data = {
                    units: @json($satuans),
                    initialConversions: @json($barang->fromConversions->toArray()),
                    initialImages: @json($images->toArray()),
                    conversionCounter: 0
                };

                this.state = {
                    selectedFiles: {
                        main: null,
                        additional: []
                    },
                    deletedConversions: [],
                    deletedImages: []
                };
            }

            saveInitialState() {
                // Save initial form state for reset functionality
                this.initialState = {
                    selectTipeUnit: this.elements.selectTipeUnit.value,
                    conversionsHtml: this.elements.actualConversions.innerHTML,
                    imagesHtml: this.elements.mainImagePreview.innerHTML + this.elements.additionalImagesPreview
                        .innerHTML
                };
            }

            setupEventListeners() {
                // Unit type change
                this.elements.selectTipeUnit?.addEventListener('change', (e) => this.handleUnitTypeChange(e));

                // Reset button
                this.elements.resetButton?.addEventListener('click', () => this.resetForm());

                // Add conversion
                this.elements.addConversionBtn?.addEventListener('click', () => this.addConversion());

                // Image uploads
                this.setupImageHandlers();

                // Form submission
                this.elements.submitBtn?.addEventListener('click', (e) => this.handleSubmit(e));
                this.elements.cancelBtn?.addEventListener('click', () => this.handleCancel());

                // Dynamic event delegation for conversion removal
                this.elements.actualConversions?.addEventListener('click', (e) => {
                    if (e.target.closest('.remove-conversion')) {
                        this.removeConversion(e.target.closest('.remove-conversion'));
                    }
                });
            }

            handleUnitTypeChange(event) {
                const selectedType = event.target.value;
                if (selectedType) {
                    this.updateUnitOptions(selectedType);
                    this.elements.resetButton?.classList.remove('hidden');
                }
            }

            updateUnitOptions(selectedType) {
                const filteredUnits = this.data.units.filter(unit => unit.type === selectedType);
                const conversions = this.elements.actualConversions.querySelectorAll('.conversion-row');

                conversions.forEach(row => {
                    const fromSelect = row.querySelector('.from-unit-select');
                    const toSelect = row.querySelector('.to-unit-select');
                    const factorInput = row.querySelector('.conversion-factor-input');

                    // Clear current options
                    fromSelect.innerHTML = '<option value="">Pilih Satuan</option>';
                    toSelect.innerHTML = '<option value="">Pilih Satuan</option>';
                    factorInput.value = '';

                    // Add filtered options
                    filteredUnits.forEach(unit => {
                        fromSelect.innerHTML += `<option value="${unit.id}">${unit.name}</option>`;
                        toSelect.innerHTML += `<option value="${unit.id}">${unit.name}</option>`;
                    });
                });
            }

            addConversion() {
                const template = this.elements.conversionTemplate.cloneNode(true);
                template.id = `conversion-${++this.data.conversionCounter}`;
                template.classList.remove('hidden', 'conversion-template');
                template.classList.add('conversion-row');

                // Update unit options based on current selection
                const selectedType = this.elements.selectTipeUnit.value;
                if (selectedType) {
                    const filteredUnits = this.data.units.filter(unit => unit.type === selectedType);
                    const fromSelect = template.querySelector('.from-unit-select');
                    const toSelect = template.querySelector('.to-unit-select');

                    filteredUnits.forEach(unit => {
                        fromSelect.innerHTML += `<option value="${unit.id}">${unit.name}</option>`;
                        toSelect.innerHTML += `<option value="${unit.id}">${unit.name}</option>`;
                    });
                }

                this.elements.actualConversions.appendChild(template);
                this.elements.resetButton?.classList.remove('hidden');
                template.querySelector('.remove-conversion').addEventListener('click', (event) => {
                    this.removeConversion(event.target);
                });
            }

            removeConversion(button) {
                const conversionRow = button.closest('.conversion-row');
                const conversionId = button.dataset.conversionId;

                if (conversionId) {
                    this.state.deletedConversions.push(conversionId);
                }

                conversionRow.remove();
                this.elements.resetButton?.classList.remove('hidden');
            }

            resetForm() {
                // Reset unit type selector
                this.elements.selectTipeUnit.value = this.initialState.selectTipeUnit;

                // Reset conversions to initial state
                this.elements.actualConversions.innerHTML = this.initialState.conversionsHtml;

                // Reset images to initial state
                this.elements.mainImagePreview.innerHTML = this.initialState.imagesHtml.split('</div>')[0] + '</div>';
                this.elements.additionalImagesPreview.innerHTML = this.initialState.imagesHtml.split('</div>').slice(1)
                    .join('</div>');

                // Reset state
                this.state.selectedFiles = {
                    main: null,
                    additional: []
                };
                this.state.deletedConversions = [];
                this.state.deletedImages = [];

                // Hide reset button
                this.elements.resetButton?.classList.add('hidden');

                // Re-setup event listeners for restored elements
                this.setupRestoredElements();
            }

            setupRestoredElements() {
                // Re-setup image removal handlers
                document.querySelectorAll('.remove-image').forEach(btn => {
                    btn.addEventListener('click', (e) => this.removeImage(e.target.closest('.remove-image')));
                });
            }

            setupImageHandlers() {
                // Main image handlers
                this.elements.mainImageDropzone?.addEventListener('click', () => this.elements.mainImage?.click());
                this.elements.mainImageDropzone?.addEventListener('dragover', (e) => this.handleDragOver(e));
                this.elements.mainImageDropzone?.addEventListener('drop', (e) => this.handleMainImageDrop(e));
                this.elements.mainImage?.addEventListener('change', (e) => this.handleMainImageSelect(e));

                // Additional images handlers
                this.elements.additionalImagesDropzone?.addEventListener('click', () => this.elements.additionalImages
                    ?.click());
                this.elements.additionalImagesDropzone?.addEventListener('dragover', (e) => this.handleDragOver(e));
                this.elements.additionalImagesDropzone?.addEventListener('drop', (e) => this.handleAdditionalImagesDrop(
                    e));
                this.elements.additionalImages?.addEventListener('change', (e) => this.handleAdditionalImagesSelect(e));

                // Image removal handlers
                document.querySelectorAll('.remove-image, #removeMainImage, #removeNewMainImage').forEach(btn => {
                    btn.addEventListener('click', (e) => this.removeImage(e.target.closest('button')));
                });
            }

            handleDragOver(event) {
                event.preventDefault();
                event.stopPropagation();
                event.currentTarget.classList.add('border-red-500', 'bg-red-100');
            }

            handleMainImageDrop(event) {
                event.preventDefault();
                event.stopPropagation();
                event.currentTarget.classList.remove('border-red-500', 'bg-red-100');

                const files = event.dataTransfer.files;
                if (files.length > 0) {
                    this.processMainImage(files[0]);
                }
            }

            handleMainImageSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    this.processMainImage(file);
                }
            }

            processMainImage(file) {
                if (!this.validateImage(file)) return;

                this.state.selectedFiles.main = file;
                this.displayImagePreview(file, 'main');
            }

            handleAdditionalImagesDrop(event) {
                event.preventDefault();
                event.stopPropagation();
                event.currentTarget.classList.remove('border-gray-500', 'bg-gray-100');

                const files = Array.from(event.dataTransfer.files);
                this.processAdditionalImages(files);
            }

            handleAdditionalImagesSelect(event) {
                const files = Array.from(event.target.files);
                this.processAdditionalImages(files);
            }

            processAdditionalImages(files) {
                const validFiles = files.filter(file => this.validateImage(file));
                const currentCount = this.state.selectedFiles.additional.length;
                const availableSlots = 4 - currentCount;

                if (validFiles.length > availableSlots) {
                    this.showMessage('Peringatan', `Maksimal 4 gambar tambahan. ${availableSlots} slot tersisa.`,
                        'warning');
                    validFiles.splice(availableSlots);
                }

                validFiles.forEach(file => {
                    this.state.selectedFiles.additional.push(file);
                    this.displayImagePreview(file, 'additional');
                });
            }

            validateImage(file) {
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                const maxSize = 5 * 1024 * 1024; // 5MB

                if (!validTypes.includes(file.type)) {
                    this.showMessage('Error', 'Format file tidak didukung. Gunakan PNG, JPG, atau JPEG.', 'error');
                    return false;
                }

                if (file.size > maxSize) {
                    this.showMessage('Error', 'Ukuran file terlalu besar. Maksimal 5MB.', 'error');
                    return false;
                }

                return true;
            }

            displayImagePreview(file, type) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    if (type === 'main') {
                        this.updateMainImagePreview(e.target.result, file.name);
                    } else {
                        this.addAdditionalImagePreview(e.target.result, file.name);
                    }
                };
                reader.readAsDataURL(file);
            }

            updateMainImagePreview(src, fileName) {
                const previewHtml = `
            <div class="relative inline-block rounded-lg border-2 border-gray-200 p-2" id="newMainImagePreview">
                <img id="newMainImageDisplay" src="${src}" alt="Preview" class="image-preview h-32 w-32 rounded-lg object-cover">
                <button type="button" id="removeNewMainImage" class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600">
                    <i class="fas fa-times text-xs"></i>
                </button>
                <div class="mt-2 text-center">
                    <span class="text-xs text-gray-600">${fileName}</span>
                </div>
            </div>
        `;

                this.elements.mainImagePreview.innerHTML = previewHtml;

                // Setup remove handler
                document.getElementById('removeNewMainImage')?.addEventListener('click', () => {
                    this.state.selectedFiles.main = null;
                    this.elements.mainImagePreview.innerHTML = '';
                });
            }

            addAdditionalImagePreview(src, fileName) {
                const previewDiv = document.createElement('div');
                previewDiv.className = 'relative inline-block rounded-lg border-2 border-gray-200 p-2';
                previewDiv.innerHTML = `
            <img src="${src}" alt="Preview" class="image-preview h-32 w-32 rounded-lg object-cover">
            <button type="button" class="remove-additional-image absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600">
                <i class="fas fa-times text-xs"></i>
            </button>
            <div class="mt-2 text-center">
                <span class="text-xs text-gray-600">${fileName}</span>
            </div>
        `;

                this.elements.additionalImagesPreview.appendChild(previewDiv);

                // Setup remove handler
                previewDiv.querySelector('.remove-additional-image').addEventListener('click', () => {
                    const index = Array.from(this.elements.additionalImagesPreview.children).indexOf(
                    previewDiv);
                    this.state.selectedFiles.additional.splice(index, 1);
                    previewDiv.remove();
                });
            }

            removeImage(button) {
                const imageId = button.dataset.imageId;
                const previewContainer = button.closest('div');

                if (imageId) {
                    this.state.deletedImages.push(imageId);
                }

                // Remove from additional files if it's a new upload
                if (button.classList.contains('remove-additional-image')) {
                    const index = Array.from(this.elements.additionalImagesPreview.children).indexOf(previewContainer);
                    if (index > -1) {
                        this.state.selectedFiles.additional.splice(index, 1);
                    }
                }

                previewContainer.remove();
            }

            async handleSubmit(event) {
                event.preventDefault();

                if (!this.validateForm()) return;

                this.showLoading(true);

                try {
                    const formData = this.prepareFormData();
                    const response = await this.submitForm(formData);
                    console.log(response);
                    if (response.ok) {
                        this.showMessage('Sukses', 'Barang berhasil diperbarui!', 'success');
                        setTimeout(() => {
                            window.location.href = response.redirectUrl || '{{ route('barang.index') }}';
                        }, 2000);
                    } else {
                        throw new Error('Gagal memperbarui barang');
                    }
                } catch (error) {
                    this.showMessage('Error', error.message || 'Terjadi kesalahan saat memperbarui barang', 'error');
                } finally {
                    this.showLoading(false);
                }
            }

            validateForm() {
                // Basic form validation
                const requiredFields = ['name', 'subcategory', 'brand', 'kebutuhan'];

                for (const field of requiredFields) {
                    const element = document.querySelector(`[name="${field}"]`);
                    if (!element?.value.trim()) {
                        this.showMessage('Error', `Field ${field} harus diisi`, 'error');
                        element?.focus();
                        return false;
                    }
                }

                // Validate conversions
                const conversions = this.elements.actualConversions.querySelectorAll('.conversion-row');
                for (const conversion of conversions) {
                    const fromUnit = conversion.querySelector('.from-unit-select').value;
                    const toUnit = conversion.querySelector('.to-unit-select').value;
                    const factor = conversion.querySelector('.conversion-factor-input').value;

                    if (!fromUnit || !toUnit || !factor) {
                        this.showMessage('Error', 'Semua field konversi harus diisi', 'error');
                        return false;
                    }
                }

                return true;
            }

            prepareFormData() {
                const formData = new FormData(this.elements.barangForm);

                // Add deleted items
                this.state.deletedConversions.forEach(id => {
                    formData.append('deleted_conversions[]', id);
                });

                this.state.deletedImages.forEach(id => {
                    formData.append('deleted_images[]', id);
                });

                // Add new images
                if (this.state.selectedFiles.main) {
                    formData.append('new_main_image', this.state.selectedFiles.main);
                }

                this.state.selectedFiles.additional.forEach((file, index) => {
                    formData.append(`new_additional_images[${index}]`, file);
                });

                return formData;
            }

            async submitForm(formData) {
                const response = await fetch(this.elements.barangForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                return response;
            }

            handleCancel() {
                if (confirm('Apakah Anda yakin ingin membatalkan perubahan?')) {
                    window.history.back();
                }
            }

            showMessage(title, message, type = 'info') {
                const modal = this.elements.messageModal;
                const titleEl = document.getElementById('modalTitle');
                const messageEl = document.getElementById('modalMessage');
                const iconEl = document.getElementById('modalIcon');
                const confirmBtn = document.getElementById('confirmBtn');

                if (titleEl) titleEl.textContent = title;
                if (messageEl) messageEl.textContent = message;

                // Update icon based on type
                if (iconEl) {
                    iconEl.className = 'fas text-6xl ';
                    switch (type) {
                        case 'success':
                            iconEl.className += 'fa-check-circle text-green-500';
                            break;
                        case 'warning':
                            iconEl.className += 'fa-exclamation-triangle text-yellow-500';
                            break;
                        case 'error':
                            iconEl.className += 'fa-times-circle text-red-500';
                            break;
                        default:
                            iconEl.className += 'fa-info-circle text-blue-500';
                    }
                }

                // Hide confirm button for non-confirmation dialogs
                if (confirmBtn) {
                    confirmBtn.style.display = type === 'confirm' ? 'inline-block' : 'none';
                }

                modal?.classList.remove('hidden');
                modal?.classList.add('flex');

                // Auto-close for success messages
                if (type === 'success') {
                    setTimeout(() => this.closeModal(), 3000);
                }
            }

            closeModal() {
                this.elements.messageModal?.classList.add('hidden');
                this.elements.messageModal?.classList.remove('flex');
            }

            showLoading(show) {
                if (show) {
                    this.elements.loadingOverlay?.classList.remove('hidden');
                } else {
                    this.elements.loadingOverlay?.classList.add('hidden');
                }
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            window.formManager = new FormManager();
        });

        // Global functions for modal
        function closeModal() {
            window.formManager?.closeModal();
        }

        function confirmAction() {
            // Implement specific confirmation logic here
            window.formManager?.closeModal();
        }
    </script>
@endpush
