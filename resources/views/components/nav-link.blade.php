{{-- resources/views/components/nav-link.blade.php --}}
@props(['active' => false])
@php
    $classes = 'nav-link transition-all duration-500' . ($active ? ' active' : '');
@endphp
<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
