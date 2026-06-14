{{-- 
    Reusable Loading Button Component
    Usage: @include('livewire.admin.components.loading-button', [
        'action' => 'verifyKtp',
        'text' => 'Verifikasi',
        'loadingText' => 'Memproses...',
        'icon' => '✓',
        'class' => 'bg-green-600 hover:bg-green-700'
    ])
--}}

<button wire:click="{{ $action }}" wire:loading.attr="disabled" wire:target="{{ $action }}"
    class="{{ $class ?? 'bg-blue-600 hover:bg-blue-700' }} text-white font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed {{ $extraClass ?? '' }}">
    @if (isset($icon) && !isset($text))
        {{-- Icon-only button: show spinner on loading --}}
        <span wire:loading.remove wire:target="{{ $action }}">
            <i class="{{ $icon }}"></i>
        </span>
        <span wire:loading wire:target="{{ $action }}">
            <i class="fas fa-spinner fa-spin"></i>
        </span>
    @else
        {{-- Text button: show loading text --}}
        <span wire:loading.remove wire:target="{{ $action }}">
            @if (isset($icon))
                {{ $icon }}
            @endif{{ $text ?? 'Submit' }}
        </span>
        <span wire:loading wire:target="{{ $action }}">
            <i class="fas fa-spinner fa-spin mr-1"></i>{{ $loadingText ?? 'Loading...' }}
        </span>
    @endif
</button>
