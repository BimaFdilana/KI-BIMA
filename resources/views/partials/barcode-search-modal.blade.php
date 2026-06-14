{{-- resources/views/partials/barcode-search-modal.blade.php --}}
<div id="findBarcodeModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden bg-black/75 backdrop-blur-sm">
    <div class="modal-overlay absolute inset-0"></div>
    <div class="modal-enter modal-shadow-2 relative z-10 w-full max-w-md rounded-2xl border border-red-100 bg-white/80 p-8 backdrop-blur-lg">

        {{-- Modal Header --}}
        <div class="mb-6 flex items-start justify-between">
            <div class="text-left">
                <h3 class="text-2xl font-bold text-red-600">
                    <i class="fas fa-barcode mr-2"></i>
                    Masukan Barcode
                </h3>
            </div>
            <div class="text-right">
                <button data-tooltip-target="tooltip-info" type="button" class="text-red-600 transition-colors duration-200 hover:text-red-800" aria-label="Info">
                    <i class="fas fa-info-circle text-xl"></i>
                </button>
                <div id="tooltip-info" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-lg transition-opacity duration-300">
                    Jika barcode ada maka akan menjadi edit produk
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </div>
        </div>

        {{-- Search Form --}}
        <form id="findBarcodeForm" class="space-y-6">
            <div class="mb-6">
                <label for="findBarcodeInput" class="sr-only">Barcode</label>
                <input type="number" id="findBarcodeInput" name="findBarcode" placeholder="Masukan Barcode" autocomplete="off" required class="input-focus w-full rounded-lg border-2 border-red-200 bg-white/80 px-4 py-3 transition duration-200 focus:outline-none">
            </div>

            <button type="submit" class="w-full cursor-pointer rounded-full bg-gradient-to-r from-red-500 to-red-600 px-6 py-3 font-semibold text-white shadow-lg transition duration-200 hover:from-red-600 hover:to-red-700 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-red-300">
                <i class="fas fa-search mr-2"></i>
                Cari
            </button>

            <div class="mt-4 text-center text-xs text-gray-500">
                <a href="{{ url()->previous() }}" class="transition-colors duration-200 hover:text-gray-700">
                    Return to previous page
                </a>
            </div>
        </form>
    </div>
</div>
