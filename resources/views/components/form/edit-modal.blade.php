@props(['action' => '', 'method' => 'POST', 'id' => 'modal-id', 'title' => 'Modal Title', 'buttonText' => 'Submit'])

<div id="edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="modal-content w-1/3 rounded-lg bg-white p-6">
        <h3 class="mb-4 text-xl font-semibold">Edit Item</h3>
        <form action="{{ $action }}" method="{{ $method === 'GET' ? 'GET' : 'POST' }}" enctype="multipart/form-data">
            @csrf
            @if ($method !== 'GET' && $method !== 'POST')
                @method($method)
            @endif
            <div class="space-y-4 px-4 py-3">
                {{ $slot }}
            </div>
            <div class="flex items-center justify-end gap-2 bg-gray-100 px-4 py-3">
                <button type="button" @click="showModal = false" class="cursor-pointer rounded bg-gray-500 px-4 py-2 text-white">Cancel</button>
                <button type="submit" class="cursor-pointer rounded bg-blue-500 px-4 py-2 text-white">{{ $buttonText }}</button>
            </div>
        </form>
    </div>
</div>
