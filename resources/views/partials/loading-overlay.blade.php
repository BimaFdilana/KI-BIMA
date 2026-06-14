{{-- resources/views/partials/loading-overlay.blade.php --}}
<div id="loadingOverlay" class="z-60 fixed inset-0 hidden cursor-progress bg-black/50 backdrop-blur-sm">
    <div class="flex min-h-screen items-center justify-center">
        <div class="rounded-2xl bg-white p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-red-600"></div>
                <span class="font-medium text-gray-700">Memproses...</span>
            </div>
        </div>
    </div>
</div>
