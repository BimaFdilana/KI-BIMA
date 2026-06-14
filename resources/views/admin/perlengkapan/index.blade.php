@extends('admin.layouts.app')

@section('content')

<div class="p-6">

    <div class="mb-6 flex items-center justify-between">

        <h1 class="text-2xl font-bold">
            Data Perlengkapan Fisik
        </h1>

        <a href="{{ route('perlengkapan-fisik.create') }}"
           class="rounded-lg bg-red-600 px-4 py-2 text-white">

            Tambah Data

        </a>

    </div>

    <div class="overflow-x-auto rounded-xl bg-white shadow">

        <table class="min-w-full">

            <thead class="bg-red-600 text-white">

                <tr>

                    <th class="px-4 py-3 text-left">
                        Gambar
                    </th>

                    <th class="px-4 py-3 text-left">
                        Nama
                    </th>

                    <th class="px-4 py-3 text-left">
                        Badge
                    </th>

                    <th class="px-4 py-3 text-left">
                        Fitur
                    </th>

                    <th class="px-4 py-3 text-center">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($data as $item)

                <tr class="border-b">

                    <td class="px-4 py-4">

                        <img src="{{ asset('storage/' . $item->gambar) }}"
                             class="h-20 w-20 rounded-lg object-cover">

                    </td>

                    <td class="px-4 py-4">

                        {{ $item->nama }}

                    </td>

                    <td class="px-4 py-4">

                        {{ $item->badge }}

                    </td>

                    <td class="px-4 py-4">

                        <ul class="list-disc pl-5">

                            @foreach($item->fitur as $fitur)

                            <li>{{ $fitur }}</li>

                            @endforeach

                        </ul>

                    </td>

                   <td class="px-6 py-5 text-center">

    <div class="flex items-center justify-center gap-3">

        <!-- Tombol Edit -->
        <a href="{{ route('perlengkapan-fisik.edit', $item->id) }}"
           class="rounded-xl bg-yellow-400 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-yellow-500">

            ✏️ Edit

        </a>

        <!-- Tombol Delete -->
        <form action="{{ route('perlengkapan-fisik.destroy', $item->id) }}"
              method="POST"
              onsubmit="return confirm('Yakin ingin menghapus data ini?')">

            @csrf
            @method('DELETE')

            <button type="submit"
                    class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-red-700">

                🗑 Hapus

            </button>

        </form>

    </div>

</td>

                </tr>

                @empty

                <tr>

                    <td colspan="5"
                        class="py-6 text-center text-gray-500">

                        Data masih kosong

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection