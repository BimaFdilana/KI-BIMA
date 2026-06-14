@extends('layouts.guest')

@section('title', 'artikel')

@section('content')

    <!-- <section class="relative w-full min-h-[450px] sm:min-h-[500px] flex items-center px-6 py-24 text-white overflow-hidden bg-red-950">
        
        <div class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat opacity-40 mix-blend-overlay pointer-events-none"
             style="background-image: url('{{ asset('storage/asset_images/images/carousel/bglogin.png') }}');">
        </div>
        
        <div class="absolute inset-0 bg-gradient-to-tr from-black via-red-950/95 to-red-900/90 z-10"></div>

        <div class="container mx-auto max-w-7xl relative z-20">
            <div class="max-w-3xl text-left">
                <p class="mb-3 text-xs sm:text-sm font-bold tracking-widest uppercase text-yellow-400 drop-shadow">
                    Kabar & Kegiatan Terbaru
                </p>
                
                <h1 class="text-3xl font-black tracking-tight sm:text-5xl md:text-6xl text-white leading-tight drop-shadow-[0_4px_8px_rgba(0,0,0,0.7)]">
                    Artikel & Transformasi <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 via-amber-400 to-yellow-200">Kedai Indonesia</span>
                </h1>
                
                <p class="mt-6 text-sm sm:text-base text-gray-100 max-w-xl font-medium leading-relaxed drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)]">
                    Informasi lengkap tentang kegiatan operasional, perkembangan jaringan UMKM, mitos kopi, dan program-program inspiratif digital ke seluruh pelosok industri nusantara.
                </p>

                <div class="mt-8 flex flex-wrap gap-4 items-center">
                    <a href="#list-artikel" 
                       class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full border border-white/50 bg-white/5 text-sm font-semibold text-white tracking-wide backdrop-blur-sm transition-all duration-300 hover:bg-white hover:text-red-950 hover:border-white shadow-md">
                        <span>Jelajahi Semua Artikel</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    
     -->

     <section
    class="relative overflow-hidden bg-gradient-to-br from-red-900 via-red-700 to-red-600 px-4 py-24 text-white">

    <!-- Blur Effect -->
    <div class="absolute -left-20 -top-20 h-72 w-72 rounded-full bg-white/20 blur-3xl"></div>

    <div class="absolute bottom-0 right-0 h-96 w-96 rounded-full bg-red-300/20 blur-3xl"></div>

    <div class="relative z-10 container mx-auto max-w-7xl text-center">

        <div
            class="mb-6 inline-flex items-center rounded-full border border-white/20 bg-white/10 px-5 py-2 text-sm font-medium backdrop-blur">

            Kabar & Kegiatan Terbaru

        </div>

        <h1 class="text-5xl font-black md:text-7xl">

            Artikel & Transformasi

            <span class="block text-yellow-300">
                Kedai Indonesia
            </span>

        </h1>

        <p class="mx-auto mt-6 max-w-3xl text-lg text-red-100">

            Informasi lengkap tentang kegiatan operasional, perkembangan jaringan UMKM,
            mitos kopi, dan program-program inspiratif digital ke seluruh pelosok
            industri nusantara.

        </p>

        <div class="mt-10 flex flex-wrap justify-center gap-4">

            <a href="#list-artikel"
                class="rounded-full bg-white px-8 py-4 font-bold text-red-700 transition hover:scale-105">

                Jelajahi Semua Artikel

            </a>

            <a href="{{ route('hubungi-kami') }}"
                class="rounded-full border border-white px-8 py-4 font-bold text-white transition hover:bg-white hover:text-red-700">

                Hubungi Kami

            </a>

        </div>

    </div>

</section>

    <section id="list-artikel" class="bg-gray-50 px-4 py-16 relative z-20">
        <div class="container mx-auto max-w-7xl">

            <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">

                @forelse ($artikels as $artikel)
                    <div class="group flex flex-col justify-between overflow-hidden rounded-xl bg-white border border-gray-100 p-3 shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl">
                        
                        <div>
                            <div class="relative overflow-hidden rounded-lg bg-gray-900 aspect-[16/9] shadow-[inner_0_4px_10px_rgba(0,0,0,0.3)]">
                                <img src="{{ asset('storage/' . $artikel->gambar) }}"
                                     alt="{{ $artikel->judul }}"
                                     class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105 opacity-95 group-hover:opacity-100">
                            </div>

                            <div class="pt-4 pb-3 px-1">
                                <div class="flex items-center gap-3 mb-2 text-[10px] font-semibold uppercase tracking-wider text-gray-400">
                                    <span class="flex items-center gap-1">
                                        📅 {{ \Carbon\Carbon::parse($artikel->published_at)->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        👁️ {{ $artikel->views }}
                                    </span>
                                </div>

                                <h3 class="mb-1.5 text-base font-bold text-gray-800 transition-colors duration-300 group-hover:text-red-600 line-clamp-2 leading-snug">
                                    {{ $artikel->judul }}
                                </h3>

                                <p class="text-xs text-gray-500 leading-relaxed line-clamp-3">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($artikel->isi), 85) }}
                                </p>
                            </div>
                        </div>

                        <div class="px-1 pb-1 pt-0">
                            <a href="{{ route('artikel.show', $artikel->slug) }}"
                               class="inline-flex items-center justify-center gap-2 w-full px-3 py-2 rounded-lg bg-gray-50 text-xs font-bold text-gray-600 border border-gray-100 transition-all duration-300 group-hover:bg-red-600 group-hover:text-white group-hover:border-red-600 active:scale-95">
                                <span>Baca Artikel</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform duration-300 group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full text-center py-20 rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
                        <p class="text-gray-500 text-sm font-medium">Belum ada artikel tersedia saat ini.</p>
                    </div>
                @endforelse

            </div>

        </div>
    </section>

    

@endsection