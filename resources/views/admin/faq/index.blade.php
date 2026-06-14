@extends('admin.layouts.app')

@section('content')

<div class="mx-auto max-w-6xl">

    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">

        <div>

            <h1 class="text-3xl font-bold text-gray-800">

                Data FAQ

            </h1>

            <p class="mt-2 text-gray-500">

                Kelola pertanyaan FAQ website

            </p>

        </div>

        <a href="{{ route('faq.create') }}"
           class="rounded-2xl bg-red-600 px-6 py-3 font-semibold text-white shadow-lg hover:bg-red-700">

            + Tambah FAQ

        </a>

    </div>

    <!-- Alert -->
    @if(session('success'))

    <div class="mb-6 rounded-2xl bg-green-100 px-6 py-4 text-green-700">

        {{ session('success') }}

    </div>

    @endif

    <!-- Table -->
    <div class="overflow-hidden rounded-3xl bg-white shadow-xl">

        <table class="min-w-full divide-y divide-gray-200">

            <thead class="bg-gray-100">

                <tr>

                    <th class="px-6 py-4 text-left">
                        Pertanyaan
                    </th>

                    <th class="px-6 py-4 text-left">
                        Jawaban
                    </th>

                    <th class="px-6 py-4 text-center">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y divide-gray-100">

                @forelse($faqs as $faq)

                <tr>

                    <td class="px-6 py-5 font-semibold text-gray-800">

                        {{ $faq->question }}

                    </td>

                    <td class="px-6 py-5 text-gray-600">

                        {{ Str::limit($faq->answer, 100) }}

                    </td>

                    <td class="px-6 py-5">

                        <div class="flex justify-center gap-3">

                            <!-- Edit -->
                            <a href="{{ route('faq.edit', $faq->id) }}"
                               class="rounded-xl bg-yellow-400 px-4 py-2 text-white hover:bg-yellow-500">

                                Edit

                            </a>

                            <!-- Delete -->
                            <form action="{{ route('faq.destroy', $faq->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin hapus FAQ ini?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="rounded-xl bg-red-600 px-4 py-2 text-white hover:bg-red-700">

                                    Hapus

                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="3"
                        class="px-6 py-10 text-center text-gray-500">

                        Belum ada FAQ

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection