@extends('layouts.guest')

@section('title', 'Produk')
<style>
    .hero-pattern{
    position:absolute;
    inset:0;

    background-image:
    linear-gradient(
        30deg,
        rgba(255,255,255,.06) 12%,
        transparent 12.5%,
        transparent 87%,
        rgba(255,255,255,.06) 87.5%
    ),
    linear-gradient(
        150deg,
        rgba(255,255,255,.06) 12%,
        transparent 12.5%,
        transparent 87%,
        rgba(255,255,255,.06) 87.5%
    ),
    linear-gradient(
        90deg,
        rgba(255,255,255,.04) 2%,
        transparent 2.5%,
        transparent 97%,
        rgba(255,255,255,.04) 97.5%
    );

    background-size:80px 138px;
    opacity:.8;
}
.hero-slide{
    animation: zoomHero 15s ease-in-out infinite;
}

@keyframes zoomHero{
    0%{
        transform:scale(1);
    }
    50%{
        transform:scale(1.08);
    }
    100%{
        transform:scale(1);
    }
}
.hero-glow{
    position:absolute;
    width:500px;
    height:500px;
    background:rgba(255,255,255,.15);
    filter:blur(120px);
    border-radius:999px;
    top:-150px;
    left:50%;
    transform:translateX(-50%);
}

/* PREMIUM HONEYCOMB */
.hero-honeycomb{
    position:absolute;
    right:8%;
    top:50%;
    transform:translateY(-50%);
    display:grid;
    grid-template-columns:repeat(3,120px);
    gap:12px;
    z-index:2;
}

.hex{
    width:110px;
    height:125px;

    clip-path:polygon(
        50% 0%,
        100% 25%,
        100% 75%,
        50% 100%,
        0% 75%,
        0% 25%
    );

    background:rgba(255,255,255,.12);
    border:1px solid rgba(255,255,255,.2);
    backdrop-filter:blur(20px);

    display:flex;
    justify-content:center;
    align-items:center;

    color:white;
    font-size:34px;

    transition:.4s ease;

    animation:floatHex 6s ease-in-out infinite;
}

.hex:hover{
    transform:translateY(-10px) scale(1.08);
    background:rgba(255,255,255,.2);
}

.hex:nth-child(2),
.hex:nth-child(5){
    margin-top:65px;
}

.hex:nth-child(1){animation-delay:0s;}
.hex:nth-child(2){animation-delay:1s;}
.hex:nth-child(3){animation-delay:2s;}
.hex:nth-child(4){animation-delay:3s;}
.hex:nth-child(5){animation-delay:4s;}
.hex:nth-child(6){animation-delay:5s;}

@keyframes floatHex{
    0%,100%{
        transform:translateY(0);
    }
    50%{
        transform:translateY(-15px);
    }
}

@media(max-width:992px){
    .hero-honeycomb{
        display:none;
    }
}
@keyframes honeyMove{
    from{
        background-position:0 0;
    }
    to{
        background-position:90px 156px;
    }
}

.product-card{
    transition:.4s ease;
}

.product-card:hover{
    transform:translateY(-12px);
}

.section-header{
    max-width:800px;
    margin:auto;
}

</style>
@section('content')

<!-- HERO -->
<section class="relative overflow-hidden bg-[#d50000] pt-12 pb-24">

    <!-- Glass Gradient Layer -->
    <div
        class="absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-black/10">
    </div>

    <!-- Glass Blobs -->
    <div
        class="absolute -left-32 -top-32 h-[450px] w-[450px] rounded-full bg-white/15 blur-[140px]">
    </div>

    <div
        class="absolute right-0 top-0 h-[400px] w-[400px] rounded-full bg-red-300/20 blur-[140px]">
    </div>

    <div
        class="absolute bottom-[-150px] left-1/2 h-[500px] w-[500px] -translate-x-1/2 rounded-full bg-white/10 blur-[180px]">
    </div>

    <!-- Decorative Shapes -->
    <div
        class="absolute bottom-0 left-0 h-48 w-48 rotate-45 rounded-3xl border border-white/10 bg-white/5 backdrop-blur-xl">
    </div>

    <div
        class="absolute right-20 top-32 h-64 w-64 rotate-12 rounded-[40px] border border-white/10 bg-white/5 backdrop-blur-xl">
    </div>

    <!-- Honeycomb -->
    <div class="hero-honeycomb">

        <div class="hex">
            <i class="fas fa-mobile-alt"></i>
        </div>

        <div class="hex">
            <i class="fas fa-book-open"></i>
        </div>

        <div class="hex">
            <i class="fas fa-microchip"></i>
        </div>

        <div class="hex">
            <i class="fas fa-chart-line"></i>
        </div>

        <div class="hex">
            <i class="fas fa-shopping-cart"></i>
        </div>

        <div class="hex">
            <i class="fas fa-cogs"></i>
        </div>

    </div>

    <div class="container relative z-10 mx-auto px-6">

        <div class="max-w-3xl">

            <span
                class="mb-6 inline-flex rounded-full border border-white/20 bg-white/10 px-5 py-2 text-sm font-semibold text-white backdrop-blur-xl">

                Produk Digital & Perangkat Bisnis

            </span>

            <h1 class="mb-6 text-5xl font-extrabold leading-tight text-white lg:text-6xl">

                Bangun Bisnis Lebih
                <span class="text-yellow-300">
                    Cepat, Modern,
                </span>
                dan Efisien

            </h1>

            <p class="mb-10 text-lg text-red-100">

                Temukan aplikasi bisnis, e-book edukatif,
                dan perangkat pendukung terbaik dalam satu tempat.
                Solusi lengkap untuk membantu pertumbuhan bisnis Anda
                di era digital.

            </p>

            <div class="flex flex-wrap gap-4">

                <a href="#produk"
                    class="rounded-full bg-white px-8 py-4 font-bold text-red-700 transition hover:scale-105">

                    Jelajahi Produk

                </a>

                <a href="{{ route('hubungi-kami') }}"
                    class="rounded-full border border-white px-8 py-4 font-bold text-white transition hover:bg-white hover:text-red-700">

                    Konsultasi Gratis

                </a>

            </div>

        </div>

    </div>

</section>

    <!-- PRODUCT SECTION -->
    <section id="produk" class="bg-gray-50 py-20">

        <div class="container mx-auto px-6">
            <!-- ===================================== -->
            <!-- PLUGIN / APLIKASI -->
            <!-- ===================================== -->

            <div class="mb-20">

                <div class="mb-12 text-center">

                    <h2 class="mb-3 text-3xl font-bold text-gray-900 md:text-4xl">
    Aplikasi
    <span class="text-red-600">(Plug In)</span>
</h2>
<!-- <div class="mx-auto mt-4 h-1 w-24 rounded-full bg-red-600"></div> -->

<p class="mx-auto max-w-3xl text-gray-600">
    Kumpulan aplikasi dan plugin yang dirancang untuk membantu
    otomatisasi proses bisnis, meningkatkan produktivitas,
    serta mempermudah pengelolaan usaha secara digital.
</p>


                </div>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">

                    @forelse($plugins as $item)

                        <div class="group overflow-hidden rounded-2xl bg-white shadow-lg transition duration-300 hover:-translate-y-2 hover:shadow-2xl">

                            <!-- IMAGE -->
                            <div class="relative h-56 overflow-hidden">

                                @if($item->gambar)

                                    <img src="{{ asset('storage/' . $item->gambar) }}"
                                         alt="{{ $item->nama }}"
                                         class="h-full w-full object-cover transition duration-500 group-hover:scale-110">

                                @else

                                    <div class="flex h-full items-center justify-center bg-gray-200">

                                        <i class="fas fa-laptop-code text-5xl text-gray-400"></i>

                                    </div>

                                @endif

                                @if($item->badge)

                                    <div class="absolute right-3 top-3 rounded-full bg-red-600 px-3 py-1 text-xs font-bold text-white">

                                        {{ $item->badge }}

                                    </div>

                                @endif

                            </div>

                            <!-- CONTENT -->
                            <div class="p-6">

                                <h3 class="mb-3 text-2xl font-bold text-gray-800">

                                    {{ $item->nama }}

                                </h3>

                                <p class="mb-4 line-clamp-3 text-gray-600">

                                    {{ $item->deskripsi }}

                                </p>

                                @if($item->harga)

                                    <div class="mb-5">

                                        <span class="text-2xl font-bold text-red-600">
                                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                                        </span>

                                    </div>

                                @endif

                                <a href="{{ route('plugin.detail', $item->id) }}"
                                   class="block rounded-lg bg-red-600 py-3 text-center font-semibold text-white transition hover:bg-red-700">

                                    Lihat Detail

                                </a>

                            </div>

                        </div>

                    @empty

                        <div class="col-span-3">

                            <div class="rounded-xl bg-white p-10 text-center shadow">

                                <h3 class="text-xl font-semibold text-gray-700">
                                    Belum ada data aplikasi
                                </h3>

                            </div>

                        </div>

                    @endforelse

                </div>

            </div>

            <!-- ===================================== -->
            <!-- EBOOK -->
            <!-- ===================================== -->

            <div class="mb-10">

                <div class="mb-12 text-center">

                    <h2 class="mb-4 text-3xl font-bold text-gray-900 md:text-4xl">

                        E-Book
                        <span class="text-red-600">(Digital Product)</span>

                    </h2>
                    <!-- <div class="mx-auto h-1 w-24 rounded-full bg-red-600"></div> -->

<p class="mx-auto max-w-3xl text-gray-600">
    Materi pembelajaran digital yang berisi panduan,
    strategi, dan wawasan praktis untuk membantu
    pengembangan bisnis maupun peningkatan keterampilan.
</p>


                </div>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">

                    @forelse($ebooks as $item)

                        <div class="group overflow-hidden rounded-2xl bg-white shadow-lg transition duration-300 hover:-translate-y-2 hover:shadow-2xl">

                            <!-- IMAGE -->
                            <div class="relative h-64 overflow-hidden bg-gray-100">

                                @if($item->gambar)

                                    <img src="{{ asset('storage/' . $item->gambar) }}"
                                         alt="{{ $item->nama }}"
                                         class="h-full w-full object-cover transition duration-500 group-hover:scale-110">

                                @else

                                    <div class="flex h-full items-center justify-center">

                                        <i class="fas fa-book text-6xl text-gray-400"></i>

                                    </div>

                                @endif

                            </div>

                            <!-- CONTENT -->
                            <div class="p-6">

                                <h3 class="mb-3 text-2xl font-bold text-gray-800">

                                    {{ $item->nama }}

                                </h3>

                                <p class="mb-4 line-clamp-3 text-gray-600">

                                    {{ $item->deskripsi }}

                                </p>

                                @if($item->harga)

                                    <div class="mb-5">

                                        <span class="text-2xl font-bold text-red-600">
                                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                                        </span>

                                    </div>

                                @endif

                                <a href="{{ route('ebook.detail', $item->id) }}"
                                   class="block rounded-lg bg-red-600 py-3 text-center font-semibold text-white transition hover:bg-red-700">

                                    Lihat Detail

                                </a>

                            </div>

                        </div>

                    @empty

                        <div class="col-span-3">

                            <div class="rounded-xl bg-white p-10 text-center shadow">

                                <h3 class="text-xl font-semibold text-gray-700">
                                    Belum ada data e-book
                                </h3>

                            </div>

                        </div>

                    @endforelse

                </div>

            </div>
            <!-- ===================================== -->
            <!-- PERLENGKAPAN FISIK -->
            <!-- ===================================== -->

            <div class="mb-20">

                <div class="mb-12 text-center">

                    <h2 class="mb-4 text-3xl font-bold text-gray-900 md:text-4xl">

                        Perlengkapan Fisik
                        <span class="text-red-600">(Device)</span>

                    </h2>
                    <!-- <div class="mx-auto h-1 w-24 rounded-full bg-red-600"></div> -->

                    <p class="mx-auto max-w-3xl text-gray-600">
    Berbagai perangkat dan perlengkapan pendukung yang
    dapat digunakan untuk menunjang operasional bisnis,
    meningkatkan efisiensi kerja, dan memperkuat produktivitas.
</p>



                </div>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">

                    @forelse($perlengkapan as $item)

                        <div class="group overflow-hidden rounded-2xl bg-white shadow-lg transition duration-300 hover:-translate-y-2 hover:shadow-2xl">

                            <!-- IMAGE -->
                            <div class="relative h-56 overflow-hidden">

                                @if($item->gambar)

                                    <img src="{{ asset('storage/' . $item->gambar) }}"
                                         alt="{{ $item->nama }}"
                                         class="h-full w-full object-cover transition duration-500 group-hover:scale-110">

                                @else

                                    <div class="flex h-full items-center justify-center bg-gray-200">

                                        <i class="fas fa-image text-5xl text-gray-400"></i>

                                    </div>

                                @endif

                                @if($item->badge)

                                    <div class="absolute right-3 top-3 rounded-full px-3 py-1 text-xs font-bold text-white

                                        @if($item->badge == 'POPULER')
                                            bg-yellow-400 text-red-800
                                        @elseif($item->badge == 'PREMIUM')
                                            bg-purple-500
                                        @else
                                            bg-green-500
                                        @endif
                                    ">

                                        {{ $item->badge }}

                                    </div>

                                @endif

                            </div>

                            <!-- CONTENT -->
                             
                            <div class="p-6">

                                <h3 class="mb-3 text-2xl font-bold text-gray-800">

                                    {{ $item->nama }}

                                </h3>

                                <p class="mb-4 line-clamp-3 text-gray-600">

                                    {{ $item->deskripsi }}

                                </p>

                                @if($item->harga)

                                    <div class="mb-5">

                                        <span class="text-2xl font-bold text-red-600">
                                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                                        </span>

                                    </div>

                                @endif

                                <a href="{{ route('perlengkapan.detail', $item->id) }}"
                                   class="block rounded-lg bg-red-600 py-3 text-center font-semibold text-white transition hover:bg-red-700">

                                    Lihat Detail

                                </a>

                            </div>

                        </div>

                    @empty

                        <div class="col-span-3">

                            <div class="rounded-xl bg-white p-10 text-center shadow">

                                <h3 class="text-xl font-semibold text-gray-700">
                                    Belum ada data perlengkapan fisik
                                </h3>

                            </div>

                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </section>

    <!-- CTA -->
    <section class="bg-gradient-to-r from-red-600 to-red-700 py-16">

        <div class="container mx-auto px-6 text-center">

            <h2 class="mb-6 text-3xl font-bold text-white md:text-4xl">

                Siap Mengembangkan Bisnis Anda?

            </h2>

            <p class="mx-auto mb-8 max-w-2xl text-xl text-red-100">

                Dapatkan konsultasi gratis dan solusi terbaik
                untuk bisnis Anda bersama tim kami.

            </p>

            <div class="flex flex-col justify-center gap-4 sm:flex-row">

                <a href="{{ route('hubungi-kami') }}"
                   class="rounded-full bg-white px-8 py-4 text-lg font-bold text-red-700 transition hover:bg-red-100">

                    <i class="fas fa-phone mr-2"></i>
                    Hubungi Kami

                </a>

                <!-- <a href="{{ route('hubungi-kami') }}"
                   class="rounded-full border-2 border-white px-8 py-4 text-lg font-bold text-white transition hover:bg-white hover:text-red-700">

                    <i class="fas fa-envelope mr-2"></i>
                    Minta Penawaran

                </a> -->

            </div>

        </div>

    </section>

@endsection

@push('scripts')

<script>

    window.addEventListener('scroll', () => {

        const elements = document.querySelectorAll('.group');

        elements.forEach(el => {

            const elementTop = el.getBoundingClientRect().top;

            if (elementTop < window.innerHeight - 100) {

                el.classList.add('opacity-100');

            }

        });

    });

</script>
<script>

document.addEventListener('DOMContentLoaded', () => {

    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.slider-dot');

    let current = 0;

    setInterval(() => {

        slides[current].classList.add('opacity-0');
        dots[current].classList.remove('bg-white');
        dots[current].classList.add('bg-white/40');

        current = (current + 1) % slides.length;

        slides[current].classList.remove('opacity-0');
        dots[current].classList.remove('bg-white/40');
        dots[current].classList.add('bg-white');

    }, 5000);

});

</script>
@endpush