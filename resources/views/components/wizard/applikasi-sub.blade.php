<style>
    .services{
    cursor:pointer;
    transition:.3s ease;
}

/* .services:hover .app-icon{
    transform:translateY(-5px);
    box-shadow:0 10px 25px rgba(0,0,0,.15);
} */

.app-icon{
    transition:.3s ease;
    border-radius:20px;
}

.app-icon.active{
    background: #dc2626;
    box-shadow: 0 0 0 10px #dc2626;
}

.app-icon.active img{
    transform:scale(1.05);
}
    .keen-slider__slide {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100%;
    }

    .app-icon {
        position: relative;
        transition: all 0.3s ease;
        transform: scale(0.9);

    }

    .app-icon {
        clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
        overflow: hidden;
    }

    .app-icon::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(255, 255, 255, 0.3);
        opacity: 0;
        transition: opacity 0.3s ease;
        clip-path: inherit;
    }

    .app-icon:not(.active):hover::before {
        opacity: 0;
    }

    .app-icon:not(.active)::before {
        opacity: 1;
    }

    .app-icon:hover {

        /* transform: scale(1);
        cursor: pointer; */
    }

    .app-icon.active{
    background: #dc2626;
    box-shadow: 0 0 0 6px #dc2626;
}

    .slider-container {
        position: relative;
    }

    .slider-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        width: 40px;
        height: 40px;
        border-radius: 9999px;
        background-color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .slider-nav-btn:hover {
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        cursor: pointer;
    }

    .slider-nav-btn.prev {
        left: 0;
        top: 30%;
    }

    .slider-nav-btn.next {
        right: 0;
        top: 30%;
    }

    @media (min-width: 1024px) {
        .slider-nav-btn {
            width: 50px;
            height: 50px;
        }
    }
</style>
@php
    $latestArticles = $latestArticles ?? collect();
@endphp
<section id="applikasi-sub" class="overlay-section bg-white ">
    <div x-data="{
        activeApp: 'belanja',
        showInfo: true,
        initialLoad: true,
        appData: {
            'belanja': {
                title: 'Belanja',
                description: 'Platform belanja online yang memudahkan pengguna menemukan dan membeli produk dari berbagai kategori dengan aman dan nyaman.',
                features: ['Pencarian produk cepat', 'Filter harga dan kategori', 'Rekomendasi produk', 'Keranjang belanja', 'Wishlist'],
                image: 'https://placehold.co/600x400'
            },
    
    
            'jualan': {
                title: 'Jualan',
                description: 'Fasilitas Point of Sale (POS) atau yang umum dikenal dengan aplikasi kasir. Di Aplikasi Kedai Indonesia “Jualan” dilengkapi dengan sejumlah fitur tambahan yang memberikan kemudahan bagi pelaku usaha Kedai untuk berjualan secara lebih efisien.',
                features: ['Manajemen produk', 'Analitik penjualan', 'Integrasi pembayaran', 'Kelola stok otomatis', 'Promosi produk'],
                image: 'https://placehold.co/600x400'
            },
            'laporan': {
                title: 'Laporan',
                description: 'Dapatkan laporan terperinci tentang performa bisnis, penjualan, dan aktivitas toko anda dalam format yang mudah dipahami.',
                features: ['Laporan penjualan harian', 'Grafik tren pendapatan', 'Analisis pelanggan', 'Ekspor data ke Excel/PDF', 'Pemberitahuan penting'],
                image: 'https://placehold.co/600x400'
            },
            'analisis': {
                title: 'Analisis',
                description: 'Tools analisis data yang kuat untuk memahami tren pasar, perilaku pelanggan, dan mengoptimalkan strategi bisnis anda.',
                features: ['Analisis pasar', 'Segmentasi pelanggan', 'Pola pembelian', 'Prediksi tren', 'Dashboard interaktif'],
                image: 'https://placehold.co/600x400'
            },
            'komunitas': {
                title: 'Komunitas',
                description: 'Bergabunglah dengan komunitas pedagang dan pelanggan untuk berbagi pengalaman, tips, dan membangun jaringan bisnis yang kuat.',
                features: ['Forum diskusi', 'Event virtual', 'Program mentor', 'Berbagi pengetahuan', 'Jaringan bisnis'],
                image: 'https://placehold.co/600x400'
            },
            'informasi': {
                title: 'Informasi',
                description: 'Dapatkan informasi terbaru tentang tren pasar, tips bisnis, pembaruan fitur platform, dan berita industri terkini.',
                features: ['Artikel bisnis', 'Tips penjualan', 'Update fitur', 'Berita industri', 'Tutorial penggunaan'],
                image: 'https://placehold.co/600x400'
            }
        },
        showAppInfo(app) {
            this.activeApp = app;
            this.showInfo = true;
    
            if (this.initialLoad) {
                this.initialLoad = false;
                return;
            }
    
            this.$nextTick(() => {
                const infoEl = document.getElementById('app-info-section');
                if (infoEl) {
                    infoEl.scrollIntoView({ behavior: 'smooth' });
                }
            });
        },
    
        closeInfo() {
            this.showInfo = false;
        }
    }" class="overlay-section bg-white">
        <!-- Slider section with navigation buttons -->
        <div class="text-center mb-12">
    <span
        class="inline-flex items-center rounded-full bg-red-100 px-4 py-2 text-sm font-semibold text-red-600">
        Fitur Unggulan Kedai Indonesia
    </span>

    <p class="mt-3 text-gray-500 max-w-2xl mx-auto">
        Kelola usaha lebih mudah melalui fitur <br> Belanja, Jualan, Laporan, Analisis, Komunitas, dan Informasi.
    </p>
</div>
<div class="slider-container mx-4 mb-12 flex flex-col items-center gap-8 text-wrap md:flex-row md:gap-12">
            <div class="flex justify-center gap-4 lg:hidden">
                <button type="button" id="app-slider-previous"
                    class="slider-nav-btn prev group z-30 flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-white shadow-md focus:outline-none">
                    <svg class="h-4 w-4 text-gray-500 rtl:rotate-180" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 1 1 5l4 4" />
                    </svg>
                    <span class="sr-only">Previous</span>
                </button>
            </div>
            <div id="subapp-slider" class="keen-slider mx-auto max-w-[70rem]">
                <div class="keen-slider__slide flex items-center justify-center">
                    <div class="ftco-services text-center">
                        <div class="ftco-animate">
                        <div
    @click="
        activeApp = 'belanja';
        showInfo = true;
    "
    class="services mb-4 flex cursor-pointer flex-col items-center justify-center md:mb-0">

    <div
        class="app-icon flex w-16 items-center justify-center rounded-2xl p-3.5 text-gray-50 transition-all duration-300 sm:w-12 md:w-12 lg:w-28"
        :class="activeApp === 'belanja'
            ? 'active bg-red-600 shadow-xl shadow-red-500/40'
            : 'hover:-translate-y-1 hover:shadow-lg'">

        <img
            src="{{ asset('storage/assets_images/images/sub-app/BelanjaLogo.png') }}"
            alt="Belanja Logo"
            class="transition-transform duration-300 w-full h-auto object-contain"
            :class="activeApp === 'belanja'
                ? 'scale-105'
                : 'group-hover:scale-105'">

    </div>

    <div class="media-body">

        <div
            class="pt-3 text-center font-bold transition-colors duration-300 sm:text-sm md:text-2xl"
            :class="activeApp === 'belanja'
                ? 'text-red-600'
                : 'text-gray-800 hover:text-red-500'">

            Belanja

        </div>

    </div>

</div>
                        </div>
                    </div>
                </div>
                <div class="keen-slider__slide flex items-center justify-center">
                    <div class="ftco-services text-center">
                        <div class="ftco-animate">
                        <div
    @click="
        activeApp = 'jualan';
        showInfo = true;
    "
    class="services mb-4 flex cursor-pointer flex-col items-center justify-center md:mb-0">

    <div
        class="app-icon flex w-16 items-center justify-center rounded-2xl p-3.5 text-gray-50 transition-all duration-300 sm:w-12 md:w-12 lg:w-28"
        :class="activeApp === 'jualan'
            ? 'active bg-red-600 shadow-xl shadow-red-500/40'
            : 'hover:-translate-y-1 hover:shadow-lg'">

        <img
            src="{{ asset('storage/assets_images/images/sub-app/JualanLogo.png') }}"
            alt="Jualan Logo"
            class="transition-transform duration-300 w-full h-auto object-contain"
            :class="activeApp === 'jualan'
                ? 'scale-105'
                : ''">

    </div>

    <div class="media-body">

        <div
            class="pt-3 text-center font-bold transition-colors duration-300 sm:text-sm md:text-2xl"
            :class="activeApp === 'jualan'
                ? 'text-red-600'
                : 'text-gray-800 hover:text-red-500'">

            Jualan

        </div>

    </div>

</div>
                        </div>
                    </div>
                </div>
                <div class="keen-slider__slide flex items-center justify-center">
                    <div class="ftco-services text-center">
                        <div class="ftco-animate">
                        <div
    @click="
        activeApp = 'laporan';
        showInfo = true;
    "
    class="services mb-4 flex cursor-pointer flex-col items-center justify-center md:mb-0">

    <div
        class="app-icon flex w-16 items-center justify-center rounded-2xl p-3.5 text-gray-50 transition-all duration-300 sm:w-12 md:w-12 lg:w-28"
        :class="activeApp === 'laporan'
            ? 'active bg-red-600 shadow-xl shadow-red-500/40'
            : 'hover:-translate-y-1 hover:shadow-lg'">

        <img
            src="{{ asset('storage/assets_images/images/sub-app/LaporanLogo.png') }}"
            alt="Laporan Logo"
            class="transition-transform duration-300 w-full h-auto object-contain"
            :class="activeApp === 'laporan'
                ? 'scale-105'
                : 'group-hover:scale-105'">

    </div>

    <div class="media-body">

        <div
            class="pt-3 text-center font-bold transition-colors duration-300 sm:text-sm md:text-2xl"
            :class="activeApp === 'laporan'
                ? 'text-red-600'
                : 'text-gray-800 hover:text-red-500'">

            Laporan

        </div>

    </div>

</div>
                        </div>
                    </div>
                </div>
                <div class="keen-slider__slide flex items-center justify-center">
                    <div class="ftco-services text-center">
                        <div class="ftco-animate">
                        <div
    @click="
        activeApp = 'analisis';
        showInfo = true;
    "
    class="services mb-4 flex cursor-pointer flex-col items-center justify-center md:mb-0">

    <div
        class="app-icon flex w-16 items-center justify-center rounded-2xl p-3.5 text-gray-50 transition-all duration-300 sm:w-12 md:w-12 lg:w-28"
        :class="activeApp === 'analisis'
            ? 'active bg-red-600 shadow-xl shadow-red-500/40'
            : 'hover:-translate-y-1 hover:shadow-lg'">

        <img
            src="{{ asset('storage/assets_images/images/sub-app/AnalisisLogo.png') }}"
            alt="Analisis Logo"
            class="transition-transform duration-300 w-full h-auto object-contain"
            :class="activeApp === 'analisis'
                ? 'scale-105'
                : 'group-hover:scale-105'">

    </div>

    <div class="media-body">

        <div
            class="pt-3 text-center font-bold transition-colors duration-300 sm:text-sm md:text-2xl"
            :class="activeApp === 'analisis'
                ? 'text-red-600'
                : 'text-gray-800 hover:text-red-500'">

            Analisis

        </div>

    </div>

</div>
                        </div>
                    </div>
                </div>
                <div class="keen-slider__slide flex items-center justify-center">
                    <div class="ftco-services text-center">
                        <div class="ftco-animate">
                        <div
    @click="
        activeApp = 'komunitas';
        showInfo = true;
    "
    class="services mb-4 flex cursor-pointer flex-col items-center justify-center md:mb-0">

    <div
        class="app-icon flex w-16 items-center justify-center rounded-2xl p-3.5 text-gray-50 transition-all duration-300 sm:w-12 md:w-12 lg:w-28"
        :class="activeApp === 'komunitas'
            ? 'active bg-red-600 shadow-xl shadow-red-500/40'
            : 'hover:-translate-y-1 hover:shadow-lg'">

        <img
            src="{{ asset('storage/assets_images/images/sub-app/KomunitasLogo.png') }}"
            alt="Komunitas Logo"
            class="transition-transform duration-300 w-full h-auto object-contain"
            :class="activeApp === 'komunitas'
                ? 'scale-105'
                : 'group-hover:scale-105'">

    </div>

    <div class="media-body">

        <div
            class="pt-3 text-center font-bold transition-colors duration-300 sm:text-sm md:text-2xl"
            :class="activeApp === 'komunitas'
                ? 'text-red-600'
                : 'text-gray-800 hover:text-red-500'">

            Komunitas

        </div>

    </div>

</div>
                        </div>
                    </div>
                </div>
                <div class="keen-slider__slide flex items-center justify-center">
                    <div class="ftco-services text-center">
                        <div class="ftco-animate">
                        <div
    @click="
        activeApp = 'informasi';
        showInfo = true;
    "
    class="services mb-4 flex cursor-pointer flex-col items-center justify-center md:mb-0">

    <div
        class="app-icon flex w-16 items-center justify-center rounded-2xl p-3.5 text-gray-50 transition-all duration-300 sm:w-12 md:w-12 lg:w-28"
        :class="activeApp === 'informasi'
            ? 'active bg-red-600 shadow-xl shadow-red-500/40'
            : 'hover:-translate-y-1 hover:shadow-lg'">

        <img
            src="{{ asset('storage/assets_images/images/sub-app/InformasiLogo.png') }}"
            alt="Belanja Logo"
            class="transition-transform duration-300 w-full h-auto object-contain"
            :class="activeApp === 'informasi'
                ? 'scale-105'
                : 'group-hover:scale-105'">

    </div>

    <div class="media-body">

        <div
            class="pt-3 text-center font-bold transition-colors duration-300 sm:text-sm md:text-2xl"
            :class="activeApp === 'informasi'
                ? 'text-red-600'
                : 'text-gray-800 hover:text-red-500'">

            Informasi

        </div>

    </div>

</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center gap-4 lg:hidden">

                <button type="button" id="app-slider-next"
                    class="slider-nav-btn next group z-30 flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-white shadow-md focus:outline-none">
                    <svg class="h-4 w-4 text-gray-500 rtl:rotate-180" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 9 4-4-4-4" />
                    </svg>
                    <span class="sr-only">Next</span>
                </button>
            </div>
        </div>
        
        <!-- App Info Sections -->
        <div id="app-info-section" x-show="showInfo" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-10"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-10" class="relative mt-16">

            <!-- Close button -->
            {{-- <button @click="closeInfo" class="absolute -top-4 right-0 rounded-full bg-white p-2 shadow-md hover:bg-gray-100 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button> --}}

            <div x-show="activeApp === 'belanja'"
    x-data="{ activeTab: 'overview' }"
    class="overflow-hidden rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200">
    <!-- Header Belanja -->
<!-- <div class="border-b border-gray-300 bg-white/70 px-8 py-6 backdrop-blur-sm">
    <div class="mx-auto flex max-w-7xl items-center gap-4">

        <div class="rounded-full bg-white p-3 shadow-md">
            <img src="{{ asset('storage/assets_images/images/sub-app/BelanjaLogo.png') }}"
                alt="Belanja Logo"
                class="h-12 w-12 object-contain">
        </div>

        <div>
            <h2 class="text-3xl font-bold text-gray-900">
                Belanja
            </h2> -->

            <!-- <p class="mt-1 text-gray-600">
                Solusi belanja dan pengelolaan stok usaha yang memudahkan mitra
                dalam melakukan restocking, mendapatkan rekomendasi produk,
                fasilitas pembiayaan syariah, pembayaran digital, dan laporan
                perkembangan usaha secara terintegrasi.
            </p> -->
        <!-- </div>

    </div>
</div> -->
<div class="p-8">

    <!-- Header -->
    <div class="mb-6 flex items-center">

        <div class="mr-4">
            <img src="{{ asset('storage/assets_images/images/sub-app/BelanjaLogo.png') }}"
                alt="Belanja Logo"
                class="h-10 w-10 object-contain">
        </div>

        <h3 class="text-2xl font-bold text-blue-800">
            Belanja
        </h3>

    </div>

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <ul class="-mb-px flex flex-wrap">

            <li class="mr-2">
                <button @click="activeTab = 'overview'"
                    :class="activeTab === 'overview'
                    ? 'text-blue-600 border-b-2 border-blue-600'
                    : 'text-gray-500'"
                    class="inline-block p-4 font-medium">
                    Overview
                </button>
            </li>

            <li class="mr-2">
                <button @click="activeTab = 'features'"
                    :class="activeTab === 'features'
                    ? 'text-blue-600 border-b-2 border-blue-600'
                    : 'text-gray-500'"
                    class="inline-block p-4 font-medium">
                    Fitur
                </button>
            </li>

            <li>
                <button @click="activeTab = 'demo'"
                    :class="activeTab === 'demo'
                    ? 'text-blue-600 border-b-2 border-blue-600'
                    : 'text-gray-500'"
                    class="inline-block p-4 font-medium">
                    Demo
                </button>
            </li>

        </ul>
    </div>

    <div x-show="activeTab === 'overview'" class="grid md:grid-cols-2 gap-8">

<div>
    <p class="mb-6 text-gray-700">
        Fitur Belanja membantu mitra melakukan restocking barang,
        memperoleh rekomendasi pembelian berbasis AI,
        menggunakan fasilitas kredit syariah,
        melakukan pembayaran digital,
        serta memantau perkembangan usaha melalui laporan yang lengkap.
    </p>

    <div class="rounded-lg bg-white p-6 shadow-md">
        <h4 class="mb-4 font-semibold text-blue-700 text-lg">
            Kelebihan Fitur Belanja
        </h4>

        <div class="space-y-4">
            <div class="flex items-center gap-3">
            <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">
    <i class="fas fa-check text-xs"></i>
</div>
                <span class="text-gray-700 font-medium">Belanja Mandiri</span>
            </div>

            <div class="flex items-center gap-3">
            <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">
    <i class="fas fa-check text-xs"></i>
</div>
                <span class="text-gray-700 font-medium">Belanja Cepat berbasis AI</span>
            </div>

            <div class="flex items-center gap-3">
            <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">
    <i class="fas fa-check text-xs"></i>
</div>
                <span class="text-gray-700 font-medium">Pakai Dulu (Kredit Syariah)</span>
            </div>

            <div class="flex items-center gap-3">
            <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">
    <i class="fas fa-check text-xs"></i>
</div>
                <span class="text-gray-700 font-medium">Pembayaran Digital</span>
            </div>

            <div class="flex items-center gap-3">
            <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">
    <i class="fas fa-check text-xs"></i>
</div>
                <span class="text-gray-700 font-medium">Laporan Usaha Terintegrasi</span>
            </div>
        </div>
    </div>

</div>

<div class="flex items-center justify-center">
    <img src="https://placehold.co/600x400"
        alt="Belanja"
        class="max-h-80 rounded-lg shadow-lg">
</div>

</div>

    <!-- FITUR -->
    <!-- <div x-show="activeTab === 'features'">

<div class="space-y-16 md:space-y-24">        
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div class="flex justify-center md:justify-start">
                <img src="{{ asset('storage/assets_images/images/barangki.png') }}" alt="Belanja Mandiri"
                    class="w-[75%] md:w-[85%] rounded-xl shadow-xl shadow-black/35">
            </div>
            <div class="space-y-3">
                <h2 class="text-2xl font-bold text-black">BELANJA MANDIRI [PILIHAN SENDIRI]</h2>
                <p class="text-gray-700 leading-relaxed">
                    Fitur ini berfungsi untuk memudahkan mitra dalam proses pemesanan barang dagangannya
                    yang sudah berkurang/habis kepada Kedai Indonesia. <span class="italic">Restocking</span> barang dilakukan dengan sangat mudah, serta
                    updating data barang di kedai juga dilakukan otomatis seketika barang orderan diterima
                    pedagang, karena proses delivery dan updating data base kedai dikelola oleh Imagi
                    Center.
                </p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div class="space-y-3 md:order-1">
                <h2 class="text-2xl font-bold text-black">BELANJA CEPAT [BANTUAN SISTEM]</h2>
                <p class="text-gray-700 leading-relaxed">
                    Untk memudahkan mitra memilih barang dagangan yang perlu di order, tersedia fitur
                    berbasis AI <span class="font-bold">"belanja cepat"</span>. Dengan sekali sentuhan
                    sistem Kedai Indonesia akan merekomendasikan barang-barang yang perlu di re-stock,
                    berdasarkan history penjualan. Fitur ini menyediakan berbagai pilihan seperti <span
                        class="font-semibold">belanja produk sesual prioritas</span> normal (restocking),
                    belanja produk <span class="font-semibold">sesuai dana</span> yang diinginkan dan
                    belanja produk sesuai <span class="font-semibold">trend terlaris</span> di kedai
                    sekitar anda. Semua barang direkomendasikan oleh sistem secara otomatis.
                </p>
            </div>
            <div class="flex justify-center md:justify-end md:order-2">
                <img src="{{ asset('storage/assets_images/images/inputBelanjaCepat.png') }}" alt="Belanja Cepat"
                    class="w-[75%] md:w-[85%] rounded-xl shadow-xl shadow-black/35">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div class="flex justify-center md:justify-start">
                <img src="{{ asset('storage/assets_images/images/RekomendasiBelanjaCepat.png') }}" alt="Pakai Dulu"
                    class="w-[75%] md:w-[85%] rounded-xl shadow-xl shadow-black/35">
            </div>
            <div class="space-y-3">
                <h2 class="text-2xl font-bold text-black">PAKAI DULU [KREDIT BERBASIS SYARIAH]</h2>
                <p class="text-gray-700 leading-relaxed">
                    Fitur layanan pakai dulu adalah fasilitas kesbon". Memberikan kesempatan kepada mitra
                    untuk melakukan belanja pemenuhan stock barang dengan pemesanan dan dikirim terlebih
                    dahulu dan bayarnya belakangan.<br>
                    <span class="font-semibold">"Akad dilakukan secara syariah dan sesuai ketentuan yang berlaku.</span>
                </p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div class="space-y-3 md:order-1">
                <h2 class="text-2xl font-bold text-black">PEMBAYARAN DIGITAL</h2>
                <p class="text-gray-700 leading-relaxed">
                    Untuk memudahkan transaksi digital, tersedia fitur Pembayaran Digital yang melayani
                    pembayaran tagihan listrik, PDAM, pembelian pulsa, dompet digital dan berbagai produk
                    jasa keuangan lainnya, yang terus di upgrade sesuai kebutuhan.
                </p>
            </div>
            <div class="flex justify-center md:justify-end md:order-2">
                <img src="{{ asset('storage/assets_images/images/barangki.png') }}" alt="Pembayaran Digital"
                    class="w-[75%] md:w-[85%] rounded-xl shadow-xl shadow-black/35">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div class="flex justify-center md:justify-start">
                <img src="{{ asset('storage/assets_images/images/inputBelanjaCepat.png') }}" alt="Laporan"
                    class="w-[75%] md:w-[85%] rounded-xl shadow-xl shadow-black/35">
            </div>
            <div class="space-y-3">
                <h2 class="text-2xl font-bold text-black">LAPORAN</h2>
                <p class="text-gray-700 leading-relaxed">
                    Layanan informasi perkembangan usaha, analisis data penjualan, informasi transaksi,
                    analisis stok produk dan informasi lainnya yang diperlukan untuk mengembangkan usaha.
                    Bisa diakses dengan berbagai pilihan harian, mingguan, bulanan atau periode sesuai
                    kebutuhan.
                </p>
            </div>
        </div>

        </div>
</div> -->

<div x-show="activeTab === 'features'" class="space-y-4">

    <h4 class="mb-3 font-semibold text-blue-700">
        Sub Fitur Belanja:
    </h4>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

        <div class="rounded-lg bg-white p-4 shadow-md">
            <div class="flex items-center">
            <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">
    <i class="fas fa-check text-xs"></i>
</div>
                <h5 class="font-medium text-gray-800">
                    Belanja Mandiri
                </h5>
            </div>
        </div>

        <div class="rounded-lg bg-white p-4 shadow-md">
            <div class="flex items-center">
            <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">
    <i class="fas fa-check text-xs"></i>
</div>
                <h5 class="font-medium text-gray-800">
                    Belanja Cepat (AI)
                </h5>
            </div>
        </div>

        <div class="rounded-lg bg-white p-4 shadow-md">
            <div class="flex items-center">
            <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">
    <i class="fas fa-check text-xs"></i>
</div>
                <h5 class="font-medium text-gray-800">
                    Pakai Dulu (Kredit Syariah)
                </h5>
            </div>
        </div>

        <div class="rounded-lg bg-white p-4 shadow-md">
            <div class="flex items-center">
            <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">
    <i class="fas fa-check text-xs"></i>
</div>
                <h5 class="font-medium text-gray-800">
                    Pembayaran Digital
                </h5>
            </div>
        </div>

        <div class="rounded-lg bg-white p-4 shadow-md">
            <div class="flex items-center">
            <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">
    <i class="fas fa-check text-xs"></i>
</div>
                <h5 class="font-medium text-gray-800">
                    Laporan Usaha
                </h5>
            </div>
        </div>

        <div class="rounded-lg bg-white p-4 shadow-md">
            <div class="flex items-center">
            <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">
    <i class="fas fa-check text-xs"></i>
</div>
                <h5 class="font-medium text-gray-800">
                    Analisis Stok Otomatis
                </h5>
            </div>
        </div>

    </div>

</div>
</div>

<!-- DEMO -->
<div x-show="activeTab === 'demo'"
    class="flex flex-col items-center">

    <div class="w-full max-w-2xl rounded-lg bg-white p-6 shadow-lg">

        <h4 class="mb-4 text-center font-semibold text-blue-700">
            Demo Platform Belanja
        </h4>

        <div
            class="mb-4 flex h-64 items-center justify-center rounded-lg bg-gray-200">

            <svg class="h-16 w-16 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                </path>

                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                </path>

            </svg>

        </div>

        <p class="text-center text-gray-600">
            Klik untuk melihat demo platform Belanja dalam aksi
        </p>

    </div>

</div>
<!-- BUTTON -->
<div class="mt-8 mb-16 text-center">
    <a href="{{ route('belanja') }}"
        class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-3 font-medium text-white transition-colors hover:bg-blue-700">
        Selengkapnya
    </a>
</div>
    
</div>


           <!-- Jualan Section -->
           <div x-show="activeApp === 'jualan'" x-data="{ activeTab: 'overview' }"
    class="overflow-hidden rounded-2xl bg-gradient-to-br from-green-50 to-emerald-50 shadow-xl">

    <div class="p-8">

        <div class="mb-6 flex items-center">

        <div class="mr-4">
                <img src="{{ asset('storage/assets_images/images/sub-app/JualanLogo.png') }}"
                    alt="Jualan Logo"
                    class="h-10 w-10 object-contain">
            </div>

            <h3 class="text-2xl font-bold text-emerald-800"
                x-text="appData.jualan.title">
            </h3>

        </div>

                    <div class="mb-6 border-b border-gray-200">
                        <ul class="-mb-px flex flex-wrap">
                            <li class="mr-2">
                                <button @click="activeTab = 'overview'"
                                    :class="{ 'text-emerald-600 border-b-2 border-emerald-600': activeTab === 'overview', 'text-gray-500 hover:text-gray-700': activeTab !== 'overview' }"
                                    class="inline-block p-4 font-medium">
                                    Overview
                                </button>
                            </li>
                            <li class="mr-2">
                                <button @click="activeTab = 'features'"
                                    :class="{ 'text-emerald-600 border-b-2 border-emerald-600': activeTab === 'features', 'text-gray-500 hover:text-gray-700': activeTab !== 'features' }"
                                    class="inline-block p-4 font-medium">
                                    Fitur
                                </button>
                            </li>
                            <li class="mr-2">
                                <button @click="activeTab = 'demo'"
                                    :class="{ 'text-emerald-600 border-b-2 border-emerald-600': activeTab === 'demo', 'text-gray-500 hover:text-gray-700': activeTab !== 'demo' }"
                                    class="inline-block p-4 font-medium">
                                    Demo
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div x-show="activeTab === 'overview'" class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <div>
                            <p class="mb-6 text-gray-700" x-text="appData.jualan.description"></p>
                            <div class="rounded-lg bg-white p-6 shadow-md">
                                <h4 class="mb-4 font-semibold text-emerald-700 text-lg">Kelebihan Fitur “Jualan”</h4>
                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>
                                        <span class="text-gray-600 font-medium">Point of Sale (kasir)</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>
                                        <span class="text-gray-600 font-medium">PPOB (Payment Point Online Bank), belanja pulsa, e-walet dll</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>
                                        <span class="text-gray-600 font-medium">Multy payment methode (cash, QRIS, Transfer dll)</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>
                                        <span class="text-gray-600 font-medium">Terintegrasi dengan manajemen stok retail</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>
                                        <span class="text-gray-600 font-medium">Infak/donasi (kelebihan belanja) terorganisir dan transparan</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>
                                        <span class="text-gray-600 font-medium">Jaminan keamanan data</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>
                                        <span class="text-gray-600 font-medium">Terkoneksi dengan sistem pelaporan usaha</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>
                                        <span class="text-gray-600 font-medium">Free pendampingan (jika aplikasi ada kendala)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-center">
                            <img :src="appData.jualan.image" alt="Jualan Feature"
                                class="max-h-80 rounded-lg object-cover shadow-lg">
                        </div>
                    </div>

                    <div x-show="activeTab === 'features'" class="space-y-4">
                        <h4 class="mb-3 font-semibold text-emerald-700">Sub Fitur Jualan:</h4>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <template x-for="feature in appData.jualan.features">
                                <div class="rounded-lg bg-white p-4 shadow-md">
                                    <div class="flex items-center">
                                        <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>
                                        <h5 class="font-medium text-gray-800" x-text="feature"></h5>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div x-show="activeTab === 'demo'" class="flex flex-col items-center">
                        <div class="w-full max-w-2xl rounded-lg bg-white p-6 shadow-lg">
                            <h4 class="mb-4 text-center font-semibold text-emerald-700">Demo Platform Jualan</h4>
                            <div
                                class="aspect-w-16 aspect-h-9 mb-4 flex items-center justify-center rounded-lg bg-gray-200">
                                <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-center text-gray-600">Klik untuk melihat demo platform Jualan dalam aksi</p>
                        </div>
                    </div>

                    <div class="mt-8 text-center">
                        <a href="{{ route('jualan.detail') }}"
                            class="inline-flex items-center rounded-lg bg-emerald-600 px-6 py-3 font-medium text-white transition-colors hover:bg-emerald-700">
                            Selengkapnya
                            <!-- <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg> -->
                            </a>
                    </div>
                </div>
            </div>
        </div>

<!-- laporan section -->
        <div x-show="activeApp === 'laporan'"
    x-data="{ activeTab: 'overview' }"
    class="overflow-hidden rounded-2xl bg-gradient-to-br from-amber-50 to-yellow-50 shadow-xl">

    <div class="p-8">

        <div class="mb-6 flex items-center">

            <div class="mr-4">
                <img src="{{ asset('storage/assets_images/images/sub-app/LaporanLogo.png') }}"
                    alt="Laporan Logo"
                    class="h-10 w-10 object-contain">
            </div>

            <h3 class="text-2xl font-bold text-amber-800">
                Laporan
            </h3>

        </div>

        <div class="mb-6 border-b border-gray-200">
            <ul class="-mb-px flex flex-wrap">

                <li class="mr-2">
                    <button
                        @click="activeTab = 'overview'"
                        :class="{
                            'text-amber-600 border-b-2 border-amber-600': activeTab === 'overview',
                            'text-gray-500 hover:text-gray-700': activeTab !== 'overview'
                        }"
                        class="inline-block p-4 font-medium">

                        Overview

                    </button>
                </li>

                <li class="mr-2">
                    <button
                        @click="activeTab = 'features'"
                        :class="{
                            'text-amber-600 border-b-2 border-amber-600': activeTab === 'features',
                            'text-gray-500 hover:text-gray-700': activeTab !== 'features'
                        }"
                        class="inline-block p-4 font-medium">

                        Fitur

                    </button>
                </li>

                <li class="mr-2">
                    <button
                        @click="activeTab = 'demo'"
                        :class="{
                            'text-amber-600 border-b-2 border-amber-600': activeTab === 'demo',
                            'text-gray-500 hover:text-gray-700': activeTab !== 'demo'
                        }"
                        class="inline-block p-4 font-medium">

                        Demo

                    </button>
                </li>

            </ul>
        </div>

        <div x-show="activeTab === 'overview'"
            class="grid grid-cols-1 gap-8 md:grid-cols-2">

            <div>

                <p class="mb-6 text-gray-700">
                    Fitur Laporan membantu mitra melihat berbagai laporan kegiatan,
                    dokumentasi program, hasil pelaksanaan, dan informasi penting
                    yang telah dipublikasikan oleh Kedai Indonesia.
                </p>

                <div class="rounded-lg bg-white p-6 shadow-md">

                    <h4 class="mb-4 font-semibold text-amber-700 text-lg">
                        Mengapa memilih Laporan?
                    </h4>

                    <div class="space-y-4">

                        <div class="flex items-center gap-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-gray-600 font-medium">
                                Melihat laporan kegiatan dan program terbaru.
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-gray-600 font-medium">
                                Akses dokumentasi kegiatan secara mudah.
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-gray-600 font-medium">
                                Mengetahui perkembangan program dan hasil kegiatan.
                            </span>
                        </div>

                    </div>

                </div>

            </div>

            <div class="flex items-center justify-center">

                <img src="https://placehold.co/600x400"
                    alt="Laporan"
                    class="max-h-80 rounded-lg object-cover shadow-lg">

            </div>

        </div>

        <div x-show="activeTab === 'features'"
            class="space-y-4">

            <h4 class="mb-3 font-semibold text-amber-700">
                Sub Fitur Laporan:
            </h4>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div class="flex items-center">

                        <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                            <i class="fas fa-check text-xs"></i>
                        </div>

                        <h5 class="font-medium text-gray-800">
                            Laporan Harian
                        </h5>

                    </div>
                </div>

                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div class="flex items-center">

                        <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                            <i class="fas fa-check text-xs"></i>
                        </div>

                        <h5 class="font-medium text-gray-800">
                            Laporan Bulanan
                        </h5>

                    </div>
                </div>

                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div class="flex items-center">

                        <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                            <i class="fas fa-check text-xs"></i>
                        </div>

                        <h5 class="font-medium text-gray-800">
                            Dokumentasi Kegiatan
                        </h5>

                    </div>
                </div>

                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div class="flex items-center">

                        <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                            <i class="fas fa-check text-xs"></i>
                        </div>

                        <h5 class="font-medium text-gray-800">
                            Rekap Performa Program
                        </h5>

                    </div>
                </div>

            </div>

        </div>

        <div x-show="activeTab === 'demo'"
            class="flex flex-col items-center">

            <div class="w-full max-w-2xl rounded-lg bg-white p-6 shadow-lg">

                <h4 class="mb-4 text-center font-semibold text-amber-700">
                    Demo Laporan
                </h4>

                <div class="mb-4 flex aspect-video items-center justify-center rounded-lg bg-gray-200">

                    <svg class="h-16 w-16 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                        </path>

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>

                    </svg>

                </div>

                <p class="text-center text-gray-600">
                    Klik untuk melihat demo sistem laporan dan dokumentasi kegiatan.
                </p>

            </div>

        </div>

        <!-- UBAH BAGIAN TOMBOL PALING BAWAH MENJADI SEPERTI INI -->
<div class="mt-8 text-center">
    <a href="{{ route('laporan.detail') }}"
        class="inline-flex items-center rounded-lg bg-amber-600 px-6 py-3 font-medium text-white transition-colors hover:bg-amber-700 group shadow-md">
        <span>Selengkapnya</span>
        <!-- <svg class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
        </svg> -->
    </a>
</div>

    </div>

</div>

        <!-- Daftar Laporan -->
        <!-- <div class="mt-12">

            <h4 class="mb-6 text-2xl font-bold text-amber-700">
                Laporan Tersedia
            </h4>

            @if(!empty($laporans) && $laporans->count())

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">

                    @foreach($laporans as $laporan)

                        <div class="overflow-hidden rounded-xl bg-white shadow-lg">

                            @if($laporan->gambar)
                                <img
                                    src="{{ asset('storage/'.$laporan->gambar) }}"
                                    alt="{{ $laporan->judul }}"
                                    class="h-56 w-full object-cover">
                            @endif

                            <div class="p-5">

                                <h4 class="mb-3 text-xl font-bold text-amber-700">
                                    {{ $laporan->judul }}
                                </h4>

                                <p class="text-gray-600">
                                    {{ $laporan->deskripsi }}
                                </p>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="py-10 text-center">

                    <h4 class="text-xl font-semibold text-gray-500">
                        Belum ada laporan yang tersedia
                    </h4>

                </div>

            @endif

        </div> -->

        <!-- Tombol -->
        

            <!-- Laporan Section --> 
            <!-- <div x-show="activeApp === 'laporan'"
    class="overflow-hidden rounded-2xl bg-gradient-to-br from-amber-50 to-yellow-50 shadow-xl">

    <div class="p-8">

        <h3 class="mb-8 text-3xl font-bold text-amber-800">
            Data Laporan
        </h3>

        @if(!empty($laporans) && $laporans->count())

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                @foreach($laporans as $laporan)

                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">

                        @if($laporan->gambar)
                            <img
                                src="{{ asset('storage/'.$laporan->gambar) }}"
                                alt="{{ $laporan->judul }}"
                                class="w-full h-56 object-cover">
                        @endif

                        <div class="p-5">

                            <h4 class="text-xl font-bold text-amber-700 mb-3">
                                {{ $laporan->judul }}
                            </h4>

                            <p class="text-gray-600">
                                {{ $laporan->deskripsi }}
                            </p>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="text-center py-10">

                <h4 class="text-xl font-semibold text-gray-500">
                    Belum ada laporan yang tersedia
                </h4>

            </div>

        @endif

    </div>

</div> -->

            <!-- Analisis Section -->
<div x-show="activeApp === 'analisis'"
     x-data="{ activeTab: 'overview' }"
     class="overflow-hidden rounded-2xl bg-gradient-to-br from-purple-50 to-violet-50 shadow-xl">

    <div class="p-8">

        <!-- Header -->
        <div class="mb-6 flex items-center">
            <div class="mr-4">
                <img src="{{ asset('storage/assets_images/images/sub-app/AnalisisLogo.png') }}"
                     alt="Analisis Logo"
                     class="h-10 w-10 object-contain">
            </div>

            <h3 class="text-2xl font-bold text-purple-800">
                Analisis
            </h3>
        </div>

        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <ul class="-mb-px flex flex-wrap">

                <li class="mr-2">
                    <button @click="activeTab = 'overview'"
                        :class="{
                            'text-purple-600 border-b-2 border-purple-600': activeTab === 'overview',
                            'text-gray-500 hover:text-purple-600': activeTab !== 'overview'
                        }"
                        class="inline-block p-4 font-medium">
                        Overview
                    </button>
                </li>

                <li class="mr-2">
                    <button @click="activeTab = 'features'"
                        :class="{
                            'text-purple-600 border-b-2 border-purple-600': activeTab === 'features',
                            'text-gray-500 hover:text-purple-600': activeTab !== 'features'
                        }"
                        class="inline-block p-4 font-medium">
                        Fitur
                    </button>
                </li>

                <li class="mr-2">
                    <button @click="activeTab = 'demo'"
                        :class="{
                            'text-purple-600 border-b-2 border-purple-600': activeTab === 'demo',
                            'text-gray-500 hover:text-purple-600': activeTab !== 'demo'
                        }"
                        class="inline-block p-4 font-medium">
                        Demo
                    </button>
                </li>

            </ul>
        </div>

        <!-- OVERVIEW -->
        <div x-show="activeTab === 'overview'"
             class="grid grid-cols-1 gap-8 md:grid-cols-2">

            <div>

                <p class="mb-6 text-gray-700">
                    Fitur Analisis membantu mitra memahami perkembangan usaha
                    melalui data yang tersaji secara otomatis dan real-time.
                    Berbagai informasi seperti penjualan, stok barang,
                    transaksi, performa produk, hingga tren usaha dapat
                    dianalisis dengan mudah sehingga membantu pengambilan
                    keputusan yang lebih tepat.
                </p>

                <div class="rounded-lg bg-white p-6 shadow-md">

                    <h4 class="mb-4 font-semibold text-purple-700 text-lg">
                        Mengapa memilih Laporan?
                    </h4>

                    <div class="space-y-4">

                        <div class="flex items-center gap-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-purple-100 text-purple-700">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-gray-600 font-medium">
                                Memantau perkembangan bisnis secara real-time.
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-purple-100 text-purple-700">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-gray-600 font-medium">
                                Mengetahui produk paling laris dan paling lambat terjual.
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-purple-100 text-purple-700">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-gray-600 font-medium">
                                Membantu menentukan strategi bisnis berdasarkan data.
                            </span>
                        </div>

                    </div>

                </div>

            </div>

            <div class="flex items-center justify-center">

                <img src="https://placehold.co/600x400"
                     alt="Analisis"
                     class="max-h-80 rounded-lg object-contain shadow-lg">

            </div>

        </div>

        <!-- FITUR -->
        <div x-show="activeTab === 'features'"
             class="space-y-4">

            <h4 class="mb-3 font-semibold text-purple-700">
                Sub Fitur Analisis:
            </h4>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div class="flex items-center">
                        <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-purple-100 text-purple-700">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <h5 class="font-medium text-gray-800">
                            Analisis Penjualan
                        </h5>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div class="flex items-center">
                        <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-purple-100 text-purple-700">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <h5 class="font-medium text-gray-800">
                            Grafik Performa Bisnis
                        </h5>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div class="flex items-center">
                        <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-purple-100 text-purple-700">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <h5 class="font-medium text-gray-800">
                            Analisis Produk Terlaris
                        </h5>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div class="flex items-center">
                        <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-purple-100 text-purple-700">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <h5 class="font-medium text-gray-800">
                            Monitoring Stok Barang
                        </h5>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div class="flex items-center">
                        <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-purple-100 text-purple-700">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <h5 class="font-medium text-gray-800">
                            Analisis Pelanggan
                        </h5>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div class="flex items-center">
                        <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-purple-100 text-purple-700">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <h5 class="font-medium text-gray-800">
                            Rekomendasi Berbasis Data
                        </h5>
                    </div>
                </div>

            </div>

        </div>

        <!-- DEMO -->
        <div x-show="activeTab === 'demo'"
             class="flex flex-col items-center">

            <div class="w-full max-w-3xl rounded-lg bg-white p-6 shadow-lg">

                <h4 class="mb-4 text-center font-semibold text-purple-700">
                    Demo Analisis Bisnis
                </h4>

                <div class="overflow-hidden rounded-xl">

                    <iframe
                        class="h-[220px] w-full md:h-[450px]"
                        src="https://www.youtube.com/embed/dQw4w9WgXcQ"
                        title="Demo Analisis"
                        frameborder="0"
                        allowfullscreen>
                    </iframe>

                </div>

                <p class="mt-4 text-center text-gray-600">
                    Pelajari bagaimana fitur Analisis membantu membaca data
                    usaha dan menghasilkan keputusan bisnis yang lebih tepat.
                </p>

            </div>

        </div>

        <!-- Button -->
        <!-- UBAH BAGIAN TOMBOL PALING BAWAH MENJADI SEPERTI INI -->
<div class="mt-8 text-center">
    <a href="{{ route('analisis.detail') }}"
       class="inline-flex items-center rounded-lg bg-purple-600 px-6 py-3 font-medium text-white transition-colors hover:bg-purple-700 group shadow-md">
        <span>Selengkapnya</span>
        <!-- <svg class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
        </svg> -->
    </a>
</div>

    </div>

</div>

          <!-- Komunitas Section -->
<div x-show="activeApp === 'komunitas'"
     x-data="{ activeTab: 'overview' }"
     class="overflow-hidden rounded-2xl bg-gradient-to-br from-rose-50 to-red-50 shadow-xl">

    <div class="p-8">

        <!-- Header -->
        <div class="mb-6 flex items-center">
            <div class="mr-4">
                <img src="{{ asset('storage/assets_images/images/sub-app/KomunitasLogo.png') }}"
                    alt="Komunitas Logo"
                    class="h-10 w-10 object-contain">
            </div>

            <h3 class="text-2xl font-bold text-rose-800">
                Komunitas
            </h3>
        </div>

        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <ul class="-mb-px flex flex-wrap">

                <li class="mr-2">
                    <button @click="activeTab = 'overview'"
                        :class="{
                            'text-rose-600 border-b-2 border-rose-600': activeTab === 'overview',
                            'text-gray-500 hover:text-rose-600': activeTab !== 'overview'
                        }"
                        class="inline-block p-4 font-medium">
                        Overview
                    </button>
                </li>

                <li class="mr-2">
                    <button @click="activeTab = 'features'"
                        :class="{
                            'text-rose-600 border-b-2 border-rose-600': activeTab === 'features',
                            'text-gray-500 hover:text-rose-600': activeTab !== 'features'
                        }"
                        class="inline-block p-4 font-medium">
                        Fitur
                    </button>
                </li>

                <li class="mr-2">
                    <button @click="activeTab = 'demo'"
                        :class="{
                            'text-rose-600 border-b-2 border-rose-600': activeTab === 'demo',
                            'text-gray-500 hover:text-rose-600': activeTab !== 'demo'
                        }"
                        class="inline-block p-4 font-medium">
                        Demo
                    </button>
                </li>

            </ul>
        </div>

        <!-- OVERVIEW -->
        <div x-show="activeTab === 'overview'"
             class="grid grid-cols-1 gap-8 md:grid-cols-2">

            <div>

                <p class="mb-6 text-gray-700">
                    Fitur Komunitas menjadi wadah bagi para mitra untuk saling
                    terhubung, berbagi pengalaman, bertukar informasi bisnis,
                    serta memperoleh wawasan baru dari sesama pelaku usaha.
                    Melalui komunitas, mitra dapat memperluas jaringan dan
                    meningkatkan kemampuan bisnis secara bersama-sama.
                </p>

                <div class="rounded-lg bg-white p-6 shadow-md">

                    <h4 class="mb-4 font-semibold text-rose-700 text-lg">
                        Mengapa Bergabung dengan Komunitas?
                    </h4>

                    <div class="space-y-4">

                        <div class="flex items-center gap-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-700">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-gray-600 font-medium">
                                Berinteraksi dengan sesama pelaku usaha.
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-700">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-gray-600 font-medium">
                                Mendapatkan informasi dan peluang bisnis terbaru.
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-700">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-gray-600 font-medium">
                                Belajar langsung dari pengalaman anggota lain.
                            </span>
                        </div>

                    </div>

                </div>

            </div>

            <div class="flex items-center justify-center">

                <img src="https://placehold.co/600x400"
                    alt="Komunitas"
                    class="max-h-80 rounded-lg object-contain shadow-lg">

            </div>

        </div>

        <!-- FITUR -->
        <div x-show="activeTab === 'features'"
             class="space-y-4">

            <h4 class="mb-3 font-semibold text-rose-700">
                Sub Fitur Komunitas:
            </h4>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div class="flex items-center">
                        <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-700">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <h5 class="font-medium text-gray-800">
                            Forum Diskusi
                        </h5>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div class="flex items-center">
                        <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-700">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <h5 class="font-medium text-gray-800">
                            Event & Webinar
                        </h5>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div class="flex items-center">
                        <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-700">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <h5 class="font-medium text-gray-800">
                            Materi Pembelajaran
                        </h5>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div class="flex items-center">
                        <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-700">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <h5 class="font-medium text-gray-800">
                            Networking Bisnis
                        </h5>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div class="flex items-center">
                        <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-700">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <h5 class="font-medium text-gray-800">
                            Tanya Jawab dengan Ahli
                        </h5>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-4 shadow-md">
                    <div class="flex items-center">
                        <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-700">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <h5 class="font-medium text-gray-800">
                            Kolaborasi Antar Mitra
                        </h5>
                    </div>
                </div>

            </div>

        </div>

        <!-- DEMO -->
        <div x-show="activeTab === 'demo'"
             class="flex flex-col items-center">

            <div class="w-full max-w-3xl rounded-lg bg-white p-6 shadow-lg">

                <h4 class="mb-4 text-center font-semibold text-rose-700">
                    Demo Komunitas Kedai Indonesia
                </h4>

                <div class="overflow-hidden rounded-xl">

                    <iframe
                        class="h-[220px] w-full md:h-[450px]"
                        src="https://www.youtube.com/embed/dQw4w9WgXcQ"
                        title="Demo Komunitas"
                        frameborder="0"
                        allowfullscreen>
                    </iframe>

                </div>

                <p class="mt-4 text-center text-gray-600">
                    Lihat bagaimana anggota komunitas saling berinteraksi,
                    berbagi pengalaman, dan mengembangkan bisnis bersama.
                </p>

            </div>

        </div>

        <!-- UBAH BAGIAN TOMBOL PALING BAWAH MENJADI SEPERTI INI -->
<div class="mt-8 text-center">
    <a href="{{ route('komunitas.detail') }}"
       class="inline-flex items-center rounded-lg bg-rose-600 px-6 py-3 font-medium text-white transition-colors hover:bg-rose-700 group shadow-md">
        <span>Selengkapnya</span>
        <!-- <svg class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
        </svg> -->
    </a>
</div>

    </div>

</div>

            <!-- Informasi Section -->
<!-- <div x-show="activeApp === 'informasi'"
    class="overflow-hidden rounded-2xl bg-gradient-to-br from-sky-50 to-blue-50 shadow-xl"> -->

    <!-- Informasi Section -->
<div x-show="activeApp === 'informasi'"
     x-data="{ activeTab: 'overview' }"
     class="overflow-hidden rounded-2xl bg-gradient-to-br from-sky-50 to-blue-50 shadow-xl">

    <div class="p-8">

        <!-- Header -->
        <div class="mb-6 flex items-center">
            <div class="mr-4">
                <img src="{{ asset('storage/assets_images/images/sub-app/InformasiLogo.png') }}"
                    alt="Informasi Logo"
                    class="h-10 w-10 object-contain">
            </div>

            <h3 class="text-2xl font-bold text-sky-800"
                x-text="appData.informasi.title">
            </h3>
        </div>

        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <ul class="-mb-px flex flex-wrap">
                <li class="mr-2">
                    <button @click="activeTab = 'overview'"
                        :class="activeTab === 'overview'
                            ? 'border-b-2 border-sky-600 text-sky-600'
                            : 'text-gray-500 hover:text-sky-600'"
                        class="inline-block p-4 font-medium">
                        Overview
                    </button>
                </li>

                <li class="mr-2">
                    <button @click="activeTab = 'features'"
                        :class="activeTab === 'features'
                            ? 'border-b-2 border-sky-600 text-sky-600'
                            : 'text-gray-500 hover:text-sky-600'"
                        class="inline-block p-4 font-medium">
                        Fitur
                    </button>
                </li>

                <li class="mr-2">
                    <button @click="activeTab = 'demo'"
                        :class="activeTab === 'demo'
                            ? 'border-b-2 border-sky-600 text-sky-600'
                            : 'text-gray-500 hover:text-sky-600'"
                        class="inline-block p-4 font-medium">
                        Demo
                    </button>
                </li>
            </ul>
        </div>

        <!-- OVERVIEW -->
        <div x-show="activeTab === 'overview'"
             class="grid grid-cols-1 gap-8 md:grid-cols-2">

            <div>
                <p class="mb-6 text-gray-700"
                    x-text="appData.informasi.description">
                </p>

                <div class="rounded-lg bg-white p-6 shadow-md">
                    <h4 class="mb-4 text-lg font-semibold text-sky-700">
                        Mengapa Memilih Informasi?
                    </h4>

                    <div class="space-y-4">

                        <div class="flex items-center gap-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-700">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-gray-700">
                                Update terbaru mengenai program dan layanan Kedai Indonesia.
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-700">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-gray-700">
                                Informasi promo, event dan kegiatan terbaru.
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-700">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-gray-700">
                                Pengumuman penting untuk seluruh mitra.
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-700">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-gray-700">
                                Wawasan bisnis dan perkembangan usaha terkini.
                            </span>
                        </div>

                    </div>
                </div>
            </div>

            <div class="flex items-center justify-center">
                <img src="https://placehold.co/600x400"
                    alt="Informasi"
                    class="max-h-80 rounded-lg object-cover shadow-lg">
            </div>
        </div>

        <!-- FITUR -->
        <div x-show="activeTab === 'features'"
             class="space-y-4">

            <h4 class="mb-3 font-semibold text-sky-700">
                Sub Fitur Informasi:
            </h4>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                <template x-for="feature in appData.informasi.features">
                    <div class="rounded-lg bg-white p-4 shadow-md">
                        <div class="flex items-center">
                            <div class="mr-3 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-700">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <h5 class="font-medium text-gray-800"
                                x-text="feature">
                            </h5>
                        </div>
                    </div>
                </template>

            </div>
        </div>

        <!-- DEMO -->
        <div x-show="activeTab === 'demo'"
             class="flex flex-col items-center">

            <div class="w-full max-w-3xl rounded-lg bg-white p-6 shadow-lg">
                <h4 class="mb-4 text-center font-semibold text-sky-700">
                    Demo Informasi
                </h4>

                <div class="overflow-hidden rounded-xl">
                    <iframe class="h-[220px] w-full md:h-[450px]"
                        src="https://www.youtube.com/embed/YOUR_VIDEO_ID"
                        title="Demo Informasi"
                        frameborder="0"
                        allowfullscreen>
                    </iframe>
                </div>

                <p class="mt-4 text-center text-gray-600">
                    Video demonstrasi fitur Informasi Kedai Indonesia.
                </p>
            </div>
        </div>

        <!-- BUTTON -->
<div class="mt-8 text-center">
    <a href="{{ route('informasi.detail') }}"
        class="inline-flex items-center rounded-lg bg-sky-600 px-6 py-3 font-medium text-white transition-colors hover:bg-sky-700 group shadow-md">
        <span>Selengkapnya</span>
        <!-- <svg class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M14 5l7 7m0 0l-7 7m7-7H3">
            </path>
        </svg> -->
    </a>
</div>

    </div>
</div>
        <!-- <div class="flex items-center justify-center">
            <img src="{{ asset('storage/assets_images/images/informasi.png') }}"
                alt="Informasi"
                class="max-h-80 rounded-lg object-cover shadow-lg">
        </div> -->

    <!-- </div>

    <div class="mt-8 text-center">
        <a href="#"
            class="inline-flex items-center rounded-lg bg-sky-600 px-6 py-3 font-medium text-white hover:bg-sky-700">
            Lihat Informasi Terbaru
        </a>
    </div>

</div>
</div> -->

            <!-- Informasi Section -->
            <!-- <div x-show="activeApp === 'informasi'"
                class="overflow-hidden rounded-2xl bg-gradient-to-br from-sky-50 to-blue-50 shadow-xl">
                <div class="p-8">
                    <div class="mb-6 flex items-center">
                        <div class="mr-4 rounded-full bg-white p-3 shadow-md">
                            <img src="https://placehold.co/100x100" alt="Informasi Logo" class="h-10 w-10">
                        </div>
                        <h3 class="text-2xl font-bold text-sky-800" x-text="appData.informasi.title"></h3>
                    </div>

                    <p class="mb-8 text-gray-700" x-text="appData.informasi.description"></p> -->

                    <!-- Featured article -->
                    

                    <!-- Article grid -->
                   <!-- Artikel Terbaru -->
                   <!-- <div class="mb-8">

<div class="mb-6 flex items-center justify-between">
    <h4 class="text-xl font-bold text-sky-800">
        Artikel Terbaru
    </h4>
</div>

<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">

    @forelse($latestArticles as $artikel)

        <div class="overflow-hidden rounded-xl bg-white shadow-md transition hover:-translate-y-1 hover:shadow-lg">

            <img src="{{ asset('storage/' . $artikel->gambar) }}"
                alt="{{ $artikel->judul }}"
                class="h-48 w-full object-cover">

            <div class="p-4">

                <p class="mb-2 text-xs text-gray-500">
                    {{ \Carbon\Carbon::parse($artikel->published_at)->translatedFormat('d F Y') }}
                </p>

                <h5 class="mb-2 line-clamp-2 font-bold text-gray-800">
                    {{ $artikel->judul }}
                </h5>

                <p class="mb-4 text-sm text-gray-600">
                    {{ \Illuminate\Support\Str::limit(strip_tags($artikel->isi), 80) }}
                </p>

                <div class="flex items-center justify-between">

                    <span class="text-xs text-gray-500">
                        👁️ {{ $artikel->views }} views
                    </span>

                    <a href="{{ route('artikel.show', $artikel->slug) }}"
                        class="font-semibold text-sky-600 hover:text-sky-800">
                        Baca →
                    </a>

                </div>

            </div>

        </div>

    @empty

        <div class="col-span-3 text-center">
            <p class="text-gray-500">
                Belum ada artikel tersedia.
            </p>
        </div>

    @endforelse

</div>

<div class="mt-8 flex justify-center">
    <a href="{{ route('artikel') }}"
        class="rounded-lg border border-sky-600 px-6 py-2 text-sm font-semibold text-sky-600 transition hover:bg-sky-600 hover:text-white">
        Lihat Semua Artikel →
    </a>
</div>

</div> -->


<script type="module">
    import KeenSlider from 'https://cdn.jsdelivr.net/npm/keen-slider@6.8.6/+esm'

    // Tunggu DOM siap sebelum inisialisasi slider
    function initSlider() {
        const sliderElement = document.getElementById('subapp-slider')
        if (!sliderElement) {
            console.error('Slider element not found')
            return
        }

        // const keenSlider = new KeenSlider(
        //     '#subapp-slider', {
        //         loop: true,
        //         slides: {
        //             origin: 'center',
        //             perView: 3,
        //             spacing: 16,
        //         },
        //         breakpoints: {
        //             '(max-width: 640px)': {
        //                 slides: {
        //                     origin: 'center',
        //                     perView: 2,
        //                     spacing: 8,
        //                 },
        //             },
        //             '(min-width: 641px) and (max-width: 1023px)': {
        //                 slides: {
        //                     origin: 'center',
        //                     perView: 3,
        //                     spacing: 12,
        //                 },
        //             },
        //             '(min-width: 1024px)': {
        //                 loop: true,
        //                 slides: {
        //                     origin: 'center',
        //                     perView: 5,
        //                     spacing: 16,
        //                 },
        //             },
        //         },
        //     },
        //     [
        //         // Auto-play plugin
        //         (slider) => {
        //             let timeout
        //             let mouseOver = false

        //             function clearNextTimeout() {
        //                 clearTimeout(timeout)
        //             }

        //             function nextTimeout() {
        //                 clearTimeout(timeout)
        //                 if (mouseOver) return
        //                 timeout = setTimeout(() => {
        //                     slider.next()
        //                 }, 5000)
        //             }

        //             slider.on("created", () => {
        //                 slider.container.addEventListener("mouseover", () => {
        //                     mouseOver = true
        //                     clearNextTimeout()
        //                 })
        //                 slider.container.addEventListener("mouseout", () => {
        //                     mouseOver = false
        //                     nextTimeout()
        //                 })
        //                 nextTimeout()
        //             })
        //             slider.on("dragStarted", clearNextTimeout)
        //             slider.on("animationEnded", nextTimeout)
        //             slider.on("updated", nextTimeout)
        //         },
        //     ]
        // )

        // Setup navigation buttons
        const keenSliderPrevious = document.getElementById('app-slider-previous')
        const keenSliderNext = document.getElementById('app-slider-next')

        if (keenSliderPrevious) {
            keenSliderPrevious.addEventListener('click', () => keenSlider.prev())
        }
        if (keenSliderNext) {
            keenSliderNext.addEventListener('click', () => keenSlider.next())
        }

        console.log('Keen Slider initialized successfully')
    }

    // Jalankan saat DOM sudah siap
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSlider)
    } else {
        // DOM sudah siap
        initSlider()
    }
</script>