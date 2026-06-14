@extends('admin.layouts.app')

@section('content')

<div class="space-y-6">

```
<!-- Header -->
<div class="flex items-center justify-between">

    <h1 class="text-3xl font-bold text-gray-800">
        Edit Laporan
    </h1>

    <a href="{{ route('laporan.index') }}"
       class="rounded-xl bg-gray-200 px-5 py-2.5 font-semibold text-gray-700 transition hover:bg-gray-300">

        ← Kembali

    </a>

</div>

<!-- Card -->
<div class="overflow-hidden rounded-2xl bg-white shadow-lg">

    <!-- Card Header -->
    <div class="border-b border-gray-100 bg-red-600 px-6 py-4">

        <h2 class="text-lg font-semibold text-white">
            Form Edit Laporan
        </h2>

    </div>

    <!-- Form -->
    <form
        action="{{ route('laporan.update',$laporan->id) }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-6 p-6">

        @csrf
        @method('PUT')

        <!-- Judul -->
        <div>

            <label class="mb-2 block font-semibold text-gray-700">
                Judul Laporan
            </label>

            <input
                type="text"
                name="judul"
                value="{{ old('judul', $laporan->judul) }}"
                class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200"
                required>

            @error('judul')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror

        </div>

        <!-- Deskripsi -->
        <div>

            <label class="mb-2 block font-semibold text-gray-700">
                Deskripsi
            </label>

            <textarea
                name="deskripsi"
                rows="6"
                class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200"
                required>{{ old('deskripsi', $laporan->deskripsi) }}</textarea>

            @error('deskripsi')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror

        </div>

        <!-- Gambar Saat Ini -->
        @if($laporan->gambar)

        <div>

            <label class="mb-3 block font-semibold text-gray-700">
                Gambar Saat Ini
            </label>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-gray-50 p-4">

                <img
                    src="{{ asset('storage/'.$laporan->gambar) }}"
                    alt="{{ $laporan->judul }}"
                    class="max-h-64 rounded-lg shadow-md">

            </div>

        </div>

        @endif

        <!-- Upload Gambar Baru -->
        <div>

            <label class="mb-2 block font-semibold text-gray-700">
                Ganti Gambar (Opsional)
            </label>

            <input
                type="file"
                name="gambar"
                accept="image/*"
                class="block w-full rounded-xl border border-gray-300 p-3 text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-red-600 file:px-4 file:py-2 file:font-semibold file:text-white hover:file:bg-red-700">

            <p class="mt-2 text-sm text-gray-500">
                Kosongkan jika tidak ingin mengganti gambar.
            </p>

            @error('gambar')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror

        </div>

        <!-- Button -->
        <div class="flex gap-3 pt-4">

            <button
                type="submit"
                class="rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-6 py-3 font-semibold text-white shadow-md transition hover:scale-105 hover:shadow-lg">

                Update Laporan

            </button>

            <a href="{{ route('laporan.index') }}"
               class="rounded-xl bg-gray-200 px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-300">

                Batal

            </a>

        </div>

    </form>

</div>
```

</div>

@endsection
