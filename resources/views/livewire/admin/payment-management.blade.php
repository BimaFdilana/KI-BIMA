<div>
    <div class="my-8 overflow-hidden rounded-lg bg-white shadow-lg">
        <!-- Search and filters header -->
        <div
            class="flex flex-col items-center justify-between space-y-4 border-b border-gray-200 p-5 lg:flex-row lg:space-y-0">
            <h1 class="text-2xl font-bold text-gray-800">Pesanan Toko</h1>

            <!-- Search Bar -->
            <div class="relative w-full lg:w-1/3">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
                <input wire:model.live.debounce.500ms="search" type="text"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 pl-10 text-sm text-gray-900 focus:border-red-500 focus:ring-red-500"
                    placeholder="Search any payment information...">
            </div>
        </div>

        <!-- Tab navigation -->
        <div class="bg-white px-2 sm:px-5">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex flex-wrap overflow-x-auto" aria-label="Tabs">
                    @foreach ($statusTabs as $tab => $label)
                        <button wire:click="setStatus('{{ $tab }}')"
                            class="{{ $status === $tab ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap border-b-2 px-3 py-4 text-sm font-medium">
                            {{ $label }}
                            <span
                                class="{{ $status === $tab ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }} ml-1 rounded-sm px-2 py-0.5 text-xs">
                                {{ $statusCounts[$tab] ?? 0 }}
                            </span>
                        </button>
                    @endforeach
                </nav>
            </div>
        </div>


        <!-- Loading indicator for search/filter -->
        <div wire:loading.delay wire:target="search,setStatus" class="flex w-full justify-center py-4">
            <div class="flex w-full justify-center py-4">
                <svg class="h-6 w-6 animate-spin text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>
        </div>

        <!-- Payments list -->
        @if (collect($payments)->isEmpty() && !$loading)
            <div wire:loading.remove wire:target="search,setStatus"
                class="flex flex-col items-center justify-center py-12">
                <svg class="h-16 w-16 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-600">Tidak ada transaksi ditemukan</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if (!empty($search))
                        Tidak ada hasil untuk pencarian "{{ $search }}"
                    @else
                        Tidak ada transaksi dengan status {{ $statusTabs[$status] ?? 'tersebut' }}
                    @endif
                </p>
            </div>
        @else
            <div wire:loading.remove wire:target="search,setStatus" class="overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    <th scope="col" class="py-3 pl-6 text-left">Transaction</th>
                                    <th scope="col" class="py-3 pl-6 text-left">Customer & Store</th>
                                    <th scope="col" class="py-3 pl-7 text-left">Amount</th>
                                    <th scope="col" class="py-3 pl-6 text-left">Payment</th>
                                    <th scope="col" class="py-3 text-center">Status</th>
                                    <th scope="col" class="py-3 text-center">Date</th>
                                    <th scope="col" class="dt-center whitespace-nowrap py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach (collect($payments) as $payment)
                                    <tr class="cursor-pointer hover:bg-gray-50"
                                        wire:click="showDetail({{ $payment->id }})">
                                        <td class="px-4 py-4 text-sm font-medium text-gray-700 sm:px-6">
                                            <div class="flex items-center">
                                                <div
                                                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-red-100">
                                                    <svg class="h-6 w-6 text-red-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div class="ml-3 space-y-1">
                                                    <div class="font-medium text-gray-900">
                                                        {{ $payment->transaction_id }}</div>
                                                    <div class="rounded-sm bg-red-100 px-2 py-0.5 text-xs text-red-500">
                                                        {{ $payment->pesanan->count() }} Jenis Barang
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-sm font-medium text-gray-700 sm:px-6">
                                            <div class="flex items-center">
                                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center">
                                                    @if ($payment->user->profile_photo_path)
                                                        <img src="{{ asset('storage/' . $payment->user->profile_photo_path) }}"
                                                            alt="{{ $payment->user->name }}"
                                                            class="h-10 w-10 rounded-full border border-gray-300 object-cover">
                                                    @else
                                                        <div
                                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200">
                                                            <svg class="h-6 w-6 text-gray-600" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                                </path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-3 space-y-1">
                                                    <div class="font-medium text-gray-900">
                                                        {{ $payment->toko->name ?? 'N/A' }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $payment->user->name ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-left text-sm font-medium text-gray-700 sm:px-6">
                                            <div class="font-semibold">
                                                Rp{{ number_format($payment->total, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-left text-sm font-medium text-gray-700 sm:px-6">
                                            <div class="capitalize">{{ $payment->payment_method }}</div>
                                            <span
                                                class="{{ $this->getPaymentTypeClass($payment->payment_type) }} mt-1 inline-flex rounded-sm px-2 text-xs leading-5">
                                                @switch($payment->payment_type)
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
                                        </td>
                                        <td class="px-4 py-4 text-center text-sm font-medium text-gray-700 sm:px-6">
                                            <span
                                                class="{{ $this->getPaymentStatusClass($payment->status) }}  text-xs ">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-center text-sm text-gray-500 sm:px-6">
                                            <div>{{ $payment->created_at->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-400">
                                                {{ $payment->created_at->format('H:i') }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-center text-sm sm:px-6">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Infinite scroll trigger -->
        @if ($hasMorePages && !collect($payments)->isEmpty())
            <div x-data="{
                observe() {
                        const observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting && !@js($loading)) {
                                    $wire.dispatch('loadMore');
                                }
                            });
                        }, { threshold: 0.1 });
                        observer.observe(this.$el);
                        this.$el._observer = observer;
                    },
                    cleanup() {
                        if (this.$el._observer) {
                            this.$el._observer.disconnect();
                        }
                    }
            }" x-init="observe()" x-on:cleanup.window="cleanup()"
                class="border-t border-gray-200 px-6 py-4 text-center">
                <div wire:loading.remove wire:target="loadMore"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold leading-6 text-red-600 transition duration-150 ease-in-out">
                    <svg wire:loading.remove wire:target="loadMore"
                        class="-ml-1 mr-3 h-5 w-5 animate-spin text-red-600" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span wire:loading.remove wire:target="loadMore,search,setStatus">Loading more...</span>
                    <span wire:loading wire:target="search,setStatus">Loading...</span>
                </div>
            </div>
        @else
            <div class="border-t border-gray-200 px-6 py-4 text-center">
                <div
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold leading-6 text-gray-600 transition duration-150 ease-in-out">
                    <span wire:loading.remove wire:target="loadMore">Sudah Menampilkan Semua Data</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Detail Modal -->
    @if ($selectedPayment && !$selectedEdit)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-data="{
            showModal: true,
            close() {
                this.showModal = false;
                document.body.style.overflow = 'auto';
                $wire.closeDetail();
            }
        }"
            x-show="showModal" x-init="document.body.style.overflow = 'hidden'" x-destroy="document.body.style.overflow = 'auto'"
            @click.self="close()">
            <div class="m-4 max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-lg bg-white shadow-xl">
                <!-- Modal header -->
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white p-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        Payment Details - {{ $selectedPayment->transaction_id }}
                    </h3>
                    <button type="button" @click="close"
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
                                    <span class="text-sm font-medium">{{ $selectedPayment->transaction_id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500">Amount:</span>
                                    <span class="text-sm font-medium">Rp
                                        {{ number_format($selectedPayment->total, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500">Payment Method:</span>
                                    <span
                                        class="text-sm font-medium">{{ ucfirst($selectedPayment->payment_method) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500">Payment Type:</span>
                                    <span class="text-sm font-medium">
                                        @switch($selectedPayment->payment_type)
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
                                        class="{{ $this->getPaymentStatusClass($selectedPayment->status) }} inline-flex rounded-xl px-2 text-sm font-medium">
                                        {{ ucfirst($selectedPayment->status) }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500">Date:</span>
                                    <span
                                        class="text-sm font-medium">{{ $selectedPayment->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500">Snap Token:</span>
                                    <span class="max-w-[180px] truncate text-sm font-medium"
                                        title="{{ $selectedPayment->snap_token }}">
                                        {{ $selectedPayment->snap_token }}
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
                                                class="text-sm font-medium">{{ $selectedPayment->user->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-500">Nomor Hp:</span>
                                            <span
                                                class="text-sm font-medium">{{ $selectedPayment->user->phone_number ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-500">Email:</span>
                                            <span
                                                class="text-sm font-medium">{{ $selectedPayment->user->email ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-700">Store</h5>
                                    <div class="mt-1 space-y-1">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-500">Nama:</span>
                                            <span
                                                class="text-sm font-medium">{{ $selectedPayment->toko->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-500">Alamat:</span>
                                            <span
                                                class="text-sm font-medium">{{ $selectedPayment->toko->address ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-500">Owner:</span>
                                            <span
                                                class="text-sm font-medium">{{ $selectedPayment->toko->owner->name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $statuses = [
                            'created' => ['icon' => 'fa-solid fa-plus', 'label' => 'Dibuat'],
                            'payment_pending' => ['icon' => 'fa-solid fa-clock', 'label' => 'Pembayaran Pending'],
                            'payment_confirmed' => [
                                'icon' => 'fa-solid fa-check',
                                'label' => 'Pembayaran Dikonfirmasi',
                            ],
                            'processing' => ['icon' => 'fa-solid fa-cog', 'label' => 'Diproses'],
                            'shipping' => ['icon' => 'fa-solid fa-truck-fast', 'label' => 'Dikirim'],
                            'delivery' => ['icon' => 'fa-solid fa-truck', 'label' => 'Dalam Pengiriman'],
                            'completed' => ['icon' => 'fa-solid fa-check', 'label' => 'Selesai'],
                            'cancelled' => ['icon' => 'fa-solid fa-xmark', 'label' => 'Dibatalkan'],
                            'refund_requested' => [
                                'icon' => 'fa-solid fa-money-bill-transfer',
                                'label' => 'Permintaan Pengembalian',
                            ],
                            'refunded' => ['icon' => 'fa-solid fa-money-bill', 'label' => 'Pengembalian Selesai'],
                            'paid' => ['icon' => 'fa-solid fa-check-double', 'label' => 'Dibayar'],
                            'pending' => ['icon' => 'fa-solid fa-clock', 'label' => 'Menunggu'],
                            'failed' => ['icon' => 'fa-solid fa-circle-xmark', 'label' => 'Gagal'],
                            'unknown' => ['icon' => 'fa-solid fa-question', 'label' => 'Tidak Diketahui'],
                            'partial_success' => ['icon' => 'fa-solid fa-check-circle', 'label' => 'Sukses Sebagian'],
                            'success' => ['icon' => 'fa-solid fa-check', 'label' => 'Sukses'],
                        ];

                        // Get the latest progress status
                        $latestProgress = $selectedPayment->progress->sortByDesc('updated_at')->first();
                        $currentStatus = $latestProgress ? $latestProgress->status : 'created';
                    @endphp

                    @if ($selectedPayment->payment_type === 'Pakdul')
                        <div class="mt-6 px-4">
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
                                                    class="@if (($selectedPayment->pakdulTransaksi?->status ?? '') == 'paid') text-green-500 bg-green-100 
                                                        @elseif (($selectedPayment->pakdulTransaksi?->status ?? '') == 'active') text-blue-500 bg-blue-100 
                                                        @elseif (($selectedPayment->pakdulTransaksi?->status ?? '') == 'overdue') text-red-500 bg-red-100 
                                                        @elseif (($selectedPayment->pakdulTransaksi?->status ?? '') == 'cancelled') text-gray-500 bg-gray-100 
                                                        @else text-gray-500 bg-gray-100 @endif rounded font-mono capitalize">
                                                    <span
                                                        class="px-2 py-1">{{ $selectedPayment->pakdulTransaksi?->status ?? 'Undefined' }}</span>
                                                </div>
                                            </div>

                                        <div class="space-y-4">
                                            @if (isset($selectedPayment->pakdulTransaksi))
                                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                    <h4
                                                        class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-900">
                                                        Payment Details</h4>
                                                    <div class="space-y-3">
                                                        <div class="flex justify-between">
                                                            <span class="text-sm text-gray-600">Jumlah pokok</span>
                                                            <span class="font-mono text-sm font-medium text-gray-900">
                                                                Rp
                                                                {{ number_format($selectedPayment->pakdulTransaksi?->principal_amount ?? 0, 0, ',', '.') }}
                                                            </span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-sm text-gray-600">Total yang harus
                                                                dibayar</span>
                                                            <span class="font-mono text-sm font-medium text-gray-900">
                                                                Rp
                                                                {{ number_format($selectedPayment->pakdulTransaksi?->total_amount ?? 0, 0, ',', '.') }}
                                                            </span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-sm text-gray-600">Total yang sudah
                                                                dibayar</span>
                                                            <span class="font-mono text-sm font-medium text-gray-900">
                                                                Rp
                                                                {{ number_format($selectedPayment->pakdulTransaksi?->paid_amount ?? 0, 0, ',', '.') }}
                                                            </span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-sm text-gray-600">Sisa yang harus
                                                                dibayar</span>
                                                            <span class="font-mono text-sm font-medium text-gray-900">
                                                                Rp
                                                                {{ number_format($selectedPayment->pakdulTransaksi?->remaining_amount ?? 0, 0, ',', '.') }}
                                                            </span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-sm text-gray-600">Tanggal jatuh
                                                                tempo</span>
                                                            <span class="font-mono text-sm font-medium text-gray-900">
                                                                {{ $selectedPayment->pakdulTransaksi?->due_date ? $selectedPayment->pakdulTransaksi->due_date->format('d M Y') : 'N/A' }}
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
                                                        @forelse ($selectedPayment->pakdulPayments as $data)
                                                            <tr>
                                                                <td
                                                                    class="whitespace-nowrap px-4 py-3 text-sm capitalize text-gray-900">
                                                                    {{ $data->payment_code ?? 'Undefined' }}</td>
                                                                <td
                                                                    class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                                                    Rp
                                                                    {{ number_format($data->amount ?? 0, 0, ',', '.') }}
                                                                </td>
                                                                <td
                                                                    class="whitespace-nowrap px-4 py-3 text-sm capitalize text-gray-500">
                                                                    {{ $data->payment_method ?? 'Undefined' }}</td>
                                                                <td
                                                                    class="whitespace-nowrap px-4 py-3 text-sm capitalize text-gray-500">
                                                                    @if (is_array($data->payment_details))
                                                                        @foreach ($data->payment_details as $key => $value)
                                                                            <div class="text-xs">
                                                                                <span
                                                                                    class="font-medium">{{ ucfirst($key) }}:</span>
                                                                                <span
                                                                                    class="ml-2">{{ $value }}</span>
                                                                            </div>
                                                                        @endforeach
                                                                    @else
                                                                        {{ $data->payment_details ?? 'Undefined' }}
                                                                    @endif
                                                                </td>
                                                                <td
                                                                    class="whitespace-nowrap px-4 py-3 text-sm capitalize text-gray-500">
                                                                    {{ $data->status ?? 'Undefined' }}</td>
                                                                <td
                                                                    class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                                                    {{ $data->paid_at->format('d M Y') ?? 'N/A' }}</td>
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
                    @endif
                    </div>
                    <div class="mt-6 px-4 ">
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
                                                {{ $selectedPayment->progress->count() }} status
                                            </div>
                                        </div>

                                        <!-- Timeline Items -->
                                        <div class="space-y-4">
                                            @php
                                                $sortedProgress = $selectedPayment->progress->sortBy('updated_at');
                                            @endphp
                                            @foreach ($sortedProgress as $progress)
                                                <div class="relative pb-4">
                                                    <!-- Timeline Line -->
                                                    @if (!$loop->last)
                                                        <div class="absolute left-4 top-4 h-full w-0.5 bg-gray-200">
                                                        </div>
                                                    @endif

                                                    <!-- Timeline Dot -->
                                                    <div
                                                        class="absolute z-10 flex h-8 w-8 items-center justify-center rounded-full border-2 border-gray-300 bg-white">
                                                        <div
                                                            class="flex h-6 w-6 items-center justify-center rounded-full bg-green-200">
                                                            <i
                                                                class="{{ $statuses[$progress->status]['icon'] ?? 'fa-solid fa-circle-info' }} text-green-500"></i>
                                                        </div>
                                                    </div>

                                                    <!-- Timeline Content -->
                                                    <div class="ml-12">
                                                        <div class="flex items-start justify-between">
                                                            <div class="space-y-1">
                                                                <div class="flex items-center space-x-2">
                                                                    <div class="font-medium text-gray-900">
                                                                        {{ $progress->keterangan }}</div>
                                                                    <div class="text-sm text-gray-500">
                                                                        <i
                                                                            class="fa-solid fa-check-circle text-green-500"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="text-sm text-gray-500"><i
                                                                        class="fa-solid fa-user text-xs"></i>
                                                                    {{ $progress->user->name }}</div>
                                                                <div class="text-xs text-gray-400">
                                                                    <span
                                                                        class="font-medium text-gray-500">{{ $progress->updated_at->format('H:i') }}</span>
                                                                    <span class="mx-1">•</span>
                                                                    <span>{{ $progress->updated_at->format('d M Y') }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="text-right text-xs text-gray-500">
                                                                <div class="font-medium text-gray-600">
                                                                    {{ $progress->updated_at->diffForHumans() }}</div>
                                                                <div class="text-gray-400">
                                                                    {{ $progress->updated_at->format('H:i') }}</div>
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
                    <div class="mt-6 px-4">
                        <h4 class="mb-3 font-medium text-gray-900">Order Items
                            ({{ $selectedPayment->pesanan->count() }})</h4>
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
                                    @forelse($selectedPayment->pesanan as $order)
                                        <tr>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">
                                                <div>{{ $order->barangki->barang->name ?? 'Product #' . $order->id }}
                                                </div>
                                                <div class="text-xs text-gray-400">{{ $order->barangki->id_barcode }}
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                                {{ $order->quantity ?? 1 }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">Rp
                                                {{ number_format($order->total / $order->quantity ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">Rp
                                                {{ number_format($order->total ?? 0, 0, ',', '.') }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                                {{ $order->barangki->expired_time->format('d M Y') ?? 'N/A' }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm capitalize text-gray-500">
                                                {{ $order->status }}</td>
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
                    <div class="sticky bottom-5 mt-6 flex justify-end space-x-3 px-4">
                        <button wire:click="printInvoice({{ $selectedPayment->id }})" wire:loading.attr="disabled"
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
                        <button type="button" wire:click="showEdit({{ $selectedPayment->id }})"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            <div class="flex items-center">
                                <svg wire:loading wire:target="showEdit" class="h-5 w-5 animate-spin text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <svg wire:loading.remove wire:target="showEdit" class="-ml-1 mr-2 h-5 w-5"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </div>
                            <span wire:loading wire:target="showEdit" class="ml-2">Updating...</span>
                            <span wire:loading.remove wire:target="showEdit">Update</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($selectedEdit)
        <!-- Edit Status Modal -->
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
            x-data="{}" x-init="$el.addEventListener('click', (e) => { if (e.target === $el) $wire.closeEdit() })">

            <div class="m-4 max-h-[95vh] w-full max-w-6xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                <!-- Modal Header with Gradient -->
                <div class="relative bg-gradient-to-r from-red-600 to-red-700 px-6 py-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Payment Details</h3>
                                <p class="text-sm text-red-100">{{ $selectedEdit->transaction_id }}</p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeEdit"
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Content -->
                <div class="max-h-[calc(95vh-120px)] overflow-y-auto">
                    <div class="grid gap-6 p-6 lg:grid-cols-5">
                        <!-- Left Column - Order Items (3/5 width) -->
                        <div class="space-y-4 lg:col-span-3">
                            <!-- Order Items Header -->
                            <div class="flex items-center justify-between">
                                <h4 class="text-lg font-semibold text-gray-900">Order Items</h4>
                                <div class="flex items-center space-x-2">
                                    <button type="button" wire:click="toggleSelectAll"
                                        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ count(array_intersect(collect($this->getFilteredItems())->pluck('id')->toArray(), $selectedItems)) === count($this->getFilteredItems()) ? 'Unselect All' : 'Select All' }}
                                    </button>

                                    <span class="text-sm text-gray-500">
                                        {{ count($selectedItems ?? []) }} of {{ count($selectedEdit->pesanan ?? []) }}
                                        selected
                                    </span>
                                </div>
                            </div>

                            <!-- Search Box -->
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" wire:model.live.debounce.300ms="itemSearch"
                                    class="block w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm text-gray-900 placeholder-gray-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20"
                                    placeholder="Search items by name or barcode...">

                            </div>

                            <!-- Items List with Scroll -->
                            <div class="max-h-96 space-y-3 overflow-y-auto pr-2">
                                @if (isset($selectedEdit->pesanan) && count($selectedEdit->pesanan) > 0)
                                    @foreach ($this->getFilteredItems() as $index => $item)
                                        <div
                                            class="relative rounded-xl border-2 border-gray-200 bg-white p-4 transition-all hover:border-red-300 hover:shadow-md">
                                            <!-- Checkbox -->
                                            <div class="absolute left-4 top-4">
                                                <input type="checkbox" wire:click="toggleSelectItem({{ $item->id }})"
                                                    {{ in_array($item->id, $selectedItems) ? 'checked' : '' }}
                                                    class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 focus:ring-offset-0">
                                            </div>


                                            <!-- Item Content -->
                                            <div class="ml-8">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <h5 class="text-sm font-semibold text-gray-900">
                                                            {{ $item->barangki->barang->name ?? 'Product #' . $item->id }}
                                                        </h5>
                                                        <p class="mt-1 text-xs text-gray-500">
                                                            Barcode:
                                                            {{ $item->barangki->id_barcode ?? 'N/A' }}
                                                        </p>

                                                        <div class="mt-2 flex items-center space-x-4">
                                                            <span class="text-xs text-gray-600">
                                                                Qty: <span
                                                                    class="font-medium">{{ $item->quantity }}</span>
                                                            </span>
                                                            <span class="text-xs text-gray-600">
                                                                Unit: <span class="font-medium text-green-600">Rp
                                                                    {{ number_format($item->price, 0, ',', '.') }}</span>
                                                            </span>
                                                        </div>

                                                    </div>
                                                    <div class="text-right">
                                                        <div class="text-sm font-semibold text-gray-900">
                                                            Rp
                                                            {{ number_format($item->total, 0, ',', '.') }}
                                                        </div>

                                                        <div class="mt-1">
                                                            <span
                                                                class="{{ ($item->status ?? 'pending') === 'completed' ? 'bg-green-100 text-green-800' : (($item->status ?? 'pending') === 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }} inline-flex items-center rounded-full px-2 py-1 text-xs font-medium">
                                                                {{ ucfirst($item->status ?? 'pending') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if (isset($item->notes) && $item->notes)
                                                    <div class="mt-2 rounded-lg bg-gray-50 p-2">
                                                        <p class="text-xs text-gray-600">{{ $item->notes }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <!-- Empty State -->
                                    <div class="py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8v2m4 0V5">
                                            </path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No items found</h3>
                                        <p class="mt-1 text-sm text-gray-500">No items match your search criteria.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Selected Items Summary -->
                            @if (count($selectedItems ?? []) > 0)
                                <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h5 class="text-sm font-medium text-red-900">Selected Items Summary</h5>
                                            <p class="text-sm text-red-700">{{ count($selectedItems) }} items selected
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-semibold text-red-900">
                                                Total: Rp {{ number_format($selectedItemsTotal ?? 0, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Right Column - Payment Info & Status Update (2/5 width) -->
                        <div class="space-y-6 lg:col-span-2">
                            <!-- Transaction Information -->
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <h4 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-900">
                                    Transaction Information</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Transaction ID</span>
                                        <span
                                            class="font-mono text-sm font-medium text-gray-900">{{ $selectedEdit->transaction_id }}</span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Total Amount</span>
                                        <span class="text-sm font-semibold text-red-600">Rp
                                            {{ number_format($selectedEdit->total, 0, ',', '.') }}</span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Payment Method</span>
                                        <span
                                            class="text-sm font-medium capitalize text-gray-900">{{ $selectedEdit->payment_method ?? 'Transfer' }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Status Update Form -->
                            <div class="rounded-xl border-2 border-red-100 bg-red-50/50 p-6">
                                <h4 class="mb-4 flex items-center text-lg font-semibold text-red-900">
                                    <svg class="mr-2 h-5 w-5 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                    Update Status
                                </h4>

                                <!-- Status Selection -->
                                <div class="mb-6">
                                    <label for="selectedStatusAction"
                                        class="mb-2 block text-sm font-semibold text-gray-900">
                                        Payment Status
                                    </label>
                                    <div class="relative">
                                        <select id="selectedStatusAction" wire:model="selectedStatusAction"
                                            class="block w-full appearance-none rounded-xl border-2 border-gray-200 bg-white px-4 py-3 pr-10 text-sm font-medium text-gray-900 transition-all focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                                            <option value="" disabled class="text-gray-500">Select new status
                                            </option>
                                            @foreach ($statusGroups as $groupKey => $group)
                                                @foreach ($group['actions'] as $key => $label)
                                                    <option value="{{ $key }}">{{ $group['label'] }} -
                                                        {{ $label }}</option>
                                                @endforeach
                                            @endforeach
                                        </select>

                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Note -->
                                <div class="mb-6">
                                    <label for="statusNote" class="mb-2 block text-sm font-semibold text-gray-900">
                                        Additional Notes
                                    </label>
                                    <textarea id="statusNote" wire:model="statusNote" rows="4"
                                        class="block w-full rounded-xl border-2 border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 placeholder-gray-400 transition-all focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20"
                                        placeholder="Add any additional notes about this status update..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="closeEdit"
                                class="inline-flex items-center rounded-xl border-2 border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition-all hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500/20">
                                <div class="flex items-center">
                                    <svg wire:loading wire:target="closeEdit"
                                        class="-ml-1 mr-1 h-4 w-4 animate-spin text-gray-600"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </div>
                                <span wire:loading wire:target="closeEdit" class="ml-2">Closing...</span>
                                <span wire:loading.remove wire:target="closeEdit">Cancel</span>
                            </button>
                            <button wire:click="updateStatus" type="button"
                                class="inline-flex items-center rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg transition-all hover:from-red-700 hover:to-red-800 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:ring-offset-2">
                                <div class="flex items-center">
                                    <svg wire:loading wire:target="updateStatus"
                                        class="-ml-1 mr-1 h-4 w-4 animate-spin text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </div>
                                <span wire:loading wire:target="updateStatus" class="ml-2">Updating...</span>
                                <span wire:loading.remove wire:target="updateStatus">Update Status</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Alpine.js for Intersection Observer -->
    <script>
        document.addEventListener('livewire:load', function() {
            if (!window.Alpine) {
                // Add Alpine.js if not already loaded
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js';
                script.defer = true;
                document.head.appendChild(script);

                // Add Intersection Observer polyfill for older browsers
                const intersectionObserver = document.createElement('script');
                intersectionObserver.src =
                    'https://cdn.jsdelivr.net/npm/intersection-observer@0.12.0/intersection-observer.js';
                document.head.appendChild(intersectionObserver);

                // Add Alpine Intersect plugin
                const alpineIntersect = document.createElement('script');
                alpineIntersect.src = 'https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js';
                document.head.appendChild(alpineIntersect);
            }
        });
    </script>
</div>
