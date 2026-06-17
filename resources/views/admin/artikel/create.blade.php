@extends('layouts.admin')

@section('content')

<h1 class="mb-5 text-3xl font-bold">
    Tambah Artikel
</h1>

<form action="{{ route('artikel.store') }}"
      method="POST"
      enctype="multipart/form-data"
      class="space-y-5 bg-white p-5">

    @csrf

    <input type="text"
           name="judul"
           placeholder="Judul"
           class="w-full border p-3">

    <textarea name="deskripsi_singkat"
              placeholder="Deskripsi Singkat"
              class="w-full border p-3"></textarea>

    <textarea name="isi"
              rows="10"
              placeholder="Isi Artikel"
              class="w-full border p-3"></textarea>

    <!-- Tanggal & Jam Publish -->
    <div>
        <label class="mb-2 block font-semibold">
            Tanggal & Jam Publish
        </label>

        <input type="datetime-local"
               name="published_at"
               class="w-full border p-3">
    </div>

    <input type="file"
           name="gambar">

    <button class="rounded bg-red-600 px-5 py-3 text-white">
        Simpan
    </button>

</form>

@endsection