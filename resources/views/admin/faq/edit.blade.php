@extends('admin.layouts.app')

@section('content')

<div class="mx-auto max-w-4xl">

    <h1 class="mb-8 text-3xl font-bold">
        Edit FAQ
    </h1>

    <form action="{{ route('faq.update', $faq->id) }}"
          method="POST"
          class="space-y-6 rounded-3xl bg-white p-10 shadow-xl">

        @csrf
        @method('PUT')

        <!-- Pertanyaan -->
        <div>

            <label class="mb-2 block font-bold text-gray-700">
                Pertanyaan
            </label>

            <input type="text"
                   name="question"
                   value="{{ $faq->question }}"
                   maxlength="255"
                   required
                   class="w-full rounded-2xl border px-5 py-4">

        </div>

        <!-- Jawaban -->
        <div>

            <label class="mb-2 block font-bold text-gray-700">
                Jawaban
            </label>

            <textarea name="answer"
                      rows="6"
                      required
                      class="w-full rounded-2xl border px-5 py-4">{{ $faq->answer }}</textarea>

        </div>

        <button type="submit"
                class="rounded-2xl bg-red-600 px-8 py-4 font-bold text-white hover:bg-red-700">

            Update FAQ

        </button>

    </form>

</div>

@endsection