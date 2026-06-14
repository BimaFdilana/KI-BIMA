@extends('layouts.guest')

@section('title', 'Hubungi Kami')

@section('content')

    <!-- Hero -->
    <section
    class="relative overflow-hidden bg-gradient-to-br from-red-900 via-red-700 to-red-600 px-4 py-24 text-white">
    <div
    class="absolute -left-20 -top-20 h-72 w-72 rounded-full bg-white/20 blur-3xl">
</div>

<div
    class="absolute bottom-0 right-0 h-96 w-96 rounded-full bg-red-300/20 blur-3xl">
</div>
      <div class="relative z-10 container mx-auto max-w-7xl text-center">
<div
    class="mb-6 inline-flex items-center rounded-full border border-white/20 bg-white/10 px-5 py-2 text-sm font-medium backdrop-blur">

    Kedai Indonesia

</div>
    <h1 class="text-5xl font-black md:text-7xl">
        Hubungi Kami
    </h1>

    <p class="mx-auto mt-6 max-w-2xl text-lg text-red-100">
        Tim Kedai Indonesia siap membantu menjawab pertanyaan,
        konsultasi produk, kerja sama bisnis, maupun kebutuhan layanan lainnya.
    </p>

</div>
    </section>

    <!-- Content -->
    <section class="bg-white px-4 py-14">
        <div class="container mx-auto max-w-7xl">

            <div class="grid gap-10 md:grid-cols-2">

                <!-- Informasi Kontak -->
                <div>

                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-800 md:text-4xl">
                            Informasi Kontak
                        </h2>

                        <p class="mt-3 text-gray-600">
                            Hubungi kami melalui platform berikut untuk mendapatkan informasi,
                            bantuan, maupun kerja sama bersama Kedai Indonesia.
                        </p>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">

                        <!-- Alamat -->
                        <div
                            class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">

                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-red-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>

                            <h3 class="mb-2 text-2xl font-bold text-gray-800">
                                Alamat
                            </h3>

                            <p class="leading-relaxed text-gray-600">
                                Jl. Bengkalis, Riau, Indonesia
                            </p>
                        
                        </div>

                        <!-- Email -->
                        <a href="mailto:info@kedaiindonesia.com"
                            class="block rounded-3xl border border-gray-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-red-400 hover:shadow-xl">

                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100">
    <svg xmlns="http://www.w3.org/2000/svg"
        class="h-7 w-7 text-red-600"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor">
        <path stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
    </svg>
</div>

                            <h3 class="mb-2 text-2xl font-bold text-gray-800">
                                Email
                            </h3>

                            <p class="text-gray-600">
                                info@kedaiindonesia.com
                            </p>
                        </a>

                        <!-- WhatsApp -->
                        <a href="https://wa.me/6281234567890"
                            target="_blank"
                            class="block rounded-3xl border border-gray-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-green-400 hover:shadow-xl">

                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-green-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-green-600" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M20.52 3.48A11.8 11.8 0 0012.06 0C5.55 0 .27 5.29.27 11.79c0 2.08.54 4.1 1.58 5.88L0 24l6.5-1.7a11.78 11.78 0 005.56 1.42h.01c6.5 0 11.79-5.29 11.79-11.79 0-3.15-1.23-6.11-3.34-8.45z" />
                                </svg>
                            </div>

                            <h3 class="mb-2 text-2xl font-bold text-gray-800">
                                WhatsApp
                            </h3>

                            <p class="text-gray-600">
                                +62 812 3456 7890
                            </p>
                        </a>

                        <!-- Instagram -->
                        <a href="https://instagram.com/kedaiindonesia"
                            target="_blank"
                            class="block rounded-3xl border border-gray-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-pink-400 hover:shadow-xl">

                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-pink-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-pink-600"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M7.75 2C4.57 2 2 4.57 2 7.75v8.5C2 19.43 4.57 22 7.75 22h8.5C19.43 22 22 19.43 22 16.25v-8.5C22 4.57 19.43 2 16.25 2h-8.5zm0 2h8.5A3.75 3.75 0 0120 7.75v8.5A3.75 3.75 0 0116.25 20h-8.5A3.75 3.75 0 014 16.25v-8.5A3.75 3.75 0 017.75 4zm8.75 1a1.25 1.25 0 100 2.5 1.25 1.25 0 000-2.5zM12 7a5 5 0 100 10 5 5 0 000-10zm0 2a3 3 0 110 6 3 3 0 010-6z" />
                                </svg>
                            </div>

                            <h3 class="mb-2 text-2xl font-bold text-gray-800">
                                Instagram
                            </h3>

                            <p class="text-gray-600">
                                @kedaiindonesia
                            </p>
                        </a>

                        <!-- Facebook -->
                        <a href="https://facebook.com/kedaiindonesia"
                            target="_blank"
                            class="block rounded-3xl border border-gray-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-blue-400 hover:shadow-xl">

                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-blue-600"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M22 12a10 10 0 10-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.19 2.23.19v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0022 12z" />
                                </svg>
                            </div>

                            <h3 class="mb-2 text-2xl font-bold text-gray-800">
                                Facebook
                            </h3>

                            <p class="text-gray-600">
                                Kedai Indonesia
                            </p>
                        </a>

                    </div>
                </div>

                <!-- Form -->
                <div>

                    <div class="rounded-[2rem] border border-red-100 bg-white p-8 shadow-[0_20px_60px_rgba(220,38,38,.12)] md:p-10">
                        <!-- Alert Success -->
                        @if(session('success'))
                            <div
                                class="mb-6 rounded-2xl border border-green-200 bg-green-100 px-5 py-4 text-green-700">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Error Validation -->
                        @if ($errors->any())
                            <div class="mb-6 rounded-2xl border border-red-200 bg-red-100 px-5 py-4 text-red-700">
                                <ul class="list-disc pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-8">
                            <h2 class="text-3xl font-bold text-gray-800 md:text-4xl">
                                Kirim Pesan
                            </h2>

                            <p class="mt-3 text-gray-500">
                                Silakan isi form berikut dan tim kami akan segera menghubungi Anda.
                            </p>
                        </div>

                        <form action="{{ route('kirim.pesan') }}" method="POST" class="space-y-6">
                            @csrf

                            <!-- Nama -->
                            <div>
                                <label class="mb-2 block font-semibold text-gray-700">
                                    Nama
                                </label>

                                <input
                                    type="text"
                                    name="nama"
                                    value="{{ old('nama') }}"
                                    placeholder="Masukkan nama lengkap"
                                    class="w-full rounded-2xl border border-gray-300 px-5 py-4 transition-all duration-300 focus:border-red-500 focus:outline-none focus:ring-4 focus:ring-red-100">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="mb-2 block font-semibold text-gray-700">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="Masukkan email"
                                    class="w-full rounded-2xl border border-gray-300 px-5 py-4 transition-all duration-300 focus:border-red-500 focus:outline-none focus:ring-4 focus:ring-red-100">
                            </div>

                            <!-- Pesan -->
                            <div>
                                <label class="mb-2 block font-semibold text-gray-700">
                                    Pesan
                                </label>

                                <textarea
                                    rows="6"
                                    name="pesan"
                                    placeholder="Tulis pesan Anda..."
                                    class="w-full rounded-2xl border border-gray-300 px-5 py-4 transition-all duration-300 focus:border-red-500 focus:outline-none focus:ring-4 focus:ring-red-100">{{ old('pesan') }}</textarea>
                            </div>

                            <!-- Button -->
                            <button
                                type="submit"
                               class="w-full rounded-2xl bg-red-600 px-6 py-4 text-lg font-semibold text-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:bg-red-700">
                                Kirim Pesan
                            </button>

                        </form>
                    </div>
                </div>

            </div>

        </div>
    </section>
<section class="bg-gray-50 py-16">
    <div class="container mx-auto max-w-7xl px-4">

        <div class="mb-8 text-center">

            <h2 class="text-4xl font-bold text-gray-800">
                Lokasi Kami
            </h2>

            <p class="mt-3 text-gray-600">
                Temukan lokasi Kedai Indonesia melalui peta berikut.
            </p>

        </div>

        <div class="overflow-hidden rounded-[2rem] shadow-2xl">

            <iframe
                src="https://www.google.com/maps?q=Jl%20Bengkalis%20Riau%20Indonesia&output=embed"
                width="100%"
                height="500"
                style="border:0;"
                loading="lazy">
            </iframe>

        </div>

    </div>
</section>
    

@endsection