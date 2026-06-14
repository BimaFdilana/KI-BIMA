@extends('layouts.guest')

@section('title', 'FAQ')

@section('content')

<section class="relative overflow-hidden bg-gradient-to-br from-gray-50 via-white to-red-50 py-24">

    <!-- Background Decoration -->
    <div class="absolute -top-32 -left-32 h-96 w-96 rounded-full bg-red-200/20 blur-3xl"></div>

    <div class="absolute bottom-0 right-0 h-[30rem] w-[30rem] rounded-full bg-red-300/10 blur-3xl"></div>

    <div class="relative mx-auto max-w-6xl px-6">

        <!-- Heading -->
        <div class="mx-auto mb-16 max-w-3xl text-center">

            <span class="inline-flex items-center rounded-full border border-red-200 bg-red-100 px-5 py-2 text-sm font-semibold tracking-wide text-red-600 shadow-sm">

                <i class="fas fa-circle-question mr-2"></i>

                PUSAT BANTUAN

            </span>

            <h1 class="mt-6 text-4xl font-black leading-tight text-gray-900 md:text-6xl">

                Frequently Asked
                <span class="bg-gradient-to-r from-red-600 to-red-500 bg-clip-text text-transparent">
                    Questions
                </span>

            </h1>

            <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-gray-500">

                Temukan jawaban dari berbagai pertanyaan seputar layanan,
                produk, sistem, dan dukungan yang kami sediakan untuk membantu
                perkembangan bisnis Anda.

            </p>

        </div>

        <!-- FAQ Container -->
        <div class="mx-auto max-w-4xl space-y-5">

            @foreach($faqs as $faq)

            <div
                class="faq-item overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-md transition-all duration-300 hover:shadow-2xl">

                <!-- Question -->
                <button
                    onclick="toggleFaq({{ $faq->id }})"
                    class="group flex w-full items-center justify-between px-8 py-6 text-left transition duration-300 hover:bg-red-50">

                    <div class="flex items-start gap-5">

                        <!-- Icon -->
                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-red-500 to-red-600 text-white shadow-lg transition duration-300 group-hover:scale-110">

                            <i class="fas fa-question text-lg"></i>

                        </div>

                        <!-- Question Text -->
                        <div>

                            <h2
                                class="text-lg font-bold leading-relaxed text-gray-800 transition duration-300 group-hover:text-red-600 md:text-xl">

                                {{ $faq->question }}

                            </h2>

                            <p class="mt-1 text-sm text-gray-400">

                                Klik untuk melihat jawaban

                            </p>

                        </div>

                    </div>

                    <!-- Arrow -->
                    <div
                        id="icon{{ $faq->id }}"
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-red-100 bg-red-50 text-red-600 transition-all duration-300">

                        <i class="fas fa-chevron-down text-sm transition-all duration-300"></i>

                    </div>

                </button>

                <!-- Answer -->
                <div
                    id="faq{{ $faq->id }}"
                    class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">

                    <div
                        class="border-t border-gray-100 bg-gradient-to-b from-gray-50 to-white px-8 py-7">

                        <div class="flex gap-5">

                            <!-- Answer Icon -->
                            <div
                                class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-600">

                                <i class="fas fa-check"></i>

                            </div>

                            <!-- Answer -->
                            <div>

                                <h3 class="mb-3 text-base font-bold text-gray-800">

                                    Jawaban

                                </h3>

                                <p class="leading-loose text-gray-600">

                                    {{ $faq->answer }}

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            @endforeach

        </div>

        <!-- Bottom Info -->
        <div
            class="mx-auto mt-20 max-w-4xl rounded-3xl border border-red-100 bg-white/80 p-10 text-center shadow-xl backdrop-blur-sm">

            <div
                class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-red-500 to-red-600 text-3xl text-white shadow-lg">

                <i class="fas fa-headset"></i>

            </div>

            <h2 class="text-3xl font-black text-gray-900">

                Masih Punya Pertanyaan?

            </h2>

            <p class="mx-auto mt-4 max-w-2xl text-lg leading-relaxed text-gray-500">

                Tim kami siap membantu Anda kapan saja.
                Hubungi customer support untuk mendapatkan bantuan lebih lanjut.

            </p>

            <div class="mt-8 flex flex-col justify-center gap-4 sm:flex-row">

                <a href="{{ route('hubungi-kami') }}"
                   class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-red-600 to-red-700 px-8 py-4 font-bold text-white shadow-lg transition duration-300 hover:-translate-y-1 hover:shadow-2xl">

                    <i class="fas fa-phone-alt mr-3"></i>

                    Hubungi Kami

                </a>

                <!-- <a href="#"
                   class="inline-flex items-center justify-center rounded-2xl border border-red-200 bg-white px-8 py-4 font-bold text-red-600 transition duration-300 hover:bg-red-50">

                    <i class="fas fa-envelope mr-3"></i>

                    Kirim Email

                </a> -->

            </div>

        </div>

    </div>

</section>

<script>

function toggleFaq(id)
{
    const currentContent =
        document.getElementById('faq' + id);

    const currentIcon =
        document.getElementById('icon' + id);

    const currentArrow =
        currentIcon.querySelector('i');

    // Close all FAQs
    document.querySelectorAll('.faq-content')
        .forEach((content) =>
    {
        if(content.id !== 'faq' + id)
        {
            content.style.maxHeight = null;
        }
    });

    // Reset all icons
    document.querySelectorAll('[id^="icon"]')
        .forEach((icon) =>
    {
        icon.classList.remove(
            'bg-red-600',
            'text-white',
            'rotate-180'
        );

        icon.classList.add(
            'bg-red-50',
            'text-red-600'
        );

        let arrow = icon.querySelector('i');

        arrow.classList.remove('fa-chevron-up');

        arrow.classList.add('fa-chevron-down');
    });

    // Toggle selected
    if(currentContent.style.maxHeight)
    {
        currentContent.style.maxHeight = null;

        currentIcon.classList.remove(
            'bg-red-600',
            'text-white',
            'rotate-180'
        );

        currentIcon.classList.add(
            'bg-red-50',
            'text-red-600'
        );

        currentArrow.classList.remove(
            'fa-chevron-up'
        );

        currentArrow.classList.add(
            'fa-chevron-down'
        );
    }

    else
    {
        currentContent.style.maxHeight =
            currentContent.scrollHeight + 'px';

        currentIcon.classList.remove(
            'bg-red-50',
            'text-red-600'
        );

        currentIcon.classList.add(
            'bg-red-600',
            'text-white',
            'rotate-180'
        );

        currentArrow.classList.remove(
            'fa-chevron-down'
        );

        currentArrow.classList.add(
            'fa-chevron-up'
        );
    }
}

</script>

@endsection