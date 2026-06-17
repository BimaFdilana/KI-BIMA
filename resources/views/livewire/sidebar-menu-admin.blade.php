<div x-data="{
    openDropdown: null,
    checkActiveRoutes() {
        const dropdown1Routes = ['/barang/ki', '/barang', '/barang/toko'];
        const dropdown2Routes = ['/barang/master'];
        const dropdown3Routes = ['/toko'];
        const dropdown4Routes = ['/user', '/user/detail', '/paylatter/account', '/paylatter/transaction'];
        const dropdown5Routes = ['/infaq/list', '/infaq/history', '/infaq/image'];
        const dropdown7Routes = ['/fonnte', '/debug/notification-templates'];
        const currentPath = window.location.pathname;

        this.$nextTick(() => {
            if (dropdown1Routes.some(route => currentPath.includes(route))) {
                this.openDropdown = 1;
            } else if (dropdown2Routes.some(route => currentPath.includes(route))) {
                this.openDropdown = 2;
            } else if (dropdown3Routes.some(route => currentPath.includes(route))) {
                this.openDropdown = 3;
            } else if (dropdown4Routes.some(route => currentPath.includes(route))) {
                this.openDropdown = 6;
            } else if (dropdown5Routes.some(route => currentPath.includes(route))) {
                this.openDropdown = 5;
            } else if (dropdown7Routes.some(route => currentPath.includes(route))) {
                this.openDropdown = 7;
            }
        });
    },
}" x-init="checkActiveRoutes()">

    <div x-show="sidebarOpen && window.innerWidth < 768" @click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-gray-900/50 transition-opacity lg:hidden"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    </div>

    <aside
        class="fixed left-0 top-0 z-40 h-screen transform border-r border-gray-200 bg-white shadow-lg transition-all duration-300 ease-in-out"
        :class="sidebarOpen ? 'translate-x-0 w-64' : 'w-20 md:translate-x-0 -translate-x-full'" x-cloak>

        <div class="flex h-16 items-center justify-between border-b border-gray-100 px-4">
            <div class="flex items-center" x-show="sidebarOpen" x-transition>
                <x-application-logo />
                <span class="ml-3 text-lg font-semibold text-gray-800">Kedai Indonesia</span>
            </div>

            <button @click="toggleSidebar()"
                class="rounded-lg p-1 text-gray-500 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-200"
                :class="sidebarOpen ? '' : 'mx-auto'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="sidebarOpen ? 'rotate-180' : 'rotate-0'"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </button>
        </div>

        <div class="h-[calc(100%-4rem)] overflow-y-auto p-4">
            <ul class="space-y-2">
                <li class="text-sm">
                    <x-side-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="fad fa-chart-pie"
                        text="Dashboard" />
                </li>
                <li class="text-sm">
                    <x-side-link href="{{ route('dashboard.approval') }}" :active="request()->routeIs('dashboard.approval')" icon="fad fa-chart-pie"
                        text="Approval" />
                </li>
                @can('access.analytics')
                    <!-- Analytics Item -->
                    <li class="text-sm">
                        <x-side-link href="{{ route('dashboard.analytics') }}" :active="request()->routeIs('dashboard.analytics')" icon="fas fa-chart-area"
                            text="Analytics" />
                    </li>
                    <li class="text-sm">
                        <x-side-link href="{{ route('dashboard.advanced-analytics') }}" :active="request()->routeIs('dashboard.advanced-analytics')"
                            icon="fas fa-chart-line" text="Advanced Analytics" />
                    </li>
                @endcan
                @can('view.payments')
                    <li class="text-sm">
                        <x-side-link href="{{ route('toko.payment.index') }}" :active="request()->routeIs('toko.payment.index')" icon="fas fa-cart-plus"
                            text="Pembelian" />
                    </li>
                @endcan

                @can(['view.barang', 'view.barang.ki', 'view.barang.toko'])
                    <!-- Section Divider -->
                    <li class="pt-2">
                        <div class="mb-2 border-t border-gray-100 pt-2">
                            <h3 class="px-2 text-xs font-semibold uppercase tracking-wider text-gray-500"
                                x-show="sidebarOpen">Barang</h3>
                            <div class="h-4" x-show="!sidebarOpen"></div>
                        </div>
                    </li>

                    <!-- Dropdown 1: Data Barang -->
                    <li class="text-sm">
                        <x-side-button @click="openDropdown = openDropdown === 1 ? null : 1" :active="request()->routeIs('barang.ki.index') ||
                            request()->routeIs('barang.index') ||
                            request()->routeIs('barang.toko.index')"
                            icon="fas fa-box" text="Data Barang" :dropdown="1" />
                        <div x-show="openDropdown === 1 && sidebarOpen"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2" class="mt-2 space-y-1 pl-5">
                            @can('view.barang.toko')
                                <x-side-dropdown-link href="{{ route('barang.toko.index') }}" :active="request()->routeIs('barang.toko.index')">
                                    Barang Toko
                                </x-side-dropdown-link>
                            @endcan
                            @can('view.barang.ki')
                                <x-side-dropdown-link href="{{ route('barang.ki.index') }}" :active="request()->routeIs('barang.ki.index')">
                                    Barang Kedai Indonesia
                                </x-side-dropdown-link>
                            @endcan
                            @can('view.barang')
                                <x-side-dropdown-link href="{{ route('barang.index') }}" :active="request()->routeIs('barang.index')">
                                    Database Barang
                                </x-side-dropdown-link>
                            @endcan
                        </div>
                    </li>
                @endcan

                @can('view.barang.master')
                    <!-- Dropdown 2: Detail Barang -->
                    <li class="text-sm">
                        <x-side-button @click="openDropdown = openDropdown === 2 ? null : 2" :active="request()->routeIs('barang.master.index')"
                            icon="fas fa-box-open" text="Detail Barang" :dropdown="2" />
                        <div x-show="openDropdown === 2 && sidebarOpen"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2" class="mt-2 space-y-1 pl-5">
                            <x-side-dropdown-link href="{{ route('barang.master.index') }}" :active="request()->routeIs('barang.master.index')">
                                Master Data
                            </x-side-dropdown-link>
                            <x-side-dropdown-link href="#" :active="request()->routeIs('barang.master.barang-in-out')">
                                Barang In & Out
                            </x-side-dropdown-link>
                        </div>
                    </li>
                @endcan

                <li class="pt-2">
                    <div class="mb-2 border-t border-gray-100 pt-2">
                        <h3 class="px-2 text-xs font-semibold uppercase tracking-wider text-gray-500"
                            x-show="sidebarOpen">
                            @can('view.toko')
                                Toko
                            @endcan
                            @can('view.users')
                                & User
                            @endcan
                        </h3>
                        <div class="h-4" x-show="!sidebarOpen"></div>
                    </div>
                </li>

                @can(['view.toko', 'view.pesanan'])
                    <!-- Dropdown 3: Data Toko -->
                    <li class="text-sm">
                        <x-side-button @click="openDropdown = openDropdown === 3 ? null : 3" :active="request()->routeIs('toko.index')"
                            icon="fas fa-store" text="Data Toko" :dropdown="3" />
                        <div x-show="openDropdown === 3 && sidebarOpen"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2" class="mt-2 space-y-1 pl-5">
                            @can('view.toko')
                                <x-side-dropdown-link href="{{ route('toko.index') }}" :active="request()->routeIs('toko.index')">
                                    Data Toko
                                </x-side-dropdown-link>
                            @endcan
                            @can('view.pesanan')
                                <x-side-dropdown-link href="{{ route('toko.payment.index') }}" :active="request()->routeIs('toko.payment.index')">
                                    Penjualan Toko
                                </x-side-dropdown-link>
                            @endcan
                        </div>
                    </li>
                @endcan

                @can('view.paylatter')
                    <li class="text-sm">
                        <x-side-button @click="openDropdown = openDropdown === 4 ? null : 4" icon="fa-solid fa-wallet"
                            text="Pakdul" :active="request()->routeIs('paylatter.account.index') ||
                                request()->routeIs('paylatter.transaction.index')" :dropdown="4" />
                        <div x-show="openDropdown === 4 && sidebarOpen"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2" class="mt-2 space-y-1 pl-5">
                            <x-side-dropdown-link href="{{ route('paylatter.account.index') }}" :active="request()->routeIs('paylatter.account.index')">
                                Akun Paylatter
                            </x-side-dropdown-link>
                            <x-side-dropdown-link href="{{ route('paylatter.transaction.index') }}" :active="request()->routeIs('paylatter.transaction.index')">
                                Transaksi Paylatter
                            </x-side-dropdown-link>

                        </div>
                    </li>
                @endcan

                @can('view.infaq')
                    <li class="text-sm">
                        <x-side-button @click="openDropdown = openDropdown === 5 ? null : 5"
                            icon="fas fa-hand-holding-heart" text="Data Infaq" :active="request()->routeIs('infaq.dashboard') ||
                                request()->routeIs('infaq.list.index') ||
                                request()->routeIs('infaq.history.index') ||
                                request()->routeIs('infaq.image.index')" :dropdown="5" />
                        <div x-show="openDropdown === 5 && sidebarOpen"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2" class="mt-2 space-y-1 pl-5">
                            <x-side-dropdown-link href="{{ route('infaq.dashboard') }}" :active="request()->routeIs('infaq.dashboard')">
                                Dashboard Infaq
                            </x-side-dropdown-link>
                            <x-side-dropdown-link href="{{ route('infaq.list.index') }}" :active="request()->routeIs('infaq.list.index')">
                                Pos Infaq
                            </x-side-dropdown-link>
                            <x-side-dropdown-link href="{{ route('infaq.history.index') }}" :active="request()->routeIs('infaq.history.index')">
                                Riwayat Donasi
                            </x-side-dropdown-link>
                            <x-side-dropdown-link href="{{ route('infaq.image.index') }}" :active="request()->routeIs('infaq.image.index')">
                                Gambar Infaq
                            </x-side-dropdown-link>
                        </div>
                    </li>
                @endcan

                @can('view.users')
                    <!-- Dropdown 4: Data User -->
                    <li class="text-sm">
                        <x-side-button @click="openDropdown = openDropdown === 6 ? null : 6" icon="fas fa-users"
                            text="Data User" :dropdown="6" />
                        <div x-show="openDropdown === 6 && sidebarOpen"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2" class="mt-2 space-y-1 pl-5">
                            <x-side-dropdown-link href="{{ route('user.index') }}" :active="request()->routeIs('user.index') || request()->routeIs('user.detail')">
                                Data User
                            </x-side-dropdown-link>
                            {{-- notification Data --}}
                            <x-side-dropdown-link href="#" :active="false">
                                Notification
                            </x-side-dropdown-link>
                        </div>
                    </li>
                @endcan
                <!-- Section Divider: Landingpage -->
                <li class="pt-2">
                    <div class="mb-2 border-t border-gray-100 pt-2">
                        <h3 class="px-2 text-xs font-semibold uppercase tracking-wider text-gray-500"
                            x-show="sidebarOpen">Landingpage</h3>
                        <div class="h-4" x-show="!sidebarOpen"></div>
                    </div>
                </li>

                <li class="text-sm">
                    <x-side-link href="{{ route('artikel.index') }}" :active="request()->routeIs('artikel.*')"
                        icon="fas fa-newspaper" text="Artikel" />
                </li>
                <li class="text-sm">
                    <x-side-link href="{{ route('faq.index') }}" :active="request()->routeIs('faq.*')"
                        icon="fas fa-question-circle" text="FAQ" />
                </li>
                <li class="text-sm">
                    <x-side-link href="{{ route('pesan.index') }}" :active="request()->routeIs('pesan.*')"
                        icon="fas fa-envelope" text="Pesan" />
                </li>
                <li class="text-sm">
                    <x-side-link href="{{ route('product.index') }}" :active="request()->routeIs('product.*')"
                        icon="fas fa-box" text="Product" />
                </li>

                <!-- failed jobs -->
                @role('programmer')
                    <li class="pt-2">
                        <div class="mb-2 border-t border-gray-100 pt-2">
                            <h3 class="px-2 text-xs font-semibold uppercase tracking-wider text-gray-500"
                                x-show="sidebarOpen">Debug</h3>
                            <div class="h-4" x-show="!sidebarOpen"></div>
                        </div>
                    </li>
                    <li class="text-sm">
                        <x-side-button @click="openDropdown = openDropdown === 7 ? null : 7" :active="false"
                            icon="fas fa-bug" text="Debug" :dropdown="7" />
                        <div x-show="openDropdown === 7 && sidebarOpen"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2" class="mt-2 space-y-1 pl-5">
                            <x-side-dropdown-link href="#" :active="false">
                                Active & Failed Jobs
                            </x-side-dropdown-link>
                            @if (env('TELESCOPE_ENABLED') == true)
                                <x-side-dropdown-link href="{{ route('telescope') }}" :active="request()->routeIs('telescope')">
                                    API Log
                                </x-side-dropdown-link>
                            @endif
                            <x-side-dropdown-link href="{{ route('fonnte.dashboard') }}" :active="request()->routeIs('fonnte.dashboard')">
                                Fonnte
                            </x-side-dropdown-link>
                            <x-side-dropdown-link href="{{ route('debug.notification-templates') }}" :active="request()->routeIs('debug.notification-templates')">
                                Format Notifikasi
                            </x-side-dropdown-link>


                        </div>
                    </li>

                    
                @endrole
                
                <!-- file manager -->
                <li class="text-sm">
                    <x-side-link href="{{ route('file-manager.index') }}" :active="request()->routeIs('file-manager.index')"
                        icon="fad fa-folder-open" text="File Manager" />
                </li>

            </ul>
        </div>
    </aside>
</div>
