<div class="mx-auto py-6">
    <!-- Tabs Navigation -->
    <div class="bg-white rounded-xl shadow-lg mb-8 border border-gray-200">
        <div class="flex justify-between">
            <div class="flex border-b border-gray-200">
                <button wire:click="setTab('overview')" wire:loading.attr="disabled" wire:target="setTab('overview')"
                    @class([
                        'px-6 py-4 font-medium transition-all whitespace-nowrap disabled:opacity-50',
                        'text-red-600 border-b-2 border-red-600 bg-red-50' => $tab === 'overview',
                        'text-gray-600 hover:text-gray-900' => $tab !== 'overview',
                    ])>
                    <div class="space-x-2" wire:loading wire:target="setTab('overview')">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Loading...</span>
                    </div>
                    <div class="space-x-2" wire:loading.remove wire:target="setTab('overview')">
                        <i class="fas fa-eye"></i>
                        <span>Overview</span>
                    </div>
                </button>
                <button wire:click="setTab('toko')" wire:loading.attr="disabled" wire:target="setTab('toko')"
                    @class([
                        'px-6 py-4 font-medium transition-all whitespace-nowrap relative disabled:opacity-50',
                        'text-red-600 border-b-2 border-red-600 bg-red-50 ' => $tab === 'toko',
                        'text-gray-600 hover:text-gray-900' => $tab !== 'toko',
                    ])>
                    <div class="space-x-2" wire:loading wire:target="setTab('toko')">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Loading...</span>
                    </div>
                    <div class="space-x-2" wire:loading.remove wire:target="setTab('toko')">
                        <i class="fas fa-store"></i>
                        <span>Verifikasi Toko</span>
                    </div>
                    @if ($summary['pending_toko'] > 0)
                        <span
                            class="absolute z-10 -top-2 -right-2 bg-red-600 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">{{ $summary['pending_toko'] }}</span>
                    @endif
                </button>
                <button wire:click="setTab('ktp')" wire:loading.attr="disabled" wire:target="setTab('ktp')"
                    @class([
                        'px-6 py-4 font-medium transition-all whitespace-nowrap relative disabled:opacity-50',
                        'text-red-600 border-b-2 border-red-600 bg-red-50' => $tab === 'ktp',
                        'text-gray-600 hover:text-gray-900' => $tab !== 'ktp',
                    ])>
                    <div class="space-x-2" wire:loading wire:target="setTab('ktp')">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Loading...</span>
                    </div>
                    <div class="space-x-2" wire:loading.remove wire:target="setTab('ktp')">
                        <i class="fas fa-id-card"></i>
                        <span>Verifikasi KTP</span>
                    </div>
                    @if ($summary['pending_ktp'] > 0)
                        <span
                            class="absolute z-10 -top-2 -right-2 bg-red-600 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">{{ $summary['pending_ktp'] }}</span>
                    @endif
                </button>

                <button wire:click="setTab('payment')" wire:loading.attr="disabled" wire:target="setTab('payment')"
                    @class([
                        'px-6 py-4 font-medium transition-all whitespace-nowrap relative disabled:opacity-50',
                        'text-red-600 border-b-2 border-red-600 bg-red-50' => $tab === 'payment',
                        'text-gray-600 hover:text-gray-900' => $tab !== 'payment',
                    ])>
                    <div class="space-x-2" wire:loading wire:target="setTab('payment')">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Loading...</span>
                    </div>
                    <div class="space-x-2" wire:loading.remove wire:target="setTab('payment')">
                        <i class="fas fa-money-bill"></i>
                        <span>Verifikasi Pembayaran</span>
                    </div>
                    @if ($summary['pending_payments'] > 0)
                        <span
                            class="absolute z-10 -top-2 -right-2 bg-red-600 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">{{ $summary['pending_payments'] }}</span>
                    @endif
                </button>
            </div>


            <div id="pusher-status"
                class="flex items-center gap-3 px-5 py-3 rounded-xl bg-gradient-to-r from-gray-100 to-gray-50 border border-gray-200 shadow-sm transition-all duration-300 hover:shadow-md">
                <!-- Status Indicator Dot -->
                <div class="relative">
                    <div id="status-dot" class="w-3 h-3 rounded-full bg-gray-400 transition-all duration-300">
                    </div>
                    <!-- Pulse Animation Ring -->
                    <div id="status-pulse"
                        class="absolute inset-0 w-3 h-3 rounded-full bg-gray-400 opacity-0 animate-ping">
                    </div>
                </div>

                <!-- Status Icon & Text -->
                <div class="flex items-center gap-2">
                    <i id="status-icon" class="fas fa-circle-notch fa-spin text-gray-500 text-sm"></i>
                    <span id="status-text" class="text-sm font-semibold text-gray-600">
                        Connecting...
                    </span>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="p-6 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="relative">
                <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" wire:model.live.debounce.500ms="search"
                    placeholder="Cari berdasarkan nama, email, atau ID..."
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>
        </div>
    </div>

    <!-- Overview Tab -->
    @if ($tab === 'overview')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">⚡ Aksi Cepat</h2>
                <div class="space-y-4">
                    <button wire:click="setTab('toko')"
                        class="w-full hover:cursor-pointer flex items-center gap-4 p-4 bg-gradient-to-r from-red-50 to-white hover:from-red-100 hover:to-red-50 border border-red-200 rounded-lg transition-all group">
                        <div class="bg-red-600 p-3 rounded-lg group-hover:scale-110 transition-transform">
                            <i class="text-xl text-white fad fa-store"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900">Verifikasi Toko</p>
                            <p class="text-sm text-gray-600">{{ $summary['pending_toko'] }} toko menunggu</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 ml-auto group-hover:translate-x-1 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove
                            wire:target="setTab('toko')">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        <i class="fas fa-spinner fa-spin ml-auto group-hover:translate-x-1 transition-transform"
                            wire:loading wire:target="setTab('toko')"></i>
                    </button>

                    <button wire:click="setTab('ktp')"
                        class="w-full flex items-center gap-4 p-4 hover:cursor-pointer bg-gradient-to-r from-yellow-50 to-white hover:from-yellow-100 hover:to-yellow-50 border border-yellow-200 rounded-lg transition-all group">
                        <div class="bg-yellow-600 p-3 rounded-lg group-hover:scale-110 transition-transform">
                            <i class="text-xl text-white fad fa-id-card"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900">Verifikasi KTP</p>
                            <p class="text-sm text-gray-600">{{ $summary['pending_ktp'] }} user menunggu</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 ml-auto group-hover:translate-x-1 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove
                            wire:target="setTab('ktp')">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        <i class="fas fa-spinner fa-spin ml-auto group-hover:translate-x-1 transition-transform"
                            wire:loading wire:target="setTab('ktp')"></i>
                    </button>

                    <button wire:click="setTab('payment')"
                        class="w-full flex items-center gap-4 p-4 hover:cursor-pointer bg-gradient-to-r from-orange-50 to-white hover:from-orange-100 hover:to-orange-50 border border-orange-200 rounded-lg transition-all group">
                        <div class="bg-orange-600 p-3 rounded-lg group-hover:scale-110 transition-transform">
                            <i class="text-xl text-white fad fa-money-bill"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900">Verifikasi Pembayaran</p>
                            <p class="text-sm text-gray-600">{{ $summary['pending_payments'] }} pembayaran menunggu
                            </p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 ml-auto group-hover:translate-x-1 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove
                            wire:target="setTab('payment')">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        <i class="fas fa-spinner fa-spin ml-auto group-hover:translate-x-1 transition-transform"
                            wire:loading wire:target="setTab('payment')"></i>
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">📊 Statistik</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm text-gray-600">Toko Aktif</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $summary['active_toko'] }}</p>
                        </div>
                        <div class="text-3xl">🏪</div>
                    </div>
                    <div
                        class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div>
                            <p class="text-sm text-gray-600">Total Menunggu Review</p>
                            <p class="text-2xl font-bold text-yellow-600">
                                {{ $summary['pending_toko'] + $summary['pending_ktp'] + $summary['pending_payments'] }}
                            </p>
                        </div>
                        <div class="text-3xl">⏳</div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                        <div>
                            <p class="text-sm text-gray-600">User Terverifikasi</p>
                            <p class="text-2xl font-bold text-green-600">{{ $summary['verified_users'] ?? 0 }}</p>
                        </div>
                        <div class="text-3xl">✓</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- UPDATE UNTUK KTP VERIFICATION TAB -->
    @if ($tab === 'ktp')
        <div class="space-y-6">
            @forelse($pendingKtps as $ktp)
                <div wire:key="ktp-{{ $ktp->id }}"
                    class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow border-l-4 border-yellow-600">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center text-white font-bold text-lg">
                                        {{ substr($ktp->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">{{ $ktp->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $ktp->email }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-4">
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-600">Nomor KTP</p>
                                        <p class="font-semibold text-gray-900 truncate">
                                            {{ $ktp->ktp_number ?? '-' }}
                                        </p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-600">Nama KTP</p>
                                        <p class="font-semibold text-gray-900">{{ $ktp->ktp_name ?? '-' }}</p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-600">No. HP</p>
                                        <p class="font-semibold text-gray-900">{{ $ktp->phone_number ?? '-' }}</p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-600">Tanggal Daftar</p>
                                        <p class="font-semibold text-gray-900">
                                            {{ $ktp->created_at->format('d M Y') }}
                                        </p>
                                    </div>
                                    <div class="flex gap-3 px-3 py-2 ml-auto">
                                        <button wire:click="showDetailModal('ktp', {{ $ktp->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="showDetailModal('ktp', {{ $ktp->id }})"
                                            class="py-3 px-4.5 bg-blue-100 text-blue-600 font-medium rounded-lg hover:bg-blue-200 text-lg active:scale-95 transition-all duration-200 border border-blue-200 cursor-pointer disabled:opacity-50">
                                            <i class="fad fa-eye" wire:loading.remove
                                                wire:target="showDetailModal('ktp', {{ $ktp->id }})"></i>
                                            <i class="fas fa-spinner fa-spin" wire:loading
                                                wire:target="showDetailModal('ktp', {{ $ktp->id }})"></i>
                                        </button>
                                        <button wire:click="setupAction('ktp', 'verify', {{ $ktp->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="setupAction('ktp', 'verify', {{ $ktp->id }})"
                                            class="py-3 px-4.5 bg-green-100 text-green-600 font-medium rounded-lg hover:bg-green-200 text-lg active:scale-95 transition-all duration-200 border border-green-200 cursor-pointer disabled:opacity-50">
                                            <i class="fad fa-check" wire:loading.remove
                                                wire:target="setupAction('ktp', 'verify', {{ $ktp->id }})"></i>
                                            <i class="fas fa-spinner fa-spin" wire:loading
                                                wire:target="setupAction('ktp', 'verify', {{ $ktp->id }})"></i>
                                        </button>
                                        <button wire:click="setupAction('ktp', 'reject', {{ $ktp->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="setupAction('ktp', 'reject', {{ $ktp->id }})"
                                            class="py-3 px-4.5 bg-red-100 text-red-600 font-medium rounded-lg hover:bg-red-200 text-lg active:scale-95 transition-all duration-200 border border-red-200 cursor-pointer disabled:opacity-50">
                                            <i class="fad fa-times" wire:loading.remove
                                                wire:target="setupAction('ktp', 'reject', {{ $ktp->id }})"></i>
                                            <i class="fas fa-spinner fa-spin" wire:loading
                                                wire:target="setupAction('ktp', 'reject', {{ $ktp->id }})"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-lg p-16 text-center border border-gray-200">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-600 text-lg font-medium">Tidak ada KTP yang menunggu verifikasi</p>
                    <p class="text-gray-500 text-sm">Semua KTP telah diverifikasi</p>
                </div>
            @endforelse
        </div>
    @endif

    <!-- UPDATE UNTUK TOKO VERIFICATION TAB -->
    @if ($tab === 'toko')
        <div class="space-y-6">
            @forelse($pendingTokos as $toko)
                <div wire:key="toko-{{ $toko['id'] }}"
                    class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow border-l-4 border-red-600">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex justify-between gap-3 mb-3">
                                    <div class="flex items-center gap-3 mb-3">
                                        @if ($toko['image'])
                                            <img src="{{ asset('storage/' . $toko['image']) }}"
                                                alt="Toko {{ $toko['name'] }}"
                                                class="w-12 h-12 rounded-lg object-cover">
                                        @else
                                            <div
                                                class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center text-white font-bold text-lg">
                                                {{ substr($toko['name'], 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">{{ $toko['name'] }}
                                                <span
                                                    class="px-2.5 py-1 ml-3 rounded-lg text-xs font-bold {{ $this->getTokoTypeClass($toko['type']) }}">
                                                    @switch($toko['type'])
                                                        @case('ki')
                                                            Kedai Indonesia
                                                        @break

                                                        @case('kmp')
                                                            Koperasi Merah Putih
                                                        @break

                                                        @case('pro')
                                                            PRO
                                                        @break

                                                        @default
                                                            {{ strtoupper($toko['type'] ?? '-') }}
                                                    @endswitch
                                                </span>
                                            </h3>
                                            <p class="text-sm text-gray-600">{{ $toko['address'] ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4">
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-600">Pemilik</p>
                                        <p class="font-semibold text-gray-900">
                                            {{ $toko['owner']['name'] ?? '-' }}
                                        </p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-600">No. HP</p>
                                        <p class="font-semibold text-gray-900 truncate">
                                            {{ $toko['owner']['phone_number'] ?? '-' }}</p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-600">Dibuat</p>
                                        <p class="font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($toko['created_at'])->format('d M Y') }}</p>
                                    </div>

                                    <div class="flex gap-3 px-3 py-2 ml-auto">
                                        <button wire:click="showDetailModal('toko', {{ $toko['id'] }})"
                                            wire:loading.attr="disabled"
                                            wire:target="showDetailModal('toko', {{ $toko['id'] }})"
                                            class="py-3 px-4.5 bg-blue-100 text-blue-600 font-medium rounded-lg hover:bg-blue-200 text-lg active:scale-95 transition-all duration-200 border border-blue-200 cursor-pointer disabled:opacity-50">
                                            <i class="fad fa-eye" wire:loading.remove
                                                wire:target="showDetailModal('toko', {{ $toko['id'] }})"></i>
                                            <i class="fas fa-spinner fa-spin" wire:loading
                                                wire:target="showDetailModal('toko', {{ $toko['id'] }})"></i>
                                        </button>
                                        <button wire:click="setupAction('toko', 'approve', {{ $toko['id'] }})"
                                            wire:loading.attr="disabled"
                                            wire:target="setupAction('toko', 'approve', {{ $toko['id'] }})"
                                            class="py-3 px-4.5 bg-green-100 text-green-600 font-medium hover:bg-green-200 rounded-lg active:scale-95 transition-all duration-200 border border-green-200 cursor-pointer disabled:opacity-50">
                                            <i class="fad fa-check" wire:loading.remove
                                                wire:target="setupAction('toko', 'approve', {{ $toko['id'] }})"></i>
                                            <i class="fas fa-spinner fa-spin" wire:loading
                                                wire:target="setupAction('toko', 'approve', {{ $toko['id'] }})"></i>
                                        </button>
                                        <button wire:click="setupAction('toko', 'reject', {{ $toko['id'] }})"
                                            wire:loading.attr="disabled"
                                            wire:target="setupAction('toko', 'reject', {{ $toko['id'] }})"
                                            class="py-3 px-4.5 bg-red-100 text-red-600 font-medium hover:bg-red-200 rounded-lg active:scale-95 transition-all duration-200 border border-red-200 cursor-pointer disabled:opacity-50">
                                            <i class="fad fa-times" wire:loading.remove
                                                wire:target="setupAction('toko', 'reject', {{ $toko['id'] }})"></i>
                                            <i class="fas fa-spinner fa-spin" wire:loading
                                                wire:target="setupAction('toko', 'reject', {{ $toko['id'] }})"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                    <div class="bg-white rounded-xl shadow-lg p-16 text-center border border-gray-200">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                            </path>
                        </svg>
                        <p class="text-gray-600 text-lg font-medium">Tidak ada toko yang menunggu verifikasi</p>
                        <p class="text-gray-500 text-sm">Semua toko telah diverifikasi</p>
                    </div>
                @endforelse
            </div>
        @endif

        @if ($tab === 'payment')
            <div class="space-y-6">
                @forelse($pendingPayments as $payment)
                    <div wire:key="payment-{{ $payment['id'] }}"
                        class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow border-l-4 border-orange-600">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div
                                            class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center text-white font-bold">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">{{ $payment['transaction_id'] }}
                                            </h3>
                                            <p class="text-sm text-gray-600">{{ $payment['user']['name'] ?? '-' }} |
                                                {{ $payment['toko']['name'] ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-4">
                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <p class="text-xs text-gray-600">Nominal</p>
                                            <p class="font-semibold text-gray-900">
                                                Rp{{ number_format($payment['total'] ?? 0, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-lg capitalize">
                                            <p class="text-xs text-gray-600">Metode
                                                <span
                                                    class="{{ $this->getPaymentTypeClass($payment['payment_type']) }} mt-1 inline-flex rounded-sm px-2 text-xs font-semibold leading-5">
                                                    @switch($payment['payment_type'])
                                                        @case('Cash')
                                                            Cash
                                                        @break

                                                        @case('Pakdul')
                                                            Pakdul
                                                        @break

                                                        @case('Virtual')
                                                            Virtual
                                                        @break

                                                        @default
                                                            Unknown
                                                    @endswitch
                                                </span>
                                            </p>
                                            <p class="font-semibold text-gray-900">
                                                {{ $payment['payment_method'] }}</p>


                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <p class="text-xs text-gray-600">Status</p>
                                            <div
                                                class="{{ $this->getPaymentStatusClass($payment['status']) }} inline-flex font-semibold items-center gap-2">
                                                <i class="{{ $this->getPaymentStatusIcon($payment['status']) }}"></i>
                                                <span>
                                                    @switch($payment['status'])
                                                        @case('refund_requested')
                                                            Refund Requested
                                                        @break

                                                        @case('refund_approved')
                                                            Refund Approved
                                                        @break

                                                        @case('refund_rejected')
                                                            Refund Rejected
                                                        @break

                                                        @default
                                                            {{ ucwords($payment['status']) }}
                                                    @endswitch
                                                </span>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <p class="text-xs text-gray-600">Waktu</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ \Carbon\Carbon::parse($payment['created_at'])->format('d M Y') }}</p>
                                        </div>
                                        <div class="flex gap-3 px-3 py-2 ml-auto">
                                            <button wire:click="showDetailModal('payment', {{ $payment['id'] }})"
                                                wire:loading.attr="disabled"
                                                wire:target="showDetailModal('payment', {{ $payment['id'] }})"
                                                class="py-3 px-4.5 bg-blue-100 text-blue-600 font-medium rounded-lg hover:bg-blue-200 text-lg active:scale-95 transition-all duration-200 border border-blue-200 cursor-pointer disabled:opacity-50">
                                                <i class="fad fa-eye" wire:loading.remove
                                                    wire:target="showDetailModal('payment', {{ $payment['id'] }})"></i>
                                                <i class="fas fa-spinner fa-spin" wire:loading
                                                    wire:target="showDetailModal('payment', {{ $payment['id'] }})"></i>
                                            </button>
                                            @if ($payment['status'] == 'refund_requested')
                                                <button
                                                    wire:click="setupAction('payment', 'approve', {{ $payment['id'] }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="setupAction('payment', 'approve', {{ $payment['id'] }})"
                                                    class="py-3 px-4.5 bg-green-100 text-green-600 font-medium rounded-lg hover:bg-green-200 text-lg active:scale-95 transition-all duration-200 border border-green-200 cursor-pointer disabled:opacity-50">
                                                    <i class="fad fa-money-bill-wave" wire:loading.remove
                                                        wire:target="setupAction('payment', 'approve', {{ $payment['id'] }})"></i>
                                                    <i class="fas fa-spinner fa-spin" wire:loading
                                                        wire:target="setupAction('payment', 'approve', {{ $payment['id'] }})"></i>
                                                </button>
                                            @else
                                                <button
                                                    wire:click="setupAction('payment', 'confirm', {{ $payment['id'] }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="setupAction('payment', 'confirm', {{ $payment['id'] }})"
                                                    class="py-3 px-4.5 bg-green-100 text-green-600 font-medium rounded-lg hover:bg-green-200 text-lg active:scale-95 transition-all duration-200 border border-green-200 cursor-pointer disabled:opacity-50">
                                                    <i class="fad fa-paper-plane" wire:loading.remove
                                                        wire:target="setupAction('payment', 'confirm', {{ $payment['id'] }})"></i>
                                                    <i class="fas fa-spinner fa-spin" wire:loading
                                                        wire:target="setupAction('payment', 'confirm', {{ $payment['id'] }})"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                        <div class="bg-white rounded-xl shadow-lg p-16 text-center border border-gray-200">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            <p class="text-gray-600 text-lg font-medium">Tidak ada pembayaran yang menunggu konfirmasi</p>
                            <p class="text-gray-500 text-sm">Semua pembayaran telah diverifikasi</p>
                        </div>
                    @endforelse
                </div>
            @endif

            <!-- Modal KTP Verify/Reject -->
            @if ($selectedKtp && $modalAction)
                <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
                    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden animate-in">
                        <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 px-6 py-6">
                            <h2 class="text-2xl font-bold text-white">
                                @if ($modalAction === 'verify')
                                    ✓ Verifikasi KTP
                                @else
                                    ✕ Tolak KTP
                                @endif
                            </h2>
                        </div>
                        <div class="p-6">
                            <!-- KTP Info -->
                            <div class="bg-yellow-50 p-4 rounded-lg mb-6 border border-yellow-200">
                                <p class="text-sm text-gray-600">Nama Pengguna</p>
                                <p class="text-lg font-bold text-gray-900">{{ $selectedKtp->name ?? '-' }}</p>
                                <p class="text-sm text-gray-600 mt-2">Email: <span
                                        class="font-semibold">{{ $selectedKtp->email ?? '-' }}</span></p>
                                <p class="text-sm text-gray-600 mt-2">No. KTP: <span
                                        class="font-semibold">{{ $selectedKtp->ktp_number ?? '-' }}</span></p>
                            </div>

                            @if ($modalAction === 'reject')
                                <!-- Reason Field -->
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                                        Alasan Penolakan <span class="text-red-600">*</span>
                                    </label>
                                    <textarea wire:model="actionReason" placeholder="Jelaskan mengapa KTP ini ditolak..." rows="4"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all resize-none"></textarea>
                                    @error('actionReason')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @else
                                <!-- Verification Note -->
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                                        Catatan (Opsional)
                                    </label>
                                    <textarea wire:model="actionNote" placeholder="Tambahkan catatan verifikasi..." rows="3"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all resize-none"></textarea>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex gap-3">
                                <button wire:click="closeModal"
                                    class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-900 font-semibold rounded-lg transition-colors">
                                    Batal
                                </button>
                                @if ($modalAction === 'verify')
                                    <button wire:click="verifyKtp" wire:loading.attr="disabled" wire:target="verifyKtp"
                                        class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50">
                                        <span wire:loading.remove wire:target="verifyKtp">✓ Verifikasi</span>
                                        <span wire:loading wire:target="verifyKtp"><i
                                                class="fas fa-spinner fa-spin mr-1"></i>Memproses...</span>
                                    </button>
                                @else
                                    <button wire:click="rejectKtp" wire:loading.attr="disabled" wire:target="rejectKtp"
                                        class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50">
                                        <span wire:loading.remove wire:target="rejectKtp">✕ Tolak</span>
                                        <span wire:loading wire:target="rejectKtp"><i
                                                class="fas fa-spinner fa-spin mr-1"></i>Memproses...</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modal Approve/Reject Toko -->
            @if ($selectedToko && $modalAction)
                <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
                    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden animate-in">
                        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-6">
                            <h2 class="text-2xl font-bold text-white">
                                @if ($modalAction === 'approve')
                                    Setujui Toko
                                @else
                                    Tolak Toko
                                @endif
                            </h2>
                        </div>
                        <div class="p-6">
                            <!-- Toko Info -->
                            <div class="bg-red-50 p-4 rounded-lg mb-6 border border-red-200">
                                <p class="text-sm text-gray-600">Nama Toko</p>
                                <p class="text-lg font-bold text-gray-900">{{ $selectedToko['name'] ?? '-' }}</p>
                                <p class="text-sm text-gray-600 mt-2">Pemilik: <span
                                        class="font-semibold">{{ $selectedToko['owner']['name'] ?? '-' }}
                                        ({{ '@' }} {{ $selectedToko['owner']['username'] }})</span></p>
                            </div>

                            @if ($modalAction === 'reject')
                                <!-- Reason Field -->
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                                        Alasan Penolakan <span class="text-red-600">*</span>
                                    </label>
                                    <textarea wire:model="actionReason" placeholder="Jelaskan mengapa toko ini ditolak..." rows="4"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all resize-none"></textarea>
                                    @error('actionReason')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex gap-3">
                                <button wire:click="closeModal"
                                    class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-900 font-semibold rounded-lg transition-colors">
                                    Batal
                                </button>
                                @if ($modalAction === 'approve')
                                    <button wire:click="approveToko" wire:loading.attr="disabled" wire:target="approveToko"
                                        class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50">
                                        <span wire:loading.remove wire:target="approveToko">✓ Setujui</span>
                                        <span wire:loading wire:target="approveToko"><i
                                                class="fas fa-spinner fa-spin mr-1"></i>Memproses...</span>
                                    </button>
                                @else
                                    <button wire:click="rejectToko" wire:loading.attr="disabled" wire:target="rejectToko"
                                        class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50">
                                        <span wire:loading.remove wire:target="rejectToko">✕ Tolak</span>
                                        <span wire:loading wire:target="rejectToko"><i
                                                class="fas fa-spinner fa-spin mr-1"></i>Memproses...</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modal Confirm/Reject Payment -->
            @if ($selectedPayment && $modalAction)
                <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
                    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden animate-in">
                        <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-6 py-6">
                            <h2 class="text-2xl font-bold text-white">
                                @if ($modalAction === 'confirm')
                                    Kirim Pesanan
                                @elseif ($modalAction === 'approve')
                                    Telah Direfund
                                @endif
                            </h2>
                        </div>
                        <div class="p-6">
                            <!-- Payment Info -->
                            <div class="bg-orange-50 p-4 rounded-lg mb-6 border border-orange-200">
                                <p class="text-sm text-gray-600">ID Transaksi</p>
                                <p class="text-lg font-bold text-gray-900">{{ $selectedPayment['transaction_id'] ?? '-' }}
                                </p>
                                <p class="text-sm text-gray-600 mt-3">Nominal</p>
                                <p class="text-2xl font-bold text-orange-600">Rp
                                    {{ number_format($selectedPayment['total'] ?? 0, 0, ',', '.') }}</p>
                                <p class="text-sm text-gray-600 mt-3">Dari: <span
                                        class="font-semibold">{{ $selectedPayment['user']['name'] ?? '-' }}</span></p>
                            </div>

                            @if ($modalAction === 'reject')
                                <!-- Reason Field -->
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                                        Alasan Penolakan <span class="text-red-600">*</span>
                                    </label>
                                    <textarea wire:model="actionReason" placeholder="Jelaskan mengapa pembayaran ini ditolak..." rows="4"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all resize-none"></textarea>
                                    @error('actionReason')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @else
                                <!-- Note Field -->
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                                        Catatan (Opsional)
                                    </label>
                                    <textarea wire:model="actionNote" placeholder="Tambahkan catatan jika ada..." rows="3"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all resize-none"></textarea>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex gap-3">
                                <button wire:click="closeModal"
                                    class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-900 font-semibold rounded-lg transition-colors">
                                    Batal
                                </button>
                                @if ($modalAction === 'confirm')
                                    <button wire:click="kirimPesanan" wire:loading.attr="disabled" wire:target="kirimPesanan"
                                        class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50">
                                        <span wire:loading.remove wire:target="kirimPesanan">Kirim</span>
                                        <span wire:loading wire:target="kirimPesanan"><i
                                                class="fas fa-spinner fa-spin mr-1"></i>Memproses...</span>
                                    </button>
                                @elseif ($modalAction === 'approve')
                                    <button wire:click="approveRefund" wire:loading.attr="disabled"
                                        wire:target="approveRefund"
                                        class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50">
                                        <span wire:loading.remove wire:target="approveRefund">Konfirmasi</span>
                                        <span wire:loading wire:target="approveRefund"><i
                                                class="fas fa-spinner fa-spin mr-1"></i>Memproses...</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- DETAIL MODAL KTP -->
            @if ($detailModalType === 'ktp' && $selectedKtp)
                <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50 overflow-y-auto">
                    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full overflow-hidden animate-in my-8">
                        <!-- Header -->
                        <div
                            class="bg-gradient-to-r from-yellow-600 to-yellow-700 px-6 py-6 flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-white">📋 Detail KTP</h2>
                            <button wire:click="closeModal" class="text-white hover:text-yellow-100 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-6 max-h-96 overflow-y-auto">
                            <!-- User Info -->
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center text-white font-bold text-2xl">
                                        {{ substr($selectedKtp->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-gray-900">{{ $selectedKtp->name }}</h3>
                                        <p class="text-sm text-gray-600">@{{ $selectedKtp - > username }}</p>
                                        <p class="text-sm text-gray-600">{{ $selectedKtp->email }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- KTP Data -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-xs text-gray-600 font-semibold">Nomor KTP</p>
                                    <p class="text-sm font-mono font-bold text-gray-900">
                                        {{ $selectedKtp->ktp_number ?? '-' }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-xs text-gray-600 font-semibold">Nama KTP</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $selectedKtp->ktp_name ?? '-' }}</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg col-span-2">
                                    <p class="text-xs text-gray-600 font-semibold">Alamat KTP</p>
                                    <p class="text-sm text-gray-900">{{ $selectedKtp->ktp_address ?? '-' }}</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-xs text-gray-600 font-semibold">No. Telepon</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $selectedKtp->phone_number ?? '-' }}</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-xs text-gray-600 font-semibold">Tanggal Lahir</p>
                                    <p class="text-sm font-bold text-gray-900">
                                        {{ $selectedKtp->date_of_birth?->format('d M Y') ?? '-' }}</p>
                                </div>
                            </div>

                            <!-- KTP Image -->
                            @if ($selectedKtp->ktp_image)
                                <div class="border-2 border-yellow-300 rounded-lg overflow-hidden bg-white p-3">
                                    <p class="text-xs text-gray-600 font-semibold mb-2">📷 Foto KTP</p>
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($selectedKtp->ktp_image) }}"
                                        target="_blank" class="block">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($selectedKtp->ktp_image) }}"
                                            alt="KTP {{ $selectedKtp->ktp_number }}"
                                            class="w-full rounded-lg hover:scale-105 transition-transform cursor-pointer">
                                    </a>
                                </div>
                            @endif

                            <!-- Additional Info -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                    <p class="text-xs text-gray-600 font-semibold">Status</p>
                                    @if ($selectedKtp->ktp_verified)
                                        <span
                                            class="inline-block mt-1 px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">✓
                                            Terverifikasi</span>
                                    @else
                                        <span
                                            class="inline-block mt-1 px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">⏳
                                            Menunggu</span>
                                    @endif
                                </div>
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                    <p class="text-xs text-gray-600 font-semibold">Terdaftar</p>
                                    <p class="text-sm font-bold text-gray-900">
                                        {{ $selectedKtp->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="bg-gray-50 border-t border-gray-200 p-4 flex gap-2 sticky bottom-0">
                            <button wire:click="closeModal"
                                class="flex-1 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold rounded-lg transition-colors">
                                Tutup
                            </button>
                            <button wire:click="setupAction('ktp', 'verify', {{ $selectedKtp->id }})"
                                class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                                ✓ Verifikasi
                            </button>
                            <button wire:click="setupAction('ktp', 'reject', {{ $selectedKtp->id }})"
                                class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                                ✕ Tolak
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- DETAIL MODAL TOKO -->
            @if ($detailModalType === 'toko' && $selectedToko)
                <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50 overflow-y-auto">
                    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full overflow-hidden animate-in my-8">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-6 flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-white">Detail Toko</h2>
                            <button wire:click="closeModal" wire:loading.attr="disabled" wire:loading.remove
                                wire:target="closeModal" class="text-white hover:text-red-100 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <i wire:loading wire:target="closeModal" class="fas fa-spinner fa-spin mr-1 text-white "></i>
                        </div>

                        <div class="p-6 space-y-6 max-h-96 overflow-y-auto">
                            <!-- Toko Basic Info -->
                            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $selectedToko['name'] }}</h3>
                                <p class="text-gray-600 text-sm mb-2">📍 {{ $selectedToko['address'] }}</p>
                                @if ($selectedToko['description'])
                                    <p class="text-gray-700 text-sm">{{ $selectedToko['description'] }}</p>
                                @endif
                            </div>

                            <!-- Owner Info -->
                            <div>
                                <h4 class="font-bold text-gray-900 mb-3">👤 Pemilik Toko</h4>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <p class="font-semibold text-gray-900">{{ $selectedToko['owner']['name'] }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ '@' }}{{ $selectedToko['owner']['username'] }}</p>
                                    <p class="text-sm text-gray-600">{{ $selectedToko['owner']['email'] }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $selectedToko['owner']['phone_number'] }}</p>
                                </div>
                            </div>

                            <!-- Toko Location Map -->
                            @if ($selectedToko['latitude'] && $selectedToko['longitude'])
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-3">📍 Lokasi GPS</h4>
                                    <div class="rounded-lg overflow-hidden border-2 border-gray-200 h-80">
                                        <iframe width="100%" height="100%" frameborder="0" style="border:0"
                                            src="https://www.google.com/maps/embed/v1/view?center={{ $selectedToko['latitude'] }},{{ $selectedToko['longitude'] }}&zoom=18&key={{ config('services.google_maps.api_key') }}"
                                            allowfullscreen="" aria-hidden="false" tabindex="false">
                                        </iframe>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Latitude: {{ $selectedToko['latitude'] }},
                                        Longitude: {{ $selectedToko['longitude'] }}</p>
                                </div>
                            @endif

                            <!-- Employees -->
                            <div>
                                <h4 class="font-bold text-gray-900 mb-3">👥 Karyawan
                                    ({{ $selectedToko['employees_count'] }})
                                </h4>
                                @if (count($selectedToko['employees']) > 0)
                                    <div class="space-y-2 max-h-48 overflow-y-auto">
                                        @foreach ($selectedToko['employees'] as $employee)
                                            <div
                                                class="bg-gray-50 p-3 rounded-lg border border-gray-200 flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                                    {{ substr($employee['name'], 0, 1) }}
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-semibold text-gray-900 text-sm">
                                                        {{ '@' }}{{ $employee['username'] }}</p>
                                                    <p class="text-xs text-gray-600">{{ $employee['jabatan_name'] }}</p>
                                                </div>
                                                <div class="text-right text-xs">
                                                    <p class="text-gray-600">{{ $employee['email'] }}</p>
                                                    <p class="text-gray-600">{{ $employee['phone_number'] }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-sm">Belum ada karyawan terdaftar</p>
                                @endif
                            </div>

                            <!-- Toko Stats -->
                            <div class="grid grid-cols-3 gap-3">
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200 text-center">
                                    <p class="text-2xl font-bold text-blue-600">{{ $selectedToko['employees_count'] }}</p>
                                    <p class="text-xs text-gray-600">Karyawan</p>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg border border-green-200 text-center">
                                    <p class="text-2xl font-bold text-green-600">{{ $selectedToko['products_count'] }}</p>
                                    <p class="text-xs text-gray-600">Produk</p>
                                </div>
                                <div class="bg-purple-50 p-3 rounded-lg border border-purple-200 text-center">
                                    <p class="text-xs font-bold text-gray-600">Terdaftar</p>
                                    <p class="text-sm text-gray-900">{{ $selectedToko['created_at']->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="bg-gray-50 border-t border-gray-200 p-4 flex gap-2 sticky bottom-0">
                            <button wire:click="closeModal"
                                class="flex-1 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold rounded-lg transition-colors">
                                <span wire:loading.remove wire:target="closeModal">Tutup</span>
                                <span wire:loading wire:target="closeModal"><i class="fas fa-spinner fa-spin mr-1"></i>
                                    Loading...</span>
                            </button>
                            <button wire:click="setupAction('toko', 'approve', {{ $selectedToko['id'] }})"
                                class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                                <span wire:loading.remove
                                    wire:target="setupAction('toko', 'approve', {{ $selectedToko['id'] }})">✓
                                    Setujui</span>
                                <span wire:loading wire:target="setupAction('toko', 'approve', {{ $selectedToko['id'] }})"><i
                                        class="fas fa-spinner fa-spin mr-1"></i> Loading...</span>
                            </button>
                            <button wire:click="setupAction('toko', 'reject', {{ $selectedToko['id'] }})"
                                class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                                <span wire:loading.remove
                                    wire:target="setupAction('toko', 'reject', {{ $selectedToko['id'] }})">✕ Tolak</span>
                                <span wire:loading wire:target="setupAction('toko', 'reject', {{ $selectedToko['id'] }})"><i
                                        class="fas fa-spinner fa-spin mr-1"></i> Loading...</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- DETAIL MODAL PAYMENT -->
            @if ($detailModalType === 'payment' && $selectedPayment)
                <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50 overflow-y-auto">
                    <div class="m-4 max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-lg bg-white shadow-xl">
                        <!-- Modal header -->
                        <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white p-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                Payment Details - {{ $selectedPayment['transaction_id'] }}
                            </h3>
                            <button type="button" wire:click="closeModal"
                                class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Payment Information -->
                                <div class="rounded-lg bg-gray-50 p-4">
                                    <h4 class="mb-3 font-medium text-gray-900">Payment Information</h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-500">Transaction ID:</span>
                                            <span class="text-sm font-medium">{{ $selectedPayment['transaction_id'] }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-500">Amount:</span>
                                            <span class="text-sm font-medium">Rp
                                                {{ number_format($selectedPayment['total'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-500">Payment Method:</span>
                                            <span
                                                class="text-sm font-medium">{{ ucfirst($selectedPayment['payment_method']) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-500">Payment Type:</span>
                                            <span class="text-sm font-medium">
                                                @switch($selectedPayment['payment_type'])
                                                    @case('Cash')
                                                        Cash
                                                    @break

                                                    @case('Pakdul')
                                                        Pakdul
                                                    @break

                                                    @case('Virtual')
                                                        Virtual
                                                    @break

                                                    @default
                                                        Unknown
                                                @endswitch
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-500">Status:</span>
                                            <span
                                                class="{{ $this->getPaymentStatusClass($selectedPayment['status']) }} inline-flex rounded-xl px-2 text-sm font-medium">
                                                @switch($selectedPayment['status'])
                                                    @case('refund_requested')
                                                        Refund Requested
                                                    @break

                                                    @case('refund_approved')
                                                        Refund Approved
                                                    @break

                                                    @case('refund_rejected')
                                                        Refund Rejected
                                                    @break

                                                    @default
                                                        {{ ucwords($payment['status']) }}
                                                @endswitch
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-500">Date:</span>
                                            <span
                                                class="text-sm font-medium">{{ $selectedPayment['created_at']->format('d M Y, H:i') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-500">Snap Token:</span>
                                            <span class="max-w-[180px] truncate text-sm font-medium"
                                                title="{{ $selectedPayment['snap_token'] }}">
                                                {{ $selectedPayment['snap_token'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Customer and Store Information -->
                                <div class="rounded-lg bg-gray-50 p-4">
                                    <h4 class="mb-3 font-medium text-gray-900">Customer & Store Information</h4>
                                    <div class="space-y-2">
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-700">Customer</h5>
                                            <div class="mt-1 space-y-1">
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-500">Nama:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $selectedPayment['user']['name'] ?? 'N/A' }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-500">Nomor Hp:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $selectedPayment['user']['phone_number'] ?? 'N/A' }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-500">Email:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $selectedPayment['user']['email'] ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-700">Store</h5>
                                            <div class="mt-1 space-y-1">
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-500">Nama:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $selectedPayment['toko']['name'] ?? 'N/A' }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-500">Alamat:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $selectedPayment['toko']['address'] ?? 'N/A' }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-500">Owner:</span>
                                                    <span
                                                        class="text-sm font-medium">{{ $selectedPayment['toko']['owner']['name'] ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @php
                                $statuses = [
                                    'paid' => ['icon' => 'fa-solid fa-money-bill', 'label' => 'Dibayar'],
                                    'pending' => ['icon' => 'fa-solid fa-clock', 'label' => 'Pending'],
                                    'failed' => [
                                        'icon' => 'fa-solid fa-xmark',
                                        'label' => 'Gagal',
                                    ],
                                    'unknown' => ['icon' => 'fa-solid fa-cog', 'label' => 'Diproses'],
                                    'success' => ['icon' => 'fa-solid fa-check', 'label' => 'Sukses'],
                                    'delivery' => ['icon' => 'fa-solid fa-truck-fast', 'label' => 'Dikirim'],
                                    'cancelled' => ['icon' => 'fa-solid fa-xmark', 'label' => 'Dibatalkan'],
                                    'refund_requested' => [
                                        'icon' => 'fa-solid fa-money-bill-transfer',
                                        'label' => 'Permintaan Pengembalian',
                                    ],
                                    'refunded' => [
                                        'icon' => 'fa-solid fa-money-bill',
                                        'label' => 'Pengembalian Selesai',
                                    ],
                                ];

                                // Get the latest progress status
                                $latestProgress = collect($selectedPayment['progress'])
                                    ->sortByDesc('updated_at')
                                    ->first();
                                $currentStatus = $latestProgress ? $latestProgress['status'] : 'created';
                            @endphp

                            <div class="mt-6">
                                <div tabindex="0" class="collapse-arrow collapse border border-gray-200">
                                    <div class="collapse-title font-semibold">Payment Track</div>
                                    <div class="collapse-content text-sm">
                                        <div class="space-y-4">
                                            <div class="space-y-4">
                                                <div class="flex items-center justify-between text-sm">
                                                    <div class="flex items-center space-x-2">
                                                        <svg class="h-5 w-5 text-yellow-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                        </svg>
                                                        <h4 class="font-medium text-gray-900">Status</h4>
                                                    </div>
                                                    <div
                                                        class="@if (($selectedPayment['pakdulTransaksi']['status'] ?? '') == 'paid') text-green-500 bg-green-100 
                                                    @elseif (($selectedPayment['pakdulTransaksi']['status'] ?? '') == 'active') text-blue-500 bg-blue-100 
                                                    @elseif (($selectedPayment['pakdulTransaksi']['status'] ?? '') == 'overdue') text-red-500 bg-red-100 
                                                    @elseif (($selectedPayment['pakdulTransaksi']['status'] ?? '') == 'cancelled') text-gray-500 bg-gray-100 
                                                    @else text-gray-500 bg-gray-100 @endif rounded font-mono capitalize">
                                                        <span
                                                            class="px-2 py-1">{{ $selectedPayment['pakdulTransaksi']['status'] ?? 'Undefined' }}</span>
                                                    </div>
                                                </div>
                                                <div class="space-y-4">
                                                    @if (isset($selectedPayment['pakdulTransaksi']))
                                                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                            <h4
                                                                class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-900">
                                                                Payment Details</h4>
                                                            <div class="space-y-3">
                                                                <div class="flex justify-between">
                                                                    <span class="text-sm text-gray-600">Jumlah pokok</span>
                                                                    <span class="font-mono text-sm font-medium text-gray-900">
                                                                        Rp
                                                                        {{ number_format($selectedPayment['pakdulTransaksi']['principal_amount'] ?? 0, 0, ',', '.') }}
                                                                    </span>
                                                                </div>
                                                                <div class="flex justify-between">
                                                                    <span class="text-sm text-gray-600">Total yang harus
                                                                        dibayar</span>
                                                                    <span class="font-mono text-sm font-medium text-gray-900">
                                                                        Rp
                                                                        {{ number_format($selectedPayment['pakdulTransaksi']['total_amount'] ?? 0, 0, ',', '.') }}
                                                                    </span>
                                                                </div>
                                                                <div class="flex justify-between">
                                                                    <span class="text-sm text-gray-600">Total yang sudah
                                                                        dibayar</span>
                                                                    <span class="font-mono text-sm font-medium text-gray-900">
                                                                        Rp
                                                                        {{ number_format($selectedPayment['pakdulTransaksi']['paid_amount'] ?? 0, 0, ',', '.') }}
                                                                    </span>
                                                                </div>
                                                                <div class="flex justify-between">
                                                                    <span class="text-sm text-gray-600">Sisa yang harus
                                                                        dibayar</span>
                                                                    <span class="font-mono text-sm font-medium text-gray-900">
                                                                        Rp
                                                                        {{ number_format($selectedPayment['pakdulTransaksi']['remaining_amount'] ?? 0, 0, ',', '.') }}
                                                                    </span>
                                                                </div>
                                                                <div class="flex justify-between">
                                                                    <span class="text-sm text-gray-600">Tanggal jatuh
                                                                        tempo</span>
                                                                    <span class="font-mono text-sm font-medium text-gray-900">
                                                                        {{ \Carbon\Carbon::parse($selectedPayment['pakdulTransaksi']['due_date'])->format('d M Y') }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="overflow-x-auto">
                                                        <table
                                                            class="min-w-full divide-y divide-gray-200 border border-gray-200">
                                                            <thead class="bg-gray-50">
                                                                <tr>
                                                                    <th scope="col"
                                                                        class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                                        Code</th>
                                                                    <th scope="col"
                                                                        class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                                        Amount</th>
                                                                    <th scope="col"
                                                                        class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                                        Method</th>
                                                                    <th scope="col"
                                                                        class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                                        Details</th>
                                                                    <th scope="col"
                                                                        class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                                        Status</th>
                                                                    <th scope="col"
                                                                        class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                                        Date</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-200 bg-white">
                                                                @forelse ($selectedPayment['pakdulPayments'] ?? [] as $data)
                                                                    <tr>
                                                                        <td
                                                                            class="whitespace-nowrap px-4 py-3 text-sm capitalize text-gray-900">
                                                                            {{ $data['payment_code'] ?? 'Undefined' }}</td>
                                                                        <td
                                                                            class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                                                            Rp
                                                                            {{ number_format($data['amount'] ?? 0, 0, ',', '.') }}
                                                                        </td>
                                                                        <td
                                                                            class="whitespace-nowrap px-4 py-3 text-sm capitalize text-gray-500">
                                                                            {{ $data['payment_method'] ?? 'Undefined' }}
                                                                        </td>
                                                                        <td
                                                                            class="whitespace-nowrap px-4 py-3 text-sm capitalize text-gray-500">
                                                                            @if (is_array($data['payment_details']))
                                                                                @foreach ($data['payment_details'] as $key => $value)
                                                                                    <div class="text-xs">
                                                                                        <span
                                                                                            class="font-medium">{{ ucfirst($key) }}:</span>
                                                                                        <span
                                                                                            class="ml-2">{{ $value }}</span>
                                                                                    </div>
                                                                                @endforeach
                                                                            @else
                                                                                {{ $data['payment_details'] ?? 'Undefined' }}
                                                                            @endif
                                                                        </td>
                                                                        <td
                                                                            class="whitespace-nowrap px-4 py-3 text-sm capitalize text-gray-500">
                                                                            {{ $data['status'] ?? 'Undefined' }}</td>
                                                                        <td
                                                                            class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                                                            {{ \Carbon\Carbon::parse($data['paid_at'])->format('d M Y') ?? 'N/A' }}
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="5"
                                                                            class="whitespace-nowrap px-4 py-3 text-center text-sm text-gray-500">
                                                                            Tidak ada data</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6">
                                <div tabindex="0" class="collapse-arrow collapse border border-gray-200">
                                    <div class="collapse-title font-semibold">Status Track</div>
                                    <div class="collapse-content text-sm">
                                        <div class="space-y-4">
                                            <!-- Detailed Progress Timeline -->
                                            <div class="space-y-4">
                                                <!-- Timeline Header -->
                                                <div class="flex items-center justify-between text-sm">
                                                    <div class="flex items-center space-x-2">
                                                        <svg class="h-5 w-5 text-yellow-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                        </svg>
                                                        <h4 class="font-medium text-gray-900">Riwayat Status</h4>
                                                    </div>
                                                    <div class="text-gray-500">
                                                        {{ count($selectedPayment['progress']) }} status
                                                    </div>
                                                </div>

                                                <!-- Timeline Items -->
                                                <div class="space-y-4">
                                                    @php
                                                        $sortedProgress = collect($selectedPayment['progress'])->sortBy(
                                                            'updated_at',
                                                        );
                                                    @endphp
                                                    @foreach ($sortedProgress as $progress)
                                                        <div class="relative pb-4">
                                                            @if (!$loop->last)
                                                                <div class="absolute left-4 top-4 h-full w-0.5 bg-gray-200">
                                                                </div>
                                                            @endif

                                                            <!-- Timeline Dot -->
                                                            <div
                                                                class="absolute flex h-8 w-8 items-center justify-center rounded-full border-2 border-gray-300 bg-white">
                                                                <div
                                                                    class="flex h-6 w-6 items-center justify-center rounded-full bg-green-200">
                                                                    <i
                                                                        class="{{ $statuses[$progress['status']]['icon'] ?? 'fa-solid fa-circle' }} text-green-500"></i>
                                                                </div>
                                                            </div>

                                                            <!-- Timeline Content -->
                                                            <div class="ml-12">
                                                                <div class="flex items-start justify-between">
                                                                    <div class="space-y-1">
                                                                        <div class="flex items-center space-x-2">
                                                                            <div class="font-medium text-gray-900">
                                                                                {{ $progress['keterangan'] }}</div>
                                                                            <div class="text-sm text-gray-500">
                                                                                <i
                                                                                    class="fa-solid fa-check-circle text-green-500"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="text-sm text-gray-500"><i
                                                                                class="fa-solid fa-user text-xs"></i>
                                                                            {{ $progress['user']['name'] ?? 'System' }}
                                                                        </div>
                                                                        <div class="text-xs text-gray-400">
                                                                            <span
                                                                                class="font-medium text-gray-500">{{ \Carbon\Carbon::parse($progress['updated_at'])->format('H:i') }}</span>
                                                                            <span class="mx-1">•</span>
                                                                            <span>{{ \Carbon\Carbon::parse($progress['updated_at'])->format('d M Y') }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="text-right text-xs text-gray-500">
                                                                        <div class="font-medium text-gray-600">
                                                                            {{ \Carbon\Carbon::parse($progress['updated_at'])->diffForHumans() }}
                                                                        </div>
                                                                        <div class="text-gray-400">
                                                                            {{ \Carbon\Carbon::parse($progress['updated_at'])->format('H:i') }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Order Items -->
                            <div class="mt-6">
                                <h4 class="mb-3 font-medium text-gray-900">Order Items
                                    ({{ count($selectedPayment['pesanan']) }})</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col"
                                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Item</th>
                                                <th scope="col"
                                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Quantity</th>
                                                <th scope="col"
                                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Price</th>
                                                <th scope="col"
                                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Subtotal</th>
                                                <th scope="col"
                                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Expired</th>
                                                <th scope="col"
                                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @forelse($selectedPayment['pesanan'] as $order)
                                                <tr>
                                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">
                                                        <div>
                                                            {{ $order['barangki']['barang']['name'] ?? 'Product #' . $order['id'] }}
                                                        </div>
                                                        <div class="text-xs text-gray-400">
                                                            {{ $order['barangki']['id_barcode'] }}
                                                        </div>
                                                    </td>
                                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                                        {{ $order['quantity'] ?? 1 }}</td>
                                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">Rp
                                                        {{ number_format($order['total'] / $order['quantity'] ?? 0, 0, ',', '.') }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">Rp
                                                        {{ number_format($order['total'] ?? 0, 0, ',', '.') }}</td>
                                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                                        {{ $order['barangki']['expired_time']->format('d M Y') ?? 'N/A' }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-4 py-3 text-sm capitalize text-gray-500">
                                                        {{ $order['status'] }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5"
                                                        class="whitespace-nowrap px-4 py-3 text-center text-sm text-gray-500">
                                                        No items found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Action Buttons -->
                            <div class="sticky bottom-5 mt-6 flex justify-end space-x-3">
                                <button wire:click="printInvoice({{ $selectedPayment['id'] }})" wire:loading.attr="disabled"
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    <div class="flex items-center">
                                        <svg wire:loading wire:target="printInvoice"
                                            class="h-5 w-5 animate-spin text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <svg wire:loading.remove wire:target="printInvoice"
                                            class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                            </path>
                                        </svg>
                                    </div>
                                    <span wire:loading wire:target="printInvoice" class="ml-2">Printing...</span>
                                    <span wire:loading.remove wire:target="printInvoice">Print Invoice</span>
                                </button>
                                @if ($selectedPayment['status'] == 'paid')
                                    <button wire:click="setupAction('payment', 'confirm', {{ $selectedPayment['id'] }})"
                                        wire:loading.attr="disabled"
                                        wire:target="setupAction('payment', 'confirm', {{ $selectedPayment['id'] }})"
                                        class="py-3 px-4.5 bg-green-100 text-green-600 font-medium rounded-lg hover:bg-green-200 text-lg active:scale-95 transition-all duration-200 border border-green-200 cursor-pointer disabled:opacity-50">
                                        <i class="fad fa-paper-plane" wire:loading.remove
                                            wire:target="setupAction('payment', 'confirm', {{ $selectedPayment['id'] }})"></i>
                                        <i class="fas fa-spinner fa-spin" wire:loading
                                            wire:target="setupAction('payment', 'confirm', {{ $selectedPayment['id'] }})"></i>
                                        <span wire:loading
                                            wire:target="setupAction('payment', 'confirm', {{ $selectedPayment['id'] }})"
                                            class="ml-2">Loading...</span>
                                        <span wire:loading.remove
                                            wire:target="setupAction('payment', 'confirm', {{ $selectedPayment['id'] }})">Kirim</span>
                                    </button>
                                @elseif($selectedPayment['status'] == 'refund_requested')
                                    <button wire:click="setupAction('payment', 'approve', {{ $selectedPayment['id'] }})"
                                        wire:loading.attr="disabled"
                                        wire:target="setupAction('payment', 'approve', {{ $selectedPayment['id'] }})"
                                        class="py-3 px-4.5 bg-green-100 text-green-600 font-medium rounded-lg hover:bg-green-200 text-lg active:scale-95 transition-all duration-200 border border-green-200 cursor-pointer disabled:opacity-50">
                                        <i class="fad fa-paper-plane" wire:loading.remove
                                            wire:target="setupAction('payment', 'approve', {{ $selectedPayment['id'] }})"></i>
                                        <i class="fas fa-spinner fa-spin" wire:loading
                                            wire:target="setupAction('payment', 'approve', {{ $selectedPayment['id'] }})"></i>
                                        <span wire:loading
                                            wire:target="setupAction('payment', 'approve', {{ $selectedPayment['id'] }})"
                                            class="ml-2">Loading...</span>
                                        <span wire:loading.remove
                                            wire:target="setupAction('payment', 'approve', {{ $selectedPayment['id'] }})">Approve
                                            Refund</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Get status elements
                        const statusContainer = document.getElementById('pusher-status');
                        const statusDot = document.getElementById('status-dot');
                        const statusPulse = document.getElementById('status-pulse');
                        const statusIcon = document.getElementById('status-icon');
                        const statusText = document.getElementById('status-text');

                        // Function to update status UI
                        function updateStatusUI(state) {
                            // Remove all state classes
                            statusContainer.classList.remove('from-gray-100', 'to-gray-50', 'from-green-100', 'to-green-50',
                                'from-red-100', 'to-red-50', 'from-yellow-100', 'to-yellow-50');
                            statusContainer.classList.remove('border-gray-200', 'border-green-300', 'border-red-300',
                                'border-yellow-300');

                            statusDot.classList.remove('bg-gray-400', 'bg-green-500', 'bg-red-500', 'bg-yellow-500');
                            statusPulse.classList.remove('bg-gray-400', 'bg-green-500', 'bg-red-500', 'bg-yellow-500');

                            statusIcon.className = ''; // Reset icon classes

                            switch (state) {
                                case 'connected':
                                    statusContainer.classList.add('from-green-100', 'to-green-50', 'border-green-300');
                                    statusDot.classList.add('bg-green-500');
                                    statusPulse.classList.add('bg-green-500', 'animate-ping');
                                    statusIcon.className = 'fas fa-wifi text-green-600 text-sm';
                                    statusText.className = 'text-sm font-semibold text-green-700';
                                    statusText.textContent = 'Connected';
                                    break;

                                case 'connecting':
                                case 'initialized':
                                    statusContainer.classList.add('from-yellow-100', 'to-yellow-50', 'border-yellow-300');
                                    statusDot.classList.add('bg-yellow-500');
                                    statusPulse.classList.add('bg-yellow-500');
                                    statusIcon.className = 'fas fa-circle-notch fa-spin text-yellow-600 text-sm';
                                    statusText.className = 'text-sm font-semibold text-yellow-700';
                                    statusText.textContent = 'Connecting...';
                                    break;

                                case 'unavailable':
                                case 'failed':
                                case 'disconnected':
                                    statusContainer.classList.add('from-red-100', 'to-red-50', 'border-red-300');
                                    statusDot.classList.add('bg-red-500');
                                    statusPulse.classList.add('bg-red-500');
                                    statusIcon.className = 'fas fa-exclamation-circle text-red-600 text-sm';
                                    statusText.className = 'text-sm font-semibold text-red-700';
                                    statusText.textContent = state === 'failed' ? 'Connection Failed' : 'Disconnected';
                                    break;

                                default:
                                    statusContainer.classList.add('from-gray-100', 'to-gray-50', 'border-gray-200');
                                    statusDot.classList.add('bg-gray-400');
                                    statusPulse.classList.add('bg-gray-400');
                                    statusIcon.className = 'fas fa-circle text-gray-500 text-sm';
                                    statusText.className = 'text-sm font-semibold text-gray-600';
                                    statusText.textContent = 'Unknown';
                            }
                        }

                        // Wait for Echo to be ready
                        if (typeof window.Echo === 'undefined') {
                            console.error('❌ Echo is not initialized. Make sure bootstrap.js is loaded.');
                            updateStatusUI('failed');
                            return;
                        }

                        // Listen to approval dashboard channel
                        window.Echo.channel('approval-dashboard')
                            .listen('.ktp.verification.updated', (e) => {
                                Livewire.dispatch('refresh-ktp-data');
                            })
                            .listen('.toko.verification.updated', (e) => {
                                Livewire.dispatch('refresh-toko-data');
                            })
                            .listen('.payment.verification.updated', (e) => {
                                Livewire.dispatch('refresh-payment-data');
                            });

                        // Monitor connection status with visual feedback
                        window.Echo.connector.pusher.connection.bind('connected', () => {
                            updateStatusUI('connected');
                        });

                        window.Echo.connector.pusher.connection.bind('connecting', () => {
                            updateStatusUI('connecting');
                        });

                        window.Echo.connector.pusher.connection.bind('disconnected', () => {
                            updateStatusUI('disconnected');
                        });

                        window.Echo.connector.pusher.connection.bind('unavailable', () => {
                            updateStatusUI('unavailable');
                        });

                        window.Echo.connector.pusher.connection.bind('failed', () => {
                            updateStatusUI('failed');
                        });

                        window.Echo.connector.pusher.connection.bind('error', (err) => {
                            updateStatusUI('failed');
                        });

                        window.Echo.connector.pusher.connection.bind('state_change', (states) => {
                            updateStatusUI(states.current);
                        });

                        // Set initial state
                        const currentState = window.Echo.connector.pusher.connection.state;
                        updateStatusUI(currentState);
                    });
                </script>
            @endpush
        </div>
