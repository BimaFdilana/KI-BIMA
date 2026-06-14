{{-- resources/views/components/detail-card.blade.php --}}
@props(['id', 'icon', 'iconColor' => 'gray', 'title', 'defaultValue' => '-', 'highlighted' => false, 'isBadge' => false])

@php
    $cardClass = $highlighted ? 'rounded-xl border-2 border-red-200 bg-gradient-to-r from-red-50 to-red-100 p-6 transition-all duration-300 hover:shadow-lg card-hover' : 'rounded-xl border border-gray-200 bg-white p-6 transition-all duration-300 hover:shadow-lg card-hover';

    $iconBgClass = "rounded-lg bg-{$iconColor}-100 p-2";
    $iconTextClass = "text-{$iconColor}-600";
    $valueClass = $highlighted ? "text-2xl font-bold text-{$iconColor}-700" : "text-2xl font-bold text-{$iconColor}-600";
    $titleClass = $highlighted ? "text-sm font-semibold uppercase tracking-wide text-{$iconColor}-700" : 'text-sm font-semibold uppercase tracking-wide text-gray-600';
@endphp

<div class="{{ $cardClass }}">
    <div class="mb-3 flex items-center justify-between">
        <div class="{{ $iconBgClass }}">
            <i class="{{ $icon }} {{ $iconTextClass }}"></i>
        </div>
        @if ($isBadge)
            <span id="{{ $id }}" class="rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800">
                {{ $defaultValue }}
            </span>
        @else
            <span id="{{ $id }}" class="{{ $valueClass }}">
                {{ $defaultValue }}
            </span>
        @endif
    </div>
    <h3 class="{{ $titleClass }}">{{ $title }}</h3>
</div>
