@extends('layouts.admin')

@section('title', 'Kelola Gambar Infaq')

@section('content')
    <div class="container-fluid px-4 py-6">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fad fa-images text-red-500 mr-2"></i>
                    Kelola Gambar Infaq
                </h1>
                <p class="text-gray-600 mt-1">Kelola galeri gambar untuk setiap pos infaq</p>
            </div>

            @can('create.infaq')
                <a href="{{ route('infaq.image.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fad fa-plus-circle mr-2"></i>
                    Tambah Gambar
                </a>
            @endcan
        </div>

        <!-- DataTable Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6">
                {{ $dataTable->table(['class' => 'table table-striped table-hover w-full']) }}
            </div>
        </div>
    </div>

    <!-- Image Viewer Modal -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 p-4">
        <div class="flex items-center justify-center h-full">
            <div class="relative max-w-4xl max-h-full">
                <button onclick="closeImageModal()" class="absolute -top-10 right-0 text-white hover:text-gray-300">
                    <i class="fad fa-times text-3xl"></i>
                </button>
                <img id="modalImage" src="" alt="Preview" class="max-w-full max-h-screen rounded-lg">
            </div>
        </div>
    </div>

    @push('scripts')
        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

        <script>
            // Image viewer function
            function viewImage(imageUrl) {
                document.getElementById('modalImage').src = imageUrl;
                document.getElementById('imageModal').classList.remove('hidden');
            }

            function closeImageModal() {
                document.getElementById('imageModal').classList.add('hidden');
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeImageModal();
            });

            // Delete handler
            document.addEventListener('DOMContentLoaded', function() {
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.delete-infaq-image-button')) {
                        const button = e.target.closest('.delete-infaq-image-button');
                        const id = button.dataset.id;
                        const tableId = button.dataset.table;

                        if (confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
                            fetch(`/infaq/image/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                            .content,
                                        'Accept': 'application/json',
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        window.LaravelDataTables[tableId].ajax.reload();
                                        alert(data.message);
                                    } else {
                                        alert(data.message || 'Terjadi kesalahan');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Terjadi kesalahan saat menghapus data');
                                });
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
