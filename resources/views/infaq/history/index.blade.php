@extends('layouts.admin')

@section('title', 'Riwayat Donasi Infaq')

@section('content')
    <div class="container-fluid px-4 py-6">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fad fa-history text-red-500 mr-2"></i>
                    Riwayat Donasi Infaq
                </h1>
                <p class="text-gray-600 mt-1">Pantau semua transaksi donasi yang masuk</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-indigo-100 text-sm font-medium">Total Donasi</p>
                        <p class="text-3xl font-bold mt-1">{{ \App\Models\Infaq\InfaqHistory::count() }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fad fa-receipt text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Donasi Selesai</p>
                        <p class="text-3xl font-bold mt-1">
                            {{ \App\Models\Infaq\InfaqHistory::where('status', 'completed')->count() }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fad fa-check-double text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm font-medium">Donasi Pending</p>
                        <p class="text-3xl font-bold mt-1">
                            {{ \App\Models\Infaq\InfaqHistory::where('status', 'pending')->count() }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fad fa-clock text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">Total Dana (Selesai)</p>
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

    @push('scripts')
        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

        <script>
            // Delete handler
            document.addEventListener('DOMContentLoaded', function() {
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.delete-infaq-history-button')) {
                        const button = e.target.closest('.delete-infaq-history-button');
                        const id = button.dataset.id;
                        const tableId = button.dataset.table;

                        if (confirm('Apakah Anda yakin ingin menghapus riwayat donasi ini?')) {
                            fetch(`/infaq/history/${id}`, {
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
