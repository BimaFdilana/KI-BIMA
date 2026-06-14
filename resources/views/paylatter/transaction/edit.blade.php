@extends('layouts.admin')

@section('title', 'Update Status Transaksi Paylatter')

@section('content')
    <div class="container-fluid px-4 py-6">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="flex items-center space-x-4 mb-6">
                <a href="{{ route('paylatter.transaction.index') }}"
                    class="p-2 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="fad fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Update Status Transaksi</h1>
                    <p class="text-gray-600">Kode: <span
                            class="font-mono font-black text-red-600">{{ $payLatterTransaction->transaction_code }}</span>
                    </p>
                </div>
            </div>

            <!-- Info Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 mb-6">
                <div class="p-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr($payLatterTransaction->account->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-black uppercase tracking-widest">Peminjam</p>
                            <p class="font-bold text-gray-900">{{ $payLatterTransaction->account->user->name ?? 'Unknown' }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400 font-black uppercase tracking-widest">Total Tagihan</p>
                        <p class="text-xl font-black text-red-600">Rp
                            {{ number_format($payLatterTransaction->total_amount, 0, ',', '.') }}</p>
                    </div>
                </div>

                <form action="{{ route('paylatter.transaction.update', $payLatterTransaction->id) }}" method="POST"
                    class="p-8 space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- Status Selection -->
                    <div class="space-y-4">
                        <label class="text-sm font-black text-gray-700 uppercase tracking-widest">Pilih Status Baru</label>
                        <div class="grid grid-cols-1 gap-4">
                            @foreach (['pending' => ['label' => 'Pending', 'desc' => 'Menunggu pembayaran dari pengguna', 'icon' => 'fa-clock', 'color' => 'text-yellow-600', 'bg' => 'bg-yellow-50'], 'paid' => ['label' => 'Lunas', 'desc' => 'Pembayaran telah diterima dan dikonfirmasi', 'icon' => 'fa-check-double', 'color' => 'text-green-600', 'bg' => 'bg-green-50'], 'overdue' => ['label' => 'Overdue', 'desc' => 'Melewati batas jatuh tempo pembayaran', 'icon' => 'fa-exclamation-circle', 'color' => 'text-red-600', 'bg' => 'bg-red-50'], 'cancelled' => ['label' => 'Dibatalkan', 'desc' => 'Transaksi dibatalkan oleh sistem atau admin', 'icon' => 'fa-ban', 'color' => 'text-gray-600', 'bg' => 'bg-gray-50']] as $value => $info)
                                <label
                                    class="relative flex items-center p-4 cursor-pointer rounded-xl border-2 transition-all hover:shadow-md
                                {{ old('status', $payLatterTransaction->status) === $value ? 'border-red-500 bg-red-50/30' : 'border-gray-100 bg-white' }}">
                                    <input type="radio" name="status" value="{{ $value }}" class="sr-only"
                                        {{ old('status', $payLatterTransaction->status) === $value ? 'checked' : '' }}>
                                    <div class="flex items-center space-x-4 w-full">
                                        <div class="p-3 {{ $info['bg'] }} {{ $info['color'] }} rounded-xl">
                                            <i class="fad {{ $info['icon'] }} text-2xl"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-black text-gray-900 uppercase tracking-tighter">
                                                {{ $info['label'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $info['desc'] }}</p>
                                        </div>
                                        @if (old('status', $payLatterTransaction->status) === $value)
                                            <div class="text-red-500">
                                                <i class="fas fa-check-circle text-xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('status')
                            <p class="text-xs text-red-500 mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($payLatterTransaction->status !== 'paid')
                        <div class="p-4 bg-blue-50 rounded-xl border border-blue-100 flex items-start space-x-3">
                            <i class="fad fa-info-circle text-blue-500 mt-0.5"></i>
                            <p class="text-xs text-blue-700 leading-relaxed">
                                <strong>Catatan:</strong> Mengubah status menjadi <span class="font-bold">LUNAS</span> akan
                                secara otomatis mengembalikan limit kredit pengguna sebesar nilai pokok pinjaman (Rp
                                {{ number_format($payLatterTransaction->principal_amount, 0, ',', '.') }}).
                            </p>
                        </div>
                    @endif

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-8 border-t border-gray-100">
                        <a href="{{ route('paylatter.transaction.index') }}"
                            class="px-8 py-3 border border-gray-200 text-gray-500 font-black rounded-xl hover:bg-gray-50 transition-all uppercase tracking-widest text-xs">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-black rounded-xl shadow-lg hover:shadow-xl transition-all uppercase tracking-widest text-xs">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
