@props(['active', 'icon', 'text'])

@php
    $classes = $active ?? false ? 'group flex items-center rounded-lg p-2.5 shadow-sm hover:shadow-none transition-all bg-red-100 text-red-700' : 'group flex  items-center rounded-lg p-2.5 transition-all hover:bg-red-50';

    $iconClasses = $active ?? false ? 'flex h-8 w-8 items-center justify-center rounded-lg bg-red-600 text-white' : 'flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600 group-hover:bg-red-100 group-hover:text-red-700';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <div class="{{ $iconClasses }}">
        <i class="{{ $icon }}"></i>
    </div>
    <span class="ml-3 truncate" x-show="sidebarOpen" x-transition>{{ $text }}</span>
    <span x-show="!sidebarOpen" x-transition class="absolute left-full ml-6 w-auto min-w-max origin-left scale-0 rounded-md bg-gray-800 px-2 py-1 text-xs font-medium text-white shadow-md transition-all group-hover:scale-100">
        {{ $text }}
    </span>
</a>
