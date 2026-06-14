<!-- Add Stock Modal -->
<div id="addStockModal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-black/60 backdrop-blur-sm">
    <div class="relative mx-4 w-full max-w-md">
        <!-- Modal Content -->
        <div class="transition-modal transform overflow-hidden rounded-2xl bg-white shadow-2xl">
            <!-- Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-red-600 to-red-700 px-8 py-6">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="rounded-lg bg-white/20 p-3">
                            <i class="fad fa-box text-xl text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">Tambah Stock</h2>
                            <p class="text-sm text-red-100">Tambah stock barang</p>
                        </div>
                    </div>
                    <button onclick="formManager.hideAddStockModal()" class="rounded-lg bg-white/20 p-2 text-white transition-all duration-200 hover:bg-white/30">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="p-8">
                <form id="addStockForm" class="space-y-6">
                    <div>
                        <label for="addStockInput" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-hashtag mr-2"></i>
                            Jumlah Stock
                        </label>
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <input type="number" id="addStockInput" name="quantityToAdd" required min="1" class="block w-full rounded-md border-gray-300 py-3 pr-12 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Masukkan jumlah stock">
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <span id="addStockSatuan" class="text-gray-500 sm:text-sm">pcs</span>
                            </div>
                        </div>
                        <p id="addStockInfo" class="mt-1 text-xs text-gray-500">
                            Minimal 1 pcs, maksimal 9999 pcs
                        </p>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="formManager.hideAddStockModal()" class="rounded-xl bg-gray-100 px-6 py-3 font-semibold text-gray-700 transition-all duration-200 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </button>
                        <button type="button" onclick="formManager.addStock()" class="transform rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-6 py-3 font-semibold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-red-700 hover:to-red-800 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-red-300">
                            <i class="fas fa-save mr-2"></i>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
