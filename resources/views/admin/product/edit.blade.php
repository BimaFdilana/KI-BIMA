@extends('admin.layouts.app')

@section('content')

<div class="mx-auto max-w-5xl">

    <h1 class="mb-8 text-3xl font-bold">
        Edit Produk
    </h1>

    <form action="{{ route('product.update', $product->id) }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6 rounded-3xl bg-white p-10 shadow-xl">

        @csrf
        @method('PUT')

        <!-- Kategori -->
        <div>

            <label class="mb-2 block font-bold text-gray-700">
                Kategori
            </label>

            <select name="kategori"
                    id="kategori"
                    onchange="toggleFields()"
                    class="w-full rounded-2xl border px-5 py-4"
                    required>

                <option value="">
                    -- Pilih Kategori --
                </option>

                <option value="perlengkapan_fisik"
                    {{ $product->kategori == 'perlengkapan_fisik' ? 'selected' : '' }}>
                    Perlengkapan Fisik
                </option>

                <option value="plugin"
                    {{ $product->kategori == 'plugin' ? 'selected' : '' }}>
                    Plug In
                </option>

                <option value="ebook"
                    {{ $product->kategori == 'ebook' ? 'selected' : '' }}>
                    E-Book
                </option>

            </select>

        </div>

       <!-- Nama -->
<div>

    <label class="mb-2 block font-bold text-gray-700">
        Nama Produk
    </label>

    <input type="text"
           name="nama"
           value="{{ $product->nama }}"
           maxlength="35"
           oninput="updateCounter(this, 'nama-counter')"
           class="w-full rounded-2xl border px-5 py-4"
           required>

    <p id="nama-counter"
       class="mt-1 text-sm text-gray-500">
        {{ strlen($product->nama) }} / 35 karakter
    </p>

</div>

<!-- Subtitle -->
<div id="subtitle-field">

    <label class="mb-2 block font-bold text-gray-700">
        Subtitle
    </label>

    <input type="text"
           name="subtitle"
           value="{{ $product->subtitle }}"
           maxlength="60"
           oninput="updateCounter(this, 'subtitle-counter')"
           class="w-full rounded-2xl border px-5 py-4">

    <p id="subtitle-counter"
       class="mt-1 text-sm text-gray-500">
        {{ strlen($product->subtitle ?? '') }} / 60 karakter
    </p>

</div>

<!-- Deskripsi -->
<div>

    <label class="mb-2 block font-bold text-gray-700">
        Deskripsi
    </label>

    <textarea name="deskripsi"
              rows="5"
              maxlength="180"
              oninput="updateCounter(this, 'deskripsi-counter')"
              class="w-full rounded-2xl border px-5 py-4"
              required>{{ $product->deskripsi }}</textarea>

    <p id="deskripsi-counter"
       class="mt-1 text-sm text-gray-500">
        {{ strlen($product->deskripsi) }} / 180 karakter
    </p>

</div>

        <!-- Badge -->
        <div id="badge-field">

            <label class="mb-2 block font-bold text-gray-700">
                Badge
            </label>

            <select name="badge"
                    class="w-full rounded-2xl border px-5 py-4">

                <option value="">
                    -- Pilih Badge --
                </option>

                <option value="POPULER"
                    {{ $product->badge == 'POPULER' ? 'selected' : '' }}>
                    POPULER
                </option>

                <option value="PREMIUM"
                    {{ $product->badge == 'PREMIUM' ? 'selected' : '' }}>
                    PREMIUM
                </option>

                <option value="TERBARU"
                    {{ $product->badge == 'TERBARU' ? 'selected' : '' }}>
                    TERBARU
                </option>

            </select>

        </div>

        <!-- File Ebook -->
        <div id="file-ebook-field" class="hidden">

            <label class="mb-2 block font-bold text-gray-700">
                File E-Book (PDF)
            </label>

            @if($product->file_ebook)

                <a href="{{ asset('storage/' . $product->file_ebook) }}"
                   target="_blank"
                   class="mb-3 inline-block text-blue-600 underline">

                    Lihat File Ebook Lama

                </a>

            @endif

            <input type="file"
                   name="file_ebook"
                   accept=".pdf"
                   class="w-full rounded-2xl border px-5 py-4">

        </div>

        <!-- Link Download -->
        <div id="link-field" class="hidden">

            <label class="mb-2 block font-bold text-gray-700">
                Link Download / Demo
            </label>

            <input type="url"
                   name="link_download"
                   value="{{ $product->link_download }}"
                   placeholder="https://"
                   class="w-full rounded-2xl border px-5 py-4">

        </div>

        <!-- Gambar -->
        <div>

            <label class="mb-2 block font-bold text-gray-700">
                Gambar
            </label>

            @if($product->gambar)

                <img src="{{ asset('storage/' . $product->gambar) }}"
                     class="mb-4 h-40 w-40 rounded-2xl object-cover">

            @endif

            <input type="file"
                   name="gambar"
                   class="w-full rounded-2xl border px-5 py-4">

        </div>

        <!-- Fitur -->
        <div id="fitur-section">

            <label class="mb-2 block font-bold text-gray-700">
                Fitur Produk
            </label>

            <div id="fitur-container" class="space-y-3">

                @if($product->fitur)

                    @foreach($product->fitur as $fitur)

                    <div class="flex items-center gap-3 fitur-item">

                        <input type="text"
                               name="fitur[]"
                               value="{{ $fitur }}"
                               placeholder="Masukkan fitur"
                               class="w-full rounded-2xl border px-5 py-4">

                        <button type="button"
                                onclick="hapusFitur(this)"
                                class="rounded-xl bg-red-500 px-4 py-3 font-bold text-white hover:bg-red-600">

                            X

                        </button>

                    </div>

                    @endforeach

                @endif

            </div>

            <button type="button"
                    onclick="tambahFitur()"
                    class="mt-4 rounded-xl bg-red-600 px-5 py-3 text-white hover:bg-red-700">

                + Tambah Fitur

            </button>

        </div>

        <!-- Tombol -->
        <button type="submit"
                class="rounded-2xl bg-red-600 px-8 py-4 font-bold text-white hover:bg-red-700">

            Update Produk

        </button>

    </form>

</div>

<script>

function updateCounter(input, counterId)
{
    let counter = document.getElementById(counterId);

    counter.innerText =
        input.value.length +
        ' / ' +
        input.maxLength +
        ' karakter';
}

function tambahFitur()
{
    let container = document.getElementById('fitur-container');

    let div = document.createElement('div');

    div.className = 'flex items-center gap-3 fitur-item';

    div.innerHTML = `
        <input type="text"
               name="fitur[]"
               placeholder="Masukkan fitur"
               class="w-full rounded-2xl border px-5 py-4">

        <button type="button"
                onclick="hapusFitur(this)"
                class="rounded-xl bg-red-500 px-4 py-3 font-bold text-white hover:bg-red-600">

            X

        </button>
    `;

    container.appendChild(div);
}

function hapusFitur(button)
{
    button.parentElement.remove();
}

function toggleFields()
{
    let kategori = document.getElementById('kategori').value;

    let fiturSection = document.getElementById('fitur-section');
    let badgeField = document.getElementById('badge-field');
    let subtitleField = document.getElementById('subtitle-field');
    let fileEbookField = document.getElementById('file-ebook-field');
    let linkField = document.getElementById('link-field');

    fiturSection.classList.remove('hidden');
    badgeField.classList.remove('hidden');
    subtitleField.classList.remove('hidden');
    fileEbookField.classList.add('hidden');
    linkField.classList.add('hidden');

    // Ebook
    if (kategori === 'ebook')
    {
        fiturSection.classList.add('hidden');

        fileEbookField.classList.remove('hidden');
    }

    // Plugin
    else if (kategori === 'plugin')
    {
        linkField.classList.remove('hidden');
    }
}

window.onload = function()
{
    toggleFields();
};

</script>

@endsection