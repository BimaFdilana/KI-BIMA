@extends('layouts.guest')

@section('title', $artikel->judul)

@section('content')

<!-- Hero Artikel -->
<section class="relative overflow-hidden bg-gradient-to-br from-red-800 via-red-700 to-red-500 py-20 text-white">

    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/20"></div>

    <!-- Background blur -->
    <div class="absolute inset-0 opacity-10">
        <img src="{{ asset('storage/' . $artikel->gambar) }}"
            class="h-full w-full object-cover">
    </div>

    <div class="relative container mx-auto max-w-5xl px-4">

        <!-- Breadcrumb -->
        <div class="mb-6 flex items-center gap-2 text-sm text-white/80">
            <a href="/" class="transition hover:text-yellow-300">
                Beranda
            </a>

            <span>/</span>

            <a href="{{ route('artikel') }}"
                class="transition hover:text-yellow-300">
                Artikel
            </a>

            <span>/</span>

            <span class="line-clamp-1 text-white">
                {{ $artikel->judul }}
            </span>
        </div>

        <!-- Judul -->
        <h1 class="max-w-4xl text-4xl font-black leading-tight md:text-6xl">
            {{ $artikel->judul }}
        </h1>

        <!-- Info -->
        <div class="mt-8 flex flex-wrap items-center gap-6 text-sm text-white/90">

            <!-- Tanggal -->
            <!-- Tanggal -->
<div class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 backdrop-blur-sm">

    <svg xmlns="http://www.w3.org/2000/svg"
        class="h-5 w-5"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor">

        <path stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z" />
    </svg>

    <span>
        {{ \Carbon\Carbon::parse($artikel->published_at)->translatedFormat('d F Y - H:i') }}
    </span>

</div>

<!-- Views -->
<div class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 backdrop-blur-sm">

    <svg xmlns="http://www.w3.org/2000/svg"
        class="h-5 w-5"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor">

        <path stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

        <path stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
    </svg>

    <span>
        {{ $artikel->views }} views
    </span>

</div>

            <!-- Author -->
            <div class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>

                <span>
                    Admin Kedai Indonesia
                </span>
            </div>

        </div>

    </div>
</section>

<!-- Content Artikel -->
<section class="bg-gray-100 px-4 py-16">

    <div class="container mx-auto max-w-5xl">

        <!-- Card Utama -->
        <div class="overflow-hidden rounded-[2rem] bg-white shadow-2xl">

            <!-- Gambar -->
            <div class="relative">

                <img src="{{ asset('storage/' . $artikel->gambar) }}"
                    class="h-[250px] w-full object-cover md:h-[500px]">

                <!-- Gradient -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>

            </div>

            <!-- Isi -->
            <div class="p-6 md:p-12">

                <!-- Deskripsi -->
                @if ($artikel->deskripsi_singkat)

                    <div class="mb-10 rounded-2xl border-l-4 border-red-500 bg-red-50 p-6">

                        <p class="text-lg italic leading-8 text-gray-700">
                            {{ $artikel->deskripsi_singkat }}
                        </p>

                    </div>

                @endif

                <!-- Konten Artikel -->
                <div class="prose prose-lg max-w-none
                    prose-headings:mb-4
                    prose-headings:font-bold
                    prose-headings:text-gray-900
                    prose-p:leading-8
                    prose-p:text-gray-700
                    prose-img:rounded-2xl
                    prose-img:shadow-lg
                    prose-a:text-red-600
                    hover:prose-a:text-red-700">

                    {!! $artikel->isi !!}

                </div>

            </div>

        </div>

        <!-- Tombol -->
        <div class="mt-12 text-center">

            <a href="{{ route('artikel') }}"
                class="inline-flex items-center gap-3 rounded-2xl bg-red-600 px-7 py-4 text-lg font-semibold text-white shadow-lg transition duration-300 hover:-translate-y-1 hover:bg-red-700 hover:shadow-2xl">

                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7" />

                </svg>

                Kembali ke Artikel

            </a>

        </div>

    </div>

</section>

<x-guest.download-footer />
<x-guest.footer-section />

@endsection