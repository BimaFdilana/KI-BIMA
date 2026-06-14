{{-- resources/views/components/dropdown-item.blade.php --}}
@props(['active' => false])
@php
    // Mengambil ID dari atribut href, misal dari "#section1" menjadi "section1"
    $id = ltrim($attributes->get('href'), '#');
@endphp

<a {{ $attributes->merge(['class' => 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-red-600']) }}
    :class="{ 'text-red-600 bg-red-50 font-bold': activeSection === '{{ $id }}' }"
    @click.prevent="
        const targetEl = document.getElementById('{{ $id }}');
        if (targetEl) {
            targetEl.scrollIntoView({
                behavior: 'smooth', // Animasi scroll halus
                block: 'center'     // Posisikan elemen di tengah layar secara vertikal
            });
        }
        activeSection = '{{ $id }}'; // Tetap update state aktif
        open = false; // Tutup dropdown setelah diklik
   ">
    {{ $slot }}
</a>
