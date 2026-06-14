@extends('admin.layouts.app')

@section('content')

<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <h1 class="text-3xl font-bold text-gray-800">
            Data Laporan
        </h1>

        <a href="{{ route('laporan.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-5 py-2.5 font-semibold text-white shadow-md transition hover:scale-105 hover:shadow-lg">

            + Tambah Laporan

        </a>

    </div>

    <!-- Success Alert -->
    @if(session('success'))

        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700 shadow-sm">
            {{ session('success') }}
        </div>

    @endif

    <!-- Table Card -->
    <div class="overflow-hidden rounded-2xl bg-white shadow-lg">

        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <!-- Head -->
                <thead class="bg-red-600 text-white">

                    <tr>

                        <th class="px-6 py-4 text-center font-semibold w-16">
                            No
                        </th>

                        <th class="px-6 py-4 text-center font-semibold">
                            Gambar
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Judul
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Deskripsi
                        </th>

                        <th class="px-6 py-4 text-center font-semibold">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <!-- Body -->
                <tbody class="divide-y divide-gray-100">

                    @forelse($laporans as $laporan)

                    <tr class="transition hover:bg-red-50/40">

                        <!-- No -->
                        <td class="px-6 py-4 text-center font-medium text-gray-700">
                            {{ $loop->iteration }}
                        </td>

                        <!-- Gambar -->
                        <td class="px-6 py-4">

                            @if($laporan->gambar)

                                <div class="flex justify-center">

                                    <img
                                        src="{{ asset('storage/'.$laporan->gambar) }}"
                                        alt="{{ $laporan->judul }}"
                                        class="h-20 w-32 rounded-lg object-cover shadow">

                                </div>

                            @else

                                <div class="text-center text-gray-400">
                                    Tidak ada gambar
                                </div>

                            @endif

                        </td>

                        <!-- Judul -->
                        <td class="px-6 py-4 font-semibold text-gray-800">
                            {{ $laporan->judul }}
                        </td>

                        <!-- Deskripsi -->
                        <td class="px-6 py-4 text-gray-600">
                            {{ \Illuminate\Support\Str::limit($laporan->deskripsi, 120) }}
                        </td>

                        <!-- Aksi -->
                        <td class="px-6 py-4">

                            <div class="flex flex-wrap items-center justify-center gap-2">

                                <!-- Edit -->
                                <a href="{{ route('laporan.edit',$laporan->id) }}"
                                   class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-yellow-500">

                                    Edit

                                </a>

                                <!-- Delete -->
                                <form action="{{ route('laporan.destroy',$laporan->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-red-700">

                                        Hapus

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">

                            <div class="flex flex-col items-center gap-2">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-14 w-14 text-gray-300"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="1.5"
                                          d="M9 17v-2a4 4 0 014-4h4m0 0l-3-3m3 3l-3 3"/>

                                </svg>

                                <p class="text-lg font-medium">
                                    Belum ada data laporan
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