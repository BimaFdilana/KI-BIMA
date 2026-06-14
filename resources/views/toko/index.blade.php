@extends('layouts.admin')

@section('styles')
@endsection

@section('content')
    <div class="p-4 sm:p-6 lg:p-8 space-y-8">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-red-600 via-red-700 to-red-800 rounded-2xl shadow-2xl p-8 text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-4xl font-bold flex items-center">
                        <i class="fas fa-store-alt mr-4"></i>
                        Manajemen Toko Partner
                    </h1>
                    <p class="mt-3 text-red-100 text-lg">Kelola, pantau, dan perbarui semua toko partner Anda dengan mudah
                    </p>
                </div>
                <div class="mt-6 md:mt-0">
                    <button onclick="openCreateModal()"
                        class="bg-white text-red-600 hover:bg-red-50 font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 inline-flex items-center">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Tambah Toko Baru
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Toko Aktif -->
            <div
                class="bg-white overflow-hidden shadow-xl rounded-2xl transform transition hover:scale-105 duration-300 border-t-4 border-red-500">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 shadow-lg">
                            <i class="fas fa-store text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Toko Aktif</dt>
                                <dd class="flex items-baseline">
                                    <p class="text-3xl font-bold text-gray-900">{{ $totalTokoActive }}</p>
                                    <p
                                        class="ml-2 flex items-baseline text-sm font-semibold {{ $totalActivePercentage >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        @if ($totalActivePercentage >= 0)
                                            <i class="fas fa-arrow-up mr-1"></i>
                                        @else
                                            <i class="fas fa-arrow-down mr-1"></i>
                                        @endif
                                        {{ abs($totalActivePercentage) }}%
                                    </p>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Token -->
            <div
                class="bg-white overflow-hidden shadow-xl rounded-2xl transform transition hover:scale-105 duration-300 border-t-4 border-amber-500">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-4 shadow-lg">
                            <i class="fas fa-coins text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Token</dt>
                                <dd class="flex items-baseline">
                                    <p class="text-3xl font-bold text-gray-900">{{ number_format($totalTokens) }}</p>
                                    <p
                                        class="ml-2 flex items-baseline text-sm font-semibold {{ $totalTokensPercentage >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        @if ($totalTokensPercentage >= 0)
                                            <i class="fas fa-arrow-up mr-1"></i>
                                        @else
                                            <i class="fas fa-arrow-down mr-1"></i>
                                        @endif
                                        {{ abs($totalTokensPercentage) }}%
                                    </p>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Karyawan -->
            <div
                class="bg-white overflow-hidden shadow-xl rounded-2xl transform transition hover:scale-105 duration-300 border-t-4 border-blue-500">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 shadow-lg">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Karyawan</dt>
                                <dd class="flex items-baseline">
                                    <p class="text-3xl font-bold text-gray-900">{{ $totalEmployee }}</p>
                                    <p
                                        class="ml-2 flex items-baseline text-sm font-semibold {{ $totalEmployeePercentage >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        @if ($totalEmployeePercentage >= 0)
                                            <i class="fas fa-arrow-up mr-1"></i>
                                        @else
                                            <i class="fas fa-arrow-down mr-1"></i>
                                        @endif
                                        {{ abs($totalEmployeePercentage) }}%
                                    </p>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toko Pending -->
            <div
                class="bg-white overflow-hidden shadow-xl rounded-2xl transform transition hover:scale-105 duration-300 border-t-4 border-orange-500">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 shadow-lg">
                            <i class="fas fa-hourglass-half text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Toko Pending</dt>
                                <dd>
                                    <p class="text-3xl font-bold text-gray-900">{{ $totalPendingToko }}</p>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable Section -->
        <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-100">
            <div class="p-6 bg-gradient-to-r from-red-50 to-white border-b border-red-100">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-table mr-3 text-red-600"></i>
                    Daftar Toko Partner
                </h2>
            </div>
            <div class="p-6">
                {!! $dataTable->table(['class' => 'w-full text-sm rounded-lg']) !!}
            </div>
        </div>
    </div>


    <!-- Create Modal -->
    <div id="createTokoModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity modal-backdrop" aria-hidden="true">
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full animate-modal-in">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-white flex items-center" id="modal-title">
                            <i class="fas fa-plus-circle mr-3"></i>
                            Tambah Toko Baru
                        </h3>
                        <button onclick="closeCreateModal()" class="text-white hover:text-red-100 transition-colors">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <form id="createTokoForm" class="p-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Toko -->
                        <div class="md:col-span-2">
                            <label for="create_name" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-store text-red-600 mr-2"></i>Nama Toko *
                            </label>
                            <input type="text" id="create_name" name="name" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring focus:ring-red-200 transition-all">
                        </div>

                        <!-- Pemilik -->
                        <!-- CREATE MODAL - OWNER SELECT -->
                        <div>
                            <label for="create_owner_id" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-user-tie text-red-600 mr-2"></i>Pemilik Toko *
                            </label>
                            <select id="create_owner_id" name="owner_id" required
                                data-hs-select='{
        "apiUrl": "{{ route('toko.get-users-wot') }}",
        "apiQuery": "type=owner",
        "apiLoadMore": {
          "perPage": 20,
          "scrollThreshold": 100
        },
        "apiSearchQueryKey": "q",
        "apiDataPart": "results",
        "apiFieldsMap": {
          "id": "id",
          "val": "id",
          "title": "text",
          "icon": "thumbnail",
          "description": "existing_toko",
          "offset": "offset",
          "limit": "limit"
        },
        "apiIconTag": "<img />",
        "isSelectedOptionOnTop": true,
        "hasSearch": true,
        "searchPlaceholder": "Cari Pemilik...",
        "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 before:absolute before:inset-0 before:z-1 py-1.5 sm:py-2 px-3",
        "searchWrapperClasses": "bg-white p-2 -mx-1 -mt-1 sticky top-0 ",
        "placeholder": "Pilih Pemilik...",
        "toggleTag": "<button type=\"button\" aria-expanded=\"false\"><span class=\"\" data-title></span></button>",
        "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-start text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500 ",
        "dropdownClasses": "mt-2 max-h-72 pb-1 px-1 space-y-0.5 z-20 w-full bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 ",
        "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-hidden focus:bg-gray-100 ",
        "optionTemplate": "<div class=\"flex items-center\"><div class=\"size-8 border border-gray-200 overflow-hidden flex-none rounded-full me-2\" data-icon></div><div><div class=\"text-sm font-semibold text-gray-800 \" data-title></div><div class=\"text-xs text-gray-500 \" data-description></div></div><div class=\"ms-auto\"><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-4 text-blue-600\" xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" viewBox=\"0 0 16 16\"><path d=\"M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z\"/></svg></span></div></div>",
        "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
      }'
                                class="hidden">
                                <option value="">Pilih Pemilik</option>
                            </select>
                        </div>
                        <div>
                            <label for="create_employees" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-users text-red-600 mr-2"></i>Karyawan
                            </label>
                            <select id="create_employees" name="employees[]" multiple
                                data-hs-select='{
        "apiUrl": "{{ route('toko.get-users-wot') }}",
        "apiQuery": "type=employee",
        "apiLoadMore": {
          "perPage": 20,
          "scrollThreshold": 100
        },
        "apiSearchQueryKey": "q",
        "apiDataPart": "results",
        "apiFieldsMap": {
          "id": "id",
          "val": "id",
          "title": "text",
          "icon": "thumbnail",
          "description": "existing_toko",
          "offset": "offset",
          "limit": "limit"
        },
        "apiIconTag": "<img />",
        "hasSearch": true,
        "isSelectedOptionOnTop": true,
        "searchPlaceholder": "Cari Karyawan...",
        "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 before:absolute before:inset-0 before:z-1 py-1.5 sm:py-2 px-3",
        "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0",
        "placeholder": "Pilih Karyawan...",
        "mode": "tags",
        "wrapperClasses": "relative ps-0.5 pe-9 min-h-11.5 flex items-center flex-wrap w-full border border-gray-200 rounded-lg text-start text-sm focus:border-blue-500 focus:ring-blue-500 ",
        "tagsItemTemplate": "<div class=\"flex flex-nowrap items-center text-nowrap relative z-10 bg-white border border-gray-200 rounded-full p-1 m-1\"><div class=\"size-6 border border-gray-200 overflow-hidden flex-none rounded-full me-1\" data-icon></div><div class=\"whitespace-nowrap text-gray-800\" data-title></div><div class=\"inline-flex shrink-0 justify-center items-center size-5 ms-2 rounded-full text-gray-800 bg-gray-200 hover:bg-gray-300 focus:outline-hidden focus:ring-2 focus:ring-gray-400 text-sm cursor-pointer\" data-remove><svg class=\"shrink-0 size-3\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M18 6 6 18\"/><path d=\"m6 6 12 12\"/></svg></div></div>",
        "tagsInputClasses": "py-3 px-2 rounded-lg order-1 border-transparent focus:ring-0 text-sm outline-hidden ",
        "dropdownClasses": "mt-2 z-50 w-full max-h-72 pb-1 px-1 space-y-0.5 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300",
        "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-hidden focus:bg-gray-100 ",
        "optionTemplate": "<div class=\"flex items-center\"><div class=\"size-8 border border-gray-200 overflow-hidden flex-none rounded-full me-2\" data-icon></div><div><div class=\"text-sm font-semibold text-gray-800 \" data-title></div><div class=\"text-xs text-gray-500 \" data-description></div></div><div class=\"ms-auto\"><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-4 text-blue-600\" xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" viewBox=\"0 0 16 16\"><path d=\"M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z\"/></svg></span></div></div>",
        "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
      }'
                                class="hidden">
                                <option value="">Pilih Karyawan</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="create_status" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-toggle-on text-red-600 mr-2"></i>Status *
                            </label>
                            <select id="create_status" name="status" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring focus:ring-red-200 transition-all">
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="suspend">Suspend</option>
                                <option value="hasReview">Has Review</option>
                            </select>
                        </div>

                        <!-- Token -->
                        <div>
                            <label for="create_token" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-coins text-red-600 mr-2"></i>Token
                            </label>
                            <input type="number" id="create_token" name="token" min="0" value="0"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring focus:ring-red-200 transition-all">
                        </div>

                        <!-- Alamat -->
                        <div class="md:col-span-2">
                            <label for="create_address" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>Alamat *
                            </label>
                            <textarea id="create_address" name="address" rows="3" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring focus:ring-red-200 transition-all"></textarea>
                        </div>

                        <!-- Latitude -->
                        <div>
                            <label for="create_latitude" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-map-pin text-red-600 mr-2"></i>Latitude
                            </label>
                            <input type="number" step="any" id="create_latitude" name="latitude"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring focus:ring-red-200 transition-all">
                        </div>

                        <!-- Longitude -->
                        <div>
                            <label for="create_longitude" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-map-pin text-red-600 mr-2"></i>Longitude
                            </label>
                            <input type="number" step="any" id="create_longitude" name="longitude"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring focus:ring-red-200 transition-all">
                        </div>

                        <!-- Deskripsi -->
                        <div class="md:col-span-2">
                            <label for="create_description" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-align-left text-red-600 mr-2"></i>Deskripsi
                            </label>
                            <textarea id="create_description" name="description" rows="4"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring focus:ring-red-200 transition-all"></textarea>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-end">
                        <button type="button" onclick="closeCreateModal()"
                            class="w-full sm:w-auto px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-all duration-300">
                            <i class="fas fa-times mr-2"></i>Batal
                        </button>
                        <button type="submit"
                            class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-lg hover:from-red-700 hover:to-red-800 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteTokoModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity modal-backdrop" aria-hidden="true">
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full animate-modal-in">
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full bg-red-100 sm:mx-0 sm:h-12 sm:w-12">
                            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-xl font-bold text-gray-900" id="modal-title">
                                Konfirmasi Hapus Toko
                            </h3>
                            <div class="mt-3">
                                <p class="text-sm text-gray-500">
                                    Apakah Anda yakin ingin menghapus toko
                                    <span id="delete_toko_name" class="font-bold text-red-600"></span>?
                                    <br><br>
                                    <span class="text-red-600 font-semibold">Tindakan ini tidak dapat dibatalkan!</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3">
                    <button type="button" onclick="confirmDelete()"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-base font-semibold text-white hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-trash mr-2"></i>Ya, Hapus
                    </button>
                    <button type="button" onclick="closeDeleteModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border-2 border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:w-auto sm:text-sm transition-all duration-300">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        let deleteTokoId = null;

        $(document).ready(function() {
            if (window.HSStaticMethods) {
                window.HSStaticMethods.autoInit();
            }
        });

        // ============================================
        // CREATE MODAL
        // ============================================

        function openCreateModal() {
            $('#createTokoModal').removeClass('hidden');
            $('body').addClass('overflow-hidden');
        }

        function closeCreateModal() {
            $('#createTokoModal').addClass('hidden');
            $('body').removeClass('overflow-hidden');
            $('#createTokoForm')[0].reset();
        }

        $('#createTokoForm').on('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Menyimpan data...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: "{{ route('toko.store') }}",
                method: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            confirmButtonColor: '#dc2626',
                            timer: 3000
                        });
                        closeCreateModal();
                        $('#toko-table').DataTable().ajax.reload();
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    let msg = 'Terjadi kesalahan saat menyimpan data.';

                    if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
                    if (xhr.responseJSON?.errors) {
                        msg += '\n\n' + Object.values(xhr.responseJSON.errors).map(e => e[0]).join(
                            '\n');
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: msg,
                        confirmButtonColor: '#dc2626'
                    });
                }
            });
        });



        // ============================================
        // DELETE MODAL
        // ============================================

        function openDeleteModal(button) {
            deleteTokoId = $(button).data('id');
            $('#delete_toko_name').text($(button).data('name'));
            $('#deleteTokoModal').removeClass('hidden');
            $('body').addClass('overflow-hidden');
        }

        function closeDeleteModal() {
            $('#deleteTokoModal').addClass('hidden');
            $('body').removeClass('overflow-hidden');
            deleteTokoId = null;
        }

        function confirmDelete() {
            if (!deleteTokoId) return;

            Swal.fire({
                title: 'Menghapus toko...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: `/toko/${deleteTokoId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.close();
                    closeDeleteModal();

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            confirmButtonColor: '#dc2626',
                            timer: 2000
                        });
                        $('#toko-table').DataTable().ajax.reload();
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    closeDeleteModal();

                    let msg = 'Terjadi kesalahan saat menghapus toko.';
                    if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: msg,
                        confirmButtonColor: '#dc2626'
                    });
                }
            });
        }

        // ============================================
        // KEYBOARD SHORTCUTS
        // ============================================

        $(document).on('keydown', function(e) {
            if (e.key !== 'Escape') return;

            if (!$('#createTokoModal').hasClass('hidden')) closeCreateModal();
            if (!$('#deleteTokoModal').hasClass('hidden')) closeDeleteModal();
        });
    </script>
@endpush
