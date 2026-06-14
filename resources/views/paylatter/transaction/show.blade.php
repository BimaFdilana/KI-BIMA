@extends('layouts.admin')

@section('title', 'Detail Transaksi Paylatter')

@section('content')
    <div class="container-fluid px-4 py-6">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="flex items-center space-x-4 mb-6">
                <a href="{{ route('paylatter.transaction.index') }}"
                    class="p-2 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="fad fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Transaksi Paylatter</h1>
                    <p class="text-gray-600">Kode Transaksi: <span
                            class="font-mono font-black text-red-600">{{ $payLatterTransaction->transaction_code }}</span>
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <!-- Receipt Card -->
                <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100 relative">
                    <!-- Status Banner -->
                    <div class="absolute top-0 right-0 p-6">
                        <span
                            class="inline-flex items-center px-4 py-1 rounded-full text-sm font-black border shadow-sm
                            @if ($payLatterTransaction->status === 'paid') bg-green-100 text-green-800 border-green-200
                            @elseif($payLatterTransaction->status === 'overdue') bg-red-100 text-red-800 border-red-200
                            @elseif($payLatterTransaction->status === 'pending') bg-yellow-100 text-yellow-800 border-yellow-200
                            @else bg-gray-100 text-gray-800 border-gray-200 @endif">
                            <i
                                class="fad 
                                @if ($payLatterTransaction->status === 'paid') fa-check-double
                                @elseif($payLatterTransaction->status === 'overdue') fa-exclamation-circle
                                @elseif($payLatterTransaction->status === 'pending') fa-clock
                                @else fa-ban @endif mr-2"></i>
                            {{ strtoupper($payLatterTransaction->status) }}
                        </span>
                    </div>

                    <div class="p-8">
                        <!-- User & Store Info -->
                        <div class="flex items-center space-x-4 mb-10">
                            <div
                                class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 text-2xl font-black border border-gray-200">
                                {{ strtoupper(substr($payLatterTransaction->account->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-black uppercase tracking-widest">Peminjam</p>
                                <h3 class="text-xl font-black text-gray-900">
                                    {{ $payLatterTransaction->account->user->name ?? 'Unknown' }}</h3>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <i class="fad fa-store mr-1.5 text-blue-500"></i>
                                    {{ $payLatterTransaction->account->toko->name ?? 'No Store' }}
                                </p>
                            </div>
                        </div>

                        <!-- Transaction Details -->
                        <div class="space-y-6">
                            <div class="border-t border-dashed border-gray-200 pt-6">
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-gray-500 font-medium">Pokok Pinjaman</span>
                                    <span class="font-mono font-bold text-gray-900">Rp
                                        {{ number_format($payLatterTransaction->principal_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-gray-500 font-medium">Bunga</span>
                                    <span class="font-mono font-bold text-red-500">+ Rp
                                        {{ number_format($payLatterTransaction->interest_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-gray-500 font-medium">Denda Keterlambatan</span>
                                    <span class="font-mono font-bold text-red-600">+ Rp
                                        {{ number_format($payLatterTransaction->penalty_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                                    <span class="text-gray-900 font-black text-lg uppercase tracking-tighter">Total
                                        Tagihan</span>
                                    <span class="font-mono font-black text-2xl text-gray-900">Rp
                                        {{ number_format($payLatterTransaction->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">
                                            Tanggal Pinjam</p>
                                        <p class="font-bold text-gray-800">
                                            {{ $payLatterTransaction->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Jatuh
                                            Tempo</p>
                                        <p
                                            class="font-bold {{ $payLatterTransaction->status !== 'paid' && $payLatterTransaction->due_date < now() ? 'text-red-600' : 'text-gray-800' }}">
                                            {{ $payLatterTransaction->due_date->format('d M Y') }}
                                        </p>
                                    </div>
                                    @if ($payLatterTransaction->paid_at)
                                        <div class="col-span-2 pt-4 border-t border-gray-200">
                                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">
                                                Tanggal Pelunasan</p>
                                            <p class="font-black text-green-600">
                                                {{ $payLatterTransaction->paid_at->format('d M Y, H:i') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        @if ($payLatterTransaction->status !== 'paid')
                            <div class="mt-10 pt-6 border-t border-gray-100 flex justify-end space-x-3">
                                @can('edit.paylatter')
                                    <a href="{{ route('paylatter.transaction.edit', $payLatterTransaction->id) }}"
                                        class="px-8 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-black rounded-xl shadow-lg hover:shadow-xl transition-all uppercase tracking-widest text-xs">
                                        Update Status Pelunasan
                                    </a>
                                @endcan
                            </div>
                        @endif
                    </div>

                    <!-- Decorative Zigzag Bottom -->
                    <div
                        class="h-4 bg-[radial-gradient(circle_at_10px_-7px,transparent_12px,#fff_13px)] bg-[length:20px_20px] absolute -bottom-4 left-0 right-0">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
