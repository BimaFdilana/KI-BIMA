<div class="relative" x-data="{
    init() {
            this.initInfiniteScroll();
            this.initRealTimeNotifications();
        },
        initInfiniteScroll() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && @this.hasMoreNotifications) {
                        @this.loadMore();
                    }
                });
            }, {
                rootMargin: '0px 0px 50px 0px'
            });

            // Initialize sentinel element watcher
            const setupObserver = () => {
                const sentinelEl = this.$el.querySelector('#infinite-scroll-sentinel');
                if (sentinelEl) {
                    observer.observe(sentinelEl);
                } else {
                    setTimeout(setupObserver, 200);
                }
            };

            setupObserver();

            this.$watch('$wire.notifications', () => {
                this.$nextTick(setupObserver);
            });
        },
        initRealTimeNotifications() {
            // Enable real-time polling every 10 seconds when page is visible
            let pollInterval;

            const startPolling = () => {
                if (pollInterval) clearInterval(pollInterval);
                pollInterval = setInterval(() => {
                    @this.call('checkForNewNotifications');
                }, 10000); // Check every 10 seconds
            };

            const stopPolling = () => {
                if (pollInterval) {
                    clearInterval(pollInterval);
                    pollInterval = null;
                }
            };

            // Handle page visibility changes
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    stopPolling();
                    @this.call('togglePolling', false);
                } else {
                    startPolling();
                    @this.call('togglePolling', true);
                    @this.call('checkForNewNotifications');
                }
            });

            // Start polling when component initializes
            if (!document.hidden) {
                startPolling();
            }

            // Listen for new notification events
            window.addEventListener('new-notification-received', (event) => {
                // Show browser notification if permitted
                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification('Notifikasi Baru', {
                        body: `Anda memiliki ${event.detail.count} notifikasi baru`,
                        icon: '/favicon.ico',
                        tag: 'new-notification'
                    });
                }

                // Visual feedback - flash the notification icon
                const icon = this.$el.querySelector('.notification-icon');
                if (icon) {
                    icon.classList.add('animate-bounce');
                    setTimeout(() => {
                        icon.classList.remove('animate-bounce');
                    }, 2000);
                }
            });

            // Request notification permission on first interaction
            this.$el.addEventListener('click', () => {
                if ('Notification' in window && Notification.permission === 'default') {
                    Notification.requestPermission();
                }
            }, { once: true });
        }
}" wire:poll.1000s="checkForNewNotifications">

    {{-- Notification button with badge --}}
    <button type="button" wire:click="toggleDropdown" class="relative p-2 text-gray-600 hover:cursor-pointer hover:text-gray-900 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="notification-icon h-6 w-6 transition-all duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if ($unopenCount > 0)
            <span class="absolute right-0 top-0 min-w-[18px] animate-pulse rounded-full bg-red-500 px-1 py-0.5 text-center text-xs text-white">
                {{ $unopenCount > 9 ? '9+' : $unopenCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown container --}}
    @if ($showDropdown)
        <div class="absolute right-0 z-50 mt-2 flex w-80 flex-col overflow-hidden rounded-md bg-white shadow-lg" style="max-height: 70vh">
            {{-- Dropdown header --}}
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white px-4 py-3">
                <h3 class="text-sm font-medium text-gray-900">Notifikasi</h3>
                <div class="flex gap-2">
                    @if (count($notifications) > 0)
                        <button wire:click="markAllAsRead" class="text-xs text-blue-600 hover:text-blue-800" title="Tandai semua sebagai dibaca">
                            Tandai semua
                        </button>
                    @endif
                    <button wire:click="viewAll" class="text-xs text-gray-600 hover:text-gray-800">
                        Lihat semua
                    </button>
                </div>
            </div>

            {{-- Notification Tabs --}}
            <div class="flex border-b border-gray-200">
                <button wire:click="setActiveTab('all')" class="{{ $activeTab === 'all' ? 'font-medium text-blue-600 border-b-2 border-blue-500' : 'text-gray-500 hover:text-gray-700' }} flex-1 py-2 text-center text-sm">
                    Semua
                </button>
                <button wire:click="setActiveTab('personal')" class="{{ $activeTab === 'personal' ? 'font-medium text-blue-600 border-b-2 border-blue-500' : 'text-gray-500 hover:text-gray-700' }} flex-1 py-2 text-center text-sm">
                    Personal
                </button>
                <button wire:click="setActiveTab('role')" class="{{ $activeTab === 'role' ? 'font-medium text-blue-600 border-b-2 border-blue-500' : 'text-gray-500 hover:text-gray-700' }} flex-1 py-2 text-center text-sm">
                    Role
                </button>
            </div>

            {{-- Notifications list --}}
            <div class="flex-grow overflow-y-auto" id="notifications-container">
                @if ($isLoading && count($notifications) === 0)
                    <div class="space-y-3 p-4">
                        <!-- Pulse loading cards -->
                        @for ($i = 0; $i < 4; $i++)
                            <div class="flex animate-pulse space-x-4">
                                <div class="flex-1 space-y-2 py-1">
                                    <div class="h-4 w-3/4 rounded bg-gray-200"></div>
                                    <div class="h-3 rounded bg-gray-200"></div>
                                    <div class="flex justify-between">
                                        <div class="h-3 w-1/4 rounded bg-gray-200"></div>
                                        <div class="h-3 w-1/4 rounded bg-gray-200"></div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                @elseif(count($notifications) === 0)
                    <div class="px-4 py-6 text-center text-gray-500">
                        <svg class="mx-auto mb-2 h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <p>Tidak ada notifikasi</p>
                    </div>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach ($notifications as $notification)
                            @php
                                // Determine background color based on status and importance
                                $bgClass = '';
                                $status = $notification['status'] ?? 'unread';

                                if ($status === 'unopen') {
                                    $bgClass = 'bg-red-50';
                                } elseif ($status === 'unread') {
                                    $bgClass = 'bg-blue-50';
                                } elseif ($status === 'clicked') {
                                    $bgClass = 'bg-gray-50';
                                } elseif ($status === 'downloaded') {
                                    $bgClass = 'bg-green-50';
                                }

                                // Determine notification title based on hierarchy of fields
                                $title = '';
                                if (isset($notification['data']['title'])) {
                                    $title = $notification['data']['title'];
                                } elseif (isset($notification['data']['message'])) {
                                    $title = $notification['data']['message'];
                                } else {
                                    $title = 'Notifikasi';
                                }

                                // Determine notification message
                                $message = '';
                                if (isset($notification['data']['details'])) {
                                    $message = $notification['data']['details'];
                                } elseif (isset($notification['data']['message']) || isset($notification['data']['title'])) {
                                    // Only use message as content if we already used title above
                                    $message = $notification['data']['message'];
                                }

                                // Check if notification has path or download_url
                                $hasPath = isset($notification['path']) && !empty($notification['path']);
                                $hasDownloadUrl = isset($notification['data']['download_url']) && !empty($notification['data']['download_url']);
                                $isClickable = $hasDownloadUrl;

                                // Determine icon type
                                $iconType = $notification['type'] ?? 'system_update';
                            @endphp

                            <li class="{{ $bgClass }} transition-colors duration-150 hover:bg-gray-50">
                                @if ($isClickable)
                                    @if ($hasDownloadUrl)
                                        <a href="{{ $notification['data']['download_url'] }}" target="_blank" class="block p-4" wire:click="handleDownload('{{ $notification['id'] }}', '{{ $notification['data']['download_url'] }}')">
                                        @else
                                            <a href="#" wire:click.prevent="handleNotificationClick('{{ $notification['id'] }}', '{{ $notification['path'] }}')" class="block p-4">
                                    @endif
                                @else
                                    <div class="p-4" wire:click="markAsRead('{{ $notification['id'] }}')">
                                @endif
                                <div class="flex">
                                    {{-- Notification Icon (dynamic based on type) --}}
                                    <div class="mr-3 flex-shrink-0">
                                        @if (isset($notification['is_system']) && $notification['is_system'])
                                            <x-user-avatar :user="Auth::user()" :show-status="true" size="sm" />
                                        @else
                                            <div class="{{ $notification['is_system'] ?? false ? 'bg-purple-100' : 'bg-gray-100' }} flex h-10 w-10 items-center justify-center rounded-full">
                                                @php
                                                    $iconClass = match ($iconType) {
                                                        'system_update' => 'text-purple-500',
                                                        'task_assigned' => 'text-blue-500',
                                                        'inventory_low' => 'text-orange-500',
                                                        'order_completed' => 'text-green-500',
                                                        'payment_received' => 'text-emerald-500',
                                                        'sale_completed' => 'text-indigo-500',
                                                        'refund_issued' => 'text-red-500',
                                                        'customer_inquiry' => 'text-amber-500',
                                                        'export_data' => 'text-blue-600',
                                                        'import_data' => 'text-green-600',
                                                        default => 'text-gray-500',
                                                    };
                                                @endphp

                                                @switch($iconType)
                                                    @case('system_update')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }} h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                        </svg>
                                                    @break

                                                    @case('task_assigned')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }} h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                        </svg>
                                                    @break

                                                    @case('inventory_low')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }} h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                    @break

                                                    @case('order_completed')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }} h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    @break

                                                    @case('payment_received')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }} h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    @break

                                                    @case('sale_completed')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }} h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                        </svg>
                                                    @break

                                                    @case('refund_issued')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }} h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                                                        </svg>
                                                    @break

                                                    @case('customer_inquiry')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }} h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                                        </svg>
                                                    @break

                                                    @case('export_data')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }} h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    @break

                                                    @case('import_data')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }} h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                                        </svg>
                                                    @break

                                                    @default
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }} h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                        </svg>
                                                @endswitch
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Notification Content --}}
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between">
                                            <div class="text-sm font-medium text-gray-900">
                                                @if (isset($notification['data']['task_name']))
                                                    {{ $notification['data']['task_name'] }}
                                                @elseif (isset($notification['data']['item_name']))
                                                    {{ $notification['data']['item_name'] }}
                                                @elseif (isset($notification['data']['customer_name']))
                                                    {{ $notification['data']['customer_name'] }}
                                                @elseif (isset($notification['data']['order_id']))
                                                    {{ $notification['data']['order_id'] }}
                                                @elseif (isset($notification['data']['sale_id']))
                                                    {{ $notification['data']['sale_id'] }}
                                                @elseif (isset($notification['data']['title']))
                                                    {{ $notification['data']['title'] }}
                                                @elseif (isset($notification['data']['message']))
                                                    {{ $notification['data']['message'] }}
                                                @endif
                                            </div>
                                        </div>

                                        <p class="mt-1 text-xs text-gray-600">
                                            @if (isset($notification['data']['details']))
                                                {{ $notification['data']['details'] }}
                                            @elseif (isset($notification['data']['message']) || isset($notification['data']['title']))
                                                {{ $notification['data']['message'] }}
                                            @endif
                                        </p>

                                        {{-- Dynamic notification metadata based on type --}}
                                        @if (isset($notification['data']) && is_array($notification['data']))
                                            @switch($notification['type'] ?? '')
                                                @case('system_update')
                                                    @if (isset($notification['data']['version']))
                                                        <div class="mt-1 flex items-center">
                                                            <span class="rounded bg-purple-100 px-2 py-0.5 text-xs text-purple-800">v{{ $notification['data']['version'] }}</span>
                                                            @if (isset($notification['data']['impact']))
                                                                <span class="{{ $notification['data']['impact'] === 'high' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }} ml-2 rounded px-2 py-0.5 text-xs">{{ ucfirst($notification['data']['impact']) }}</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @break

                                                @case('inventory_low')
                                                    @if (isset($notification['data']['items_count']))
                                                        <div class="mt-1 text-xs text-red-600">
                                                            {{ $notification['data']['items_count'] }} items below threshold
                                                        </div>
                                                    @endif
                                                @break

                                                @case('payment_received')
                                                    @if (isset($notification['data']['amount']))
                                                        <div class="mt-1 text-xs text-green-600">
                                                            Rp {{ number_format($notification['data']['amount'], 0, ',', '.') }}
                                                            @if (isset($notification['data']['payment_method']))
                                                                via {{ $notification['data']['payment_method'] }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                @break

                                                @case('sale_completed')
                                                    @if (isset($notification['data']['total_sales']))
                                                        <div class="mt-1 text-xs text-indigo-600">
                                                            Rp {{ number_format($notification['data']['total_sales'], 0, ',', '.') }}
                                                            @if (isset($notification['data']['growth']))
                                                                <span class="ml-1 text-green-600">↑ {{ $notification['data']['growth'] }}</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @break

                                                @case('import_data')
                                                    @if (isset($notification['data']['filename']))
                                                        <div class="mt-1 flex items-center gap-2">
                                                            <span class="rounded bg-green-100 px-2 py-0.5 text-xs text-green-800">
                                                                {{ strtoupper($notification['data']['format'] ?? 'file') }}
                                                            </span>
                                                            <span class="text-xs text-green-600">{{ $notification['data']['filename'] }}</span>
                                                            @if (isset($notification['data']['records_count']))
                                                                <span class="text-xs text-green-600">{{ $notification['data']['records_count'] }} records</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @break
                                            @endswitch
                                        @endif

                                        <div class="mt-2 flex items-center justify-between">
                                            <span class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                                            </span>
                                            <div class="ml-2 flex items-center gap-1">
                                                @if (isset($notification['is_system']) && $notification['is_system'])
                                                    <span class="inline-flex items-center rounded-full bg-purple-100 px-1.5 py-0.5 text-xs font-medium text-purple-800">
                                                        System
                                                    </span>
                                                @endif

                                                @if (isset($notification['data']['filename']))
                                                    <span class="rounded-full bg-blue-100 px-1.5 py-0.5 text-xs font-medium text-blue-800">
                                                        {{ ucfirst($notification['data']['format'] ?? 'file') }}
                                                    </span>
                                                @endif

                                                @if (isset($notification['is_important']) && $notification['is_important'])
                                                    <span class="inline-flex items-center rounded-full bg-red-100 px-1.5 py-0.5 text-xs font-medium text-red-800">
                                                        Penting
                                                    </span>
                                                @endif

                                                {{-- Status indicators --}}
                                                @php $status = $notification['status'] ?? 'unread'; @endphp

                                                @if ($status === 'unopen')
                                                    <div class="flex items-center gap-1">
                                                        <div class="h-2 w-2 animate-pulse rounded-full bg-red-500"></div>
                                                        <span class="text-xs font-medium text-red-600">Baru</span>
                                                    </div>
                                                @elseif ($status === 'unread')
                                                    <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                                                @elseif ($status === 'clicked')
                                                    <svg class="h-3 w-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @elseif ($status === 'downloaded')
                                                    <svg class="h-3 w-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if ($isClickable)
                                    </a>
                                @else
            </div>
    @endif
    </li>
    @endforeach

    {{-- Infinite scroll sentinel --}}
    <div id="infinite-scroll-sentinel" class="place-items-center p-2">
        @if ($hasMoreNotifications)
            <div wire:loading.remove wire:target="loadMore" class="h-1"></div>
            <div wire:loading wire:target="loadMore" class="flex justify-center py-2">
                <div class="loader h-5 w-5 animate-spin rounded-full border-2 border-t-2 border-gray-300 border-t-red-500"></div>
            </div>
        @else
            <div class="py-1 text-center text-xs text-gray-400">Tidak ada notifikasi lainnya</div>
        @endif
    </div>
    </ul>
    @endif
</div>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Close notification dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const notificationBadge = document.querySelector('[wire\\:id][wire\\:initial-data*="NotificationBadge"]');
            if (notificationBadge && !notificationBadge.contains(event.target)) {
                @this.call('closeDropdown');
            }
        });
    });
</script>
</div>
