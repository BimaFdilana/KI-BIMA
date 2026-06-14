<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Panel</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

</head>

<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-72 bg-red-600 text-white shadow-2xl">

        <!-- Header -->
        <div class="border-b border-red-500 p-6">

            <h1 class="text-3xl font-black tracking-wide text-white">

                ADMIN PANEL

            </h1>

            <p class="mt-2 text-sm text-red-100">

                Kelola Website Anda

            </p>

        </div>

        <!-- Menu -->
        <div class="p-4">

            <ul class="space-y-3">

                <!-- Artikel -->
                <li>

                    <a href="{{ route('artikel.index') }}"
                       class="flex items-center gap-4 rounded-2xl px-5 py-4 font-semibold text-white transition hover:bg-white hover:text-red-600">

                        <i class="fas fa-newspaper text-lg"></i>

                        <span>Artikel</span>

                    </a>

                </li>

                <!-- Pesan -->
                <li>

                    <a href="{{ route('pesan.index') }}"
                       class="flex items-center gap-4 rounded-2xl px-5 py-4 font-semibold text-white transition hover:bg-white hover:text-red-600">

                        <i class="fas fa-envelope text-lg"></i>

                        <span>Pesan</span>

                    </a>

                </li>

                <!-- Produk -->
                <li>

                    <a href="{{ route('product.index') }}"
                       class="flex items-center gap-4 rounded-2xl px-5 py-4 font-semibold text-white transition hover:bg-white hover:text-red-600">

                        <i class="fas fa-box-open text-lg"></i>

                        <span>Produk</span>

                    </a>

                </li>

                <!-- FAQ -->
                <li>

                    <a href="{{ route('faq.index') }}"
                       class="flex items-center gap-4 rounded-2xl px-5 py-4 font-semibold text-white transition hover:bg-white hover:text-red-600">

                        <i class="fas fa-circle-question text-lg"></i>

                        <span>FAQ</span>

                    </a>

                </li>

                <!-- Laporan -->
<li>

<a href="{{ route('laporan.index') }}"
   class="flex items-center gap-4 rounded-2xl px-5 py-4 font-semibold text-white transition hover:bg-white hover:text-red-600">

    <i class="fas fa-chart-line text-lg"></i>

    <span>Laporan</span>

</a>

</li>

            </ul>

        </div>

        <!-- Bottom -->
        <div class="mt-10 px-4">

            <div class="rounded-3xl bg-red-500 p-5 shadow-lg">

                <div class="flex items-center gap-4">

                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-red-600">

                        <i class="fas fa-user-shield text-xl"></i>

                    </div>

                    <div>

                        <h3 class="font-bold text-white">

                            Administrator

                        </h3>

                        <p class="text-sm text-red-100">

                            Full Access

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </aside>

    <!-- Main -->
    <main class="flex-1 p-10">

        <!-- Navbar -->
        <div
            class="mb-8 flex items-center justify-between rounded-3xl bg-white px-8 py-5 shadow-lg">

            <div>

                <h2 class="text-2xl font-bold text-gray-800">

                    Dashboard Admin

                </h2>

                <p class="mt-1 text-gray-500">

                    Selamat datang kembali 

                </p>

            </div>

            <!-- Logout -->
            <form method="POST"
                  action="{{ route('logout') }}">

                @csrf

                <button type="submit"
                        class="rounded-2xl bg-red-600 px-6 py-3 font-semibold text-white transition hover:bg-red-700">

                    <i class="fas fa-right-from-bracket mr-2"></i>

                    Logout

                </button>

            </form>

        </div>

        <!-- Content -->
        @yield('content')

    </main>

</div>

</body>
</html>