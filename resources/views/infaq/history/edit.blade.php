@extends('layouts.admin')

@section('title', 'Ubah Status Donasi')

@section('content')
    <div class="container-fluid px-4 py-6">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="flex items-center space-x-4 mb-6">
                <a href="{{ route('infaq.history.show', $infaqHistory->id) }}"
                    class="p-2 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="fad fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Ubah Status Donasi</h1>
                    <p class="text-gray-600">Transaksi #INF-{{ str_pad($infaqHistory->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 bg-gray-50 border-b border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Donatur</p>
                            <p class="font-bold text-gray-900">{{ $infaqHistory->donor_name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Jumlah</p>
                            <p class="font-bold text-red-600 text-lg">Rp
                                {{ number_format($infaqHistory->amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('infaq.history.update', $infaqHistory->id) }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <label class="text-sm font-semibold text-gray-700">Pilih Status Baru</label>
                        <div class="grid grid-cols-1 gap-3">
                            @foreach ($statuses as $value => $label)
                                <label
                                    class="relative flex items-center p-4 cursor-pointer rounded-xl border-2 transition-all hover:bg-gray-50
                                @if (old('status', $infaqHistory->status) == $value) border-red-500 bg-red-50 @else border-gray-100 @endif">
                                    <input type="radio" name="status" value="{{ $value }}" class="sr-only"
                                        {{ old('status', $infaqHistory->status) == $value ? 'checked' : '' }}
                                        onchange="this.closest('form').querySelectorAll('label').forEach(l => { l.classList.remove('border-red-500', 'bg-red-50'); l.classList.add('border-gray-100'); }); this.parentElement.classList.add('border-red-500', 'bg-red-50'); this.parentElement.classList.remove('border-gray-100');">

                                    <div class="flex items-center justify-between w-full">
                                        <div class="flex items-center">
                                            <div
                                                class="p-2 rounded-lg mr-3
                                            @if ($value === 'completed') bg-green-100 text-green-600
                                            @elseif($value === 'pending') bg-yellow-100 text-yellow-600
                                            @elseif($value === 'failed') bg-red-100 text-red-600
                                            @else bg-gray-100 text-gray-600 @endif">
                                                <i
                                                    class="fad 
                                                @if ($value === 'completed') fa-check-circle
                                                @elseif($value === 'pending') fa-clock
                                                @elseif($value === 'failed') fa-times-circle
                                                @else fa-ban @endif text-xl"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900">{{ $label }}</p>
                                                <p class="text-xs text-gray-500">
                                                    @if ($value === 'completed')
                                                        Tandai transaksi sebagai berhasil dan dana masuk.
                                                    @elseif($value === 'pending')
                                                        Transaksi masih menunggu proses verifikasi.
                                                    @elseif($value === 'failed')
                                                        Tandai transaksi sebagai gagal/ditolak.
                                                    @else
                                                        Batalkan transaksi ini.
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div
                                            class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center
                                        @if (old('status', $infaqHistory->status) == $value) border-red-500 @endif">
                                            @if (old('status', $infaqHistory->status) == $value)
                                                <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('status')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 flex items-start space-x-3">
                        <i class="fad fa-info-circle text-blue-500 mt-0.5"></i>
                        <p class="text-xs text-blue-700 leading-relaxed">
                            <strong>Penting:</strong> Perubahan status ke <span class="font-bold">Selesai</span> akan secara
                            otomatis menambah total dana terkumpul pada pos infaq terkait. Pastikan dana sudah benar-benar
                            diterima sebelum mengubah status.
                        </p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('infaq.history.show', $infaqHistory->id) }}"
                            class="px-6 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
