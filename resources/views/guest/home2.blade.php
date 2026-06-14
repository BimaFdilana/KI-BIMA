@extends('layouts.guest')

@section('title', 'Halaman Utama')

@section('content')

    <x-guest.banner-carousel />
    <section id="section1" class="overlay-section bg-white px-4 py-12 md:py-24">
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
                    <div class="font-heading-regular text-lg text-black md:text-xl">
                        <p class="mb-4">
                            Tidak hanya sebatas memberikan layanan penyedia aplikasi <i>Point of Sale (POS)</i> atau kasir
                            toko,
                            <span class="font-bold uppercase text-red-500">KEDAI INDONESIA</span> By <span
                                class="font-bold text-blue-500">IMAGI</span> membangun ekosistem digital yang holistik.
                            Secara aktif membantu setiap mitra dalam mengelola usahanya berbasis digital.
                        </p>
                        <p>
                            Seluruh mitra KI akan mendapatkan layanan Aplikasi berbasis android "Kedai Indonesia"
                            mulai dari proses stocking yang terkoneksi ke distributor utama, menjual (kasir), menganalisis
                            hasil usaha, hingga bertukar informasi bersama seluruh mitra yang tergabung dalam komunitas.
                            Dengan demikian persaingan usaha kedai kelontong menjadi lebih baik, kondusif dan berdaya saing.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="section2" class="overlay-section bg-white px-4">
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
    </section>
    
    <x-wizard.applikasi-sub :latestArticles="$latestArticles" />
   
@endsection