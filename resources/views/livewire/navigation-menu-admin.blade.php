<nav class="sticky z-30 rounded-xl border-b border-gray-200 bg-white shadow-sm">
    <div class="px-4 py-3 md:px-6">
        <div class="flex items-center justify-between">
            <!-- Left side - Logo and Title -->
            <div class="flex items-center">
                <!-- Mobile menu button -->
                <button @click="toggleSidebar()" class="mr-2 rounded-lg p-2 text-gray-600 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-200 md:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <span class="hidden text-2xl font-bold text-gray-800 md:block">
                    @yield('nav_title', 'Kedai Indonesia')
                </span>
            </div>

            <!-- Right side - Search, weather, notifications, profile -->
            <div class="flex items-center space-x-4">
                <!-- Search - visible on all screen sizes but styled differently -->
                <div class="relative hidden md:block">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="search" class="w-64 rounded-lg border border-gray-200 bg-gray-50 py-2 pl-10 pr-4 text-sm text-gray-700 focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="Cari...">
                </div>

                <!-- Weather widget -->
                <!-- <div class="hidden items-center rounded-lg bg-gray-50 px-3 py-2 md:flex">
                    <span class="weather-box">
                        <i id="weather-icon" class="bi mr-2" aria-hidden="true"></i>
                        <span id="temperature"><x-loading class="loading-bars loading-xs" /></span>
                    </span>
                </div> -->
                <livewire:notification-badge />
                <x-profile-circle />
            </div>
        </div>
    </div>
</nav>
