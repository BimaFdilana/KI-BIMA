@extends('layouts.admin')
@section('nav_title', 'Database Barang')

@section('page_title', 'Data Barang')
@section('content')
    <div class="mx-auto ">
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
                            <a href="{{ route('barang.index') }}"
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
                                        data-dropdown-placement="left-start" data-dropdown-trigger="hover" type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100"><i
                                            class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Filter by status</span>
                                    </button>
                                    <div id="statusDropdown"
                                        class="z-10 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow-sm">
                                        <ul class="py-2 text-sm text-gray-700" aria-labelledby="doubleDropdownButton">
                                            <li>
                                                <a href="{{ route('barang.index') }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Semua</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('barang.index', ['filter' => ['status' => 'active']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Aktif</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('barang.index', ['filter' => ['status' => 'nonactive']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Inaktif</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('barang.index', ['filter' => ['status' => 'deleted']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Deleted</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <button id="roleDropdownButton" data-dropdown-trigger="hover"
                                        data-dropdown-toggle="roleDropdown" data-dropdown-placement="left-start"
                                        type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100"><i
                                            class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Lainnya</span>
                                    </button>
                                    <div id="roleDropdown"
                                        class="z-10 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow-sm">
                                        <ul class="py-2 text-sm text-gray-700" aria-labelledby="doubleDropdownButton">
                                            @foreach ($types as $type)
                                                <li>
                                                    <a href="{{ route('barang.index', ['filter' => ['type' => $type->id]]) }}"
                                                        class="block px-4 py-2 hover:bg-gray-100">{{ $type->name }}</a>
                                                </li>
                                            @endforeach
                                            <li>
                                                <a href="{{ route('barang.index', ['filter' => ['more' => 'new_added']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Baru Ditambahkan</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @can('import.barang')
                        {{-- Download Template --}}
                        <div class="relative">
                            <button id="btnDownloadTemplate" type="button"
                                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-200">
                                <svg class="mr-2 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Template
                            </button>
                        </div>

                        {{-- Import Excel --}}
            <div class="relative">
                            <button id="btnOpenImport" type="button"
                                class="inline-flex items-center rounded-lg border border-green-600 bg-green-600 px-4 py-2 text-center text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-200">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Import Excel
                            </button>
                        </div>
                    @endcan
                    @can('create.barang')
                        <div class="relative">
                            <a href="{{ route('barang.tambah-barang') }}"
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
                        <input type="hidden" action="" id="barangId">
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

    {{-- ======================================================
         IMPORT MODAL
         ====================================================== --}}
    <div id="importModal"
        class="z-50 fixed inset-0 hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-black/60 backdrop-blur-sm">
        <div class="relative w-full max-w-lg mx-4">
            <div class="animate-jump-in relative rounded-2xl bg-white shadow-2xl overflow-hidden">

                {{-- Header --}}
                <div class="flex items-center justify-between bg-gradient-to-r from-green-600 to-emerald-500 px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-white/20">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white">Import Data Barang</h3>
                            <p class="text-xs text-green-100">Upload file Excel (.xlsx / .xls)</p>
                        </div>
                    </div>
                    <button id="closeImportModal" type="button"
                        class="rounded-lg p-1.5 text-white/80 hover:bg-white/20 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-5">

                    {{-- Info petunjuk --}}
                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                        <div class="flex items-start space-x-3">
                            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div class="text-sm text-blue-700">
                                <p class="font-semibold mb-1">Petunjuk Import:</p>
                                <ul class="list-disc list-inside space-y-0.5 text-xs">
                                    <li>Gunakan template resmi (klik tombol <strong>Template</strong>)</li>
                                    <li>Kolom <strong>KODE_BARANG, NAMA, BRAND, TIPE_BARANG, SATUAN_1, ISI_1</strong> wajib diisi</li>
                                    <li>SATUAN harus terdaftar di master satuan</li>
                                    <li>KODE_BARCODE, HPP, HARGA_JUAL akan disimpan sebagai data harga referensi</li>
                                    <li>Ukuran file maksimal <strong>10 MB</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Drop zone --}}
                    <div id="importDropZone"
                        class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center transition hover:border-green-400 hover:bg-green-50 cursor-pointer group">
                        <input type="file" id="importFileInput" accept=".xlsx,.xls" class="absolute inset-0 opacity-0 cursor-pointer z-10" />
                        <div id="dropZoneDefault">
                            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-green-100 group-hover:bg-green-200 transition">
                                <svg class="h-7 w-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Drag & drop file Excel di sini</p>
                            <p class="text-xs text-gray-500 mt-1">atau klik untuk memilih file</p>
                            <p class="mt-2 text-xs text-gray-400">.xlsx, .xls • maks. 10 MB</p>
                        </div>
                        <div id="dropZoneSelected" class="hidden w-full">
                            <div class="flex items-center justify-center space-x-3">
                                <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-left">
                                    <p id="selectedFileName" class="text-sm font-semibold text-gray-800 truncate max-w-xs"></p>
                                    <p id="selectedFileSize" class="text-xs text-gray-500"></p>
                                </div>
                            </div>
                            <button type="button" id="clearFileBtn"
                                class="mt-3 text-xs text-red-500 hover:text-red-700 underline">
                                Ganti file
                            </button>
                        </div>
                    </div>

                    {{-- Progress bar (hidden by default) --}}
                    <div id="importProgress" class="hidden">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium text-gray-600">Mengupload & memproses...</span>
                            <span id="importProgressPct" class="text-xs font-semibold text-green-600">0%</span>
                        </div>
                        <div class="h-2 rounded-full bg-gray-200 overflow-hidden">
                            <div id="importProgressBar"
                                class="h-2 rounded-full bg-gradient-to-r from-green-500 to-emerald-400 transition-all duration-300"
                                style="width: 0%"></div>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end space-x-3 border-t border-gray-100 px-6 py-4 bg-gray-50">
                    <button type="button" id="cancelImportBtn"
                        class="rounded-lg border border-gray-300 bg-white px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                        Batal
                    </button>
                    <button type="button" id="submitImportBtn"
                        class="inline-flex items-center rounded-lg bg-green-600 px-5 py-2 text-sm font-bold text-white hover:bg-green-700 focus:ring-4 focus:ring-green-200 disabled:opacity-50 disabled:cursor-not-allowed transition"
                        disabled>
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span id="submitImportLabel">Mulai Import</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================
         IMPORT RESULT MODAL
         ====================================================== --}}
    <div id="importResultModal"
        class="z-50 fixed inset-0 hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-black/60 backdrop-blur-sm">
        <div class="relative w-full max-w-lg mx-4">
            <div class="animate-jump-in rounded-2xl bg-white shadow-2xl overflow-hidden">

                {{-- Header --}}
                <div id="resultModalHeader"
                    class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div id="resultModalIcon" class="flex h-9 w-9 items-center justify-center rounded-full"></div>
                        <h3 id="resultModalTitle" class="text-base font-bold"></h3>
                    </div>
                    <button id="closeResultModal" type="button"
                        class="rounded-lg p-1.5 hover:bg-black/10 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Summary cards --}}
                <div class="grid grid-cols-4 gap-3 px-6 pb-4">
                    <div class="rounded-xl bg-green-50 p-3 text-center">
                        <p id="summaryCreated" class="text-2xl font-bold text-green-600">0</p>
                        <p class="text-xs text-gray-500 mt-0.5">Baru</p>
                    </div>
                    <div class="rounded-xl bg-blue-50 p-3 text-center">
                        <p id="summaryUpdated" class="text-2xl font-bold text-blue-600">0</p>
                        <p class="text-xs text-gray-500 mt-0.5">Diupdate</p>
                    </div>
                    <div class="rounded-xl bg-yellow-50 p-3 text-center">
                        <p id="summarySkipped" class="text-2xl font-bold text-yellow-600">0</p>
                        <p class="text-xs text-gray-500 mt-0.5">Dilewati</p>
                    </div>
                    <div class="rounded-xl bg-red-50 p-3 text-center">
                        <p id="summaryErrors" class="text-2xl font-bold text-red-600">0</p>
                        <p class="text-xs text-gray-500 mt-0.5">Error</p>
                    </div>
                </div>

                {{-- Error list --}}
                <div id="errorListWrapper" class="hidden px-6 pb-4">
                    <p class="text-xs font-semibold text-red-600 mb-2">Detail error:</p>
                    <div id="errorList"
                        class="max-h-48 overflow-y-auto rounded-lg border border-red-200 bg-red-50 p-3 space-y-1">
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end border-t border-gray-100 px-6 py-4 bg-gray-50">
                    <button type="button" id="closeResultModalBtn"
                        class="rounded-lg bg-gray-800 px-6 py-2 text-sm font-bold text-white hover:bg-gray-700 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
    <script>
        class DataTableManager {
            constructor() {
                this.currentAction = null;
                this.init();
                this.setupEventListeners();
            }

            init() {
                // DOM Elements
                this.elements = {
                    // Form elements
                    actionBtnMessage: document.getElementById('actionBtnMessage'),
                    closeBtnMessage: document.getElementById('closeBtnMessage'),

                    // Modal elements
                    messageModal: document.getElementById('message-modal'),
                    loadingOverlay: document.getElementById('loadingOverlay'),
                    modalTitle: document.getElementById('modalTitle'),
                    modalMessage: document.getElementById('modalMessage'),
                    modalIcon: document.getElementById('modalIcon'),
                    barangId: document.getElementById('barangId')
                };
            }

            setupEventListeners() {
                // Modal button listeners
                this.elements.actionBtnMessage?.addEventListener('click', () => this.handleAction());
                this.elements.closeBtnMessage?.addEventListener('click', () => this.closeModal());

                // Event delegation for DataTable buttons (use document body for better compatibility)
                document.body.addEventListener('click', (e) => {
                    if (e.target.closest('.delete-barang-button')) {
                        e.preventDefault();
                        this.handleDelete(e);
                    } else if (e.target.closest('.restore-barang-button')) {
                        e.preventDefault();
                        this.handleRestore(e);
                    }
                });
            }

            handleAction() {
                const id = this.elements.barangId.value;
                if (!id) return;

                this.showLoading(true);
                this.closeModal();
                // Perform the actual action based on currentAction
                if (this.currentAction === 'delete') {
                    this.performDelete(id);
                } else if (this.currentAction === 'restore') {
                    this.performRestore(id);
                }
            }

            handleDelete(e) {
                const button = e.target.closest('.delete-barang-button');
                const id = button?.getAttribute('data-id');

                if (!id) {
                    this.showMessage('Error', 'ID tidak ditemukan', 'error');
                    return;
                }

                this.currentAction = 'delete';
                this.elements.barangId.value = id;
                this.showMessage('Konfirmasi Hapus', 'Apakah Anda yakin ingin menghapus item ini?', 'warning');

                // Update action button text
                this.elements.actionBtnMessage.textContent = 'Hapus';
                this.elements.actionBtnMessage.className =
                    'cursor-pointer rounded-lg bg-red-500 px-6 py-2 font-bold text-white transition duration-200 hover:bg-red-600';
            }

            handleRestore(e) {
                const button = e.target.closest('.restore-barang-button');
                const id = button?.getAttribute('data-id');

                if (!id) {
                    this.showMessage('Error', 'ID tidak ditemukan', 'error');
                    return;
                }

                this.currentAction = 'restore';
                this.elements.barangId.value = id;
                this.showMessage('Konfirmasi Restore', 'Apakah Anda yakin ingin memulihkan item ini?', 'warning');

                // Update action button text
                this.elements.actionBtnMessage.textContent = 'Restore';
                this.elements.actionBtnMessage.className =
                    'cursor-pointer rounded-lg bg-green-500 px-6 py-2 font-bold text-white transition duration-200 hover:bg-green-600';
            }

            performDelete(id) {
                // Add your delete AJAX logic here
                fetch(`{{ route('barang.destroy', ['barang' => ':id']) }}`.replace(':id', id), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.showLoading(false);
                        if (data.success) {
                            this.showMessage('Berhasil', 'Item berhasil dihapus', 'success');
                            // Reload DataTable
                            if (window.LaravelDataTables && window.LaravelDataTables['barang-table']) {
                                window.LaravelDataTables['barang-table'].ajax.reload();
                            }
                        } else {
                            this.showMessage('Error', data.message || 'Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(error => {
                        this.showLoading(false);
                        this.showMessage('Error', 'Terjadi kesalahan jaringan', 'error');
                    });
            }

            performRestore(id) {
                // Add your restore AJAX logic here
                fetch(`{{ route('barang.destroy', ['barang' => ':id']) }}`.replace(':id', id), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.showLoading(false);
                        if (data.success) {
                            this.showMessage('Berhasil', 'Item berhasil dipulihkan', 'success');
                            // Reload DataTable
                            if (window.LaravelDataTables && window.LaravelDataTables['barang-table']) {
                                window.LaravelDataTables['barang-table'].ajax.reload();
                            }
                        } else {
                            this.showMessage('Error', data.message || 'Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(error => {
                        this.showLoading(false);
                        this.showMessage('Error', 'Terjadi kesalahan jaringan', 'error');
                    });
            }

            showLoading(show) {
                if (show) {
                    this.elements.loadingOverlay?.classList.remove('hidden');
                    this.elements.loadingOverlay?.classList.add('flex');
                } else {
                    this.elements.loadingOverlay?.classList.add('hidden');
                    this.elements.loadingOverlay?.classList.remove('flex');
                }
            }

            showMessage(title, message, type = 'info') {
                const modal = this.elements.messageModal;
                const titleEl = this.elements.modalTitle;
                const messageEl = this.elements.modalMessage;
                const iconEl = this.elements.modalIcon;

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

                // Show/hide action buttons based on type
                const actionButtons = document.getElementById('actionButtons');
                if (actionButtons) {
                    if (type === 'warning') {
                        actionButtons.style.display = 'flex';
                    } else {
                        actionButtons.style.display = 'none';
                    }
                }

                modal?.classList.remove('hidden');
                modal?.classList.add('flex');

                // Auto-close for success messages
                if (type === 'success') {
                    setTimeout(() => this.closeModal(), 3000);
                } else {
                    setTimeout(() => this.closeModal(), 5000);
                }
            }

            closeModal() {
                this.elements.messageModal?.classList.add('hidden');
                this.elements.messageModal?.classList.remove('flex');
                this.elements.barangId.value = '';
            }
        }

        $(function() {
            let table = $('#barang-table').DataTable();

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

            setTimeout(() => {
                new DataTableManager();
            }, 100);
        });

        /* ============================================================
           IMPORT MANAGER
           ============================================================ */
        class ImportManager {
            constructor() {
                this.selectedFile = null;
                this.isUploading  = false;

                this.els = {
                    btnOpen:        document.getElementById('btnOpenImport'),
                    btnTemplate:    document.getElementById('btnDownloadTemplate'),
                    importModal:    document.getElementById('importModal'),
                    closeModal:     document.getElementById('closeImportModal'),
                    cancelBtn:      document.getElementById('cancelImportBtn'),
                    submitBtn:      document.getElementById('submitImportBtn'),
                    submitLabel:    document.getElementById('submitImportLabel'),
                    fileInput:      document.getElementById('importFileInput'),
                    dropZone:       document.getElementById('importDropZone'),
                    dropDefault:    document.getElementById('dropZoneDefault'),
                    dropSelected:   document.getElementById('dropZoneSelected'),
                    fileName:       document.getElementById('selectedFileName'),
                    fileSize:       document.getElementById('selectedFileSize'),
                    clearFile:      document.getElementById('clearFileBtn'),
                    progressWrap:   document.getElementById('importProgress'),
                    progressBar:    document.getElementById('importProgressBar'),
                    progressPct:    document.getElementById('importProgressPct'),

                    // Result modal
                    resultModal:    document.getElementById('importResultModal'),
                    resultHeader:   document.getElementById('resultModalHeader'),
                    resultIcon:     document.getElementById('resultModalIcon'),
                    resultTitle:    document.getElementById('resultModalTitle'),
                    closeResult:    document.getElementById('closeResultModal'),
                    closeResultBtn: document.getElementById('closeResultModalBtn'),
                    summaryCreated: document.getElementById('summaryCreated'),
                    summaryUpdated: document.getElementById('summaryUpdated'),
                    summarySkipped: document.getElementById('summarySkipped'),
                    summaryErrors:  document.getElementById('summaryErrors'),
                    errorWrapper:   document.getElementById('errorListWrapper'),
                    errorList:      document.getElementById('errorList'),
                };

                this.bindEvents();
            }

            bindEvents() {
                // Open/close import modal
                this.els.btnOpen?.addEventListener('click', () => this.openModal());
                this.els.closeModal?.addEventListener('click', () => this.closeModal());
                this.els.cancelBtn?.addEventListener('click', () => this.closeModal());

                // Download template
                this.els.btnTemplate?.addEventListener('click', () => this.downloadTemplate());

                // File input change
                this.els.fileInput?.addEventListener('change', (e) => {
                    if (e.target.files && e.target.files[0]) {
                        this.setFile(e.target.files[0]);
                    }
                });

                // Drag & drop
                const dz = this.els.dropZone;
                if (dz) {
                    dz.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        dz.classList.add('border-green-400', 'bg-green-50');
                    });
                    dz.addEventListener('dragleave', () => {
                        dz.classList.remove('border-green-400', 'bg-green-50');
                    });
                    dz.addEventListener('drop', (e) => {
                        e.preventDefault();
                        dz.classList.remove('border-green-400', 'bg-green-50');
                        const file = e.dataTransfer?.files?.[0];
                        if (file) this.setFile(file);
                    });
                }

                // Clear file
                this.els.clearFile?.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.clearFile();
                });

                // Submit import
                this.els.submitBtn?.addEventListener('click', () => this.submitImport());

                // Close result modal
                this.els.closeResult?.addEventListener('click', () => this.closeResultModal());
                this.els.closeResultBtn?.addEventListener('click', () => this.closeResultModal());
            }

            openModal() {
                this.clearFile();
                this.clearProgress();
                this.els.importModal?.classList.remove('hidden');
                this.els.importModal?.classList.add('flex');
            }

            closeModal() {
                if (this.isUploading) return;
                this.els.importModal?.classList.add('hidden');
                this.els.importModal?.classList.remove('flex');
                this.clearFile();
            }

            closeResultModal() {
                this.els.resultModal?.classList.add('hidden');
                this.els.resultModal?.classList.remove('flex');
                // Reload DataTable
                if (window.LaravelDataTables?.['barang-table']) {
                    window.LaravelDataTables['barang-table'].ajax.reload();
                }
            }

            setFile(file) {
                const allowedTypes = [
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel',
                ];
                const allowedExts = ['.xlsx', '.xls'];
                const ext = '.' + file.name.split('.').pop().toLowerCase();

                if (!allowedExts.includes(ext)) {
                    alert('Format file tidak valid. Harus .xlsx atau .xls');
                    return;
                }
                if (file.size > 10 * 1024 * 1024) {
                    alert('Ukuran file melebihi 10 MB.');
                    return;
                }

                this.selectedFile = file;
                this.els.fileName.textContent = file.name;
                this.els.fileSize.textContent = this.formatBytes(file.size);
                this.els.dropDefault.classList.add('hidden');
                this.els.dropSelected.classList.remove('hidden');
                this.els.submitBtn.disabled = false;
            }

            clearFile() {
                this.selectedFile = null;
                if (this.els.fileInput) this.els.fileInput.value = '';
                this.els.dropDefault?.classList.remove('hidden');
                this.els.dropSelected?.classList.add('hidden');
                this.els.submitBtn.disabled = true;
            }

            clearProgress() {
                this.els.progressWrap?.classList.add('hidden');
                if (this.els.progressBar) this.els.progressBar.style.width = '0%';
                if (this.els.progressPct) this.els.progressPct.textContent = '0%';
            }

            setProgress(pct) {
                this.els.progressWrap?.classList.remove('hidden');
                const v = Math.min(100, Math.round(pct));
                if (this.els.progressBar) this.els.progressBar.style.width = v + '%';
                if (this.els.progressPct) this.els.progressPct.textContent = v + '%';
            }

            submitImport() {
                if (!this.selectedFile || this.isUploading) return;

                this.isUploading = true;
                this.els.submitBtn.disabled = true;
                if (this.els.submitLabel) this.els.submitLabel.textContent = 'Mengupload...';
                this.setProgress(5);

                const formData = new FormData();
                formData.append('file', this.selectedFile);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route("barang.import") }}', true);

                xhr.upload.onprogress = (e) => {
                    if (e.lengthComputable) {
                        const pct = (e.loaded / e.total) * 80;
                        this.setProgress(5 + pct);
                        if (this.els.submitLabel) this.els.submitLabel.textContent = 'Memproses...';
                    }
                };

                xhr.onload = () => {
                    this.setProgress(100);
                    this.isUploading = false;
                    this.els.submitBtn.disabled = false;
                    if (this.els.submitLabel) this.els.submitLabel.textContent = 'Mulai Import';

                    let data;
                    try { data = JSON.parse(xhr.responseText); }
                    catch (e) { data = { success: false, message: 'Respons server tidak valid.' }; }

                    // Close import modal
                    setTimeout(() => {
                        this.closeModal();
                        this.showResult(data);
                    }, 500);
                };

                xhr.onerror = () => {
                    this.isUploading = false;
                    this.els.submitBtn.disabled = false;
                    if (this.els.submitLabel) this.els.submitLabel.textContent = 'Mulai Import';
                    this.clearProgress();
                    alert('Terjadi kesalahan jaringan. Coba lagi.');
                };

                xhr.send(formData);
            }

            showResult(data) {
                const isSuccess = data.success === true;
                const header    = this.els.resultHeader;
                const iconEl    = this.els.resultIcon;
                const titleEl   = this.els.resultTitle;
                const summary   = data.summary || {};
                const errors    = data.errors   || [];

                // Header color
                if (header) {
                    header.className = 'flex items-center justify-between px-6 py-4 ' +
                        (isSuccess ? 'bg-gradient-to-r from-green-600 to-emerald-500' : 'bg-gradient-to-r from-red-600 to-rose-500');
                }

                // Icon
                if (iconEl) {
                    iconEl.className = 'flex h-9 w-9 items-center justify-center rounded-full bg-white/20';
                    iconEl.innerHTML = isSuccess
                        ? '<svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
                        : '<svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
                }

                // Title
                if (titleEl) {
                    titleEl.className = 'text-base font-bold text-white';
                    titleEl.textContent = isSuccess ? 'Import Berhasil!' : 'Import Gagal';
                }

                // Button close - update color
                const closeBtn = this.els.closeResult;
                if (closeBtn) {
                    closeBtn.className = 'rounded-lg p-1.5 text-white/80 hover:bg-white/20 hover:text-white transition';
                }

                // Summary
                if (this.els.summaryCreated) this.els.summaryCreated.textContent = summary.created ?? 0;
                if (this.els.summaryUpdated) this.els.summaryUpdated.textContent = summary.updated ?? 0;
                if (this.els.summarySkipped) this.els.summarySkipped.textContent = summary.skipped ?? 0;
                if (this.els.summaryErrors)  this.els.summaryErrors.textContent  = summary.errors  ?? errors.length;

                // Errors
                if (errors.length > 0) {
                    this.els.errorWrapper?.classList.remove('hidden');
                    if (this.els.errorList) {
                        this.els.errorList.innerHTML = errors.map(err =>
                            `<p class="text-xs text-red-700 py-0.5 border-b border-red-100 last:border-0">${this.escapeHtml(err)}</p>`
                        ).join('');
                    }
                } else {
                    this.els.errorWrapper?.classList.add('hidden');
                }

                // Show result modal
                this.els.resultModal?.classList.remove('hidden');
                this.els.resultModal?.classList.add('flex');
            }

            downloadTemplate() {
                this.els.btnTemplate.disabled = true;
                this.els.btnTemplate.innerHTML = `
                    <svg class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Mengunduh...`;

                fetch('{{ route("barang.template") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Gagal mengunduh template');
                    return response.blob();
                })
                .then(blob => {
                    const url  = window.URL.createObjectURL(blob);
                    const a    = document.createElement('a');
                    a.href     = url;
                    a.download = 'Template_Import_Barang_{{ date("Y-m-d") }}.xlsx';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                })
                .catch(err => alert(err.message))
                .finally(() => {
                    this.els.btnTemplate.disabled = false;
                    this.els.btnTemplate.innerHTML = `
                        <svg class="mr-2 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Template`;
                });
            }

            formatBytes(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
            }

            escapeHtml(text) {
                return text
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            new ImportManager();
        });
    </script>
@endpush
