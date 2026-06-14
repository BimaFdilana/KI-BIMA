@extends('layouts.admin')

@section('nav_title', 'Tambah Barang KI')
@section('page_title', 'Tambah Data Barang KI')

@push('styles')
    <style>
        /* Modal Shadows */
        .modal-shadow-1 {
            box-shadow: 0 10px 30px rgba(239, 68, 68, 0.3);
        }

        .modal-shadow-2 {
            box-shadow: 0 15px 35px rgba(220, 38, 38, 0.3);
        }

        /* Input Focus */
        .input-focus:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
            outline: none;
        }

        /* Modal Animations */
        .modal-enter {
            animation: modalEnter 0.3s ease-out forwards;
        }

        .modal-exit {
            animation: modalExit 0.2s ease-in forwards;
        }

        @keyframes modalEnter {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes modalExit {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        /* Custom Transitions */
        .transition-modal {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }


        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Hover Effects */
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        /* Status Badges */
        .status-active {
            @apply bg-green-100 text-green-800;
        }

        .status-inactive {
            @apply bg-red-100 text-red-800;
        }

        /* Responsive Utilities */
        @media (max-width: 768px) {
            .modal-content {
                margin: 1rem;
            }

            .grid-responsive {
                grid-template-columns: 1fr;
            }
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
                <h2 class="text-2xl font-bold text-red-800">Informasi Dasar <span class="text-red-500">*</span></h2>
            </div>

            <div class="rounded-2xl border-gray-200 bg-white p-8 shadow-2xl">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-black">
                            <i class="fas fa-tag mr-1"></i>
                            Barang
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="relative w-full">
                                <select id="barang-select" name="barang_id" required
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
                                    <option value=""></option>
                                    @foreach ($barang as $item)
                                        <option value="{{ $item->id }}"
                                            data-hs-select-option='{
                                                    "icon": "<div class=\"shrink-0 text-xs text-gray-500\"><i class=\"fas fa-box text-gray-400\"></i></div>"}'>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-black">
                            <i class="fas fa-barcode mr-1"></i>
                            ID Barcode
                        </label>
                        <input type="text" id="id_barcode" name="id_barcode" required
                            class="relative flex w-full items-center gap-x-2 rounded-lg border border-gray-200 bg-white py-3 pe-10 ps-4 text-start text-sm text-gray-800 transition-colors duration-200 hover:border-gray-300 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500"
                            placeholder="Masukkan kode barcode">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-black">
                            <i class="fas fa-balance-scale mr-1"></i>
                            Satuan
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="relative w-full">
                                <select id="satuan-select" name="satuan_id" required
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
                                    <option disabled>
                                        Pilih Barang Terlebih Dahulu
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-black">
                            <i class="fas fa-boxes mr-1"></i>
                            Quantity
                        </label>
                        <input type="number" name="stock" required min="0"
                            class="relative flex w-full items-center gap-x-2 rounded-lg border border-gray-200 bg-white py-3 pe-10 ps-4 text-start text-sm text-gray-800 transition-colors duration-200 hover:border-gray-300 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500"
                            placeholder="Masukkan jumlah stok">
                    </div>
                </div>
            </div>
            <div class="mb-6 flex items-center">
                <div class="mr-4 rounded-full bg-red-600">
                    <i class="fas fa-dollar-sign px-4 py-3 text-xl text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-red-800">Informasi Harga</h2>
            </div>
            <!-- Pricing Section -->
            <div class="rounded-2xl border-gray-200 bg-white p-8 shadow-2xl">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-black">
                            <i class="fas fa-arrow-down mr-1"></i>
                            Harga Beli <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-black">Rp</span>
                            <input type="number" name="price_buy" required min="0" step="0.01"
                                class="form-input w-full rounded-lg border border-gray-200 py-3 pl-10 pr-4 transition-all duration-200 focus:border-red-500 focus:ring-2 focus:ring-red-500"
                                placeholder="0.00">
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-black">
                            <i class="fas fa-arrow-up mr-1"></i>
                            Harga Jual <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-black">Rp</span>
                            <input type="number" name="price_sell" required min="0" step="0.01"
                                class="form-input w-full rounded-lg border border-gray-200 py-3 pl-10 pr-4 transition-all duration-200 focus:border-red-500 focus:ring-2 focus:ring-red-500"
                                placeholder="0.00">
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-black">
                            <i class="fas fa-chart-line mr-1"></i>
                            Harga Mark Up
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-black">Rp</span>
                            <input type="number" name="price_up" min="0" step="0.01"
                                class="form-input w-full rounded-lg border border-gray-200 py-3 pl-10 pr-4 transition-all duration-200 focus:border-red-500 focus:ring-2 focus:ring-red-500"
                                placeholder="0.00">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6 flex items-center">
                <div class="mr-4 rounded-full bg-red-600">
                    <i class="fas fa-cog p-3 text-xl text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-red-800">Informasi Tambahan <span class="text-red-500">*</span></h2>
            </div>
            <!-- Additional Information Section -->
            <div class="rounded-2xl border-gray-200 bg-white p-8 shadow-2xl">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-black">
                            <i class="fas fa-clock mr-1"></i>
                            Tanggal Kadaluwarsa
                        </label>
                        <input required type="datetime-local" name="expired_time"
                            class="form-input w-full rounded-lg border border-gray-200 px-4 py-3 transition-all duration-200 focus:border-red-500 focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-black">
                            <i class="fas fa-toggle-on mr-1"></i>
                            Status
                        </label>
                        <select name="status" required
                            class="form-input w-full rounded-lg border border-gray-200 px-4 py-3 transition-all duration-200 focus:border-red-500 focus:ring-2 focus:ring-red-500">
                            <option value="">Pilih Status</option>
                            <option value="active">Aktif</option>
                            <option value="nonactive">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 border-t border-gray-300 pt-6">
                <button type="reset" id="resetBtn"
                    class="rounded-lg bg-red-100 px-6 py-3 font-medium text-red-700 transition-colors duration-200 hover:bg-red-200">
                    <i class="fas fa-undo mr-2"></i>
                    Batal
                </button>
                <button type="submit"
                    class="transform rounded-lg bg-gradient-to-r from-red-600 to-red-700 px-8 py-3 font-medium text-white transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Barang
                </button>
            </div>
        </form>
    </div>

    <!-- Barcode Search Modal -->
    @include('partials.barcode-search-modal')

    <!-- Message Modal -->
    @include('partials.message-modal')

    <!-- Item Detail Modal -->
    @include('partials.item-detail-modal')

    <!-- Loading Overlay -->
    @include('partials.loading-overlay')

    <!-- Add Stock Modal -->
    @include('partials.add-stock-modal')
@endsection

@push('scripts')
    <script>
        /**
         * FormManager Class
         * Handles barcode search, modal management, and form interactions
         */
        class FormManager {
            constructor(config = {}) {
                this.config = {
                    findBarcodeUrl: config.findBarcodeUrl || '',
                    previousUrl: config.previousUrl || '',
                    addStockUrl: config.addStockUrl || '',
                    addnewBarangUrl: config.addnewBarangUrl || '',
                    scannerTimeout: config.scannerTimeout || 100, // timeout untuk mendeteksi scanner
                    minBarcodeLength: config.minBarcodeLength || 8, // minimal panjang barcode
                    apiTimeout: 30000,
                    ...config
                };

                this.barcode = null;
                this.scannerBuffer = ''; // buffer untuk menampung input scanner
                this.scannerTimer = null; // timer untuk mendeteksi akhir scan
                this.form = document.getElementById('barangForm');

                this.validationRules = {
                    barang_id: {
                        required: true,
                        message: 'Barang harus dipilih'
                    },
                    id_barcode: {
                        required: true,
                        minLength: this.config.minBarcodeLength,
                        maxLength: this.config.maxBarcodeLength,
                        pattern: /^[A-Za-z0-9\-_]+$/,
                        message: 'ID Barcode harus diisi dengan format yang valid (8-50 karakter, hanya huruf, angka, dash, underscore)'
                    },
                    satuan_id: {
                        required: true,
                        message: 'Satuan harus dipilih'
                    },
                    stock: {
                        required: true,
                        min: 0,
                        type: 'number',
                        message: 'Quantity harus berupa angka positif'
                    },
                    price_buy: {
                        required: true,
                        min: 0,
                        type: 'number',
                        message: 'Harga beli harus berupa angka positif'
                    },
                    price_sell: {
                        required: true,
                        min: 0,
                        type: 'number',
                        message: 'Harga jual harus berupa angka positif'
                    },
                    price_up: {
                        required: false,
                        min: 0,
                        type: 'number',
                        message: 'Harga mark up harus berupa angka positif'
                    },
                    expired_time: {
                        required: true,
                        type: 'datetime',
                        message: 'Tanggal kadaluwarsa harus diisi'
                    },
                    status: {
                        required: true,
                        values: ['active', 'nonactive'],
                        message: 'Status harus dipilih (Aktif/Tidak Aktif)'
                    }
                };

                this.init();
            }


            /**
             * Initialize the FormManager
             */
            init() {
                this.cacheElements();
                this.bindEvents();
                this.initializeRealTimeValidation();
            }

            /**
             * Cache DOM elements for better performance
             */
            cacheElements() {
                // Modal elements
                this.elements = {
                    // Message Modal
                    messageModal: document.getElementById('message-modal'),
                    messageModalContainer: document.getElementById('message-modal-container'),
                    modalTitle: document.getElementById('modalTitle'),
                    modalMessage: document.getElementById('modalMessage'),
                    modalIcon: document.getElementById('modalIcon'),
                    actionButtons: document.getElementById('actionButtons'),
                    confirmButton: document.getElementById('confirmBtn'),
                    cancelButton: document.getElementById('closeMessageModalBtn'),

                    // Barcode Modal
                    findBarcodeModal: document.getElementById('findBarcodeModal'),
                    findBarcodeForm: document.getElementById('findBarcodeForm'),
                    findBarcodeInput: document.getElementById('findBarcodeInput'),

                    // Detail Modal
                    modalDetailBarang: document.getElementById('modalDetailBarang'),
                    modalContent: document.getElementById('modalContent'),

                    // Loading
                    loadingOverlay: document.getElementById('loadingOverlay'),

                    // Detail fields
                    detailNama: document.getElementById('detailNama'),
                    detailBarcode: document.getElementById('detailBarcode'),
                    detailSatuan: document.getElementById('detailSatuan'),
                    detailQuantity: document.getElementById('detailQuantity'),
                    detailStatus: document.getElementById('detailStatus'),
                    detailPriceUp: document.getElementById('detailPriceUp'),
                    detailPriceBuy: document.getElementById('detailPriceBuy'),
                    detailPriceSell: document.getElementById('detailPriceSell'),
                    detailExpiredDate: document.getElementById('detailExpiredDate'),
                    detailExpiredTime: document.getElementById('detailExpiredTime'),
                    detailDiscountStart: document.getElementById('detailDiscountStart'),
                    detailDiscountEnd: document.getElementById('detailDiscountEnd'),
                    detailDiscountType: document.getElementById('detailDiscountType'),
                    detailDiscount: document.getElementById('detailDiscount'),

                    // Add Stock Modal
                    addStockModal: document.getElementById('addStockModal'),
                    addStockForm: document.getElementById('addStockForm'),
                    addStockInput: document.getElementById('addStockInput'),
                    addStockSatuan: document.getElementById('addStockSatuan'),
                    addStockInfo: document.getElementById('addStockInfo'),
                    addStockBtn: document.getElementById('addStockBtn'),

                    barangSelect: document.getElementById('barang-select'),
                    satuanSelect: document.getElementById('satuan-select'),
                    idBarcodeInput: document.getElementById('id_barcode'),
                    resetBtn: document.getElementById('resetBtn'),


                    // Auto-calculate markup price
                    priceBuyInput: document.querySelector('[name="price_buy"]'),
                    priceSellInput: document.querySelector('[name="price_sell"]'),
                    priceUpInput: document.querySelector('[name="price_up"]'),
                };
            }

            /**
             * Bind event listeners
             */
            bindEvents() {
                // Barcode form submission
                if (this.elements.findBarcodeForm) {
                    this.elements.findBarcodeForm.addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.handleBarcodeSubmit();
                    });
                }

                this.elements.barangSelect?.addEventListener('change', (e) => this.handleBarangChange(e));
                this.elements.resetBtn?.addEventListener('click', () => this.showFindBarcodeModal());
                this.form?.addEventListener('submit', (e) => this.handleSubmit(e));

                // Real-time validation
                this.bindRealTimeValidation();

                // Custom validations
                this.bindCustomValidations();

                // Scanner input detection
                this.bindScannerEvents();

                // Modal overlay clicks
                this.bindModalCloseEvents();

                // Keyboard events
                this.bindKeyboardEvents();
            }
            bindRealTimeValidation() {
                const inputs = this.form.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.addEventListener('blur', (e) => this.validateField(e.target));
                    input.addEventListener('input', (e) => this.clearFieldError(e.target));
                });
            }

            bindCustomValidations() {
                // Validate price sell >= price buy
                const priceBuyInput = this.form.querySelector('[name="price_buy"]');
                const priceSellInput = this.form.querySelector('[name="price_sell"]');

                if (priceBuyInput && priceSellInput) {
                    [priceBuyInput, priceSellInput].forEach(input => {
                        input.addEventListener('input', () => {
                            this.validatePriceComparison();
                        });
                    });
                }

                // Validate expired date is in future
                const expiredTimeInput = this.form.querySelector('[name="expired_time"]');
                if (expiredTimeInput) {
                    expiredTimeInput.addEventListener('change', () => {
                        this.validateExpiredDate();
                    });
                }

                // Validate barcode uniqueness on blur
                const barcodeInput = this.form.querySelector('[name="id_barcode"]');
                if (barcodeInput) {
                    barcodeInput.addEventListener('blur', () => {
                        this.validateBarcodeUniqueness();
                    });
                }
            }

            initializeRealTimeValidation() {
                // Add required field indicators
                this.addRequiredIndicators();
            }

            addRequiredIndicators() {
                Object.keys(this.validationRules).forEach(fieldName => {
                    const rule = this.validationRules[fieldName];
                    if (rule.required) {
                        const field = this.form.querySelector(`[name="${fieldName}"]`);
                        const label = this.form.querySelector(
                            `label[for="${fieldName}"], label:has(+ * [name="${fieldName}"]), label:has(+ [name="${fieldName}"])`
                        );

                        if (label && !label.querySelector('.text-red-500')) {
                            label.innerHTML += ' <span class="text-red-500">*</span>';
                        }
                    }
                });
            }
            validateField(field) {
                const fieldName = field.name;
                const rule = this.validationRules[fieldName];

                if (!rule) return true;

                const value = field.value.trim();
                let isValid = true;
                let errorMessage = '';

                // Required validation
                if (rule.required && !value) {
                    isValid = false;
                    errorMessage = rule.message;
                }

                // Type validation
                if (isValid && value && rule.type === 'number') {
                    const numValue = parseFloat(value);
                    if (isNaN(numValue)) {
                        isValid = false;
                        errorMessage = `${fieldName} harus berupa angka`;
                    } else if (rule.min !== undefined && numValue < rule.min) {
                        isValid = false;
                        errorMessage = `${fieldName} tidak boleh kurang dari ${rule.min}`;
                    }
                }

                // Length validation
                if (isValid && value) {
                    if (rule.minLength && value.length < rule.minLength) {
                        isValid = false;
                        errorMessage = `${fieldName} minimal ${rule.minLength} karakter`;
                    }
                    if (rule.maxLength && value.length > rule.maxLength) {
                        isValid = false;
                        errorMessage = `${fieldName} maksimal ${rule.maxLength} karakter`;
                    }
                }

                // Pattern validation
                if (isValid && value && rule.pattern && !rule.pattern.test(value)) {
                    isValid = false;
                    errorMessage = rule.message;
                }

                // Values validation
                if (isValid && value && rule.values && !rule.values.includes(value)) {
                    isValid = false;
                    errorMessage = rule.message;
                }

                // DateTime validation
                if (isValid && value && rule.type === 'datetime') {
                    const dateValue = new Date(value);
                    if (isNaN(dateValue.getTime())) {
                        isValid = false;
                        errorMessage = 'Format tanggal tidak valid';
                    }
                }

                this.showFieldValidation(field, isValid, errorMessage);
                return isValid;
            }

            validatePriceComparison() {
                const priceBuyInput = this.form.querySelector('[name="price_buy"]');
                const priceSellInput = this.form.querySelector('[name="price_sell"]');

                if (!priceBuyInput || !priceSellInput) return true;

                const priceBuy = parseFloat(priceBuyInput.value) || 0;
                const priceSell = parseFloat(priceSellInput.value) || 0;

                if (priceBuy > 0 && priceSell > 0 && priceSell < priceBuy) {
                    this.showFieldValidation(priceSellInput, false,
                        'Harga jual tidak boleh lebih rendah dari harga beli');
                    return false;
                } else {
                    this.clearFieldError(priceSellInput);
                    return true;
                }
            }

            validateExpiredDate() {
                const expiredTimeInput = this.form.querySelector('[name="expired_time"]');
                if (!expiredTimeInput || !expiredTimeInput.value) return true;

                const expiredDate = new Date(expiredTimeInput.value);
                const now = new Date();

                if (expiredDate <= now) {
                    this.showFieldValidation(expiredTimeInput, false, 'Tanggal kadaluwarsa harus di masa depan');
                    return false;
                } else {
                    this.clearFieldError(expiredTimeInput);
                    return true;
                }
            }
            async validateBarcodeUniqueness() {
                const barcodeInput = this.form.querySelector('[name="id_barcode"]');
                if (!barcodeInput || !barcodeInput.value.trim()) return true;

                const barcode = barcodeInput.value.trim();
                if (barcode.length >= this.config.minBarcodeLength) {
                    try {
                        // Show loading state
                        this.showFieldLoading(barcodeInput, true);

                        const response = await axios.post(this.config.findBarcodeUrl, {
                            barcode
                        });

                        this.showFieldLoading(barcodeInput, false);

                        if (!response.data.success) {
                            if (response.data.message.includes('tidak ditemukan')) {
                                return true;
                            } else {
                                this.showFieldValidation(barcodeInput, false, response.data.message);
                                return false;
                            }
                        } else {
                            this.clearFieldError(barcodeInput);
                            return true;
                        }
                    } catch (error) {
                        this.showFieldLoading(barcodeInput, false);
                        console.warn('Gagal memvalidasi barcode:', error);
                        return true;
                    }
                } else {
                    this.showFieldValidation(barcodeInput, false, 'Barcode minimal ' + this.config.minBarcodeLength +
                        ' karakter');
                    return false;
                }
            }

            showFieldValidation(field, isValid, message) {
                this.clearFieldError(field);

                if (!isValid) {
                    // Add error styling
                    field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                    field.classList.remove('border-gray-200', 'focus:border-blue-500');

                    // Create and show error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'field-error mt-1 text-sm text-red-600';
                    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle mr-1"></i>${message}`;

                    field.parentNode.appendChild(errorDiv);
                } else {
                    // Add success styling
                    field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                    field.classList.add('border-green-500', 'focus:border-green-500');

                    // Remove success styling after a moment
                    setTimeout(() => {
                        field.classList.remove('border-green-500', 'focus:border-green-500');
                        field.classList.add('border-gray-200');
                    }, 1000);
                }
            }

            clearFieldError(field) {
                // Remove error styling
                field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                field.classList.add('border-gray-200');

                // Remove error message
                const errorDiv = field.parentNode.querySelector('.field-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }

            showFieldLoading(field, show) {
                const loadingDiv = field.parentNode.querySelector('.field-loading');

                if (show) {
                    if (!loadingDiv) {
                        const loading = document.createElement('div');
                        loading.className = 'field-loading mt-1 text-sm text-blue-600';
                        loading.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Memvalidasi...';
                        field.parentNode.appendChild(loading);
                    }
                } else {
                    if (loadingDiv) {
                        loadingDiv.remove();
                    }
                }
            }

            validateAllFields() {
                let isValid = true;
                const errors = [];

                // Validate each field
                Object.keys(this.validationRules).forEach(fieldName => {
                    const field = this.form.querySelector(`[name="${fieldName}"]`);
                    if (field) {
                        const fieldValid = this.validateField(field);
                        if (!fieldValid) {
                            isValid = false;
                            errors.push(fieldName);
                        }
                    }
                });

                // Custom validations
                if (!this.validatePriceComparison()) {
                    isValid = false;
                    errors.push('price_comparison');
                }

                if (!this.validateExpiredDate()) {
                    isValid = false;
                    errors.push('expired_date');
                }

                return {
                    isValid,
                    errors
                };
            }
            // Method baru untuk menangani scanner events
            bindScannerEvents() {
                // Deteksi input dari scanner pada barcode input field
                if (this.elements.findBarcodeInput) {
                    this.elements.findBarcodeInput.addEventListener('input', (e) => {
                        this.handleScannerInput(e);
                    });

                    // Deteksi keydown untuk scanner yang menggunakan Enter
                    this.elements.findBarcodeInput.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            this.handleScannerEnter(e);
                        }
                    });
                }

                // Global scanner detection (ketika tidak ada focus pada input)
                document.addEventListener('keypress', (e) => {
                    this.handleGlobalScannerInput(e);
                });
            }

            /**
             * Bind modal close events
             */
            bindModalCloseEvents() {
                // Close modal when clicking outside
                document.addEventListener('click', (e) => {
                    if (e.target.classList.contains('modal-overlay')) {
                        this.hideCurrentModal();
                    }
                });
            }

            /**
             * Bind keyboard events
             */
            bindKeyboardEvents() {
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        this.hideCurrentModal();
                    }
                });
            }

            /**
             * Handle barcode form submission
             */
            handleBarcodeSubmit() {
                const barcode = this.elements.findBarcodeInput.value.trim();

                if (this.validateBarcode(barcode)) {
                    this.findBarcode(barcode);
                    this.showLoading();
                }
            }

            async handleBarangChange(e) {
                const selectedValue = e.target.value;

                if (!selectedValue) return;

                try {
                    const satuan = await axios.post(
                        "{{ route('barang.ki.get-satuan-convert-barang') }}", {
                            barang_id: selectedValue,
                        }
                    );

                    if (!satuan.data.success) {
                        throw new Error(satuan.data.message || 'Gagal mengambil data');
                    }

                    this.updateSatuanOptions(satuan.data.data);
                } catch (error) {
                    showToast('error', error.message || 'Gagal mengambil data', 'Error!');
                }
            }

            updateSatuanOptions(satuanData) {
                // This would update the satuan dropdown based on the response
                // Implementation depends on your API response structure
                if (satuanData && Array.isArray(satuanData)) {
                    // Update satuan select options
                    const satuanSelect = this.elements.satuanSelect;
                    if (satuanSelect) {
                        // Clear existing options except the first one
                        while (satuanSelect.children.length > 0) {
                            satuanSelect.removeChild(satuanSelect.lastChild);
                        }

                        // Add new options
                        satuanData.forEach(satuan => {
                            const option = document.createElement('option');
                            option.value = satuan.id;
                            option.textContent = satuan.name;
                            if (satuan.selected) {
                                option.selected = true;
                            }
                            option.setAttribute('data-hs-select-option', JSON.stringify({
                                icon: `<div class="shrink-0 size-5 text-xs text-gray-500"><i class="fas fa-l"></i>${satuan.level}</div>`
                            }));
                            satuanSelect.appendChild(option);
                        });

                        // Trigger HSSelect update if using HSSelect
                        if (window.HSSelect) {
                            window.HSSelect.getInstance(satuanSelect)?.destroy();
                            window.HSSelect.autoInit();
                        }
                    } else {
                        showToast('error', 'Gagal mengambil data', 'Error!');
                    }
                }
            }

            async handleSubmit(e) {
                e.preventDefault();

                // Validate all fields
                const validation = this.validateAllFields();

                if (!validation.isValid) {
                    this.showValidationSummary(validation.errors);
                    this.focusFirstError();
                    return;
                }

                // Show loading state
                this.showLoading();

                try {
                    // Prepare form data
                    const formData = new FormData(this.form);

                    // Add CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                        document.querySelector('input[name="_token"]')?.value;

                    if (csrfToken) {
                        formData.append('_token', csrfToken);
                    }

                    // Submit to server
                    const response = await axios.post(this.config.addnewBarangUrl, formData, {
                        timeout: this.config.apiTimeout,
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });

                    this.hideLoading();

                    if (response.data.success) {
                        showToast('success', response.data.message || 'Barang berhasil disimpan', 'Success!');
                        this.showFindBarcodeModal();
                    } else {
                        throw new Error(response.data.message || 'Gagal menyimpan barang');
                    }

                } catch (error) {
                    this.hideLoading();

                    const errorMessage = error.response?.data?.message || error.message;
                    const errorErrors = error.response?.data?.errors;

                    if (errorErrors) {
                        this.howServerValidationErrors(errorErrors);
                    } else {
                        showToast('error', errorMessage, 'Terjadi kesalahan');
                    }
                }
            }

            howServerValidationErrors(errors) {
                Object.keys(errors).forEach(fieldName => {
                    const field = this.form.querySelector(`[name="${fieldName}"]`);
                    if (field && errors[fieldName][0]) {
                        this.showFieldValidation(field, false, errors[fieldName][0]);
                    }
                });

                this.focusFirstError();
            }

            showValidationSummary(errors) {
                const errorMessages = {
                    barang_id: 'Barang harus dipilih',
                    id_barcode: 'ID Barcode tidak valid',
                    satuan_id: 'Satuan harus dipilih',
                    stock: 'Quantity tidak valid',
                    price_buy: 'Harga beli tidak valid',
                    price_sell: 'Harga jual tidak valid',
                    expired_time: 'Tanggal kadaluwarsa tidak valid',
                    status: 'Status harus dipilih',
                    price_comparison: 'Harga jual tidak boleh lebih rendah dari harga beli',
                    expired_date: 'Tanggal kadaluwarsa harus di masa depan'
                };

                const messages = errors.map(error => errorMessages[error] || error).join(', ');
                showToast('warning', `Mohon perbaiki: ${messages}`, 'Perhatian!');
            }

            focusFirstError() {
                const firstError = this.form.querySelector('.border-red-500');
                if (firstError) {
                    firstError.focus();
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }

            setSubmitLoading(loading) {
                if (!this.submitBtn) return;

                if (loading) {
                    this.submitBtn.disabled = true;
                    this.submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
                } else {
                    this.submitBtn.disabled = false;
                    this.submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Simpan Barang';
                }
            }

            handleReset(e) {
                if (e) e.preventDefault();

                // Reset form
                this.form.reset();

                // Clear all validation states
                const fields = this.form.querySelectorAll('input, select');
                fields.forEach(field => {
                    this.clearFieldError(field);
                });

                const selects = this.form.querySelectorAll('select');
                selects.forEach(select => {
                    const instance = window.HSSelect?.getInstance(select);

                    // 1. Hapus semua opsi lama dari satuanSelect
                    if (select === this.elements.satuanSelect) {
                        if (instance) instance.destroy();

                        // Kosongkan isi select
                        select.innerHTML = '';

                        // Tambahkan opsi default yang disabled & selected
                        const option = document.createElement('option');
                        option.value = '1';
                        option.textContent = 'Pilih Barang Terlebih Dahulu';
                        option.disabled = true;
                        option.selected = true;

                        select.appendChild(option);

                        select.value = '';
                    } else {
                        if (instance) instance.destroy();
                        select.value = '';
                    }
                });

                // 3. Inisialisasi ulang semua HSSelect
                window.HSSelect?.autoInit();

                // 4. Fokus ke field pertama
                const firstField = this.form.querySelector('input, select');
                if (firstField) {
                    firstField.focus();
                }

            }

            handleScannerInput(e) {
                const value = e.target.value;

                // Clear timer sebelumnya
                if (this.scannerTimer) {
                    clearTimeout(this.scannerTimer);
                }

                // Set timer untuk mendeteksi akhir input scanner
                this.scannerTimer = setTimeout(() => {
                    this.processScannerInput(value);
                }, this.config.scannerTimeout);
            }

            // Method untuk menangani Enter dari scanner
            handleScannerEnter(e) {
                const value = e.target.value.trim();

                if (value.length >= this.config.minBarcodeLength) {
                    e.preventDefault();
                    this.processScannerInput(value);
                }
            }

            // Method untuk menangani scanner input global
            handleGlobalScannerInput(e) {
                // Hanya proses jika tidak ada input yang sedang focus
                if (document.activeElement.tagName !== 'INPUT' &&
                    document.activeElement.tagName !== 'TEXTAREA') {

                    const char = String.fromCharCode(e.which || e.keyCode);

                    // Hanya proses karakter angka dan huruf
                    if (/[0-9a-zA-Z]/.test(char)) {
                        this.scannerBuffer += char;

                        // Clear timer sebelumnya
                        if (this.scannerTimer) {
                            clearTimeout(this.scannerTimer);
                        }

                        // Set timer untuk mendeteksi akhir scan
                        this.scannerTimer = setTimeout(() => {
                            if (this.scannerBuffer.length >= this.config.minBarcodeLength) {
                                this.processGlobalScannerInput(this.scannerBuffer);
                            }
                            this.scannerBuffer = '';
                        }, this.config.scannerTimeout);
                    }
                }
            }

            // Method untuk memproses input scanner dari input field
            processScannerInput(barcode) {
                if (barcode && barcode.length >= this.config.minBarcodeLength) {
                    // Deteksi apakah input berasal dari scanner (input cepat)
                    if (this.isScannerInput(barcode)) {
                        this.handleBarcodeSubmit();
                    }
                }
            }

            // Method untuk memproses input scanner global
            processGlobalScannerInput(barcode) {
                // Jika modal barcode sedang terbuka, isi input dan submit
                if (this.elements.findBarcodeModal &&
                    !this.elements.findBarcodeModal.classList.contains('hidden')) {

                    this.elements.findBarcodeInput.value = barcode;
                    this.handleBarcodeSubmit();
                } else {
                    // Jika modal tidak terbuka, buka modal dan isi barcode
                    this.showFindBarcodeModal();
                    setTimeout(() => {
                        this.elements.findBarcodeInput.value = barcode;
                        this.handleBarcodeSubmit();
                    }, 100);
                }
            }

            // Method untuk mendeteksi apakah input berasal dari scanner
            isScannerInput(value) {
                // Scanner biasanya input dengan sangat cepat
                // Ini adalah heuristik sederhana untuk mendeteksi scanner
                return value.length >= this.config.minBarcodeLength &&
                    /^[0-9]+$/.test(value); // Barcode biasanya hanya angka
            }

            /**
             * Validate barcode input
             * @param {string} barcode - Barcode to validate
             * @returns {boolean} - Validation result
             */
            validateBarcode(barcode) {
                if (!barcode) {
                    this.showMessage('Barcode tidak boleh kosong', 'error');
                    return false;
                }

                if (barcode.length < this.config.minBarcodeLength) {
                    this.showMessage(`Barcode minimal ${this.config.minBarcodeLength} karakter`, 'error');
                    return false;
                }

                if (isNaN(parseInt(barcode))) {
                    this.showMessage('Barcode harus berupa angka', 'error');
                    return false;
                }

                return true;
            }

            /**
             * Find barcode via API
             * @param {string} barcode - Barcode to search
             */
            async findBarcode(barcode) {
                try {

                    const response = await axios.post(this.config.findBarcodeUrl, {
                        barcode
                    });

                    this.handleBarcodeResponse(response.data);

                } catch (error) {
                    this.handleBarcodeError(error);
                } finally {
                    this.hideLoading();
                }
            }

            async addStock() {
                try {
                    this.showLoading();

                    const response = await axios.post(this.config.addStockUrl, {
                        barcode: this.barcode,
                        quantity: this.elements.addStockInput.value
                    });

                    this.handleAddStockResponse(response.data);

                } catch (error) {
                    this.handleAddStockError(error);
                } finally {
                    this.hideLoading();
                }
            }

            /**
             * Handle successful barcode response
             * @param {Object} data - Response data
             */
            handleBarcodeResponse(data) {
                if (data.success && data.data) {
                    this.populateItemDetails(data.data);
                    this.showItemDetailModal();
                    this.hideFindBarcodeModal();
                } else {
                    this.hideFindBarcodeModal();
                    this.elements.idBarcodeInput.value = data.barcode;
                }
            }

            /**
             * Handle barcode search error
             * @param {Error} error - Error object
             */
            handleBarcodeError(error) {
                const errorMessage = error.response?.data?.message ||
                    error.message ||
                    'Terjadi kesalahan saat mencari barcode';

                this.showMessage(errorMessage, 'error');
                setTimeout(() => this.showFindBarcodeModal(), 3000);
            }

            /**
             * Handle successful add stock response
             * @param {Object} data - Response data
             */
            handleAddStockResponse(data) {
                if (data.success) {
                    this.showMessage(data.message || 'Stock berhasil ditambahkan', 'success');
                    this.hideAddStockModal();
                    this.findBarcode(this.barcode);
                } else {
                    this.showMessage(data.message || 'Gagal menambahkan stock', 'error');
                }
            }

            /**
             * Handle add stock error
             * @param {Error} error - Error object
             */
            handleAddStockError(error) {
                const errorMessage = error.response?.data?.message ||
                    error.message ||
                    'Terjadi kesalahan saat menambahkan stock';

                this.showMessage(errorMessage, 'error');
            }

            /**
             * Populate item details in modal
             * @param {Object} item - Item data
             */
            populateItemDetails(item) {
                this.barcode = item.id_barcode;

                // Basic information
                this.setElementText('detailNama', item.name);
                this.setElementText('detailBarcode', item.id_barcode);
                this.setElementText('detailSatuan', item.satuan?.name);
                this.setElementText('detailQuantity', item.quantity);
                this.setElementText('detailStatus', item.status);

                // Prices
                this.setElementText('detailPriceBuy', this.formatCurrency(item.price_buy));
                this.setElementText('detailPriceSell', this.formatCurrency(item.price_sell));
                this.setElementText('detailPriceUp', this.formatPercentage(item.price_up));

                // Dates
                this.setElementText('detailExpiredDate', this.formatDate(item.expired_time_date));
                this.setElementText('detailExpiredTime', item.expired_time_time);

                // Discount information
                this.setElementText('detailDiscountStart', this.formatDate(item.discount_start));
                this.setElementText('detailDiscountEnd', this.formatDate(item.discount_end));
                this.setElementText('detailDiscountType', item.discount_type || 'Percentage');
                this.setElementText('detailDiscount', this.formatDiscount(item.discount, item.discount_type));

                this.elements.addStockInput.value = item.quantity;
                this.elements.addStockSatuan.textContent = item.satuan?.cut_name;
                this.elements.addStockInfo.textContent =
                    `Minimal 1 ${item.satuan?.cut_name}, maksimal 9999 ${item.satuan?.cut_name}`;
            }

            /**
             * Set element text content with fallback
             * @param {string} elementId - Element ID
             * @param {*} value - Value to set
             * @param {string} fallback - Fallback text
             */
            setElementText(elementId, value, fallback = 'Tidak tersedia') {
                const element = this.elements[elementId];
                if (element) {
                    element.textContent = value || fallback;
                }
            }

            /**
             * Format currency value
             * @param {number} value - Currency value
             * @returns {string} - Formatted currency
             */
            formatCurrency(value) {
                if (!value) return 'Rp 0';

                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(value);
            }

            /**
             * Format percentage value
             * @param {number} value - Percentage value
             * @returns {string} - Formatted percentage
             */
            formatPercentage(value) {
                if (!value) return '0%';
                return `${Math.round(value * 10) / 10}%`;
            }

            /**
             * Format date value
             * @param {string} date - Date string
             * @returns {string} - Formatted date
             */
            formatDate(date) {
                if (!date) return 'Tidak tersedia';

                try {
                    return new Date(date).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                } catch (error) {
                    return 'Format tanggal tidak valid';
                }
            }

            /**
             * Format discount value
             * @param {number} value - Discount value
             * @param {string} type - Discount type
             * @returns {string} - Formatted discount
             */
            formatDiscount(value, type) {
                if (!value) return '0';

                if (type === 'percentage') {
                    return `${value}%`;
                }

                return this.formatCurrency(value);
            }

            /**
             * Show barcode search modal
             * @param {boolean} show - Show or hide modal
             */
            showFindBarcodeModal(show = true) {
                const modal = this.elements.findBarcodeModal;
                if (!modal) return;
                if (show) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    this.resetBarcodeForm();
                    this.focusBarcodeInput();
                    this.handleReset()

                    // Clear scanner buffer ketika modal dibuka
                    this.scannerBuffer = '';
                    if (this.scannerTimer) {
                        clearTimeout(this.scannerTimer);
                    }
                } else {
                    this.hideFindBarcodeModal();
                }
            }

            /**
             * Hide barcode search modal
             */
            hideFindBarcodeModal() {
                const modal = this.elements.findBarcodeModal;
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }

            /**
             * Show item detail modal
             */
            showItemDetailModal() {
                const modal = this.elements.modalDetailBarang;
                const content = this.elements.modalContent;

                if (modal && content) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');

                    // Animate modal appearance
                    setTimeout(() => {
                        content.classList.remove('scale-95', 'opacity-0');
                        content.classList.add('scale-100', 'opacity-100');
                    }, 10);
                }
            }

            /**
             * Hide item detail modal
             * @param {boolean} showBarcodeModal - Whether to show barcode modal after hiding
             */
            hideModalDetailBarang(showBarcodeModal = true) {
                const modal = this.elements.modalDetailBarang;
                const content = this.elements.modalContent;

                if (modal && content) {
                    // Animate modal disappearance
                    content.classList.remove('scale-100', 'opacity-100');
                    content.classList.add('scale-95', 'opacity-0');

                    setTimeout(() => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');

                        if (showBarcodeModal) {
                            this.showFindBarcodeModal();
                        }
                    }, 300);
                }
            }

            /**
             * Hide add stock modal
             */
            hideAddStockModal() {
                const modal = this.elements.addStockModal;
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }

            /**
             * Show loading overlay
             */
            showLoading() {
                const overlay = this.elements.loadingOverlay;
                if (overlay) {
                    overlay.classList.remove('hidden');
                }
            }

            /**
             * Hide loading overlay
             */
            hideLoading() {
                const overlay = this.elements.loadingOverlay;
                if (overlay) {
                    overlay.classList.add('hidden');
                }
            }
            setScannerConfig(config) {
                this.config = {
                    ...this.config,
                    ...config
                };
            }

            // Method untuk enable/disable scanner
            enableScanner(enable = true) {
                this.scannerEnabled = enable;
            }
            /**
             * Show message modal
             * @param {string} message - Message to display
             * @param {string} type - Message type (success, error, warning)
             * @param {number} duration - Auto-hide duration in milliseconds
             */
            showMessage(message, type = 'info', duration = 3000) {
                const modal = this.elements.messageModal;
                const title = this.elements.modalTitle;
                const messageEl = this.elements.modalMessage;
                const icon = this.elements.modalIcon;
                const actionButtons = this.elements.actionButtons;

                if (!modal || !title || !messageEl || !icon) return;

                // Configure modal content
                const config = this.getMessageConfig(type);

                title.textContent = config.title;
                messageEl.textContent = message;
                icon.className = config.iconClass;

                // Show modal
                modal.classList.remove('hidden');
                modal.classList.add('flex');

                // Hide action buttons for auto-close messages
                if (actionButtons) {
                    actionButtons.classList.add('hidden');
                }

                // Auto-hide if duration is specified
                if (duration > 0) {
                    setTimeout(() => this.closeModal(), duration);
                }
            }

            /**
             * Get message configuration based on type
             * @param {string} type - Message type
             * @returns {Object} - Message configuration
             */
            getMessageConfig(type) {
                const configs = {
                    success: {
                        title: 'Berhasil',
                        iconClass: 'fas fa-check-circle text-6xl text-green-500'
                    },
                    error: {
                        title: 'Error',
                        iconClass: 'fas fa-circle-exclamation text-6xl text-red-500'
                    },
                    warning: {
                        title: 'Peringatan',
                        iconClass: 'fas fa-triangle-exclamation text-6xl text-yellow-500'
                    },
                    info: {
                        title: 'Informasi',
                        iconClass: 'fas fa-info-circle text-6xl text-blue-500'
                    }
                };

                return configs[type] || configs.info;
            }

            /**
             * Close message modal
             */
            closeModal() {
                const modal = this.elements.messageModal;
                if (modal) {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                }
            }

            /**
             * Hide current visible modal
             */
            hideCurrentModal() {
                if (this.elements.messageModal && !this.elements.messageModal.classList.contains('hidden')) {
                    this.closeModal();
                } else if (this.elements.modalDetailBarang && !this.elements.modalDetailBarang.classList.contains(
                        'hidden')) {
                    this.hideModalDetailBarang();
                } else if (this.elements.findBarcodeModal && !this.elements.findBarcodeModal.classList.contains(
                        'hidden')) {
                    // Don't hide barcode modal with Escape, as it's the main entry point
                } else if (this.elements.addStockModal && !this.elements.addStockModal.classList.contains('hidden')) {
                    this.hideAddStockModal();
                }
            }

            /**
             * Reset barcode form
             */
            resetBarcodeForm() {
                if (this.elements.findBarcodeInput) {
                    this.elements.findBarcodeInput.value = '';
                }
                this.barcode = null;
            }

            /**
             * Focus barcode input
             */
            focusBarcodeInput() {
                if (this.elements.findBarcodeInput) {
                    setTimeout(() => {
                        this.elements.findBarcodeInput.focus();
                    }, 100);
                }
            }

            /**
             * Edit item - navigate to edit page
             */
            editBarang() {
                if (this.barcode) {
                    // Implement edit navigation logic here
                    console.log('Navigating to edit page for barcode:', this.barcode);
                    this.elements.addStockModal.classList.remove('hidden');
                    this.elements.addStockModal.classList.add('flex');
                } else {
                    this.showMessage('Barcode tidak ditemukan', 'error');
                }
            }

            /**
             * Scroll to top of page
             */
            scrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            /**
             * Utility method to debounce function calls
             * @param {Function} func - Function to debounce
             * @param {number} wait - Wait time in milliseconds
             * @returns {Function} - Debounced function
             */
            debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
        }

        // Global functions for backward compatibility
        window.hideModal = () => {
            if (window.formManager) {
                window.formManager.closeModal();
            }
        };

        window.editBarang = () => {
            if (window.formManager) {
                window.formManager.editBarang();
            }
        };

        window.confirmAction = () => {
            // Implement confirmation action logic
            console.log('Confirm action triggered');
            if (window.formManager) {
                window.formManager.closeModal();
            }
        };
        document.addEventListener('DOMContentLoaded', function() {
            window.formManager = new FormManager({
                findBarcodeUrl: '{{ route('barang.ki.find-barcode') }}',
                addStockUrl: '{{ route('barang.ki.add-stock') }}',
                addnewBarangUrl: '{{ route('barang.ki.store') }}',
                previousUrl: '{{ url()->previous() }}',
                scannerTimeout: 1000, // 100ms timeout untuk scanner
                minBarcodeLength: 8, // minimal 8 karakter
            });

            // Optional: Set focus ke input barcode saat halaman dimuat
            // untuk memastikan scanner bisa langsung bekerja
            setTimeout(() => {
                const barcodeInput = document.getElementById('findBarcodeInput');
                if (barcodeInput) {
                    barcodeInput.focus();
                }
            }, 500);
        });
    </script>
@endpush
