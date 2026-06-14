@extends('admin.layouts.app')

@section('content')

<!-- 🔥 BOUNDARY WRAPPER -->
<div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">

    <!-- HEADER -->
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div>
            <h1 class="text-2xl font-bold md:text-3xl">Data Produk</h1>
            <p class="mt-2 text-sm text-gray-500">
                Kelola semua produk website Anda
            </p>
        </div>

        <a href="{{ route('product.create') }}"
           class="w-full md:w-auto rounded-2xl bg-red-600 px-6 py-3 text-center text-white font-semibold hover:scale-105 transition">
            + Tambah Produk
        </a>

    </div>

    <!-- ALERT -->
    @if(session('success'))
        <div class="mb-6 rounded-2xl bg-green-100 px-6 py-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <!-- TABLE DESKTOP -->
    <div class="hidden lg:block bg-white rounded-3xl shadow-xl overflow-hidden">

        <!-- HEADER TABLE -->
        <div class="border-b bg-gray-50 px-6 py-4">
            <h2 class="text-xl font-bold">List Produk</h2>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full min-w-[900px] divide-y divide-gray-200">

                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-4 text-left">Gambar</th>
                        <th class="px-4 py-4 text-left">Detail</th>
                        <th class="px-4 py-4 text-left">Kategori</th>
                        <th class="px-4 py-4 text-left">Badge</th>
                        <th class="px-4 py-4 text-left">Fitur</th>
                        <th class="px-4 py-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($products as $item)

                    <tr class="hover:bg-gray-50">

                        <!-- GAMBAR -->
                        <td class="px-4 py-4">
                            @if($item->gambar)
                                <img src="{{ asset('storage/' . $item->gambar) }}"
                                     class="h-16 w-16 rounded-xl object-cover">
                            @else
                                <div class="h-16 w-16 bg-gray-200 flex items-center justify-center rounded-xl text-xs">
                                    No Image
                                </div>
                            @endif
                        </td>

                        <!-- DETAIL -->
                        <td class="px-4 py-4 max-w-xs">
                            <h3 class="font-bold truncate">
                                {{ $item->nama }}
                            </h3>
                            <p class="text-sm text-gray-500 truncate">
                                {{ $item->subtitle }}
                            </p>
                            <p class="text-sm text-gray-600 line-clamp-2">
                                {{ $item->deskripsi }}
                            </p>
                        </td>

                        <!-- KATEGORI -->
                        <td class="px-4 py-4">
                            @if($item->kategori == 'perlengkapan_fisik')
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">
                                    Fisik
                                </span>
                            @elseif($item->kategori == 'plugin')
                                <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs">
                                    Plugin
                                </span>
                            @elseif($item->kategori == 'ebook')
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">
                                    E-Book
                                </span>
                            @endif
                        </td>

                        <!-- BADGE -->
                        <td class="px-4 py-4">
                            @if($item->badge)
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">
                                    {{ $item->badge }}
                                </span>
                            @else
                                -
                            @endif
                        </td>

                        <!-- FITUR -->
                        <td class="px-4 py-4">
                            @if($item->fitur)
                                <ul class="text-sm text-gray-600 space-y-1">
                                    @foreach(array_slice($item->fitur, 0, 2) as $fitur)
                                        <li>• {{ $fitur }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-400">Tidak ada</span>
                            @endif
                        </td>

                        <!-- AKSI -->
                        <td class="px-4 py-4">
                            <div class="flex gap-2 justify-center">

                                <a href="{{ route('product.edit', $item->id) }}"
                                   class="bg-yellow-400 text-white px-3 py-1 rounded-lg text-sm">
                                    Edit
                                </a>

                                <form action="{{ route('product.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button onclick="return confirm('Hapus?')"
                                            class="bg-red-600 text-white px-3 py-1 rounded-lg text-sm">
                                        Hapus
                                    </button>

                                </form>

                            </div>
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-500">
                            Belum ada data produk
                        </td>
                    </tr>
                @endforelse

                </tbody>

            </table>
        </div>

    </div>

    <!-- MOBILE CARD -->
    <div class="lg:hidden space-y-5">

        @forelse($products as $item)

        <div class="bg-white rounded-3xl shadow-lg overflow-hidden">

            @if($item->gambar)
                <img src="{{ asset('storage/' . $item->gambar) }}"
                     class="h-52 w-full object-cover">
            @endif

            <div class="p-5">

                <h3 class="text-xl font-bold">{{ $item->nama }}</h3>
                <p class="text-sm text-gray-500">{{ $item->subtitle }}</p>

                <p class="mt-3 text-sm text-gray-600 line-clamp-3">
                    {{ $item->deskripsi }}
                </p>

                <div class="mt-4 flex gap-2 flex-wrap">

                    <span class="bg-gray-100 px-3 py-1 rounded-full text-xs">
                        {{ $item->kategori }}
                    </span>

                    @if($item->badge)
                        <span class="bg-red-100 px-3 py-1 rounded-full text-xs">
                            {{ $item->badge }}
                        </span>
                    @endif

                </div>

                <div class="mt-5 flex gap-3">

                    <a href="{{ route('product.edit', $item->id) }}"
                       class="flex-1 bg-yellow-400 text-white py-2 rounded-xl text-center">
                        Edit
                    </a>

                    <form action="{{ route('product.destroy', $item->id) }}"
                          method="POST"
                          class="flex-1">
                        @csrf
                        @method('DELETE')

                        <button class="w-full bg-red-600 text-white py-2 rounded-xl">
                            Hapus
                        </button>

                    </form>

                </div>

            </div>

        </div>

        @empty

        <div class="bg-white p-10 text-center rounded-3xl text-gray-500">
            Belum ada produk
        </div>

        @endforelse

    </div>

</div>

@endsection