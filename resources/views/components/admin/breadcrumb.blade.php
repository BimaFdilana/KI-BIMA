@props(['items'])

<nav class="flex items-center gap-2 text-sm mb-6" aria-label="Breadcrumb">
    @foreach ($items as $item)
        @if (!$loop->first)
            <span class="text-gray-300">/</span>
        @endif

        @if (isset($item['url']) && $item['url'])
            <a href="{{ $item['url'] }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                @if ($loop->first)
                    <i class="fas fa-home mr-1"></i>
                @endif
                {{ $item['label'] }}
            </a>
        @else
            <span class="text-gray-900 font-medium">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
