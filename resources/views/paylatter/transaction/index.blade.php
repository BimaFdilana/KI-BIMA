@extends('layouts.admin')

@section('title', 'Daftar Transaksi Paylatter')

@section('content')
    <div class="container-fluid px-4 py-6">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fad fa-history text-red-500 mr-2"></i>
                    Daftar Transaksi Paylatter
                </h1>
                <p class="text-gray-600 mt-1">Monitor semua transaksi dan status pembayaran paylatter</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Transaksi</p>
                        <p class="text-3xl font-bold mt-1">{{ \App\Models\PakDul\PayLatterTransaction::count() }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fad fa-file-invoice-dollar text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm font-medium">Pending</p>
                        <p class="text-3xl font-bold mt-1">
                            {{ \App\Models\PakDul\PayLatterTransaction::where('status', 'pending')->count() }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fad fa-clock text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">Overdue</p>
                        <p class="text-3xl font-bold mt-1">
                            {{ \App\Models\PakDul\PayLatterTransaction::where('status', 'overdue')->count() }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fad fa-exclamation-circle text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Total Pelunasan</p>
                        <p class="text-2xl font-bold mt-1">Rp
                            {{ number_format(\App\Models\PakDul\PayLatterTransaction::where('status', 'paid')->sum('total_amount'), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fad fa-check-double text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="p-6">
                {{ $dataTable->table(['class' => 'table table-striped table-hover w-full']) }}
            </div>
        </div>
    </div>

    @push('scripts')
        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    @endpush
@endsection
