@extends('layouts.guest')

@section('title', 'Analisis Detail')

@section('content')
<div class="min-h-screen bg-purple-50/30 py-12 flex items-center">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 w-full">
        
        <!-- Tombol Kembali Dinamis -->
        <div class="mb-8 text-left">
            <a href="#" id="btnBackToTab" class="inline-flex items-center text-sm font-semibold text-purple-700 hover:text-purple-800 transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        <!-- Card Box Pesan Status -->
        <div class="rounded-2xl border border-purple-100 bg-white p-8 md:p-12 shadow-xl text-center">
            
            <!-- Icon Animasi / Ilustrasi Mini -->
            <div class="inline-flex h-20 w-20 items-center justify-center rounded-3xl bg-purple-100 text-purple-600 mb-6 shadow-sm animate-pulse">
                <i class="fas fa-chart-pie text-3xl"></i>
            </div>
            
            <!-- Judul Indikator -->
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl mb-3">
                Halaman Ini Dalam Proses
            </h1>
            
            <!-- Penjelasan Ringkas -->
            <p class="text-base text-gray-600 leading-relaxed max-w-md mx-auto">
                Kami sedang mempersiapkan dasbor kalkulasi data, grafik interaktif, dan mesin kecerdasan buatan (AI) untuk membantu pembacaan data toko Anda secara otomatis. Fitur ini akan segera tersedia.
            </p>

            <!-- Dekorasi Garis Batas Lembut -->
            <div class="my-8 border-t border-dashed border-purple-200"></div>

            <!-- Pesan Estimasi / Status Tambahan -->
            <div class="inline-flex items-center gap-2 rounded-full bg-purple-50 px-4 py-1.5 text-xs font-semibold text-purple-800">
                <span class="flex h-2 w-2 rounded-full bg-purple-500 animate-ping"></span>
                Tahap Pemodelan Algoritma & UI Eksklusif
            </div>
        </div>

    </div>
</div>

<!-- SCRIPT UNTUK MEMASTIKAN HISTORI KEMBALI AKURAT -->
<script>
    document.getElementById('btnBackToTab').addEventListener('click', function(e) {
        e.preventDefault();
        if (document.referrer !== "") {
            window.history.back();
        } else {
            window.location.href = "{{ url('/') }}";
        }
    });
</script>
@endsection