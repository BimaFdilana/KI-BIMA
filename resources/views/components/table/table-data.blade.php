@props([
    'routeAdd' => null,
    'routeExport' => null,
    'dataTable' => null, // DataTable instance
    'buttonGroup' => null, // Jumlah produk
    'title' => null,
    'tableId' => null,
])

<div class="relative overflow-hidden">
    @isset($title)
        <div class="flex flex-col pt-5 lg:flex-row lg:items-center lg:justify-between lg:space-x-4 lg:space-y-0">
            <div class="inline-flex items-center">
                <span class="mr-2 text-2xl font-bold text-gray-900">{{ $title }}</span>
            </div>
        </div>
    @endisset

    <div class="flex flex-col space-y-3 lg:flex-row lg:items-center lg:justify-between lg:space-x-4 lg:space-y-0">
        @isset($buttonGroup)
            <div class="inline-flex items-center rounded-md shadow-sm">
                <div class="relative">
                    @isset($routeExport)
                        <button data-dropdown-toggle="dropdown" class="inline-flex items-center space-x-1 rounded-l-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100 hover:text-red-600">
                            <span><i class="fad fa-download"></i></span>
                            <span> Export</span>
                        </button>
                        <div id="dropdown" class="z-10 hidden w-44 divide-y divide-gray-100 rounded bg-white shadow">
                            <ul class="py-1 text-sm text-gray-700" aria-labelledby="dropdownDefaultButton">
                                <li>
                                    <form action="{{ $routeExport }}" method="GET">
                                        <input type="hidden" value="pdf" name="format">
                                        <button type="submit">PDF</button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ $routeExport }}" method="GET">
                                        <input type="hidden" value="xlsx" name="format">
                                        <button type="submit">Excel</button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ $routeExport }}" method="GET">
                                        <input type="hidden" value="csv" name="format">
                                        <button type="submit">CSV</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endisset
                </div>
                @isset($routeExport)
                    <button id="buttonPrint" class="inline-flex items-center space-x-1 border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100 hover:text-red-600">
                        <span><i class="fad fa-print"></i></span>
                        <span> Print</span>
                    </button>
                @endisset
                <button id="buttonReload" class="@isset($routeExport) rounded-r-lg @else rounded-lg @endisset inline-flex items-center space-x-1 border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100 hover:text-red-600" data-table-id="{{ $tableId }}">
                    <span><i class="fas fa-rotate-right"></i></span>
                    <span> Reload</span>
                </button>
                <button id="buttonDelete" class="hidden items-center space-x-1 rounded-r-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-100 hover:text-red-600">
                    <span><i class="fad fa-trash"></i></span>
                    <span> Delete Select</span>
                </button>
            </div>
        @endisset
        <div class="flex space-x-2">
            <span class="inline-flex items-center py-2 text-center text-sm font-medium text-gray-500">Menampilkan</span>
            <div class="relative flex items-center md:mt-0">
                <select id="lengthMenu" class="form-select block w-20 rounded-lg border border-gray-200 bg-white text-gray-600 focus:border-red-400 focus:outline-none focus:ring focus:ring-red-300 focus:ring-opacity-40" data-table-id="{{ $tableId }}">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <span class="inline-flex items-center py-2 text-center text-sm font-medium text-gray-500">Data Perhalaman</span>
        </div>

        <div class="flex flex-col items-center justify-between space-y-3 p-4 md:flex-row md:space-x-4 md:space-y-0">
            {{-- @if ($dataTable)
                <div class="relative flex items-center md:mt-0">
                    <span class="absolute">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-3 h-5 w-5 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </span>
                    <input type="text" placeholder="Search" class="search-input block w-full rounded-lg border border-gray-200 bg-white py-1.5 pl-11 pr-5 text-gray-700 placeholder-gray-400/70 focus:border-red-400 focus:outline-none focus:ring focus:ring-red-300 focus:ring-opacity-40 md:w-80 rtl:pl-5 rtl:pr-11" data-table-id="{{ $tableId }}">
                </div>
            @endif --}}

            <div class="flex w-full flex-shrink-0 flex-col items-stretch justify-end">

                @isset($routeAdd)
                    @can('create.barang.master')
                        <x-form.add-modal action="{{ $routeAdd }}" method="POST" title="Tambah Data" buttonText="Tambah Data" enctype="multipart/form-data">
                            <div class="space-y-4">
                                @foreach ($formInputs as $input)
                                    <div>
                                        <label for="{{ $input['name'] }}" class="mb-2 block text-sm font-medium text-gray-700">{{ $input['title'] }}</label>
                                        @if ($input['type'] === 'textarea')
                                            <textarea name="{{ $input['name'] }}" id="{{ $input['name'] }}" placeholder="{{ $input['placeholder'] }}" rows="3" required class="w-full rounded-md border border-neutral-300 bg-white px-2.5 py-2 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">{{ $input['value'] }}</textarea>
                                        @elseif ($input['type'] === 'select')
                                            <select name="{{ $input['name'] }}" id="{{ $input['name'] }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                                @foreach ($input['options'] as $option)
                                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                                @endforeach
                                            </select>
                                        @elseif ($input['type'] === 'number')
                                            <div class="relative flex items-center">
                                                <button type="button" id="decrement-button" data-input-counter-decrement="{{ $input['name'] }}" class="h-11 rounded-s-lg border border-gray-300 bg-gray-100 p-3 shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100">
                                                    <svg class="h-3 w-3 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                    </svg>
                                                </button>
                                                <input type="text" data-input-counter data-input-counter-min="1" data-input-counter-max="{{ $input['max'] }}" value="{{ $input['value'] }}" id="{{ $input['name'] }}" data-input-counter aria-describedby="helper-text-explanation" class="block h-11 w-full border-x-0 border-gray-300 bg-white py-2.5 text-center text-sm text-gray-900 focus:border-red-500 focus:ring-red-500" placeholder="{{ $input['placeholder'] }}" required />
                                                <button type="button" id="increment-button" data-input-counter-increment="{{ $input['name'] }}" class="h-11 rounded-e-lg border border-gray-300 bg-gray-100 p-3 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100">
                                                    <svg class="h-3 w-3 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @elseif ($input['type'] === 'file')
                                            <x-form.file-upload max-files="1" accepted-types="image/*, .pdf, .docx" max-size="5"></x-form.file-upload>
                                        @else
                                            <input type="{{ $input['type'] }}" name="{{ $input['name'] }}" id="{{ $input['name'] }}" placeholder="{{ $input['placeholder'] }}" value="{{ $input['value'] }}" required class="w-full rounded-md border border-neutral-300 bg-white px-2 py-2 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed disabled:opacity-75" autocomplete="off" />
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </x-form.add-modal>
                    @endcan
                @endisset
            </div>
        </div>
    </div>
    <div class="rounded-lg bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            {!! $dataTable->table(['id' => $tableId, 'class' => 'w-full text-sm text-left text-gray-500']) !!}
        </div>
    </div>
</div>
