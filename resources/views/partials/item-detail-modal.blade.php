{{-- resources/views/partials/item-detail-modal.blade.php --}}
<div id="modalDetailBarang" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-black/60 p-4 backdrop-blur-sm">
    <div class="relative w-full max-w-4xl">
        <div class="scale-95 transform overflow-hidden rounded-2xl bg-white opacity-0 shadow-2xl transition-all duration-300" id="modalContent">

            {{-- Header - Fixed positioning to prevent cutting --}}
            <div class="sticky top-0 z-10 overflow-hidden bg-gradient-to-r from-red-600 to-red-700 px-8 py-6">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="rounded-lg bg-white/20 p-3">
                            <i class="fas fa-box text-xl text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">Detail Barang</h2>
                            <p class="text-sm text-red-100">Informasi lengkap produk</p>
                        </div>
                    </div>
                    <button onclick="formManager.hideModalDetailBarang()" class="rounded-lg bg-white/20 p-2 text-white transition-all duration-200 hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50" aria-label="Close">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            {{-- Content --}}
            <div class="max-h-[calc(80vh-120px)] overflow-y-auto p-8">
                {{-- Basic Information --}}
                <div class="mb-8">
                    <div class="rounded-xl border-l-4 border-red-500 bg-gradient-to-r from-red-50 to-red-100 p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div>
                                <label class="text-sm font-semibold uppercase tracking-wide text-red-700">Nama Barang</label>
                                <p id="detailNama" class="mt-2 text-lg font-bold text-gray-900">-</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold uppercase tracking-wide text-red-700">ID Barcode</label>
                                <p id="detailBarcode" class="mt-2 rounded-lg bg-white px-3 py-1 font-mono text-lg text-gray-900">-</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold uppercase tracking-wide text-red-700">Satuan</label>
                                <p id="detailSatuan" class="mt-2 text-lg text-gray-900">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Detail Information Grid --}}
                <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    {{-- Stock --}}
                    @include('components.detail-card', [
                        'id' => 'detailQuantity',
                        'icon' => 'fas fa-cubes',
                        'iconColor' => 'red',
                        'title' => 'Quantity',
                        'defaultValue' => '0',
                    ])

                    {{-- Purchase Price --}}
                    @include('components.detail-card', [
                        'id' => 'detailPriceBuy',
                        'icon' => 'fas fa-shopping-cart',
                        'iconColor' => 'green',
                        'title' => 'Harga Beli',
                        'defaultValue' => 'Rp 0',
                    ])

                    {{-- Sell Price --}}
                    @include('components.detail-card', [
                        'id' => 'detailPriceSell',
                        'icon' => 'fas fa-tag',
                        'iconColor' => 'red',
                        'title' => 'Harga Jual',
                        'defaultValue' => 'Rp 0',
                        'highlighted' => true,
                    ])

                    {{-- Price Up --}}
                    @include('components.detail-card', [
                        'id' => 'detailPriceUp',
                        'icon' => 'fas fa-arrow-up',
                        'iconColor' => 'blue',
                        'title' => 'Harga Up',
                        'defaultValue' => '0%',
                    ])

                    {{-- Status --}}
                    @include('components.detail-card', [
                        'id' => 'detailStatus',
                        'icon' => 'fas fa-check-circle',
                        'iconColor' => 'green',
                        'title' => 'Status',
                        'defaultValue' => 'Active',
                        'isBadge' => true,
                    ])

                    {{-- Expired Time --}}
                    <div class="card-hover rounded-xl border border-gray-200 bg-white p-6 transition-all duration-300 hover:shadow-lg">
                        <div class="mb-3 flex items-center justify-between">
                            <div class="rounded-lg bg-orange-100 p-2">
                                <i class="fas fa-calendar-alt text-orange-600"></i>
                            </div>
                            <div class="text-right">
                                <div id="detailExpiredDate" class="text-lg font-bold text-orange-600">-</div>
                                <div id="detailExpiredTime" class="text-sm text-orange-500">-</div>
                            </div>
                        </div>
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-600">Expired Time</h3>
                    </div>
                </div>

                {{-- Discount Information --}}
                @include('components.discount-section')
            </div>

            {{-- Fixed Action Buttons at bottom --}}
            <div class="fixed bottom-0 left-0 right-0 px-8 py-4">
                <div class="flex flex-col justify-end space-y-3 sm:flex-row sm:space-x-4 sm:space-y-0">
                    <button onclick="formManager.editBarang()" class="transform rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-6 py-3 font-semibold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-red-700 hover:to-red-800 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-red-300">
                        <i class="fas fa-edit mr-2"></i>Tambah Stock
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
