@extends('layouts.admin')

@section('title', 'Edit Akun Paylatter')

@section('content')
    <div class="container-fluid px-4 py-6">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="flex items-center space-x-4 mb-6">
                <a href="{{ route('paylatter.account.index') }}"
                    class="p-2 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="fad fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Akun Paylatter</h1>
                    <p class="text-gray-600">Kelola limit kredit dan status akun pengguna</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                <form action="{{ route('paylatter.account.update', $payLatterAccount->id) }}" method="POST"
                    class="p-8 space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- User Info (Read Only) -->
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <div
                            class="w-12 h-12 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($payLatterAccount->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-black uppercase tracking-widest">Pengguna</p>
                            <p class="font-bold text-gray-900">{{ $payLatterAccount->user->name ?? 'Unknown' }}</p>
                        </div>
                    </div>

                    <!-- Credit Limit -->
                    <div class="space-y-2">
                        <label for="credit_limit" class="text-sm font-black text-gray-700 uppercase tracking-widest">Limit
                            Kredit (Rp)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-400 font-bold">Rp</span>
                            </div>
                            <input type="number" name="credit_limit" id="credit_limit"
                                value="{{ old('credit_limit', $payLatterAccount->credit_limit) }}"
                                class="block w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-red-500 focus:border-red-500 font-mono font-bold text-lg @error('credit_limit') border-red-500 @enderror"
                                placeholder="0" required>
                        </div>
                        @error('credit_limit')
                            <p class="text-xs text-red-500 mt-1 font-bold">{{ $message }}</p>
                        @enderror
                        <p class="text-[10px] text-gray-400 font-bold italic">Limit saat ini: Rp
                            {{ number_format($payLatterAccount->credit_limit, 0, ',', '.') }}</p>
                    </div>

                    <!-- Status Selection -->
                    <div class="space-y-4">
                        <label class="text-sm font-black text-gray-700 uppercase tracking-widest">Status Akun</label>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach (['active' => ['label' => 'Aktif', 'icon' => 'fa-check-circle', 'color' => 'text-green-600', 'bg' => 'bg-green-50'], 'suspended' => ['label' => 'Suspended', 'icon' => 'fa-ban', 'color' => 'text-red-600', 'bg' => 'bg-red-50'], 'pending' => ['label' => 'Pending', 'icon' => 'fa-clock', 'color' => 'text-yellow-600', 'bg' => 'bg-yellow-50'], 'closed' => ['label' => 'Closed', 'icon' => 'fa-times-circle', 'color' => 'text-gray-600', 'bg' => 'bg-gray-50']] as $value => $info)
                                <label
                                    class="relative flex items-center p-4 cursor-pointer rounded-xl border-2 transition-all hover:shadow-md
                                {{ old('status', $payLatterAccount->status) === $value ? 'border-red-500 bg-red-50/30' : 'border-gray-100 bg-white' }}">
                                    <input type="radio" name="status" value="{{ $value }}" class="sr-only"
                                        {{ old('status', $payLatterAccount->status) === $value ? 'checked' : '' }}>
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 {{ $info['bg'] }} {{ $info['color'] }} rounded-lg">
                                            <i class="fad {{ $info['icon'] }} text-xl"></i>
                                        </div>
                                        <span class="font-bold text-gray-900">{{ $info['label'] }}</span>
                                    </div>
                                    @if (old('status', $payLatterAccount->status) === $value)
                                        <div class="absolute top-2 right-2">
                                            <i class="fas fa-check-circle text-red-500"></i>
                                        </div>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                        @error('status')
                            <p class="text-xs text-red-500 mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-8 border-t border-gray-100">
                        <a href="{{ route('paylatter.account.index') }}"
                            class="px-8 py-3 border border-gray-200 text-gray-500 font-black rounded-xl hover:bg-gray-50 transition-all uppercase tracking-widest text-xs">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-black rounded-xl shadow-lg hover:shadow-xl transition-all uppercase tracking-widest text-xs">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
