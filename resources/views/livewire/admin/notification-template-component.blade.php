<div>
    <div class="my-8 overflow-hidden rounded-lg bg-white shadow-lg">
        <!-- Header Section -->
        <div class="flex flex-col items-center justify-between space-y-4 border-b border-gray-200 p-5 lg:flex-row lg:space-y-0">
            <h1 class="text-2xl font-bold text-gray-800">Format Notifikasi (Debug)</h1>

            <div class="flex w-full flex-col space-y-4 lg:w-2/3 lg:flex-row lg:items-center lg:justify-end lg:space-x-4 lg:space-y-0">
                <!-- Search Bar -->
                <div class="relative w-full lg:w-1/2">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input wire:model.live.debounce.500ms="search" type="text" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 pl-10 text-sm text-gray-900 focus:border-red-500 focus:ring-red-500" placeholder="Cari tipe atau judul template...">
                </div>

                <!-- Add Button -->
                <button wire:click="create" class="inline-flex items-center rounded-lg bg-red-600 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                    </svg>
                    Tambah Format
                </button>
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tipe / Key</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Judul & Pesan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Path</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($templates as $template)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="rounded bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-800">{{ $template->type }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $template->title_template }}</div>
                                <div class="mt-1 text-sm text-gray-500">{{ Str::limit($template->message_template, 100) }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-gray-500">
                                <code>{{ $template->path_template ?: '-' }}</code>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                <button wire:click="toggleStatus('{{ $template->id }}')" class="relative inline-flex h-6 w-11 items-center rounded-full {{ $template->is_active ? 'bg-green-600' : 'bg-gray-200' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $template->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                <button wire:click="edit('{{ $template->id }}')" class="mr-3 text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="confirm('Yakin ingin menghapus?') || event.stopImmediatePropagation()" wire:click="delete('{{ $template->id }}')" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-gray-500">Data tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">
            {{ $templates->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-gray-900 bg-opacity-50 px-4">
        <div class="w-full max-w-2xl rounded-lg bg-white shadow-xl">
            <div class="flex items-center justify-between border-b p-4">
                <h3 class="text-xl font-semibold text-gray-900">
                    {{ $isEdit ? 'Edit Format Notifikasi' : 'Tambah Format Notifikasi Baru' }}
                </h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-900">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                <div class="space-y-4 p-6">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-900">Tipe / Key Notifikasi</label>
                        <input wire:model="type" type="text" class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-red-500 focus:ring-red-500" placeholder="misal: order_status_changed" required>
                        @error('type') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-900">Judul Template</label>
                        <input wire:model="title_template" type="text" class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-red-500 focus:ring-red-500" placeholder="misal: Kedai Indonesia - {transaction_id}" required>
                        @error('title_template') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-900">Isi Pesan Template</label>
                        <textarea wire:model="message_template" rows="4" class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Gunakan variabel seperti {transaction_id}, {status_label}" required></textarea>
                        @error('message_template') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-900">Path Template (URL)</label>
                        <input wire:model="path_template" type="text" class="block w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-red-500 focus:ring-red-500" placeholder="misal: /toko/orders/{transaction_id}">
                        @error('path_template') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="rounded-lg bg-blue-50 p-4">
                        <h4 class="mb-1 text-sm font-bold text-blue-800">Petunjuk Variabel:</h4>
                        <p class="text-xs text-blue-700">Anda bisa menggunakan variabel di dalam kurung kurawal seperti <code>{transaction_id}</code>, <code>{status_label}</code>, <code>{total}</code>, <code>{user_name}</code> yang akan diganti otomatis oleh sistem saat notifikasi dikirim.</p>
                    </div>

                    <div class="flex items-center">
                        <input wire:model="is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <label class="ml-2 text-sm font-medium text-gray-900">Aktifkan Template Ini</label>
                    </div>
                </div>

                <div class="flex items-center space-x-2 border-t p-6">
                    <button type="submit" class="rounded-lg bg-red-600 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300">
                        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Format' }}
                    </button>
                    <button wire:click="closeModal" type="button" class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-200">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
