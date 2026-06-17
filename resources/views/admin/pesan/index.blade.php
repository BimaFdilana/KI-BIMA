@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div>
            <h1 class="text-3xl font-black text-gray-800">
                Pesan Hubungi Kami
            </h1>

            <p class="mt-1 text-gray-500">
                Semua pesan yang dikirim pengunjung website Kedai Indonesia.
            </p>
        </div>

        <!-- Statistik -->
        <div class="flex flex-wrap gap-4">

            <!-- Total -->
            <div class="rounded-2xl bg-red-600 px-6 py-4 text-white shadow-lg">
                <p class="text-sm opacity-80">
                    Total Pesan
                </p>

                <h2 class="text-3xl font-bold">
                    {{ $pesans->count() }}
                </h2>
            </div>

            <!-- Belum Dibaca -->
            <div class="rounded-2xl bg-yellow-500 px-6 py-4 text-white shadow-lg">
                <p class="text-sm opacity-80">
                    Belum Dibaca
                </p>

                <h2 class="text-3xl font-bold">
                    {{ $pesans->where('is_read', false)->count() }}
                </h2>
            </div>

        </div>

    </div>

    <!-- Filter -->
    <div class="flex flex-wrap gap-3">

        <a href="{{ route('pesan.index') }}"
           class="rounded-xl px-5 py-3 font-semibold shadow transition
           {{ request('status') == null ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">

            Semua

        </a>

        <a href="{{ route('pesan.index', ['status' => 'unread']) }}"
           class="rounded-xl px-5 py-3 font-semibold shadow transition
           {{ request('status') == 'unread' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">

            Belum Dibaca

        </a>

        <a href="{{ route('pesan.index', ['status' => 'read']) }}"
           class="rounded-xl px-5 py-3 font-semibold shadow transition
           {{ request('status') == 'read' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">

            Sudah Dibaca

        </a>

    </div>

    <!-- Table Card -->
    <div class="overflow-hidden rounded-3xl bg-white shadow-xl">

        <!-- Header -->
        <div class="border-b bg-gradient-to-r from-red-600 to-red-500 px-6 py-5">

            <h2 class="text-xl font-bold text-white">
                Daftar Pesan Masuk
            </h2>

        </div>

        <!-- Table -->
        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="bg-gray-100 text-gray-700">

                    <tr>

                        <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider">
                            No
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider">
                            Pengirim
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider">
                            Email
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider">
                            Pesan
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider">
                            Status
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider">
                            Waktu
                        </th>

                        <th class="px-6 py-4 text-center text-sm font-bold uppercase tracking-wider">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse ($pesans as $index => $pesan)

                        <tr class="transition hover:bg-red-50/40
                            {{ !$pesan->is_read ? 'bg-yellow-50/40' : '' }}">

                            <!-- No -->
                            <td class="px-6 py-5 font-semibold text-gray-600">
                                {{ $index + 1 }}
                            </td>

                            <!-- Nama -->
                            <td class="px-6 py-5">

                                <div class="flex items-center gap-4">

                                    <!-- Avatar -->
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-lg font-bold text-red-600">

                                        {{ strtoupper(substr($pesan->nama, 0, 1)) }}

                                    </div>

                                    <div>

                                        <h3 class="font-bold text-gray-800">
                                            {{ $pesan->nama }}
                                        </h3>

                                        <p class="text-sm text-gray-500">
                                            Pengunjung Website
                                        </p>

                                    </div>

                                </div>

                            </td>

                            <!-- Email -->
                            <td class="px-6 py-5">

                                <a href="mailto:{{ $pesan->email }}"
                                   class="font-medium text-red-600 transition hover:text-red-800 hover:underline">

                                    {{ $pesan->email }}

                                </a>

                            </td>

                            <!-- Pesan -->
                            <td class="max-w-md px-6 py-5">

                                <div class="rounded-2xl bg-gray-50 p-4 leading-7 text-gray-700 shadow-sm">

                                    {{ $pesan->pesan }}

                                </div>

                            </td>

                            <!-- Status -->
                            <td class="px-6 py-5">

                                @if (!$pesan->is_read)

                                    <span class="rounded-full bg-yellow-100 px-4 py-2 text-xs font-bold text-yellow-700">

                                        Belum Dibaca

                                    </span>

                                @else

                                    <span class="rounded-full bg-green-100 px-4 py-2 text-xs font-bold text-green-700">

                                        Sudah Dibaca

                                    </span>

                                @endif

                            </td>

                            <!-- Waktu -->
                            <td class="px-6 py-5 text-sm text-gray-500 whitespace-nowrap">

                                {{ \Carbon\Carbon::parse($pesan->created_at)->translatedFormat('d F Y') }}

                                <br>

                                <span class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($pesan->created_at)->format('H:i') }} WIB
                                </span>

                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-5 text-center">

                                @if (!$pesan->is_read)

                                    <form action="{{ route('pesan.read', $pesan->id) }}"
      method="POST">

    @csrf
    @method('PUT')

    <button
        type="submit"
        class="rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-700">

        Tandai Dibaca

    </button>

</form>
                                @else

                                    <span class="text-sm font-medium text-gray-400">
                                        Sudah Dibaca
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="px-6 py-16 text-center">

                                <div class="flex flex-col items-center justify-center">

                                    <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-red-100">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-10 w-10 text-red-500"
                                             fill="none"
                                             viewBox="0 0 24 24"
                                             stroke="currentColor">

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V8a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z" />

                                        </svg>

                                    </div>

                                    <h3 class="text-xl font-bold text-gray-700">
                                        Belum Ada Pesan
                                    </h3>

                                    <p class="mt-2 text-gray-500">
                                        Pesan dari pengunjung website akan muncul di sini.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection