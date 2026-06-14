<div class="p-4 sm:ml-64">
    <div class="mt-14 rounded-lg border-2 border-dashed border-gray-200 bg-white p-4 shadow-lg">
        <button id="dropdownSearchButton" data-dropdown-toggle="dropdownSearch" data-dropdown-placement="bottom"
            class="inline-flex items-center rounded-lg bg-blue-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300"
            type="button">
            {{ $selectedBarangName ?? 'Pilih Barang' }}
            <svg class="ms-3 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 1 4 4 4-4" />
            </svg>
        </button>

        <!-- Dropdown menu -->
        <div id="dropdownSearch" class="z-10 hidden w-60 rounded-lg bg-white shadow">
            <div class="p-3">
                <label for="input-group-search" class="sr-only">Search</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="text"
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2 pl-10 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Search barang..." wire:model.live.debounce.300ms="search" />
                </div>
            </div>
            <ul class="h-48 overflow-y-auto px-3 pb-3 text-sm text-gray-700" aria-labelledby="dropdownSearchButton">
                @if (count($barangNames) > 0)
                    @foreach ($barangNames as $barang)
                        <li>
                            <div class="flex items-center rounded pl-2 hover:bg-gray-100">
                                <input type="radio" id="barang-{{ $barang['id'] }}" name="barang"
                                    wire:click="selectBarang({{ $barang['id'] }})"
                                    class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-blue-600 focus:ring-2 focus:ring-blue-500">
                                <label for="barang-{{ $barang['id'] }}"
                                    class="ml-2 w-full rounded py-2 text-sm font-medium text-gray-900">{{ $barang['name'] }}</label>
                            </div>
                        </li>
                    @endforeach
                @else
                    <li class="py-2 text-center text-gray-500">Tidak ada barang ditemukan</li>
                @endif
            </ul>
            <div
                class="flex items-center rounded-b-lg border-t border-gray-200 bg-gray-50 p-3 text-sm font-medium text-red-600 hover:bg-gray-100 hover:underline">
            </div>
        </div>
    </div>
</div>
