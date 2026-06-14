{{-- resources/views/components/nav-dropdown.blade.php --}}
@props(['active' => false])
@php
    $classes = 'nav-link transition-all duration-500 flex items-center' . ($active ? ' active' : '');
@endphp

{{-- Tambahkan x-data dan x-init di sini --}}
<div class="relative" x-data="{
    open: false,
    activeSection: '{{ ltrim(request()->getUri(), url('/')) }}' === '' ? 'section1' : '' // Default aktif section 1 di homepage
}" x-init="() => {
    // Hanya jalankan observer jika ada link anchor di dropdown
    const hasAnchorLinks = $el.querySelector('a[href^=\'#\']');
    if (!hasAnchorLinks) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                activeSection = entry.target.getAttribute('id');
            }
        });
    }, {
        rootMargin: '-50% 0px -50% 0px' // Anggap aktif saat section berada di tengah layar
    });

    // Ambil semua section yang akan di-observe
    document.querySelectorAll('section[id]').forEach((section) => {
        observer.observe(section);
    });
}" @click.away="open = false">
    <button @click="open = !open" class="{{ $classes }}">
        {{ $trigger }}
        <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4 transition-transform duration-200"
            :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute left-0 mt-2 w-max rounded-md bg-white py-2 shadow-lg ring-1 ring-black ring-opacity-5 z-50"
        style="display: none;">
        {{ $content }}
    </div>
</div>
