@extends('layouts.admin')

@section('title', 'Detail Transaksi Infaq')

@section('content')
    <div class="container-fluid px-4 py-6">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="flex items-center space-x-4 mb-6">
                <a href="{{ route('infaq.history.index') }}"
                    class="p-2 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="fad fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Transaksi Infaq</h1>
                    <p class="text-gray-600">ID Transaksi: <span
                            class="font-mono font-semibold text-red-600">#INF-{{ str_pad($infaqHistory->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <!-- Main Info Card -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-red-100 rounded-lg text-red-600">
                                <i class="fad fa-file-invoice-dollar text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Status Pembayaran</p>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-bold mt-1
                                @if ($infaqHistory->status === 'completed') bg-green-100 text-green-800
                                @elseif($infaqHistory->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($infaqHistory->status === 'failed') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                    <i
                                        class="fad 
                                    @if ($infaqHistory->status === 'completed') fa-check-circle
                                    @elseif($infaqHistory->status === 'pending') fa-clock
                                    @elseif($infaqHistory->status === 'failed') fa-times-circle
                                    @else fa-ban @endif mr-1.5"></i>
                                    {{ $infaqHistory->status_label }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Jumlah Donasi</p>
                            <p class="text-3xl font-black text-gray-900">Rp
                                {{ number_format($infaqHistory->amount, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Donor Info -->
                        <div class="space-y-4">
                            <h4
                                class="text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">
                                Informasi Donatur</h4>
                            <div class="flex items-center space-x-4">
                                @if ($infaqHistory->user && isset($infaqHistory->user->profile_photo_path))
                                    <img src="{{ asset($infaqHistory->user->profile_photo_path) }}"
                                        class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-100">
                                @else
                                    <div
                                        class="w-12 h-12 rounded-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                        {{ strtoupper(substr($infaqHistory->donor_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-bold text-gray-900 text-lg">{{ $infaqHistory->donor_name }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $infaqHistory->user ? $infaqHistory->user->email : 'Donatur Umum' }}</p>
                                </div>
                            </div>
                            <div class="space-y-2 pt-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Metode Pembayaran:</span>
                                    <span
                                        class="font-semibold text-gray-800">{{ $infaqHistory->payment_method_label }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Tanggal Transaksi:</span>
                                    <span
                                        class="font-semibold text-gray-800">{{ $infaqHistory->created_at->translatedFormat('d F Y, H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Destination Info -->
                        <div class="space-y-4">
                            <h4
                                class="text-sm font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">
                                Tujuan Infaq</h4>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <p class="text-xs text-gray-500 mb-1">Pos Infaq:</p>
                                <p class="font-bold text-gray-900 mb-3">{{ $infaqHistory->infaqList->name }}</p>

                                @if ($infaqHistory->toko)
                                    <p class="text-xs text-gray-500 mb-1">Melalui Toko:</p>
                                    <div class="flex items-center space-x-2">
                                        <i class="fad fa-store text-blue-500"></i>
                                        <span class="font-semibold text-gray-800">{{ $infaqHistory->toko->name }}</span>
                                    </div>
                                @endif
                            </div>

                            @if ($infaqHistory->note)
                                <div class="pt-2">
                                    <p class="text-xs text-gray-500 mb-1">Catatan/Doa:</p>
                                    <p
                                        class="text-sm text-gray-700 italic bg-yellow-50 p-3 rounded-lg border border-yellow-100">
                                        "{{ $infaqHistory->note }}"
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($infaqHistory->canChangeStatus())
                        <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end">
                            @can('edit.infaq')
                                <a href="{{ route('infaq.history.edit', $infaqHistory->id) }}"
                                    class="px-6 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-lg shadow-md transition-all">
                                    Ubah Status Transaksi
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
