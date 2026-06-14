@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="overflow-hidden rounded-lg bg-white shadow-md">
            <div class="bg-blue-600 p-4">
                <h1 class="text-2xl font-bold text-white">Profil Pengguna</h1>
            </div>

            @if (session('success'))
                <div class="mb-4 border-l-4 border-green-500 bg-green-100 p-4 text-green-700" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <div class="rounded-lg bg-gray-100 p-4 text-center">
                            <div class="mx-auto mb-4 flex h-32 w-32 items-center justify-center rounded-full bg-gray-300">
                                <span class="text-4xl font-bold text-gray-600">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                            <p class="text-gray-600">{{ $user->email }}</p>

                            @if ($user->roles->isNotEmpty())
                                <div class="mt-3">
                                    @foreach ($user->roles as $role)
                                        <span class="rounded-full bg-blue-100 px-2 py-1 text-sm font-semibold text-blue-800">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="mt-4">
                                <p class="text-sm font-semibold">Status Profil:</p>
                                @if ($user->profile_completed)
                                    <span class="rounded-full bg-green-100 px-2 py-1 text-sm font-semibold text-green-800">
                                        Lengkap
                                    </span>
                                @else
                                    <span class="rounded-full bg-red-100 px-2 py-1 text-sm font-semibold text-red-800">
                                        Belum Lengkap
                                    </span>
                                @endif
                            </div>

                            <div class="mt-6">
                                <a href="{{ route('profile.edit') }}" class="rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
                                    Edit Profil
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <div class="rounded-lg bg-gray-50 p-6">
                            <h3 class="mb-4 border-b pb-2 text-lg font-semibold">Informasi Pribadi</h3>

                            <div class="space-y-4">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <p class="text-sm text-gray-600">Nama Lengkap</p>
                                        <p class="font-medium">{{ $user->name ?? 'Belum diisi' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Email</p>
                                        <p class="font-medium">{{ $user->email }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <p class="text-sm text-gray-600">Nomor Telepon</p>
                                        <p class="font-medium">{{ $user->phone_number ?? 'Belum diisi' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Nomor KTP</p>
                                        <p class="font-medium">{{ $user->ktp_number ?? 'Belum diisi' }}</p>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600">Alamat</p>
                                    <p class="font-medium">{{ $user->address ?? 'Belum diisi' }}</p>
                                </div>

                                @if ($user->ktp_image)
                                    <div>
                                        <p class="mb-2 text-sm text-gray-600">Foto KTP</p>
                                        <img src="{{ Storage::url($user->ktp_image) }}" alt="KTP" class="h-auto max-h-40 max-w-full rounded-lg border">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-6 rounded-lg bg-gray-50 p-6">
                            <h3 class="mb-4 border-b pb-2 text-lg font-semibold">Toko Saya</h3>

                            @if ($user->ownedTokos->isNotEmpty())
                                <div class="space-y-4">
                                    @foreach ($user->ownedTokos as $toko)
                                        <div class="rounded-lg border bg-white p-4 shadow-sm">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <h4 class="text-lg font-bold">{{ $toko->name }}</h4>
                                                    <p class="text-sm text-gray-600">{{ $toko->address }}</p>
                                                    <span class="@if ($toko->status == 'active') bg-green-100 text-green-800
                                                    @elseif($toko->status == 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800 @endif mt-2 inline-block rounded-full px-2 py-1 text-xs">
                                                        {{ ucfirst($toko->status) }}
                                                    </span>
                                                </div>
                                                <a href="{{ route('toko.dashboard', $toko->slug) }}" class="rounded bg-blue-500 px-3 py-1 text-sm text-white hover:bg-blue-600">
                                                    Lihat Toko
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-600">Anda belum memiliki toko.</p>
                                <a href="{{ route('toko.create') }}" class="mt-3 inline-block rounded-lg bg-green-500 px-4 py-2 text-white hover:bg-green-600">
                                    Buat Toko Baru
                                </a>
                            @endif
                        </div>

                        <div class="mt-6 rounded-lg bg-gray-50 p-6">
                            <h3 class="mb-4 border-b pb-2 text-lg font-semibold">Keanggotaan Toko</h3>

                            @if ($user->tokos->isNotEmpty())
                                <div class="space-y-4">
                                    @foreach ($user->tokos as $toko)
                                        @php
                                            $jabatan = \App\Models\JabatanModel::find($toko->pivot->jabatan_id);
                                        @endphp
                                        <div class="rounded-lg border bg-white p-4 shadow-sm">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <h4 class="text-lg font-bold">{{ $toko->name }}</h4>
                                                    <p class="text-sm text-gray-600">{{ $toko->address }}</p>
                                                    <p class="mt-1 text-sm">
                                                        <span class="text-gray-600">Jabatan:</span>
                                                        <span class="font-medium">{{ $jabatan->name }}</span>
                                                    </p>
                                                </div>
                                                <a href="{{ route('toko.dashboard', $toko->slug) }}" class="rounded bg-blue-500 px-3 py-1 text-sm text-white hover:bg-blue-600">
                                                    Lihat Toko
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-600">Anda belum bergabung dengan toko manapun.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
