@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center gap-2 mt-6">
        <!-- Previous Button -->
        @if ($paginator->onFirstPage())
            <button disabled
                class="flex cursor-not-allowed h-10 w-10 items-center justify-center rounded border border-gray-300 bg-white text-gray-400">
                <i class="fas fa-chevron-left text-sm"></i>
            </button>
        @else
            <button wire:click="previousPage('page')"
                class="flex cursor-pointer h-10 w-10 items-center justify-center rounded border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-chevron-left text-sm"></i>
            </button>
        @endif

        <!-- Page Input -->
        <div class="flex items-center gap-2">
            <input type="number" min="1" max="{{ $paginator->lastPage() }}"
                value="{{ $paginator->currentPage() }}" wire:change="gotoPage($event.target.value, 'page')"
                class="w-16 h-10 text-center border border-gray-300 rounded focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
            <span class="text-gray-600 text-sm">of {{ $paginator->lastPage() }}</span>
        </div>

        <!-- Next Button -->
        @if ($paginator->hasMorePages())
            <button wire:click="nextPage('page')"
                class="flex cursor-pointer h-10 w-10 items-center justify-center rounded border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-chevron-right text-sm"></i>
            </button>
        @else
            <button disabled
                class="flex cursor-not-allowed h-10 w-10 items-center justify-center rounded border border-gray-300 bg-white text-gray-400">
                <i class="fas fa-chevron-right text-sm"></i>
            </button>
        @endif
    </nav>
@endif
