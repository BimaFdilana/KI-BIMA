@extends('layouts.guest')

@section('title', 'Halaman Utama')

@section('content')

    <x-guest.banner-carousel />

    <!-- Section 1: Apa Itu KEDAI INDONESIA -->
    <p></p>
    <section id="section1" class="overlay-section bg-white px-4 py-6 md:py-12">
        <div class="container mx-auto max-w-[85rem]">
            <div class="mx-auto flex flex-col items-center gap-8 text-wrap md:flex-row md:gap-12">
                <div class="relative mb-8 w-full text-center md:mb-0 md:w-1/2 md:pr-10 md:text-right" data-aos="fade-right"
                    data-aos-duration="1000">
                    <h2 class="font-heading-bold mb-4 text-3xl font-black capitalize text-gray-800 md:mb-6 md:text-5xl">Apa
                        Itu <p><span class="uppercase text-red-500">KEDAI </span><span
                            class="text-stroke-black-500 text-stroke uppercase text-transparent">INDONESIA</span> ?</h2></p>
                                  <!-- KEDAI INDONESIA adalah ekosistem digital terintegrasi yang digerakkan oleh PT. 
                                  IMAGI bersama BADAN USAHA LOKAL untuk membantu transformasi UMKM kedai konvensional. 
                                  Kami hadir memodernisasi warung tradisional secara terstruktur, menciptakan lompatan ekonomi daerah, 
                                  dan mengantarkan masyarakat menuju kesejahteraan yang berkelanjutan. Bersama KEDAI INDONESIA,
                                   kedai konvensional tidak lagi tergilas oleh ritel modern, 
                                  melainkan siap memimpin persaingan. Melalui satu aplikasi yang user-friendly, 
                                  kami mengintegrasikan tata kelola bisnis berbasis online: memangkas rantai pasok untuk menjamin harga kulakan termurah, 
                                  memberikan pembinaan intensif, dan melipatgandakan produktivitas usaha. Saatnya kedai lokal bergerak lebih efisien, 
                                  lebih profit, dan menjadi pilar utama kemandirian ekonomi rakyat.Berbisnis dengan lebih mudah,  efektif dan efisien. -->
                    <p>KEDAI INDONESIA adalah ekosistem digital terintegrasi yang digerakkan oleh PT. 
                                  IMAGI bersama BADAN USAHA LOKAL untuk <strong>membantu transformasi UMKM kedai konvensional.</strong>   Kami hadir memodernisasi warung tradisional secara terstruktur, menciptakan lompatan ekonomi daerah, 
                                  dan mengantarkan masyarakat menuju kesejahteraan yang berkelanjutan. 
                    </p>    
                    <p>Bersama KEDAI INDONESIA,
                                   kedai konvensional tidak lagi tergilas oleh ritel modern, 
                                  melainkan siap memimpin persaingan. Melalui satu aplikasi yang <i>user-friendly</i>, 
                                  kami mengintegrasikan tata kelola bisnis berbasis online, memangkas rantai pasok untuk menjamin harga kulakan termurah, 
                                  memberikan pembinaan intensif, dan melipatgandakan produktivitas usaha. <Strong>Saatnya kedai lokal bergerak lebih efisien, 
                                  lebih profit, dan menjadi pilar utama kemandirian ekonomi daerah.</Strong> Berbisnis dengan lebih mudah,  efektif dan efisien.</p>
                    <!-- <p class="mb-4 text-base text-gray-600 md:text-lg">KEDAI INDONESIA hadir sebagai teman bagi UMKM kedai
                        konvensional, melalui ekosistem digital yang dikelola PT.
                        IMAGI bersama badan usaha lokal. Kami ingin <strong>membantu kedai konvensional</strong> untuk terus
                        tumbuh dan
                        berkembang dengan terencana. Sehingga perekonomian semakin meningkat menuju kesejahteraan masyarakat
                        yang
                        berkelanjutan.
                    </p>
                    <p class="mb-4 text-base text-gray-600 md:text-lg">Bersama KEDAI INDONESIA, kedai konvensional akan
                        lebih siap untuk bersaing. Dengan tata kelola bisnis berbasis online terintegrasi, mulai dari
                        distribusi rantai pasok, pembinaan berkelanjutan, perolehan harga terbaik dan kompetitif, serta
                        aplikasi <i>user friendly</i>, akan sangat membantu kedai konvensional menjadi lebih
                        produktif.
                    </p> -->

                    <div class="absolute -right-0 -top-8 hidden rotate-90 md:-top-16 md:left-5 md:block md:rotate-0">
                        <!-- Baris pertama -->
                        <div class="absolute" style="top: 0; left: 0px;">
                            <svg class="h-auto w-16 rotate-90 md:w-20 md:rotate-0" viewBox="0 0 200 200"
                                xmlns="http://www.w3.org/2000/svg">
                                <polygon points="100,16.7 175,58.3 175,141.7 100,183.3 25,141.7 25,58.3" fill="transparent"
                                    stroke="black" stroke-width="3" />
                            </svg>
                        </div>
                        <div class="absolute" style="top: 40px; left: 60px;">
                            <svg class="h-auto w-16 rotate-90 md:w-20 md:rotate-0" viewBox="0 0 200 200"
                                xmlns="http://www.w3.org/2000/svg">
                                <polygon points="100,16.7 175,58.3 175,141.7 100,183.3 25,141.7 25,58.3" fill="transparent"
                                    stroke="red" stroke-width="3" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-[40%]" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <img src="{{ asset('storage/assets_images/images/toko-ki.png') }}" alt="Section One Image"
                        class="mx-auto h-auto w-full max-w-md md:max-w-full">
                </div>
            </div>
        </div>
    </section>
    <!-- Section 2: Kenapa bersama KEDAI INDONESIA -->
    <section id="section2" class="overlay-section bg-white px-4 py-6 md:py-12">
        <div class="container mx-auto max-w-7xl">
            <div class="mx-auto flex flex-col items-center justify-between gap-8 md:flex-row-reverse md:gap-12">
                <div class="mb-8 text-center md:mb-0 md:w-[60%] md:pl-10 md:text-left" data-aos="fade-left"
                    data-aos-duration="1000">
                    <h2 class="font-heading-bold mb-4 text-3xl font-black capitalize text-gray-800 md:mb-6 md:text-5xl">
                        Kenapa bersama <span class="uppercase text-red-500">KEDAI </span><span
                            class="font-heading-bold-stroke uppercase text-transparent">INDONESIA</span> ?</h2>
                    <!-- <p class="mb-4 text-base text-gray-600 md:text-lg">Data menunjukan bahwa <strong>lebih dari 50%</strong>
                        kedai kelontong
                        terancam masalah finansial (tutup) akibat meluasnya invasi retail modern. Hal ini karena lebih dari
                        90% pelaku usaha kedai konvensional menjalankan usaha hanya berdasarkan kebiasaan,tanpa rencana dan
                        analisis usaha yang memadai. Dampaknya adalah usaha kedai konvensional sangat rentan terhadap
                        persaingan kapitalisasi. Semetara <strong>UMKM merupakan salah satu ujung tombak ketahan ekonomi
                            lokal.</strong></p>
                    <p class="mb-4 text-base text-gray-600 md:text-lg"><strong>KEDAI INDONESIA bagian dari solusi bagi
                            UMKM</strong> untuk
                        membangun usaha yang terencana dan produktif. Dengan bergabung Bersama ekosistim digital Kedai
                        Indonesia, usaha kedai konvensional akan lebih bardaya saing, karena dikelola secara digital dan
                        mendapatkan pembinaan yang komprehensif. Dengan demikian usaha akan menjadi lebih mudan dan
                        akuntable, Bergabung dalam komunitas para pelaku kedai konvensional akan mendorong lebih
                        <strong>berkembang dan berjaya bersama.</strong></strong> -->

                        <p>
                    Berbagai kajian ekonomi menunjukkan bahwa eksistensi kedai kelontong kini kian terancam akibat masifnya ekspansi ritel modern. 
                    Tantangan terbesar muncul karena lebih dari 90% pelaku usaha konvensional masih menjalankan bisnisnya berdasarkan kebiasaan semata,
                    <Strong>tanpa perencanaan finansial dan perhitungan manajemen yang matang.</Strong> 
                    Menjadi bagian dari KEDAI INDONESIA berarti menempatkan usaha Anda di dalam sebuah ekosistem pelindung yang kuat dan berkelanjutan. 
                    Kolaborasi strategis antara PT. IMAGI dan badan usaha lokal ini hadir untuk memastikan bahwa kedai tradisional tidak lagi berjalan 
                    sendirian menghadapi gempuran ritel modern. Dengan sistem digital terintegrasi, Anda akan dibekali dengan manajemen bisnis berbasis 
                    online yang efisien, kepastian pasokan barang dengan harga terbaik, serta pendampingan dan pembinaan intensif yang terarah. Bersama 
                    KEDAI INDONESIA, kita tidak hanya melipatgandakan omzet dan memodernisasi warung rakyat, tetapi juga bersama-sama <Strong>mengamankan 
                    perputaran modal</Strong> di daerah demi tegaknya KEMANDIRIAN EKONOMI masyarakat yang berkelanjutan.
                    </p>
            
                </div>
                <div class="w-full md:w-[40%]" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200">
                    <img src="{{ asset('storage/assets_images/images/tablet-ki.png') }}" alt="Section Two Image"
                        class="mx-auto h-auto w-full max-w-md md:max-w-full">
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: KEDAI INDONESIA & Kemandirian Ekonomi Daerah -->
    <section id="section3" class="overlay-section bg-white px-4 py-6 md:py-12">
        <div class="container mx-auto max-w-7xl">
            <!-- Mobile layout: Title first, then description -->
            <div class="flex flex-col md:hidden">
                <!-- Title on mobile -->
                <div class="mb-8 text-center" data-aos="fade-right" data-aos-duration="1000">
                    <h2 class="font-heading-bold text-4xl font-black capitalize text-gray-800">
                        <span class="uppercase text-red-500">KEDAI </span>
                        <span class="font-heading-bold-stroke uppercase text-transparent">INDONESIA</span>
                        <div class="text-3xl">& Kemandirian Ekonomi Daerah</div>
                    </h2>
                </div>

                <!-- Description on mobile (justified) -->
                <div data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">

        <p>Dengan memfungsikan badan usaha lokal sebagai jembatan distribusi dari produsen ke konsumen, 
        sirkulasi uang daerah akan terjaga dan sepenuhnya mengalir untuk memperkuat ekonomi wilayah. 
        Digitalisasi dan modernisasi tata kelola kedai tradisional ini tidak hanya mengamankan pasar, 
        tetapi juga menciptakan ekosistem baru bagi produk UMKM lokal untuk memenuhi kebutuhan domestik. 
        Lewat filosofi “Dari Kita untuk Kita”, kita sedang membangun kedaulatan dan kemandirian ekonomi 
        daerah—sebuah transformasi terstruktur demi melahirkan kesejahteraan masyarakat yang nyata dan berkelanjutan.</p>

</div>
            </div>

            <!-- Desktop layout: Original side-by-side design -->
            <div class="hidden md:flex md:flex-row-reverse md:items-center md:justify-between md:gap-12">
                <!-- Left content (description) -->
                <div class="md:w-[62%] md:border-l-8 md:border-gray-900 md:pl-10 md:text-left" data-aos="fade-left"
                    data-aos-duration="1000">
                    <p style="font-size: 16px;">
                    Dengan memfungsikan badan usaha lokal sebagai jembatan distribusi dari produsen ke konsumen, 
        sirkulasi uang daerah akan terjaga dan sepenuhnya mengalir untuk memperkuat ekonomi wilayah. 
        Digitalisasi dan modernisasi tata kelola kedai tradisional ini tidak hanya mengamankan pasar, 
        tetapi juga menciptakan ekosistem baru bagi produk UMKM lokal untuk memenuhi kebutuhan domestik. 
        Lewat filosofi <Strong>“Dari Kita untuk Kita”</Strong>, kita sedang membangun kedaulatan dan kemandirian ekonomi 
        daerah—sebuah transformasi terstruktur demi melahirkan kesejahteraan masyarakat yang nyata dan berkelanjutan.
                    </p>
                </div>

                <!-- Right content (title) -->
                <div class="md:w-[40%] md:text-right" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200">
                    <h2 class="font-heading-bold text-7xl font-black capitalize text-gray-800">
                        <span class="uppercase text-red-500">KEDAI </span>
                        <span class="font-heading-bold-stroke uppercase text-transparent">INDONESIA</span>
                        <div class="text-6xl lg:text-7xl">& Kemandirian Ekonomi Daerah</div>
                    </h2>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 4: Kami Hadir -->
    <section id="section4" class="overlay-section bg-white px-4 ">
        <div class="container mx-auto max-w-[85rem]">
            <div class="mx-auto flex flex-col items-center gap-8 text-wrap md:flex-row md:gap-12">
                <div class="relative mb-8 w-full text-center md:mb-0 md:w-[35%] md:pr-10 md:text-left" data-aos="fade-right"
                    data-aos-duration="1000">
                    <h2
                        class="font-heading-bold text-3xl font-bold uppercase leading-relaxed text-gray-800 md:text-4xl lg:text-5xl">
                        Kami Hadir untuk pastikan usahamu tumbuh, terencana
                    </h2>
                    <div class="absolute -right-0 -top-12 hidden md:-top-56 md:left-5 md:block">
                        <!-- Baris pertama -->
                        <div class="absolute" style="top: 0; left: 0px;">
                            <svg class="h-auto w-24 md:w-40" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                <polygon points="100,16.7 175,58.3 175,141.7 100,183.3 25,141.7 25,58.3" fill="transparent"
                                    stroke="black" stroke-width="3" />
                            </svg>
                        </div>
                        <div class="absolute" style="top: 100px; left: 150px;">
                            <svg class="h-auto w-16 md:w-20" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                <polygon points="100,16.7 175,58.3 175,141.7 100,183.3 25,141.7 25,58.3"
                                    fill="transparent" stroke="red" stroke-width="3" />
                            </svg>
                        </div>
                        <div class="absolute" style="top: 160px; left: 130px;">
                            <svg class="h-auto w-10 md:w-12" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                <polygon points="100,16.7 175,58.3 175,141.7 100,183.3 25,141.7 25,58.3" fill="red"
                                    stroke="red" stroke-width="3" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-[65%]" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <img src="{{ asset('storage/assets_images/images/diagram_trans.gif') }}" alt="Section One Image"
                        class="h-auto w-full">
                </div>
            </div>
        </div>
    </section>
    <section id="section5" class="overlay-section bg-white px-4">
        <div class="container relative mx-auto max-w-[85rem]">
            <div class="flex flex-col items-center gap-8 md:flex-row md:gap-12">
                <div class="w-full md:w-1/2" data-aos="fade-right" data-aos-duration="1000">
                    <img src="{{ asset('storage/assets_images/images/ekosistem-digital.gif') }}" alt="Ekosistem Digital"
                        class="h-auto w-full">
                </div>
                <div class="w-full md:w-1/2" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <h2 class="font-heading-bold mb-6 text-3xl font-black capitalize text-gray-800 md:text-5xl">
                        Ekosistem Digital
                        <div class="text-3xl md:text-7xl">
                            <span class="uppercase text-red-500">KEDAI </span>
                            <span class="font-heading-bold-stroke uppercase text-transparent">INDONESIA</span>
                        </div>
                    </h2>
                    <div>
                        <p class="mb-4">
                            Tidak hanya sebatas memberikan layanan penyedia aplikasi <i>Point of Sale (POS)</i> atau kasir
                            toko,
                            <span class="font-bold uppercase text-red-500">KEDAI INDONESIA</span> By <span
                                class="font-bold text-blue-500">IMAGI</span> membangun ekosistem digital yang holistik.
                            Secara aktif membantu setiap mitra dalam mengelola usahanya berbasis digital.
                        </p>
                        <p>
                            Seluruh mitra KI akan mendapatkan layanan Aplikasi berbasis android "KEDAI INDONESIA"
                            mulai dari proses stocking yang terkoneksi ke distributor utama, menjual (kasir), menganalisis
                            hasil usaha, hingga bertukar informasi bersama seluruh mitra yang tergabung dalam komunitas.
                            Dengan demikian persaingan usaha kedai kelontong menjadi lebih baik, kondusif dan berdaya saing.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="section6" class="overlay-section bg-white px-4 pt-12 md:pt-24">
        <div class="container relative mx-auto max-w-[90rem]">
            <div class="flex flex-col md:flex-row">
                <div class="relative md:w-[45%]">
                    <div class="absolute hidden md:block" style="top: 0; right: 15%;">
                        <svg class="h-auto w-32" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="100,16.7 175,58.3 175,141.7 100,183.3 25,141.7 25,58.3" fill="transparent"
                                stroke="black" stroke-width="3" />
                        </svg>
                    </div>
                    <div class="absolute hidden md:block" style="top: 40px; right: 5%;">
                        <svg class="h-auto w-20" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="100,16.7 175,58.3 175,141.7 100,183.3 25,141.7 25,58.3" fill="transparent"
                                stroke="red" stroke-width="3" />
                        </svg>
                    </div>
                    <div class="absolute hidden md:block" style="bottom: 20%; right: 2%;">
                        <svg class="w-15 h-auto" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="100,16.7 175,58.3 175,141.7 100,183.3 25,141.7 25,58.3" fill="red"
                                stroke="red" stroke-width="3" />
                        </svg>
                    </div>
                </div>
                <div class="md:w-[55%]">
                    <h2
                        class="font-heading-regular mb-4 text-left text-3xl font-black uppercase leading-tight text-gray-800 md:mb-6 md:text-7xl">
                        Berbagai Kemudahan Dalam Satu Genggaman</h2>

                        
                </div>
            </div>
        </div>
    <!-- </section> -->

    
    <x-wizard.applikasi-sub
    :latestArticles="$latestArticles"
    :laporans="$laporans"
/>
   
@endsection