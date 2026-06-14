@extends('layouts.guest')

@section('title', $item->nama)

@section('content')

<div class="min-h-screen bg-gray-50 py-16">

    <div class="container mx-auto px-6">

        <div class="overflow-hidden rounded-3xl bg-white shadow-2xl">

            <div class="grid grid-cols-1 lg:grid-cols-2">

                <!-- Gambar -->
                <div class="bg-gradient-to-br from-red-600 to-red-700 p-10">

                    @if($item->gambar)

                        <img src="{{ asset('storage/' . $item->gambar) }}"
                             alt="{{ $item->nama }}"
                             class="mx-auto h-[500px] w-full rounded-2xl object-cover shadow-2xl">

                    @else

                        <div class="flex h-[500px] items-center justify-center rounded-2xl bg-white/10">

                            <i class="fas fa-book text-8xl text-white"></i>

                        </div>

                    @endif

                </div>

                <!-- Detail -->
                <div class="p-10">

                    @if($item->badge)

                        <span class="inline-block rounded-full bg-red-100 px-4 py-2 text-sm font-bold text-red-700">

                            {{ $item->badge }}

                        </span>

                    @endif

                    <h1 class="mt-4 text-4xl font-bold text-gray-900">

                        {{ $item->nama }}

                    </h1>

                    @if($item->subtitle)

                        <p class="mt-3 text-xl text-gray-500">

                            {{ $item->subtitle }}

                        </p>

                    @endif

                    @if($item->harga)

                        <div class="mt-6">

                            <span class="text-3xl font-bold text-red-600">

                                Rp {{ number_format($item->harga, 0, ',', '.') }}

                            </span>

                        </div>

                    @endif

                    <!-- Deskripsi -->
                    <div class="mt-8">

                        <h2 class="mb-3 text-2xl font-bold text-gray-800">

                            Deskripsi E-Book

                        </h2>

                        <p class="leading-relaxed text-gray-600">

                            {{ $item->deskripsi }}

                        </p>

                    </div>

                    <!-- Download -->
                    <div class="mt-10">

                        @if($item->file_ebook)

                            <a href="{{ asset('storage/' . $item->file_ebook) }}"
                               target="_blank"
                               class="inline-flex items-center rounded-2xl bg-red-600 px-8 py-4 font-bold text-white transition hover:bg-red-700">

                                <i class="fas fa-download mr-3"></i>

                                Download E-Book

                            </a>

                        @else

                            <button
                                class="cursor-not-allowed rounded-2xl bg-gray-400 px-8 py-4 font-bold text-white">

                                File Belum Tersedia

                            </button>

                        @endif

                    </div>

                    <!-- Back -->
                    <div class="mt-6">

                        <a href="{{ route('produk') }}"
                           class="inline-flex items-center text-red-600 hover:text-red-700">

                            <i class="fas fa-arrow-left mr-2"></i>

                            Kembali ke Produk

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection