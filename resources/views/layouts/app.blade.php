<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://kit-pro.fontawesome.com/releases/v5.12.1/css/pro.min.css" />
    <script src="https://kit.fontawesome.com/0cfbed0a9a.js" crossorigin="anonymous"></script>
    <link rel="icon" href="{{ Storage::url('/assets_images/logo/logo-ki-putih.svg') }}">
    <title>
        @yield('page_title')
        @if (isset($sub_title))
            | {{ $sub_title }}
        @else
            | {{ config('app.name', 'Laravel') }}
        @endif
    </title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />

    <!-- Flowbite CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.css" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Flowbite JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
</head>

<body class="font-sans antialiased">

    <!-- Page Heading -->
    @if (isset($header))
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <!-- Page Content -->
    <!-- <main>
        {{ $slot }}
    </main> -->
    <main>
    @yield('content')

    <x-guest.download-footer />
    <x-guest.footer-section />

    <livewire:floatingMenu />
</main>

    @livewireScripts

</body>

</html>
