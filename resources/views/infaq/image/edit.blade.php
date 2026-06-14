@extends('layouts.admin')

@section('title', 'Edit Gambar Infaq')

@section('content')
    <div class="container-fluid px-4 py-6">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="flex items-center space-x-4 mb-6">
                <a href="{{ route('infaq.image.index') }}"
                    class="p-2 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="fad fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Gambar Infaq</h1>
                    <p class="text-gray-600">Perbarui informasi gambar untuk pos penggalangan dana</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <form action="{{ route('infaq.image.update', $infaqImage->id) }}" method="POST"
                    enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Infaq List Selection -->
                    <div class="space-y-2">
                        <label for="infaq_list_id" class="text-sm font-semibold text-gray-700">Pilih Pos Infaq</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fad fa-list-alt text-gray-400"></i>
                            </div>
                            <select name="infaq_list_id" id="infaq_list_id"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 @error('infaq_list_id') border-red-500 @enderror"
                                required>
                                <option value="">Pilih Pos Infaq</option>
                                @foreach ($infaqLists as $list)
                                    <option value="{{ $list->id }}"
                                        {{ old('infaq_list_id', $infaqImage->infaq_list_id) == $list->id ? 'selected' : '' }}>
                                        {{ $list->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('infaq_list_id')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Image Preview -->
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Gambar Saat Ini</label>
                        <div
                            class="relative rounded-xl overflow-hidden border border-gray-200 shadow-sm aspect-video bg-gray-50">
                            <img id="current-image" src="{{ asset($infaqImage->image_path) }}" alt="Current Image"
                                class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center">
                                <span
                                    class="px-3 py-1 bg-white/90 text-gray-800 text-xs font-bold rounded-full shadow-sm">GAMBAR
                                    AKTIF</span>
                            </div>
                        </div>
                    </div>

                    <!-- New Image Upload (Optional) -->
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Ganti Gambar (Opsional)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-red-400 transition-colors bg-gray-50"
                            id="drop-zone">
                            <div class="space-y-1 text-center">
                                <i class="fad fa-cloud-upload text-4xl text-gray-400 mb-2" id="upload-icon"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="image"
                                        class="relative cursor-pointer bg-white rounded-md font-semibold text-red-600 hover:text-red-500 focus-within:outline-none">
                                        <span>Upload file baru</span>
                                        <input id="image" name="image" type="file" class="sr-only" accept="image/*"
                                            onchange="previewFile()">
                                    </label>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                        <div id="preview-container" class="hidden mt-4 relative">
                            <img id="image-preview" src="#" alt="Preview"
                                class="w-full h-48 object-cover rounded-lg border border-gray-200 shadow-sm">
                            <button type="button" onclick="removePreview()"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600">
                                <i class="fad fa-times-circle"></i>
                            </button>
                        </div>
                        @error('image')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Is Main Checkbox -->
                    <div class="flex items-center space-x-3 p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                        <div class="flex items-center h-5">
                            <input id="is_main" name="is_main" type="checkbox" value="1"
                                {{ old('is_main', $infaqImage->is_main) ? 'checked' : '' }}
                                class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded transition-all">
                        </div>
                        <div class="text-sm">
                            <label for="is_main" class="font-medium text-gray-800">Jadikan Gambar Utama</label>
                            <p class="text-gray-500 text-xs">Gambar utama akan ditampilkan sebagai cover pos infaq.</p>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('infaq.image.index') }}"
                            class="px-6 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                            Perbarui Gambar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function previewFile() {
                const preview = document.getElementById('image-preview');
                const file = document.getElementById('image').files[0];
                const reader = new FileReader();
                const container = document.getElementById('preview-container');
                const dropZone = document.getElementById('drop-zone');

                reader.onloadend = function() {
                    preview.src = reader.result;
                    container.classList.remove('hidden');
                    dropZone.classList.add('hidden');
                }

                if (file) {
                    reader.readAsDataURL(file);
                } else {
                    preview.src = "";
                }
            }

            function removePreview() {
                document.getElementById('image').value = "";
                document.getElementById('preview-container').classList.add('hidden');
                document.getElementById('drop-zone').classList.remove('hidden');
            }

            // Drag and drop logic
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('image');

            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-red-400', 'bg-red-50');
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('border-red-400', 'bg-red-50');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-red-400', 'bg-red-50');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    previewFile();
                }
            });
        </script>
    @endpush
@endsection
