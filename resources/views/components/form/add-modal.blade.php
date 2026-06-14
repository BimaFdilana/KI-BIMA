@props(['action' => '', 'method' => 'POST', 'id' => 'modal-id', 'title' => 'Modal Title', 'buttonText' => 'Submit'])

<div x-data="{ showModal: false }">
    <!-- Button to open the modal -->
    <button @click="showModal = true" class="flex items-center justify-center rounded-lg bg-red-500 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300">
        <svg class="mr-2 h-3.5 w-3.5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
        </svg>
        {{ $buttonText }}
    </button>

    <!-- Modal backdrop -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 bg-opacity-50">
        <!-- Modal content -->
        <div @click.away="showModal = false" class="transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:w-full sm:max-w-lg">
            <div class="flex items-center justify-between bg-gray-100 px-4 py-3">
                <h2 class="text-lg font-semibold">{{ $title }}</h2>
                <button @click="showModal = false" class="text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form action="{{ $action }}" method="{{ $method === 'GET' ? 'GET' : 'POST' }}" enctype="multipart/form-data">
                @csrf
                @if ($method !== 'GET' && $method !== 'POST')
                    @method($method)
                @endif
                <div class="space-y-4 px-4 py-3">
                    {{ $slot }}
                </div>
                <div class="bg-gray-100 px-4 py-3 text-right">
                    <button type="button" @click="showModal = false" class="rounded bg-gray-500 px-4 py-2 text-white">Cancel</button>
                    <button type="submit" class="rounded bg-red-600 px-4 py-2 text-white">{{ $buttonText }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
