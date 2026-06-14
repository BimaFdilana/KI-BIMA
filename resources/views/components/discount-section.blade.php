{{-- resources/views/components/discount-section.blade.php --}}
<div class="mb-8 rounded-xl border border-yellow-200 bg-gradient-to-r from-yellow-50 to-orange-50 p-6">
    <h3 class="mb-4 flex items-center text-lg font-bold text-yellow-800">
        <i class="fas fa-percent mr-2"></i>
        Informasi Diskon
    </h3>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
            <label class="text-sm font-semibold text-yellow-700">Mulai Diskon</label>
            <p id="detailDiscountStart" class="mt-1 font-medium text-gray-900">-</p>
        </div>
        <div>
            <label class="text-sm font-semibold text-yellow-700">Akhir Diskon</label>
            <p id="detailDiscountEnd" class="mt-1 font-medium text-gray-900">-</p>
        </div>
        <div>
            <label class="text-sm font-semibold text-yellow-700">Nilai Diskon</label>
            <div class="mt-1 flex items-center space-x-2">
                <span id="detailDiscountType" class="rounded-full bg-yellow-200 px-2 py-1 text-xs font-semibold text-yellow-800">
                    Percentage
                </span>
                <span id="detailDiscount" class="font-bold text-yellow-800">0%</span>
            </div>
        </div>
    </div>
</div>
