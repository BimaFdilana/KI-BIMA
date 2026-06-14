{{-- CONTOH PENGGUNAAN LOADING BUTTON COMPONENT --}}

{{-- 1. BUTTON DENGAN TEXT (untuk button seperti "Verifikasi", "Tolak", dll) --}}
@include('livewire.admin.components.loading-button', [
    'action' => "setupAction('ktp', 'verify', {$ktp->id})",
    'text' => 'Verifikasi',
    'loadingText' => 'Loading...',
    'icon' => '✓',
    'class' => 'px-6 py-3 bg-green-600 hover:bg-green-700',
])

{{-- 2. BUTTON ICON SAJA (untuk button seperti eye, check, times di toko) --}}
@include('livewire.admin.components.loading-button', [
    'action' => "showDetailModal('toko', {$toko['id']})",
    'icon' => 'fad fa-eye',
    'class' => 'py-3 px-4.5 bg-blue-100 text-blue-600 hover:bg-blue-200',
    'extraClass' =>
        'font-medium rounded-lg text-lg active:scale-95 transition-all duration-200 border border-blue-200',
])

{{-- 3. BUTTON DENGAN TEXT DAN ICON --}}
@include('livewire.admin.components.loading-button', [
    'action' => 'confirmPayment',
    'text' => 'Konfirmasi',
    'loadingText' => 'Memproses...',
    'icon' => '✓',
    'class' => 'flex-1 px-4 py-3 bg-green-600 hover:bg-green-700',
])

{{-- 4. BUTTON LIHAT DETAIL (dengan SVG icon) --}}
{{-- Untuk button dengan SVG, lebih baik tetap manual karena komponen ini untuk FontAwesome icon --}}
<button wire:click="showDetailModal('ktp', {{ $ktp->id }})" wire:loading.attr="disabled"
    wire:target="showDetailModal('ktp', {{ $ktp->id }})"
    class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove
        wire:target="showDetailModal('ktp', {{ $ktp->id }})">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
        </path>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
        </path>
    </svg>
    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" wire:loading
        wire:target="showDetailModal('ktp', {{ $ktp->id }})">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
        </path>
    </svg>
    <span wire:loading.remove wire:target="showDetailModal('ktp', {{ $ktp->id }})">Lihat Detail</span>
    <span wire:loading wire:target="showDetailModal('ktp', {{ $ktp->id }})">Loading...</span>
</button>
