@extends('admin.layouts.app')

@section('content')

<div class="mx-auto max-w-5xl">

    <!-- Header -->
    <div class="mb-8">

        <h1 class="text-3xl font-bold text-gray-800">

            Edit Perlengkapan Fisik

        </h1>

        <p class="mt-2 text-gray-500">

            Update data produk perangkat fisik

        </p>

    </div>

    <!-- Card -->
    <div class="overflow-hidden rounded-3xl bg-white shadow-xl">

        <!-- Top -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-10 py-8 text-white">

            <h2 class="text-2xl font-bold">

                Form Edit Produk

            </h2>

        </div>

        <!-- Form -->
        <form action="{{ route('perlengkapan-fisik.update', $item->id) }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-8 p-10">

            @csrf
            @method('PUT')

            <!-- Nama -->
            <div>

                <label class="mb-3 block font-bold text-gray-700">

                    Nama Produk

                </label>

                <input type="text"
                       name="nama"
                       value="{{ $item->nama }}"
                       class="w-full rounded-2xl border border-gray-300 px-5 py-4 focus:border-red-500 focus:ring-4 focus:ring-red-100">

            </div>

            <!-- Deskripsi -->
            <div>

                <label class="mb-3 block font-bold text-gray-700">

                    Deskripsi

                </label>

                <textarea name="deskripsi"
                          rows="5"
                          class="w-full rounded-2xl border border-gray-300 px-5 py-4 focus:border-red-500 focus:ring-4 focus:ring-red-100">{{ $item->deskripsi }}</textarea>

            </div>

            <!-- Badge -->
            <div>

                <label class="mb-3 block font-bold text-gray-700">

                    Badge

                </label>

                <select name="badge"
                        class="w-full rounded-2xl border border-gray-300 px-5 py-4 focus:border-red-500 focus:ring-4 focus:ring-red-100">

                    <option value="">-- Pilih Badge --</option>

                    <option value="POPULER"
                        {{ $item->badge == 'POPULER' ? 'selected' : '' }}>

                        POPULER

                    </option>

                    <option value="PREMIUM"
                        {{ $item->badge == 'PREMIUM' ? 'selected' : '' }}>

                        PREMIUM

                    </option>

                </select>

            </div>

            <!-- Gambar -->
            <div>

                <label class="mb-3 block font-bold text-gray-700">

                    Gambar Produk

                </label>

                <img src="{{ asset('storage/' . $item->gambar) }}"
                     class="mb-4 h-32 w-32 rounded-2xl object-cover">

                <input type="file"
                       name="gambar"
                       class="w-full rounded-2xl border border-gray-300 p-4">

            </div>

            <!-- Fitur -->
            <div>

                <label class="mb-3 block font-bold text-gray-700">

                    Fitur Produk

                </label>

                <div id="fitur-container" class="space-y-4">

                    @foreach($item->fitur as $fitur)

                    <div class="fitur-item flex items-center gap-3">

                        <input type="text"
                               name="fitur[]"
                               value="{{ $fitur }}"
                               class="w-full rounded-2xl border border-gray-300 px-5 py-4">

                        <button type="button"
                                onclick="hapusFitur(this)"
                                class="rounded-xl bg-red-100 px-4 py-4 font-bold text-red-600">

                            ✕

                        </button>

                    </div>

                    @endforeach

                </div>

                <!-- Tombol -->
                <button type="button"
                        onclick="tambahFitur()"
                        class="mt-5 rounded-2xl bg-red-600 px-5 py-3 font-semibold text-white">

                    + Tambah Fitur

                </button>

            </div>

            <!-- Button -->
            <div class="flex justify-end gap-4 border-t pt-8">

                <a href="{{ route('perlengkapan-fisik.index') }}"
                   class="rounded-2xl border border-gray-300 px-6 py-3 font-semibold text-gray-700">

                    Batal

                </a>

                <button type="submit"
                        class="rounded-2xl bg-gradient-to-r from-red-600 to-red-700 px-8 py-3 font-bold text-white">

                    Update Produk

                </button>

            </div>

        </form>

    </div>

</div>

<script>

function tambahFitur() {

    let container = document.getElementById('fitur-container');

    let div = document.createElement('div');

    div.classList.add('fitur-item', 'flex', 'items-center', 'gap-3');

    div.innerHTML = `
    
        <input type="text"
               name="fitur[]"
               class="w-full rounded-2xl border border-gray-300 px-5 py-4">

        <button type="button"
                onclick="hapusFitur(this)"
                class="rounded-xl bg-red-100 px-4 py-4 font-bold text-red-600">

            ✕

        </button>

    `;

    container.appendChild(div);
}

function hapusFitur(button) {

    let fiturItems = document.querySelectorAll('.fitur-item');

    if (fiturItems.length > 1) {

        button.parentElement.remove();

    } else {

        alert('Minimal harus ada 1 fitur');

    }
}

</script>

@endsection