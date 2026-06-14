@extends('admin.layouts.app')

@section('content')

<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <h1 class="text-3xl font-bold text-gray-800">
            Artikel
        </h1>

        <a href="{{ route('artikel.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-5 py-2.5 font-semibold text-white shadow-md transition hover:scale-105 hover:shadow-lg">

            + Tambah Artikel

        </a>

    </div>

    <!-- Table Card -->
    <div class="overflow-hidden rounded-2xl bg-white shadow-lg">

        <!-- Table -->
        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <!-- Head -->
                <thead class="bg-red-600 text-white">

                    <tr>

                        <th class="px-6 py-4 text-left font-semibold">
                            Judul
                        </th>

                        <th class="px-6 py-4 text-center font-semibold">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <!-- Body -->
                <tbody class="divide-y divide-gray-100">

                    @foreach ($artikels as $artikel)

                    <tr class="transition hover:bg-red-50/40">

                        <!-- Judul -->
                        <td class="px-6 py-4 font-medium text-gray-700">
                            {{ $artikel->judul }}
                        </td>

                        <!-- Aksi -->
                        <td class="px-6 py-4">

    <div class="flex items-center justify-center gap-3">

        <!-- Detail -->
        <a href="{{ route('artikel.show', $artikel->id) }}"
           class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-blue-600">

            Detail

        </a>

        <!-- Edit -->
        <a href="{{ route('artikel.edit', $artikel->id) }}"
           class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-yellow-500">

            Edit

        </a>

        <!-- Delete -->
        <form action="{{ route('artikel.destroy', $artikel->id) }}"
              method="POST"
              onsubmit="return confirm('Yakin ingin menghapus artikel ini?')">

            @csrf
            @method('DELETE')

            <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-red-700">

                Hapus

            </button>

        </form>

    </div>

</td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection