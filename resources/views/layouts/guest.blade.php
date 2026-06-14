<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://kit-pro.fontawesome.com/releases/v5.12.1/css/pro.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-responsive-bs4/2.4.1/responsive.bootstrap4.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('datatables/datatables.css') }}">
    <link rel="stylesheet" href="{{ asset('datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('datatables/button-datatables.min.css') }}">

    <link rel="icon" href="{{ Storage::url('/assets_images/logo/logo-ki-putih.svg') }}">

    <title>
        @yield('page_title')
        @if (isset($sub_title) || View::hasSection('sub_title'))
            | @yield('sub_title')
        @else
            | Kedai Indonesia
        @endif
    </title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>

<body id="bodyS" class="goto-here">

    <x-validation-errors class="mb-4" />
    <!-- Page Heading -->
    @if (isset($header))
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <body id="bodyS" class="goto-here bg-gradient-to-br from-indigo-50 to-blue-50 text-black">
        {{-- Toast Container Component --}}
        <x-alert.toast position="center-top" :duration="5000" :max-toasts="5" />
        <div class="min-h-screen py-12 transition-all duration-300 ease-in-out">
            @if (isset($header) || View::hasSection('header'))
                <header class="bg-white shadow">
                    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        @yield('header')
                        <x-guest.navbar-guest />
                    </div>
                </header>
            @else
                <header class="">
                    <x-guest.navbar-guest />
                </header>
            @endif
            <!-- Page Content -->
            <main>
    @yield('content')

    <x-guest.download-footer />
    <x-guest.footer-section />

    <livewire:floatingMenu />
</main>
        </div>
        </div>

        {{-- External Scripts --}}
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://kit.fontawesome.com/0cfbed0a9a.js" crossorigin="anonymous"></script>
        <script src="https://unpkg.com/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>

        <!-- DataTables JS -->
        <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

        <!-- Flowbite -->
        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
        <!-- Preline -->
        <script src="https://cdn.jsdelivr.net/npm/preline"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Custom Scripts -->
        <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/export-data.js') }}"></script>

        <!-- CSRF Token Setup for Ajax -->
        <script>
            $(document).ready(function() {
                // Setup CSRF token for all AJAX requests
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Handle AJAX toast responses
                $(document).ajaxComplete(function(event, xhr, settings) {
                    if (xhr.responseJSON && xhr.responseJSON.toast) {
                        const toast = xhr.responseJSON.toast;
                        if (typeof showToast === 'function') {
                            showToast(toast.type, toast.message, toast.title);
                        }
                    }
                });

                // Initialize Preline after DOM is ready
                if (typeof HSStaticMethods !== 'undefined') {
                    HSStaticMethods.autoInit();
                }
            });
        </script>

        @livewireScripts
        @stack('scripts')
    </body>

</html>
