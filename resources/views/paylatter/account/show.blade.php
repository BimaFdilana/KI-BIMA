@extends('layouts.admin')

@section('title', 'Detail Akun Paylatter')

@section('content')
    <div class="container-fluid px-4 py-6">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex items-center space-x-4 mb-6">
                <a href="{{ route('paylatter.account.index') }}"
                    class="p-2 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="fad fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Akun Paylatter</h1>
                    <p class="text-gray-600">Informasi limit dan riwayat kredit pengguna</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- User Info Card -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 p-6 text-center">
                        <div
                            class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 mx-auto flex items-center justify-center text-white text-3xl font-bold shadow-lg mb-4">
                            {{ strtoupper(substr($payLatterAccount->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $payLatterAccount->user->name ?? 'Unknown User' }}
                        </h2>
                        <p class="text-sm text-gray-500 mb-4">{{ $payLatterAccount->user->email ?? '-' }}</p>

                        <div
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border 
                            @if ($payLatterAccount->status === 'active') bg-green-100 text-green-800 border-green-200
                            @elseif($payLatterAccount->status === 'suspended') bg-red-100 text-red-800 border-red-200
                            @else bg-gray-100 text-gray-800 border-gray-200 @endif">
                            <i
                                class="fad @if ($payLatterAccount->status === 'active') fa-check-circle @elseif($payLatterAccount->status === 'suspended') fa-ban @else fa-clock @endif mr-1.5"></i>
                            {{ strtoupper($payLatterAccount->status) }}
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 p-6">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Informasi Toko</h3>
                        @if ($payLatterAccount->toko)
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                                    <i class="fad fa-store text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $payLatterAccount->toko->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $payLatterAccount->toko->address ?? 'No Address' }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-400 italic text-sm">Tidak terikat dengan toko spesifik</p>
                        @endif
                    </div>
                </div>

                <!-- Credit Info Card -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                        <div class="p-6 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Ringkasan Kredit</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-6 mb-8">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Limit Kredit
                                    </p>
                                    <p class="text-2xl font-black text-gray-900">Rp
                                        {{ number_format($payLatterAccount->credit_limit, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Sisa Limit</p>
                                    <p class="text-2xl font-black text-green-600">Rp
                                        {{ number_format($payLatterAccount->available_credit, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between text-sm font-bold">
                                    <span class="text-gray-600">Penggunaan Kredit</span>
                                    <span class="text-red-600">Rp
                                        {{ number_format($payLatterAccount->used_credit, 0, ',', '.') }}</span>
                                </div>
                                @php
                                    $percentage =
                                        $payLatterAccount->credit_limit > 0
                                            ? min(
                                                100,
                                                ($payLatterAccount->used_credit / $payLatterAccount->credit_limit) *
                                                    100,
                                            )
                                            : 0;
                                @endphp
                                <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden shadow-inner">
                                    <div class="bg-gradient-to-r from-red-400 to-red-600 h-full rounded-full transition-all duration-1000 shadow-sm"
                                        style="width: {{ $percentage }}%"></div>
                                </div>
                                <p class="text-right text-[10px] text-gray-500 font-bold uppercase tracking-tighter">
                                    {{ number_format($percentage, 1) }}% Terpakai</p>
                            </div>

                            <div class="grid grid-cols-3 gap-4 mt-8 pt-8 border-t border-gray-100">
                                <div class="text-center">
                                    <p class="text-[10px] text-gray-400 uppercase font-black mb-1">Skor Kredit</p>
                                    <div class="flex justify-center space-x-0.5">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i
                                                class="fas fa-star {{ $i <= $payLatterAccount->payment_history_score ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <div class="text-center">
                                    <p class="text-[10px] text-gray-400 uppercase font-black mb-1">Lancar</p>
                                    <p class="text-lg font-black text-green-600">
                                        {{ $payLatterAccount->successful_payments }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-[10px] text-gray-400 uppercase font-black mb-1">Terlambat</p>
                                    <p class="text-lg font-black text-red-600">{{ $payLatterAccount->late_payments }}</p>
                                </div>
                            </div>
                        </div>

                        @can('edit.paylatter')
                            <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end">
                                <a href="{{ route('paylatter.account.edit', $payLatterAccount->id) }}"
                                    class="px-6 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-lg shadow-md transition-all">
                                    Kelola Akun & Limit
                                </a>
                            </div>
                        @endcan
                    </div>

                    <!-- Recent Transactions -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                        <div class="p-6 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Transaksi Terakhir</h3>
                        </div>
                        <div class="p-0">
                            <table class="w-full text-left">
                                <thead
                                    class="bg-gray-50 text-[10px] uppercase font-black text-gray-500 border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-3">Kode</th>
                                        <th class="px-6 py-3">Jumlah</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3">Jatuh Tempo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($payLatterAccount->transactions()->latest()->limit(5)->get() as $tx)
                                        <tr class="hover:bg-gray-50 transition-colors cursor-pointer"
                                            onclick="window.location.href='{{ route('paylatter.transaction.show', $tx->id) }}'">
                                            <td class="px-6 py-4">
                                                <span
                                                    class="font-mono font-bold text-red-600 text-xs">{{ $tx->transaction_code }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="font-bold text-gray-900">Rp
                                                    {{ number_format($tx->total_amount, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="px-2 py-0.5 rounded text-[10px] font-black border 
                                                    @if ($tx->status === 'paid') bg-green-50 text-green-700 border-green-100
                                                    @elseif($tx->status === 'overdue') bg-red-50 text-red-700 border-red-100
                                                    @else bg-yellow-50 text-yellow-700 border-yellow-100 @endif">
                                                    {{ strtoupper($tx->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-xs text-gray-500">
                                                {{ $tx->due_date->format('d M Y') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic text-sm">
                                                Belum ada transaksi</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
