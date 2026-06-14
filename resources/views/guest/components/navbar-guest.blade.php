{{-- Navbar dengan animasi berdasarkan posisi scroll dan bannerCarousel --}}
<nav id="main-navbar" class="fixed top-0 z-50 w-full bg-red-700 transition-all duration-500 ease-in-out">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex flex-shrink-0 items-center">
                <img class="h-8 w-auto transition-all duration-500"
                    src="{{ url('/storage/assets_images/logo/logo-ki-putih.svg') }}" alt="Logo">
                
                <span class="ml-2 text-lg font-bold text-white transition-all duration-500">KEDAI INDONESIA</span>
            </div>
            
            <div class="hidden md:ml-6 md:flex md:items-center md:space-x-4">
                
                {{-- 
                <x-nav-dropdown :active="request()->routeIs('home*')" class="nav-link transition-all duration-500" id="services-dropdown">
                    <x-slot name="trigger">
                        Beranda
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-item href="#section1" :active="request()->routeIs('home')">
                            Apa Itu KEDAI INDONESIA
                        </x-dropdown-item>
                        <x-dropdown-item href="#section2" :active="request()->routeIs('home')">
                            Kenapa bersama KEDAI INDONESIA
                        </x-dropdown-item>
                        <x-dropdown-item href="#section3" :active="request()->routeIs('home')">
                            KEDAI INDONESIA & Kemandirian Ekonomi Daerah
                        </x-dropdown-item>
                    </x-slot>
                </x-nav-dropdown> 
                --}}
                <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')" class="nav-link transition-all duration-500">
                    Beranda
                </x-nav-link>


                {{-- 
                <x-nav-dropdown :active="request()->routeIs('homes*')" class="nav-link transition-all duration-500" id="services-dropdown">
                    <x-slot name="trigger">
                        Layanan
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-item href="{{ route('home') }}" :active="request()->routeIs('dashboard')">
                            Aplikasi POS
                        </x-dropdown-item>
                        <x-dropdown-item href="{{ route('home') }}" :active="request()->routeIs('dashboard')">
                            Distributor
                        </x-dropdown-item>
                        <x-dropdown-item href="{{ route('home') }}" :active="request()->routeIs('dashboard')">
                            Rantai Pasok
                        </x-dropdown-item>
                    </x-slot>
                </x-nav-dropdown> 
                --}}
                <!-- <x-nav-link href="#" :active="request()->routeIs('layanan')" class="nav-link transition-all duration-500">
                    Layanan
                </x-nav-link> -->


                <!-- <x-nav-link href="{{ route('produk') }}" :active="request()->routeIs('.produk')" class="nav-link transition-all duration-500">
                    Produk
                </x-nav-link> -->
                <x-nav-link href="{{ route('produk') }}"
    :active="request()->routeIs('produk')"
    class="nav-link transition-all duration-500">
    Produk
</x-nav-link>
                <!-- <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('dashboard')" class="nav-link transition-all duration-500">
                    Tentang Kami
                </x-nav-link> -->

                <x-nav-link href="{{ route('artikel') }}"
    :active="request()->routeIs('artikel')"
    class="nav-link transition-all duration-500">
    Artikel
</x-nav-link>
                
                <x-nav-link href="{{ route('faq') }}" :active="request()->routeIs('faq')" class="nav-link transition-all duration-500">
                    FAQs
                </x-nav-link>
                
                <x-nav-link href="{{ route('hubungi-kami') }}" :active="request()->routeIs('hubungi-kami')" class="nav-link transition-all duration-500">
                    Hubungi Kami
                </x-nav-link>
             
            </div>
        </div>
    </div>
</nav>

{{-- Add this CSS to your stylesheet --}}
<style>
    .nav-link {
        position: relative;
        font-weight: 500;
        transition: color 0.3s ease;
        color: #e8e8e8;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: -4px;
        left: 0;
        background-color: #ffffff;
        transition: width 0.3s ease;
    }

    .nav-link:hover::after,
    .nav-link.active::after {
        width: 100%;
    }

    .nav-link.active {
        color: #ffffff;
    }

    .nav-link:hover {
        color: #ffffff;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(-10px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #main-navbar .nav-link {
        animation: fadeIn 0.3s ease-in-out forwards;
        animation-delay: calc(var(--index, 0) * 0.1s);
    }
</style>

{{-- Add this JavaScript at the end of your body tag --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.getElementById('main-navbar');
        const navLinks = document.querySelectorAll('.nav-link');

        // Set animation delay untuk setiap link
        navLinks.forEach((link, index) => {
            link.style.setProperty('--index', index);
        });

        // Function untuk menutup dropdown saat scroll
        function closeDropdowns() {
            const openDropdowns = document.querySelectorAll('.dropdown-open');
            openDropdowns.forEach(dropdown => {
                dropdown.classList.remove('dropdown-open');
                if (dropdown.__x) {
                    dropdown.__x.$data.open = false;
                }
            });
        }

        // Event listener untuk scroll
        window.addEventListener('scroll', closeDropdowns, {
            passive: true
        });
    });
</script>