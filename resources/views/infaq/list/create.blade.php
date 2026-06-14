@extends('layouts.admin')

@section('title', 'Tambah Pos Infaq')

@section('content')
    <div class="container-fluid px-4 py-6">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex items-center space-x-4 mb-6">
                <a href="{{ route('infaq.list.index') }}"
                    class="p-2 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="fad fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Tambah Pos Infaq Baru</h1>
                    <p class="text-gray-600">Buat pos penggalangan dana infaq baru</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <form action="{{ route('infaq.list.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="space-y-2">
                            <label for="name" class="text-sm font-semibold text-gray-700">Nama Pos Infaq</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fad fa-heading text-gray-400"></i>
                                </div>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 @error('name') border-red-500 @enderror"
                                    placeholder="Contoh: Infaq Pembangunan Masjid" required>
                            </div>
                            @error('name')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div class="space-y-2">
                            <label for="slug" class="text-sm font-semibold text-gray-700">Slug (URL)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fad fa-link text-gray-400"></i>
                                </div>
                                <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-red-500 focus:border-red-500 @error('slug') border-red-500 @enderror"
                                    placeholder="infaq-pembangunan-masjid" readonly required>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1 italic">*Otomatis terisi berdasarkan nama</p>
                            @error('slug')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="space-y-2">
                            <label for="category" class="text-sm font-semibold text-gray-700">Kategori</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fad fa-tags text-gray-400"></i>
                                </div>
                                <select name="category" id="category"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 @error('category') border-red-500 @enderror"
                                    required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('category') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dana Dibutuhkan -->
                        <div class="space-y-2">
                            <label for="dana_dibutuhkan" class="text-sm font-semibold text-gray-700">Dana Dibutuhkan
                                (Rp)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fad fa-coins text-gray-400"></i>
                                </div>
                                <input type="number" name="dana_dibutuhkan" id="dana_dibutuhkan"
                                    value="{{ old('dana_dibutuhkan', 0) }}"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 @error('dana_dibutuhkan') border-red-500 @enderror"
                                    min="0" required>
                            </div>
                            @error('dana_dibutuhkan')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <label for="description" class="text-sm font-semibold text-gray-700">Deskripsi</label>
                        <textarea name="description" id="description" rows="4"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 @error('description') border-red-500 @enderror"
                            placeholder="Jelaskan tujuan penggalangan dana ini...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center h-5">
                            <input id="is_active" name="is_active" type="checkbox" value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded transition-all">
                        </div>
                        <div class="text-sm">
                            <label for="is_active" class="font-medium text-gray-700">Aktifkan Pos Infaq</label>
                            <p class="text-gray-500 text-xs">Jika dicentang, pos ini akan muncul di aplikasi/web publik.</p>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('infaq.list.index') }}"
                            class="px-6 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                            Simpan Pos Infaq
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('name').addEventListener('input', function() {
                let name = this.value;
                let slug = name.toLowerCase()
                    .replace(/[^\w ]+/g, '')
                    .replace(/ +/g, '-');
                document.getElementById('slug').value = slug;
            });
        </script>
    @endpush
@endsection
