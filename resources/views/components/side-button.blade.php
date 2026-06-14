@props(['active', 'icon', 'text', 'dropdown' => false])
@php
    $baseClasses = 'group flex items-center justify-between rounded-lg p-2.5 transition-all';
    $activeClasses = $active ?? false ? $baseClasses . ' bg-red-600 text-white hover:bg-red-700' : $baseClasses . ' hover:bg-red-50';

    $iconClasses = $active ?? false ? 'flex h-8 w-8 items-center justify-center rounded-lg bg-red-700 text-white' : 'flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600 group-hover:bg-red-100 group-hover:text-red-700';
@endphp
<button {{ $attributes->merge(['class' => $activeClasses]) }} x-bind:class="sidebarOpen ? 'w-full' : 'w-auto'">
    <div class="flex items-center">
        <div class="{{ $iconClasses }}">
            <i class="{{ $icon }}"></i>
        </div>
        <span class="ml-3 truncate" x-show="sidebarOpen" x-transition>{{ $text }}</span>
    </div>
    @if ($dropdown)
        <svg class="h-4 w-4 transform transition-transform duration-300" :class="openDropdown === {{ $dropdown }} ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="sidebarOpen">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    @endif
    <span x-show="!sidebarOpen" x-transition class="absolute left-full ml-6 w-auto min-w-max origin-left scale-0 rounded-md bg-gray-800 px-2 py-1 text-xs font-medium text-white shadow-md transition-all group-hover:scale-100">
        {{ $text }}
    </span>
</button>
