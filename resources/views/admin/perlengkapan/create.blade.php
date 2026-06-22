@extends('layouts.admin')

@section('content')

<div class="mx-auto max-w-5xl">

    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">

        <div>

            <h1 class="text-3xl font-bold text-gray-800">

                Tambah Perlengkapan Fisik

            </h1>

            <p class="mt-2 text-gray-500">

                Tambahkan produk perangkat fisik baru ke website Anda

            </p>

        </div>

        <!-- <a href="{{ route('perlengkapan-fisik.index') }}"
           class="rounded-xl border border-gray-300 bg-white px-5 py-3 font-semibold text-gray-700 shadow-sm transition hover:bg-gray-100">

            ← Kembali

        </a> -->

    </div>

    <!-- Card -->
    <div class="overflow-hidden rounded-3xl bg-white shadow-xl">

        <!-- Top -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-10 py-8 text-white">

            <h2 class="text-2xl font-bold">

                Form Tambah Produk

            </h2>

            <p class="mt-2 text-red-100">

                Isi data produk dengan lengkap dan benar

            </p>

        </div>

        <!-- Form -->
        <form action="{{ route('perlengkapan-fisik.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-8 p-10">

            @csrf

            <!-- Nama -->
            <div>

                <label class="mb-3 block text-sm font-bold text-gray-700">

                    Nama Produk

                </label>

                <input type="text"
                       name="nama"
                       placeholder="Masukkan nama produk"
                       class="w-full rounded-2xl border border-gray-300 px-5 py-4 text-gray-700 outline-none transition focus:border-red-500 focus:ring-4 focus:ring-red-100">

            </div>

            <!-- Deskripsi -->
            <div>

                <label class="mb-3 block text-sm font-bold text-gray-700">

                    Deskripsi Produk

                </label>

                <textarea name="deskripsi"
                          rows="5"
                          placeholder="Masukkan deskripsi produk"
                          class="w-full rounded-2xl border border-gray-300 px-5 py-4 text-gray-700 outline-none transition focus:border-red-500 focus:ring-4 focus:ring-red-100"></textarea>

            </div>

            <!-- Badge -->
<div>

    <label class="mb-3 block text-sm font-bold text-gray-700">

        Badge Produk

    </label>

    <select name="badge"
            class="w-full rounded-2xl border border-gray-300 bg-white px-5 py-4 text-gray-700 outline-none transition focus:border-red-500 focus:ring-4 focus:ring-red-100">

        <option value="">

            -- Pilih Badge Produk --

        </option>

        <option value="POPULER">

            POPULER

        </option>

        <option value="PREMIUM">

            PREMIUM

        </option>

    </select>

</div>

            <!-- Upload -->
            <div>

                <label class="mb-3 block text-sm font-bold text-gray-700">

                    Gambar Produk

                </label>

                <div class="rounded-2xl border-2 border-dashed border-red-200 bg-red-50 p-8 text-center">

                    <input type="file"
                           name="gambar"
                           class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-xl file:border-0 file:bg-red-600 file:px-5 file:py-3 file:font-semibold file:text-white hover:file:bg-red-700">

                    <p class="mt-3 text-sm text-gray-500">

                        Upload gambar produk dengan kualitas terbaik

                    </p>

                </div>

            </div>

            <!-- Fitur -->
            <div>

                <div class="mb-5">

                    <h3 class="text-xl font-bold text-gray-800">

                        Fitur Produk

                    </h3>

                    <p class="text-sm text-gray-500">

                        Tambahkan keunggulan produk

                    </p>

                </div>

                <!-- Container -->
                <div id="fitur-container" class="space-y-4">

                    <!-- Default 1 Input -->
                    <div class="fitur-item flex items-center gap-3">

                        <input type="text"
                               name="fitur[]"
                               placeholder="Masukkan fitur produk"
                               class="w-full rounded-2xl border border-gray-300 px-5 py-4 outline-none transition focus:border-red-500 focus:ring-4 focus:ring-red-100">

                        <button type="button"
                                onclick="hapusFitur(this)"
                                class="rounded-xl bg-red-100 px-4 py-4 font-bold text-red-600 transition hover:bg-red-200">

                            ✕

                        </button>

                    </div>

                </div>

                <!-- Tombol Tambah -->
                <div class="mt-5">

                    <button type="button"
                            onclick="tambahFitur()"
                            class="flex items-center gap-2 rounded-2xl bg-red-600 px-5 py-3 font-semibold text-white shadow-lg transition hover:scale-105 hover:bg-red-700">

                        <span class="text-xl">＋</span>

                        Tambah Fitur

                    </button>

                </div>

            </div>

            <!-- Button -->
            <div class="flex items-center justify-end gap-4 border-t pt-8">

                <a href="{{ route('perlengkapan-fisik.index') }}"
                   class="rounded-2xl border border-gray-300 px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-100">

                    Batal

                </a>

                <button type="submit"
                        class="rounded-2xl bg-gradient-to-r from-red-600 to-red-700 px-8 py-3 font-bold text-white shadow-lg transition hover:scale-105 hover:shadow-red-200">

                    Simpan Produk

                </button>

            </div>

        </form>

    </div>

</div>

<!-- Script -->
<script>

    function tambahFitur() {

        let container = document.getElementById('fitur-container');

        let div = document.createElement('div');

        div.classList.add('fitur-item', 'flex', 'items-center', 'gap-3');

        div.innerHTML = `
        
            <input type="text"
                   name="fitur[]"
                   placeholder="Masukkan fitur produk"
                   class="w-full rounded-2xl border border-gray-300 px-5 py-4 outline-none transition focus:border-red-500 focus:ring-4 focus:ring-red-100">

            <button type="button"
                    onclick="hapusFitur(this)"
                    class="rounded-xl bg-red-100 px-4 py-4 font-bold text-red-600 transition hover:bg-red-200">

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