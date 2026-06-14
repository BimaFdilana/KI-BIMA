@extends('layouts.admin')

@section('title', 'Daftar Akun Paylatter')

@section('content')
    <div class="container-fluid px-4 py-6">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fad fa-wallet text-red-500 mr-2"></i>
                    Daftar Akun Paylatter
                </h1>
                <p class="text-gray-600 mt-1">Kelola limit kredit dan status akun pengguna</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-indigo-100 text-sm font-medium">Total Akun</p>
                        <p class="text-3xl font-bold mt-1">{{ \App\Models\PakDul\PayLatterAccount::count() }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fad fa-users text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Akun Aktif</p>
                        <p class="text-3xl font-bold mt-1">
                            {{ \App\Models\PakDul\PayLatterAccount::where('status', 'active')->count() }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fad fa-user-check text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">Akun Suspended</p>
                        <p class="text-3xl font-bold mt-1">
                            {{ \App\Models\PakDul\PayLatterAccount::where('status', 'suspended')->count() }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fad fa-user-slash text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Total Kredit Terpakai</p>
                        <p class="text-2xl font-bold mt-1">Rp
                            {{ number_format(\App\Models\PakDul\PayLatterAccount::sum('used_credit'), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <i class="fad fa-money-bill-wave text-2xl"></i>
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
