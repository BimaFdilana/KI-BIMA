@props(['user', 'size' => 'md'])

@php
    $sizes = [
        '10' => 'w-10 h-10',
        'xs' => 'w-6 h-6',
        'sm' => 'w-8 h-8',
        'md' => 'w-12 h-12',
        'lg' => 'w-16 h-16',
        'xl' => 'w-24 h-24',
        '32' => 'w-32 h-32',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $fontSizes = [
        '10' => 'text-sm',
        'xs' => 'text-xs',
        'sm' => 'text-sm',
        'md' => 'text-md',
        'lg' => 'text-lg',
        'xl' => 'text-xl',
        '32' => 'text-3xl',
    ];
    $fontSize = $fontSizes[$size] ?? $fontSizes['md'];
@endphp

<div {{ $attributes->merge(['class' => "relative rounded-full overflow-hidden flex items-center justify-center $sizeClass"]) }} style="{{ $user->avatar_background_style }}">
    @if ($user && isset($user->profile_photo_path) && $user->profile_photo_path)
        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
    @else
        <div class="{{ $user->getAvatarGradientClass() }} {{ $sizeClass }} {{ $fontSize }} flex items-center justify-center rounded-full bg-white/20 font-bold text-white">
            {{ $user->initials }}
        </div>
    @endif
</div>
