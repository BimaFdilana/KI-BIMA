@extends('admin.layouts.app')

@section('content')

<div class="p-6">

    <div class="mb-6 flex items-center justify-between">

        <h1 class="text-2xl font-bold">
            Data Plug In
        </h1>

        <a href="{{ route('plug-in.create') }}"
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
                        Subtitle
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

                        {{ $item->subtitle }}

                    </td>

                    <td class="px-4 py-4">

                        <ul class="list-disc pl-5">

                            @foreach($item->fitur as $fitur)

                            <li>{{ $fitur }}</li>

                            @endforeach

                        </ul>

                    </td>

                    <td class="px-4 py-4 text-center">

                        <div class="flex justify-center gap-2">

                            <a href="#"
                               class="rounded bg-yellow-400 px-3 py-1 text-white">

                                Edit

                            </a>

                            <button class="rounded bg-red-600 px-3 py-1 text-white">

                                Hapus

                            </button>

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