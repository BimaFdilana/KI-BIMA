<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://kit-pro.fontawesome.com/releases/v5.12.1/css/pro.min.css" />
    <script src="https://kit.fontawesome.com/0cfbed0a9a.js" crossorigin="anonymous"></script>

    <style>
        @font-face {
            font-family: 'Heading Now';
            src: url('/fonts/heading_now/HeadingNowTrial-56Bold.ttf') format('truetype');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }
    </style>

    <title>@yield('title', 'Kedai Indonesia')</title>
    <link rel="icon" href="{{ Storage::url('/assets_images/logo/logo-ki-putih.svg') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Responsive CSS -->
    <link href="https://cdn.jsdelivr.net/npm/keen-slider@6.8.6/keen-slider.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.0.0/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.0.0/dist/js/tom-select.complete.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/keen-slider@6.8.6/keen-slider.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body id="bodyS" class="goto-here">

    <x-guest.navbar-guest />
    <x-validation-errors class="mb-4" />
    <!-- Page Heading -->
    @if (isset($header))
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <!-- Page Content -->
    <main class="bg-gray-100">
        @yield('content')
        <livewire:floatingMenu />
    </main>
    @stack('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/datepicker.min.js"></script>
    @livewireScripts

</body>

</html>
