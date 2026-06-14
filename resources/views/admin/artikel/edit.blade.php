@extends('admin.layouts.app')

@section('content')

<div class="mx-auto max-w-3xl space-y-6">

    <!-- Header -->
    <div>

        <h1 class="text-3xl font-bold text-gray-800">
            Edit Artikel
        </h1>

        <p class="mt-2 text-gray-500">
            Perbarui data artikel yang sudah ada
        </p>

    </div>

    <!-- Form Card -->
    <div class="rounded-2xl bg-white p-6 shadow-lg">

        <form action="{{ route('artikel.update', $artikel->id) }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-5">

            @csrf
            @method('PUT')

            <!-- Judul -->
            <div>

                <label class="mb-2 block font-semibold text-gray-700">
                    Judul
                </label>

                <input type="text"
                       name="judul"
                       value="{{ $artikel->judul }}"
                       class="w-full rounded-xl border border-gray-300 p-3 focus:border-red-500 focus:outline-none">

            </div>

            <!-- Deskripsi Singkat -->
            <div>

                <label class="mb-2 block font-semibold text-gray-700">
                    Deskripsi Singkat
                </label>

                <textarea name="deskripsi_singkat"
                          class="w-full rounded-xl border border-gray-300 p-3 focus:border-red-500 focus:outline-none">{{ $artikel->deskripsi_singkat }}</textarea>

            </div>

            <!-- Isi Artikel -->
            <div>

                <label class="mb-2 block font-semibold text-gray-700">
                    Isi Artikel
                </label>

                <textarea name="isi"
                          rows="10"
                          class="w-full rounded-xl border border-gray-300 p-3 focus:border-red-500 focus:outline-none">{{ $artikel->isi }}</textarea>

            </div>

            <!-- Gambar -->
            <div>

                <label class="mb-2 block font-semibold text-gray-700">
                    Gambar Artikel
                </label>

                @if($artikel->gambar)

                    <img src="{{ asset('storage/' . $artikel->gambar) }}"
                         class="mb-3 h-32 w-32 rounded-xl object-cover">

                @endif

                <input type="file"
                       name="gambar"
                       class="w-full">

                <p class="mt-2 text-sm text-gray-500">
                    Kosongkan jika tidak ingin mengganti gambar
                </p>

            </div>

            <!-- Button -->
            <div class="flex items-center justify-end gap-3 border-t pt-5">

                <a href="{{ route('artikel.index') }}"
                   class="rounded-xl border border-gray-300 px-5 py-2 font-semibold text-gray-700 hover:bg-gray-100">

                    Batal

                </a>

                <button type="submit"
                        class="rounded-xl bg-red-600 px-5 py-2 font-semibold text-white shadow hover:bg-red-700">

                    Update Artikel

                </button>

            </div>

        </form>

    </div>

</div>

@endsection