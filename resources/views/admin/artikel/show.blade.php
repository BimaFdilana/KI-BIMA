@extends('admin.layouts.app')

@section('content')

<div class="mx-auto max-w-4xl space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">

        <h1 class="text-3xl font-bold text-gray-800">
            Detail Artikel
        </h1>

        <a href="{{ route('artikel.index') }}"
           class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700">

            Kembali

        </a>

    </div>

    <!-- Card -->
    <div class="overflow-hidden rounded-2xl bg-white shadow-lg">

        @if($artikel->gambar)
        <img src="{{ asset('storage/' . $artikel->gambar) }}"
             class="h-64 w-full object-cover">
        @endif

        <div class="p-6 space-y-4">

            <h2 class="text-2xl font-bold text-gray-800">
                {{ $artikel->judul }}
            </h2>

            <p class="text-gray-500">
                {{ $artikel->deskripsi_singkat }}
            </p>

            <div class="prose max-w-none text-gray-700">
                {!! nl2br(e($artikel->isi)) !!}
            </div>

        </div>

    </div>

</div>

@endsection