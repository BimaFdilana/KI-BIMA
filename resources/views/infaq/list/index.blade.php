@extends('layouts.admin')

@section('title', 'Daftar Pos Infaq')

@section('content')
    <div class="container-fluid px-4 py-6">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fad fa-hand-holding-heart text-red-500 mr-2"></i>
                    Daftar Pos Infaq
                </h1>
                <p class="text-gray-600 mt-1">Kelola semua pos infaq dan donasi</p>
            </div>

            @can('create.infaq')
                <a href="{{ route('infaq.list.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fad fa-plus-circle mr-2"></i>
                    Tambah Pos Infaq
                </a>
            @endcan
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Pos Infaq</p>
                        <p class="text-3xl font-bold mt-1">{{ \App\Models\Infaq\InfaqList::count() }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fad fa-list-alt text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Pos Aktif</p>
                        <p class="text-3xl font-bold mt-1">
                            {{ \App\Models\Infaq\InfaqList::where('is_active', true)->count() }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fad fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Total Donasi</p>
                        <p class="text-3xl font-bold mt-1">
                            {{ \App\Models\Infaq\InfaqHistory::where('status', 'completed')->count() }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fad fa-hand-holding-usd text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Dana Terkumpul</p>
                        <p class="text-2xl font-bold mt-1">Rp
                            {{ number_format(\App\Models\Infaq\InfaqHistory::where('status', 'completed')->sum('amount'), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fad fa-coins text-2xl"></i>
                    </div>
                </div>
            </div>
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

            // Close modal on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeImageModal();
                }
            });

            // Delete handler
            document.addEventListener('DOMContentLoaded', function() {
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.delete-infaq-button')) {
                        const button = e.target.closest('.delete-infaq-button');
                        const id = button.dataset.id;
                        const tableId = button.dataset.table;

                        if (confirm('Apakah Anda yakin ingin menghapus pos infaq ini?')) {
                            fetch(`/infaq/list/${id}`, {
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
                                        // Reload DataTable
                                        window.LaravelDataTables[tableId].ajax.reload();

                                        // Show success message (you can use your toast notification here)
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
