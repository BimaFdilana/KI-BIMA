@props(['active'])

@php
$classes = $active ?? false
? 'flex items-center rounded-lg p-2 bg-red-50 text-red-600 font-medium'
: 'flex items-center rounded-lg p-2 text-gray-600 transition hover:bg-red-50 hover:text-red-600';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <div class="mr-2 h-1.5 w-1.5 rounded-full bg-red-600"></div>
    {{ $slot }}
</a>