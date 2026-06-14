@extends('layouts.admin')

@section('title', 'Edit Toko - ' . $toko->name)
@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900">Edit Toko</h1>
                        <p class="text-red-600 font-semibold mt-1">{{ $toko->name }}</p>
                    </div>
                </div>
                <span
                    class="px-4 py-2 rounded-full text-sm font-semibold border-2
                    @if ($toko->status === 'active') bg-green-100 text-green-700 border-green-300
                    @elseif($toko->status === 'pending') bg-yellow-100 text-yellow-700 border-yellow-300
                    @else bg-red-100 text-red-700 border-red-300 @endif">
                    {{ ucfirst($toko->status) }}
                </span>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-xl shadow-md mb-6 border-t-4 border-red-600" x-data="{
            activeTab: localStorage.getItem('activeTab_toko_{{ $toko->id }}') || 'info',
            init() {
                this.$watch('activeTab', val => {
                    localStorage.setItem('activeTab_toko_{{ $toko->id }}', val);
                });
            }
        }">
            <nav class="flex border-b border-gray-200">
                <button @click="activeTab = 'info'"
                    :class="activeTab === 'info' ? 'border-red-600 text-red-600 bg-red-50' :
                        'border-transparent text-gray-600 hover:text-red-600'"
                    class="px-6 py-4 border-b-2 font-semibold text-sm transition-all flex items-center gap-2 hover:bg-red-50">
                    <i class="fas fa-store text-lg"></i>
                    Informasi Toko
                </button>
                <button @click="activeTab = 'employees'"
                    :class="activeTab === 'employees' ? 'border-red-600 text-red-600 bg-red-50' :
                        'border-transparent text-gray-600 hover:text-red-600'"
                    class="px-6 py-4 border-b-2 font-semibold text-sm transition-all flex items-center gap-2 hover:bg-red-50">
                    <i class="fas fa-users text-lg"></i>
                    Karyawan
                    <span
                        class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-red-100 text-red-700">{{ $toko->users->count() }}</span>
                </button>
                <button @click="activeTab = 'products'"
                    :class="activeTab === 'products' ? 'border-red-600 text-red-600 bg-red-50' :
                        'border-transparent text-gray-600 hover:text-red-600'"
                    class="px-6 py-4 border-b-2 font-semibold text-sm transition-all flex items-center gap-2 hover:bg-red-50">
                    <i class="fas fa-box text-lg"></i>
                    Barang Toko
                    <span
                        class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-red-100 text-red-700">{{ $toko->barangs->count() }}</span>
                </button>
            </nav>

            <!-- TAB: Informasi Toko -->
            <div x-show="activeTab === 'info'" x-transition class="p-8">
                <form action="{{ route('toko.update', $toko->id) }}" method="POST" id="tokoInfoForm" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Toko -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2.5">
                                <i class="fas fa-store text-red-600 mr-2"></i>Nama Toko *
                            </label>
                            <input type="text" name="name" value="{{ $toko->name }}" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all">
                        </div>

                        <!-- Owner -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2.5">
                                <i class="fas fa-user-tie text-red-600 mr-2"></i>Pemilik Toko *
                            </label>
                            <select name="owner_id" id="owner_select" required
                                data-hs-select='{
                                "apiUrl": "{{ route('toko.get-users-wost', $toko->id) }}",
                                "apiQuery": "type=owner&offset=0&limit=20",
                                "apiLoadMore": { "perPage": 20, "scrollThreshold": 100 },
                                "apiSearchQueryKey": "q",
                                "apiDataPart": "results",
                                "apiSelectedValues": ["{{ (string) $toko->owner_id }}"],
                                "apiFieldsMap": {
                                    "id": "id",
                                    "val": "id",
                                    "title": "text",
                                    "icon": "thumbnail",
                                    "description": "existing_toko"
                                },
                                "apiIconTag": "<img />",
                                "isSelectedOptionOnTop": true,
                                "hasSearch": true,
                                "searchPlaceholder": "Cari Pemilik...",
                                "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-red-500 focus:ring-red-200 py-2 px-3",
                                "placeholder": "Pilih Pemilik...",
                                "toggleClasses": "relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border-2 border-gray-200 rounded-lg text-sm focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-200",
                                "dropdownClasses": "mt-2 max-h-72 pb-1 px-1 space-y-0.5 z-50 w-full bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto",
                                "optionClasses": "py-2.5 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-red-50 rounded-lg",
                             "optionTemplate": "<div class=\"flex items-center\"><div class=\"size-8 border border-gray-200 overflow-hidden flex-none rounded-full me-2\" data-icon></div><div><div class=\"text-sm font-semibold text-gray-800 \" data-title></div><div class=\"text-xs text-gray-500 \" data-description></div></div><div class=\"ms-auto\"><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-4 text-blue-600\" xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" viewBox=\"0 0 16 16\"><path d=\"M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z\"/></svg></span></div></div>",
        "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
      }'
                                class="hidden">
                                <option value="">Pilih Pemilik</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2.5">
                                <i class="fas fa-toggle-on text-red-600 mr-2"></i>Status *
                            </label>
                            <select name="status" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all">
                                <option value="active" {{ $toko->status === 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="pending" {{ $toko->status === 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="suspend" {{ $toko->status === 'suspend' ? 'selected' : '' }}>Suspend
                                </option>
                                <option value="hasReview" {{ $toko->status === 'hasReview' ? 'selected' : '' }}>Has
                                    Review</option>
                            </select>
                        </div>

                        <!-- Token -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2.5">
                                <i class="fas fa-coins text-red-600 mr-2"></i>Token
                            </label>
                            <input type="number" name="token" value="{{ $toko->token }}" min="0"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all">
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2.5">
                                <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>Alamat *
                            </label>
                            <textarea name="address" rows="3" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all">{{ $toko->address }}</textarea>
                        </div>

                        <!-- Latitude & Longitude -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2.5">
                                <i class="fas fa-map-pin text-red-600 mr-2"></i>Latitude
                            </label>
                            <input type="number" step="any" name="latitude" value="{{ $toko->latitude }}"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2.5">
                                <i class="fas fa-map-pin text-red-600 mr-2"></i>Longitude
                            </label>
                            <input type="number" step="any" name="longitude" value="{{ $toko->longitude }}"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all">
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2.5">
                                <i class="fas fa-align-left text-red-600 mr-2"></i>Deskripsi
                            </label>
                            <textarea name="description" rows="4"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all">{{ $toko->description }}</textarea>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="window.location.href='{{ route('toko.index') }}'"
                            class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-all">
                            <i class="fas fa-times mr-2"></i>Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-lg hover:from-red-700 hover:to-red-800 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- TAB: Karyawan -->
            <div x-show="activeTab === 'employees'" x-transition class="p-8" x-data="employeeManager()">
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-1">
                                <i class="fas fa-users text-red-600 mr-2"></i>Manajemen Karyawan
                            </h2>
                            <p class="text-gray-600">Kelola karyawan dan jabatan mereka di toko ini</p>
                        </div>
                        <button @click="showAddForm = !showAddForm"
                            class="px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition-all font-semibold shadow-md hover:shadow-lg">
                            <i class="fas fa-plus mr-2"></i>Tambah Karyawan
                        </button>
                    </div>
                </div>

                <!-- Current Employees -->
                <div x-show="employees.length > 0" class="space-y-3" id="employee-list-container">
                    <template x-for="user in employees" :key="user.id">
                        <div
                            class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-white border-l-4 border-red-600 rounded-lg hover:shadow-lg transition-all group">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="relative">
                                    <img :src="user.thumbnail" :alt="user.name"
                                        class="w-14 h-14 rounded-full border-3 border-red-200 group-hover:border-red-400 transition-all object-cover">
                                    <div
                                        class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 rounded-full border-2 border-white">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900 text-base" x-text="user.name"></p>
                                    <p class="text-sm text-gray-500" x-text="user.email"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 flex-1 justify-center">
                                <select
                                    class="px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200 font-semibold text-gray-700 transition-all"
                                    @change="updateEmployeeJabatan(user.id, $event.target.value)">
                                    <option value="">Pilih Jabatan</option>
                                    <template x-for="jabatan in jabatans" :key="jabatan.id">
                                        <option :value="jabatan.id" :selected="user.jabatan_id === jabatan.id"
                                            x-text="jabatan.name"></option>
                                    </template>
                                </select>
                            </div>
                            <button @click="removeEmployee(user.id, user.name)"
                                class="ml-4 px-4 py-2.5 text-red-600 hover:bg-red-50 hover:text-red-700 rounded-lg transition-all font-semibold hover:shadow-md">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="employees.length === 0"
                    class="text-center py-16 bg-gradient-to-br from-red-50 to-red-100 rounded-xl border-2 border-dashed border-red-300">
                    <div class="mb-4">
                        <i class="fas fa-users text-red-300 text-6xl"></i>
                    </div>
                    <p class="text-gray-700 font-bold text-lg mb-1">Belum ada karyawan</p>
                    <p class="text-gray-600 mb-6">Tambahkan karyawan pertama untuk mengelola tim toko Anda</p>
                    <button @click="showAddForm = true"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition-all shadow-md hover:shadow-lg">
                        <i class="fas fa-plus"></i>Tambah Karyawan Pertama
                    </button>
                </div>

                <!-- Add Employee Form -->
                <div x-show="showAddForm" x-transition
                    class="mt-8 p-6 bg-gradient-to-br from-red-50 to-red-100 border-2 border-red-300 rounded-xl shadow-lg">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">
                        <i class="fas fa-user-plus text-red-600 mr-2"></i>Tambah Karyawan Baru
                    </h3>
                    <p class="text-gray-600 text-sm mb-4">Tambahkan karyawan baru ke toko Anda</p>
                    <form @submit.prevent="addEmployee" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Karyawan</label>
                            <select x-model="newEmployee.userId" required
                                data-hs-select='{
                                "apiUrl": "{{ route('toko.get-users-wot') }}",
                                "apiQuery": "type=employee&offset=0&limit=20",
                                "apiLoadMore": { "perPage": 20, "scrollThreshold": 100 },
                                "apiSearchQueryKey": "q",
                                "apiDataPart": "results",
                                "apiFieldsMap": {
                                    "id": "id",
                                    "val": "id",
                                    "title": "text",
                                    "icon": "thumbnail",
                                    "description": "existing_toko"
                                },
                                "apiIconTag": "<img />",
                                "isSelectedOptionOnTop": true,
                                "hasSearch": true,
                                "searchPlaceholder": "Cari Karyawan...",
                                "searchClasses": "block w-full sm:text-sm border-gray-200 rounded-lg focus:border-red-500 focus:ring-red-200 py-2 px-3",
                                "placeholder": "Pilih Karyawan...",
                                "toggleClasses": "relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border-2 border-gray-300 rounded-lg text-sm focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-200",
                                "dropdownClasses": "mt-2 max-h-72 pb-1 px-1 space-y-0.5 z-50 w-full bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto shadow-lg",
                                "optionClasses": "py-2.5 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-red-50 rounded-lg",
                             "optionTemplate": "<div class=\"flex items-center\"><div class=\"size-8 border border-gray-200 overflow-hidden flex-none rounded-full me-2\" data-icon></div><div><div class=\"text-sm font-semibold text-gray-800\" data-title></div><div class=\"text-xs text-gray-500\" data-description></div></div><div class=\"ms-auto\"><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-4 text-red-600\" xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" viewBox=\"0 0 16 16\"><path d=\"M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z\"/></svg></span></div></div>",
        "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
      }'
                                class="hidden">
                                <option value="">Pilih Karyawan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jabatan</label>
                            <select x-model="newEmployee.jabatanId" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200 font-semibold transition-all">
                                <option selected disabled value="">-- Pilih Jabatan --</option>
                                @foreach ($jabatans as $jabatan)
                                    @if ($jabatan->name !== 'Pemilik Toko')
                                        <option value="{{ $jabatan->id }}">{{ $jabatan->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2 flex justify-end gap-2">
                            <button type="button" @click="showAddForm = false"
                                class="px-5 py-2.5 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-semibold transition-all">
                                Batal
                            </button>
                            <button type="submit" :disabled="isLoading"
                                class="px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 font-semibold transition-all shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-plus mr-2" x-show="!isLoading"></i>
                                <i class="fas fa-spinner fa-spin mr-2" x-show="isLoading"></i>
                                <span x-text="isLoading ? 'Menambahkan...' : 'Tambahkan'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TAB: Barang Toko -->
            <div x-show="activeTab === 'products'" x-transition class="p-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">
                        <i class="fas fa-box text-red-600 mr-2"></i>Barang Toko
                    </h2>
                    <p class="text-gray-600">Kelola barang yang tersedia di toko ini</p>
                </div>

                @if ($toko->barangs->count() > 0)
                    <div class="overflow-x-auto rounded-xl shadow-md border border-gray-200">
                        <table class="w-full text-sm">
                            <thead class="bg-gradient-to-r from-red-600 to-red-700 border-b-2 border-red-800">
                                <tr>
                                    <th class="px-6 py-4 text-left font-bold text-white">Barang</th>
                                    <th class="px-6 py-4 text-left font-bold text-white">Satuan</th>
                                    <th class="px-6 py-4 text-left font-bold text-white">Harga Jual</th>
                                    <th class="px-6 py-4 text-left font-bold text-white">Stock</th>
                                    <th class="px-6 py-4 text-left font-bold text-white">Expired</th>
                                    <th class="px-6 py-4 text-right font-bold text-white">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($toko->barangs as $barangki)
                                    <tr class="hover:bg-red-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-gray-900">
                                                {{ $barangki->barang->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">{{ $barangki->barcode }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 font-medium">
                                            {{ $barangki->barang->satuan->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 font-bold text-red-600">Rp
                                            {{ number_format($barangki->harga_jual, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-3 py-1.5 text-xs font-bold rounded-full {{ $barangki->jumlah_stock > 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $barangki->jumlah_stock }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 font-medium">
                                            {{ $barangki->expired_date ? \Carbon\Carbon::parse($barangki->expired_date)->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-3">
                                            <button
                                                class="text-blue-600 hover:text-blue-800 font-bold hover:bg-blue-50 px-2 py-1 rounded transition-all">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button
                                                onclick="deleteProduct({{ $barangki->id }}, '{{ $barangki->barang->name ?? 'N/A' }}')"
                                                class="text-red-600 hover:text-red-800 font-bold hover:bg-red-50 px-2 py-1 rounded transition-all">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div
                        class="text-center py-16 bg-gradient-to-br from-red-50 to-red-100 rounded-xl border-2 border-dashed border-red-300">
                        <div class="mb-4">
                            <i class="fas fa-box-open text-red-300 text-6xl"></i>
                        </div>
                        <p class="text-gray-700 font-bold text-lg">Belum ada barang di toko ini</p>
                        <p class="text-gray-600">Tambahkan barang untuk mulai berjualan</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Data dari server
        window.employeesData = @json(
            $toko->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'thumbnail' => $user->thumbnail,
                    'jabatan_id' => $user->pivot->jabatan_id ?? null,
                ];
            }));

        window.jabatansData = @json($jabatans);
        window.tokoId = {{ $toko->id }};
        window.csrfToken = '{{ csrf_token() }}';
    </script>
    <script src="{{ asset('js/toko/toko-edit-manager.js') }}"></script>
@endpush
