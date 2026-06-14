<div class="relative rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition-all hover:shadow-md">
    <!-- Header Section -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center">
            <div class="mr-3 flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7" />
                    <rect x="16" y="3" width="6" height="11" rx="2" ry="2" />
                    <path d="M8 21v-4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v4" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900">Active Sessions</h3>
        </div>
        @if(count($devices) > 1)
        <button type="button" wire:click="confirmRemoveAllDevices" wire:loading.attr="disabled" class="d inline-flex items-center rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-600 transition-all hover:bg-red-100">
            <svg wire:loading wire:target="confirmRemoveAllDevices" class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg wire:loading.remove wire:target="confirmRemoveAllDevices" xmlns="http://www.w3.org/2000/svg" class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Logout All Other Devices ({{ count($devices) }})
        </button>
        @endif
    </div>

    <!-- Description Section -->
    <div class="mb-6 text-sm">
        <p class="text-gray-600">
            These are the devices that are currently logged in to your account. For security, you can log out devices you no longer use.
        </p>
    </div>
    <!-- Devices Container with loading state -->
    <div wire:loading.class="opacity-50" wire:target="loadDevices, removeDevice, gotoPage" class="transition-opacity">
        <div wire:loading wire:target="loadDevices, gotoPage" class="flex justify-center py-10">
            <div class="flex flex-col items-center">
                <div class="h-10 w-10 animate-spin rounded-full border-4 border-indigo-200 border-t-indigo-600"></div>
                <span class="mt-3 text-sm text-gray-500">Loading devices...</span>
            </div>
        </div>

        <div wire:loading.remove wire:target="loadDevices, gotoPage" class="space-y-4">
            <!-- First render the current device at the top -->
            @php
                $currentDevice = collect($devices)->where('device_id', $currentDeviceId)->first();
                $otherDevices = collect($devices)->where('device_id', '!=', $currentDeviceId);
                $allDevices = collect([$currentDevice])
                    ->filter()
                    ->merge($otherDevices);
                $paginatedDevices = $allDevices->take(5);
                $hasMoreDevices = $allDevices->count() > 5;
            @endphp

            @forelse($paginatedDevices as $device)
                @php
                    $isOnline = $device->last_active_at->diffInMinutes(now()) < 1;
                    $isCurrent = $device->device_id === $currentDeviceId;
                    $browser = null;
                    $os = null;
                    $deviceType = 'Unknown';

                    // Parse user agent
                    if ($device->user_agent) {
                        if (strpos($device->user_agent, 'Chrome') !== false) {
                            $browser = 'Chrome';
                        } elseif (strpos($device->user_agent, 'Firefox') !== false) {
                            $browser = 'Firefox';
                        } elseif (strpos($device->user_agent, 'Safari') !== false) {
                            $browser = 'Safari';
                        } elseif (strpos($device->user_agent, 'Edge') !== false) {
                            $browser = 'Edge';
                        }

                        if (strpos($device->user_agent, 'Windows') !== false) {
                            $os = 'Windows';
                        } elseif (strpos($device->user_agent, 'Mac') !== false) {
                            $os = 'macOS';
                        } elseif (strpos($device->user_agent, 'iPhone') !== false) {
                            $os = 'iOS';
                            $deviceType = 'Mobile';
                        } elseif (strpos($device->user_agent, 'Android') !== false) {
                            $os = 'Android';
                            $deviceType = 'Mobile';
                        } elseif (strpos($device->user_agent, 'Linux') !== false) {
                            $os = 'Linux';
                        }

                        if (strpos($device->user_agent, 'Mobile') !== false) {
                            $deviceType = 'Mobile';
                        } elseif (strpos($device->user_agent, 'Tablet') !== false) {
                            $deviceType = 'Tablet';
                        } else {
                            $deviceType = 'Desktop';
                        }
                    }
                @endphp
                <div class="device-card backdrop-blur-xs {{ $isCurrent ? 'bg-red-200/50' : 'bg-blue-50/50' }} group relative overflow-hidden rounded-xl to-white p-1 shadow-sm transition-all hover:shadow-md">
                    <div class="flex gap-4 rounded-lg p-4">
                        <div class="flex-shrink-0">
                            <div class="{{ $isCurrent ? 'bg-red-200 text-red-800 ' : 'bg-primary-100 text-primary-600' }} flex h-14 w-14 items-center justify-center rounded-xl">
                                @if ($deviceType == 'Mobile')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2" />
                                        <line x1="12" y1="18" x2="12" y2="18" />
                                    </svg>
                                @elseif($deviceType == 'Tablet')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="4" y="2" width="16" height="20" rx="2" ry="2" />
                                        <line x1="12" y1="18" x2="12" y2="18" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                                        <line x1="8" y1="21" x2="16" y2="21" />
                                        <line x1="12" y1="17" x2="12" y2="21" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="{{ $isCurrent ? 'text-yellow-800' : 'text-sky-800' }} flex flex-1 flex-col">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="text-base font-medium">{{ $device->device_name ?? 'Unknown Device' }}</span>

                                    <div class="ml-2 flex flex-wrap gap-1.5">
                                        @if ($isCurrent)
                                            <span class="rounded-full bg-red-200 px-2.5 py-0.5 text-xs font-medium text-red-800">Your Device</span>
                                        @endif

                                        @if ($isOnline)
                                            <span class="flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                                <span class="relative mr-1.5 flex h-2 w-2">
                                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                                                    <span class="relative inline-flex h-2 w-2 rounded-full bg-green-500"></span>
                                                </span>
                                                Online
                                            </span>
                                        @else
                                            <div class="{{ $isCurrent ? 'text-red-800 ' : 'text-primary-600' }} flex items-center text-xs text-gray-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>{{ $device->last_active_at->diffForHumans() }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if (!$isCurrent)
                                    <button type="button" wire:click="confirmRemoveDevice('{{ $device->device_id }}')" wire:loading.attr="disabled" class="inline-flex items-center rounded-lg bg-sky-50 px-3 py-1.5 text-xs font-medium text-sky-600 opacity-0 transition-opacity hover:bg-sky-100 focus:outline-none focus:ring-2 focus:ring-sky-200 group-hover:opacity-100">
                                        <svg wire:loading wire:target="confirmRemoveDevice('{{ $device->device_id }}')" class="mr-1.5 h-3 w-3 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <svg wire:loading.remove wire:target="confirmRemoveDevice('{{ $device->device_id }}')" xmlns="http://www.w3.org/2000/svg" class="mr-1.5 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Logout
                                    </button>
                                @endif
                            </div>

                            <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-2">
                                @if ($browser && $os)
                                    <div class="{{ $isCurrent ? 'bg-red-200 text-red-800 ' : 'bg-primary-100 text-primary-600' }} inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium">
                                        @if ($browser == 'Chrome')
                                            <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                                                <circle cx="12" cy="12" r="5" />
                                            </svg>
                                        @elseif($browser == 'Firefox')
                                            <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                                                <path d="M14 13h-4v-4h4v4z" />
                                            </svg>
                                        @elseif($browser == 'Safari')
                                            <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                                                <path d="M14.66 6.5L9.3 11.86a.5.5 0 000 .71l5.36 5.36" />
                                            </svg>
                                        @elseif($browser == 'Edge')
                                            <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                                                <path d="M15 13l-6 .02v-2L15 11v2z" />
                                            </svg>
                                        @endif
                                        {{ $browser }}
                                    </div>
                                    <div class="{{ $isCurrent ? 'bg-red-200 text-red-800 ' : 'bg-primary-100 text-primary-600' }} inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium">
                                        @if ($os == 'Windows')
                                            <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M3 5.5h8v8H3v-8zm10 0h8v8h-8v-8zm-10 10h8v8H3v-8zm10 0h8v8h-8v-8z" />
                                            </svg>
                                        @elseif($os == 'macOS')
                                            <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M15.5 2c-1.94 0-3.5 1.56-3.5 3.5v13c0 1.94 1.56 3.5 3.5 3.5s3.5-1.56 3.5-3.5v-13c0-1.94-1.56-3.5-3.5-3.5zm-7 0C6.56 2 5 3.56 5 5.5v13C5 20.44 6.56 22 8.5 22s3.5-1.56 3.5-3.5v-13C12 3.56 10.44 2 8.5 2z" />
                                            </svg>
                                        @elseif($os == 'iOS' || $os == 'Android')
                                            <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z" />
                                            </svg>
                                        @elseif($os == 'Linux')
                                            <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                                                <circle cx="12" cy="12" r="5" />
                                            </svg>
                                        @endif
                                        {{ $os }}
                                    </div>
                                @endif
                                @if (isset($device->ip_address))
                                    <div class="{{ $isCurrent ? 'bg-red-200 text-red-800 ' : 'bg-primary-100 text-primary-600' }} inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium">
                                        <span class="mr-4 inline-flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                            </svg>
                                            {{ $device->ip_address }}
                                        </span>
                                        @if (isset($device->location))
                                            <span class="inline-flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ $device->location }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-10 text-center">
                    <div class="mb-3 inline-flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No devices found</h3>
                    <p class="mt-2 text-sm text-gray-500">You don't have any active devices linked to your account.</p>
                </div>
            @endforelse

            <!-- Pagination Controls -->
            @if ($hasMoreDevices)
                <div class="mt-6 flex items-center justify-center space-x-2">
                    @for ($i = 0; $i < ceil($allDevices->count() / 5); $i++)
                        <button type="button" wire:click="$set('page', {{ $i + 1 }})" wire:loading.attr="disabled" class="{{ $page === $i + 1 ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 ' }} inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-medium transition-colors focus:outline-none">
                            {{ $i + 1 }}
                        </button>
                    @endfor
                </div>
            @endif
        </div>
    </div>

    <div class="mt-8 flex items-center justify-between">
        <div class="flex items-center text-sm text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            For security, we'll notify you when new devices sign in to your account
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div x-data="{ show: @entangle('showConfirmationModal') }" x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:w-full sm:max-w-lg" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            Confirm Logout
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                @if ($deviceToRemove === 'all')
                                    Are you sure you want to log out all other devices? Any other devices will need to sign in again with full verification.
                                @else
                                    Are you sure you want to log out this device? It will need to sign in again with full verification.
                                @endif
                            </p>
                            <div class="mt-4">
                                <label for="password" class="block text-sm font-medium text-gray-700">Please enter your password to confirm</label>
                                <div class="relative mt-1 rounded-md shadow-sm">
                                    <input type="password" id="password" wire:model.defer="password" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm" placeholder="Your current password">
                                    @error('password')
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    @enderror
                                </div>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="button" wire:click="removeDevice" wire:loading.attr="disabled" class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm transition-all hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 sm:ml-3 sm:w-auto sm:text-sm">
                    <span wire:loading wire:target="removeDevice" class="inline-flex items-center">
                        <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                    <span wire:loading.remove wire:target="removeDevice">Confirm</span>
                </button>
                <button type="button" wire:click="cancelRemove" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:mt-0 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
