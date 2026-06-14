@extends('layouts.admin')

@section('title', 'WhatsApp Devices')

@section('content')
    @if ($error)
        <div class="mb-6 rounded-lg bg-red-100 p-4 text-red-800 shadow-sm" role="alert">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="font-bold">{{ $error }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl bg-white p-6 shadow-lg lg:col-span-1">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">Status Akun</h2>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div class="mt-4 space-y-2 text-sm text-gray-600">
                <p>
                    <span class="font-semibold">Deposit:</span>
                    <span class="font-bold text-gray-900">Rp{{ number_format($accountStatus['deposit'] ?? 0, 0, ',', '.') }}</span>
                </p>
                <p>
                    <span class="font-semibold">Pesan:</span>
                    <span class="font-bold text-gray-900">{{ number_format($accountStatus['messages'] ?? 0, 0, ',', '.') }}</span>
                </p>
                <p>
                    <span class="font-semibold">Perangkat:</span>
                    <span class="font-bold text-gray-900">{{ number_format($accountStatus['devices'] ?? 0, 0, ',', '.') }}</span>
                </p>
                <p>
                    <span class="font-semibold">Kuota Pesan:</span>
                    <span class="font-bold text-gray-900">{{ number_format($accountStatus['quota'] ?? 0, 0, ',', '.') }}</span>
                </p>
                <p>
                    <span class="font-semibold">Kuota AI:</span>
                    <span class="font-bold text-gray-900">{{ number_format($accountStatus['ai-quota'] ?? 0, 0, ',', '.') }}</span>
                </p>
            </div>
        </div>

        <div class="flex flex-col gap-4 lg:col-span-2">
            <div class="rounded-xl bg-white p-6 shadow-lg">
                <h2 class="mb-4 text-2xl font-bold text-gray-800">Manajemen Perangkat</h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <button onclick="showAddDeviceModal()" class="w-full rounded-md bg-red-600 px-6 py-3 font-semibold text-white shadow-md transition duration-300 ease-in-out hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        <i class="fas fa-plus-circle mr-2"></i> Tambah Perangkat
                    </button>
                    <button onclick="showSendMessageModal()" class="w-full rounded-md bg-red-600 px-6 py-3 font-semibold text-white shadow-md transition duration-300 ease-in-out hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        <i class="fas fa-paper-plane mr-2"></i> Kirim Pesan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 rounded-2xl bg-white p-6 shadow-xl ring-1 ring-gray-200">
        <h2 class="mb-6 text-2xl font-bold text-gray-800">📱 Daftar Perangkat Anda</h2>

        @if (count($devices) > 0)
            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-b-gray-300 bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Nomor</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Paket</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">AI Data</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Expired</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach ($devices as $device)
                            <tr class="border-b-gray-400 transition hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 font-medium text-gray-900">{{ $device['name'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-gray-600">{{ $device['device'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="@if ($device['status'] === 'connect') bg-green-100 text-green-700
                                        @elseif($device['status'] === 'disconnect') bg-red-100 text-red-700
                                        @else bg-yellow-100 text-yellow-700 @endif inline-flex items-center rounded-full px-3 py-1 text-xs font-medium">
                                        ● {{ ucfirst($device['status']) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-gray-600">
                                    {{ $device['package'] }}
                                    <span class="ml-1 text-gray-500"><i class="bi bi-send"></i> {{ $device['quota'] }}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-gray-600">
                                    {{ $device['ai-data'] }}
                                    <span class="ml-1 text-gray-500"><i class="bi bi-robot"></i> {{ $device['ai-quota'] }}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-gray-600">{{ date('j M Y', $device['expired']) }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button class="inline-flex cursor-pointer items-center rounded-lg bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-200" onclick="copyToken('{{ $device['token'] }}')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0
                                                                                                                                                    012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0
                                                                                                                                                    002-2v-8a2 2 0 00-2-2h-8a2 2 0
                                                                                                                                                    00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            Salin
                                        </button>

                                        @if ($device['status'] === 'disconnect')
                                            <button onclick="showQRModal('{{ $device['device'] }}', '{{ $device['token'] }}')" class="inline-flex cursor-pointer items-center rounded-lg bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700 shadow-sm hover:bg-blue-200">
                                                <i class="fas fa-qrcode mr-1"></i> QR
                                            </button>
                                        @endif
                                        @if ($device['status'] === 'connect')
                                            <button onclick="showConfirmModal('disconnect', '{{ $device['token'] }}', 'Putuskan Koneksi', 'Apakah Anda yakin ingin memutuskan koneksi perangkat {{ $device['name'] }}?')" class="inline-flex cursor-pointer items-center rounded-lg bg-orange-100 px-2 py-1 text-xs font-medium text-orange-700 shadow-sm hover:bg-orange-200">
                                                <i class="fas fa-plug mr-1"></i> Putus
                                            </button>
                                        @endif
                                        <button onclick="showDeleteModal('{{ $device['token'] }}')" class="inline-flex cursor-pointer items-center rounded-lg bg-red-100 px-2 py-1 text-xs font-medium text-red-700 shadow-sm hover:bg-red-200">
                                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="py-10 text-center text-gray-500">🚫 Belum ada perangkat yang ditambahkan.</p>
        @endif
    </div>

    <!-- Confirm Modal -->
    <div id="confirm-modal" class="z-60 fixed inset-0 hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50">
        <div class="relative w-full max-w-md">
            <div id="message-modal-container" class="animate-jump-in w-full max-w-md overflow-hidden rounded-lg bg-white shadow-xl transition-all duration-300">
                <div class="p-6">
                    <div class="text-center">
                        <!-- Icon Container -->
                        <div id="iconContainer" class="animate__animated animate__rotateIn my-6 flex justify-center">
                            <i id="modalIcon" class="fas fa-circle-exclamation text-6xl text-red-500"></i>
                        </div>
                        <!-- Message -->
                        <h3 id="modalTitle" class="mb-2 text-2xl font-bold text-gray-800">Konfirmasi Aksi</h3>
                        <p id="modalMessage" class="mb-6 text-gray-600">
                            Apakah Anda yakin ingin melakukan aksi ini?
                        </p>
                        <input type="hidden" id="confirmAction" value="">
                        <input type="hidden" id="confirmToken" value="">
                        <!-- Buttons -->
                        <div id="actionButtons" class="flex justify-center space-x-4">
                            <button id="actionBtnMessage" class="cursor-pointer rounded-lg bg-red-500 px-6 py-2 font-bold text-white transition duration-200 hover:bg-red-600">
                                Ya, Lanjutkan
                            </button>
                            <button id="closeBtnMessage" class="cursor-pointer rounded-lg bg-gray-300 px-6 py-2 font-bold text-gray-800 transition duration-200 hover:bg-gray-400">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div id="loadingModal" class="z-60 fixed inset-0 hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50">
        <div class="flex min-h-screen items-center justify-center">
            <div class="rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-center space-x-3">
                    <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-red-600"></div>
                    <span class="font-medium text-gray-700">Memproses...</span>
                </div>
            </div>
        </div>
    </div>

    <div id="addDeviceModal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-black/50 p-4">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-2xl">
            <h3 class="mb-4 text-2xl font-bold text-gray-800">Tambah Perangkat Baru</h3>
            <div id="addDeviceMessage" class="mb-4 hidden"></div>
            <form id="addDeviceForm" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Perangkat</label>
                    <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-gray-300 p-2 shadow-sm focus:border-red-500 focus:ring-red-500">
                </div>
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700">Nomor WhatsApp (misal: 081234567890)</label>
                    <input type="text" name="phone_number" id="phone_number" required class="mt-1 block w-full rounded-md border-gray-300 p-2 shadow-sm focus:border-red-500 focus:ring-red-500">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="hideModal('addDeviceModal')" class="rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">Batal</button>
                    <button type="submit" class="rounded-md bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700">Tambahkan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="sendMessageModal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-black/50 p-4">
        <div class="w-full max-w-2xl rounded-xl bg-white p-6 shadow-2xl">
            <h3 class="mb-4 text-2xl font-bold text-gray-800">Kirim Pesan</h3>
            <div id="sendMessageMessage" class="mb-4 hidden"></div>
            <form id="sendMessageForm" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipe Pengiriman</label>
                    <div class="mt-1 flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="message_type" value="normal" class="message-type-radio form-radio text-red-600" checked onchange="updateMessageForm()">
                            <span class="ml-2 text-gray-700">Normal</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="message_type" value="bulk" class="message-type-radio form-radio text-red-600" onchange="updateMessageForm()">
                            <span class="ml-2 text-gray-700">Bulk (Masukan nomor satu per baris)</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="phone_numbers" class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                    <textarea name="phone_numbers" id="phone_numbers" required rows="3" class="mt-1 block w-full rounded-md border-gray-300 p-2 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="081234567890"></textarea>
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700">Isi Pesan</label>
                    <textarea name="message" id="message" required rows="5" class="mt-1 block w-full rounded-md border-gray-300 p-2 shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
                </div>
                <div>
                    <label for="device_token_send" class="block text-sm font-medium text-gray-700">Pilih Perangkat Pengirim</label>
                    <select name="device_token" id="device_token_send" class="mt-1 block w-full rounded-md border-gray-300 p-2 shadow-sm focus:border-red-500 focus:ring-red-500">
                        @foreach ($devices as $device)
                            @if ($device['status'] === 'connect')
                                <option value="{{ $device['token'] }}">{{ $device['name'] }} - {{ $device['device'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="hideModal('sendMessageModal')" class="rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">Batal</button>
                    <button type="submit" class="rounded-md bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700">Kirim</button>
                </div>
            </form>
        </div>
    </div>

    <div id="qrModal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-black/50 p-4">
        <div class="w-full max-w-sm rounded-xl bg-white p-8 shadow-2xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">Pindai QR Code</h3>
            </div>
            <p class="mb-4 text-sm text-gray-600">Buka WhatsApp > Perangkat Tertaut > Tautkan Perangkat. Lalu pindai kode ini.</p>
            <div id="qrImageContainer" class="flex h-64 items-center justify-center rounded-lg bg-gray-100">
                <p id="qrLoadingText" class="text-gray-400">Memuat QR Code...</p>
                <img id="qrImage" alt="QR Code" class="hidden h-full w-full object-contain p-4">
            </div>
            <div class="mt-6 flex justify-end space-x-2">

                <div id="connectionStatus" class="hidden">
                    <span id="statusIndicator" class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium">
                        <span id="statusDot" class="mr-1">●</span>
                        <span id="statusText">Menunggu...</span>
                    </span>
                </div>
                <button id="refreshQRBtn" onclick="refreshQRCode()" class="hidden rounded-md bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">Refresh QR</button>
                <button onclick="hideQRModal()" class="rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">Tutup</button>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-black/50 p-4">
        <div class="w-full max-w-sm rounded-xl bg-white p-8 shadow-2xl">
            <h3 class="mb-4 text-2xl font-bold text-gray-800">Hapus Perangkat</h3>
            <p class="mb-4 text-sm text-gray-600">Untuk menghapus perangkat, Anda perlu memasukkan kode OTP yang dikirim ke nomor WhatsApp Anda.</p>
            <div id="deleteMessage" class="mb-4 hidden"></div>
            <form id="deleteForm" class="space-y-4">
                @csrf
                <input type="hidden" name="device_token" id="deleteDeviceToken">
                <div>
                    <label for="otp" class="block text-sm font-medium text-gray-700">Kode OTP</label>
                    <input type="text" name="otp" id="otp" required class="mt-1 block w-full rounded-md border-gray-300 p-2 shadow-sm focus:border-red-500 focus:ring-red-500">
                </div>
                <div class="mt-6 flex justify-end space-x-2">
                    <button type="button" onclick="hideModal('deleteModal')" class="rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">Batal</button>
                    <button type="submit" class="rounded-md bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700">Hapus</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Global variables untuk QR monitoring
        let qrCheckInterval;
        let currentQRToken = null;
        let currentPhoneNumber = null;

        // Fungsi dasar untuk mengontrol modal
        function showModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function hideModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        }

        // Fungsi untuk confirm modal
        function showConfirmModal(action, token, title, message) {
            document.getElementById('confirmAction').value = action;
            document.getElementById('confirmToken').value = token;
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalMessage').innerText = message;

            // Set icon based on action
            const icon = document.getElementById('modalIcon');
            if (action === 'disconnect') {
                icon.className = 'fas fa-plug text-6xl text-orange-500';
            } else {
                icon.className = 'fas fa-circle-exclamation text-6xl text-red-500';
            }

            showModal('confirm-modal');
        }

        function hideConfirmModal() {
            hideModal('confirm-modal');
        }

        function showLoadingModal() {
            showModal('loadingModal');
        }

        function hideLoadingModal() {
            hideModal('loadingModal');
        }

        // Fungsi untuk memuat ulang halaman setelah aksi sukses
        function refreshDashboard() {
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        // Menambahkan event listener ke window untuk menutup modal saat klik di luar area
        window.onclick = function(event) {
            const modals = ['addDeviceModal', 'sendMessageModal', 'qrModal', 'deleteModal', 'confirm-modal', 'loadingModal'];
            modals.forEach(id => {
                const modal = document.getElementById(id);
                if (event.target === modal) {
                    if (id === 'qrModal') {
                        hideQRModal();
                    } else if (id === 'confirm-modal') {
                        hideConfirmModal();
                    } else {
                        hideModal(id);
                    }
                }
            });
        }

        // Mengubah placeholder textarea pengiriman pesan
        function updateMessageForm() {
            const type = document.querySelector('input[name="message_type"]:checked').value;
            const textarea = document.getElementById('phone_numbers');
            if (type === 'bulk') {
                textarea.placeholder = '081234567890\n089876543210\n...';
                textarea.rows = 5;
            } else {
                textarea.placeholder = '081234567890';
                textarea.rows = 3;
            }
        }

        // Logika untuk menampilkan modal tambah perangkat
        function showAddDeviceModal() {
            const canAddDevice = {{ $canAddDevice ? 'true' : 'false' }};
            const form = document.getElementById('addDeviceForm');
            const messageDiv = document.getElementById('addDeviceMessage');
            messageDiv.classList.add('hidden');
            form.reset();

            if (!canAddDevice) {
                form.classList.add('hidden');
                messageDiv.classList.remove('hidden');
                messageDiv.classList.add('bg-red-100', 'p-3', 'text-sm', 'text-red-700');
                messageDiv.innerHTML = 'Anda sudah memiliki satu perangkat gratis yang terhubung. Harap putuskan koneksi perangkat yang ada untuk menambahkan yang baru.';
            } else {
                form.classList.remove('hidden');
            }
            showModal('addDeviceModal');
        }

        // Fungsi untuk memonitor status koneksi
        function startConnectionMonitoring(token, phoneNumber) {
            const statusIndicator = document.getElementById('connectionStatus');
            const statusDot = document.getElementById('statusDot');
            const statusText = document.getElementById('statusText');
            const refreshBtn = document.getElementById('refreshQRBtn');

            statusIndicator.classList.remove('hidden');
            statusIndicator.className = 'bg-yellow-100 text-yellow-700 inline-flex items-center rounded-full px-2 py-1 text-xs font-medium';
            statusText.innerText = 'Menunggu scan...';

            qrCheckInterval = setInterval(() => {
                fetch('{{ route('fonnte.check-connection') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            device_token: token,
                            phone_number: phoneNumber
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.status === 'connect') {
                                // Berhasil terhubung
                                statusIndicator.className = 'bg-green-100 text-green-700 inline-flex items-center rounded-full px-2 py-1 text-xs font-medium';
                                statusText.innerText = 'Terhubung!';

                                clearInterval(qrCheckInterval);

                                // Tampilkan notifikasi sukses
                                showToast("success", "WhatsApp berhasil terhubung! Perangkat sudah siap digunakan.", "Success!");

                                // Tutup modal setelah delay
                                setTimeout(() => {
                                    hideQRModal();
                                    refreshDashboard();
                                }, 2000);
                            } else if (data.status === 'disconnect') {
                                // Masih belum terhubung, tapi QR mungkin expired
                                const qrImage = document.getElementById('qrImage');
                                if (!qrImage.src || data.qr_expired) {
                                    statusIndicator.className = 'bg-red-100 text-red-700 inline-flex items-center rounded-full px-2 py-1 text-xs font-medium';
                                    statusText.innerText = 'QR Expired';
                                    refreshBtn.classList.remove('hidden');
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error checking connection:', error);
                    });
            }, 3000); // Check setiap 3 detik
        }

        // Fungsi untuk refresh QR Code
        function refreshQRCode() {
            if (currentQRToken && currentPhoneNumber) {
                document.getElementById('refreshQRBtn').classList.add('hidden');
                showQRModal(currentPhoneNumber, currentQRToken);
            }
        }

        // Logika untuk menampilkan modal QR Code
        function showQRModal(phoneNumber, token) {
            currentQRToken = token;
            currentPhoneNumber = phoneNumber;

            const qrImage = document.getElementById('qrImage');
            const qrLoadingText = document.getElementById('qrLoadingText');
            const connectionStatus = document.getElementById('connectionStatus');
            const refreshBtn = document.getElementById('refreshQRBtn');

            // Reset UI
            qrImage.classList.add('hidden');
            qrImage.src = '';
            qrLoadingText.classList.remove('hidden');
            connectionStatus.classList.add('hidden');
            refreshBtn.classList.add('hidden');

            // Clear interval sebelumnya jika ada
            if (qrCheckInterval) {
                clearInterval(qrCheckInterval);
            }

            showModal('qrModal');

            // Panggil API untuk mendapatkan QR Code
            fetch('{{ route('fonnte.request-qr') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        phone_number: phoneNumber,
                        device_token: token
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        qrImage.src = 'data:image/png;base64,' + data.url;
                        qrImage.classList.remove('hidden');
                        qrLoadingText.classList.add('hidden');

                        // Mulai monitoring koneksi
                        startConnectionMonitoring(token, phoneNumber);
                    } else {
                        hideQRModal();
                        showToast("error", "Gagal mendapatkan QR Code: " + (data.error || 'Terjadi kesalahan.'), "Error!");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    hideQRModal();
                    showToast("error", "Terjadi kesalahan saat meminta QR Code.", "Error!");
                });
        }

        // Fungsi untuk menutup QR Modal
        function hideQRModal() {
            if (qrCheckInterval) {
                clearInterval(qrCheckInterval);
                qrCheckInterval = null;
            }
            currentQRToken = null;
            currentPhoneNumber = null;
            hideModal('qrModal');
        }

        // Logika untuk menampilkan modal Hapus Perangkat
        function showDeleteModal(token) {
            document.getElementById('deleteDeviceToken').value = token;
            const messageDiv = document.getElementById('deleteMessage');
            messageDiv.classList.add('hidden');
            document.getElementById('deleteForm').reset();
            showModal('deleteModal');

            fetch('{{ route('fonnte.request-delete-otp') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        device_token: token
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        messageDiv.innerText = 'Gagal meminta OTP: ' + (data.error || 'Terjadi kesalahan.');
                        messageDiv.classList.remove('hidden');
                        messageDiv.classList.add('bg-red-100', 'p-2', 'text-sm', 'text-red-700');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    messageDiv.innerText = 'Terjadi kesalahan saat meminta OTP.';
                    messageDiv.classList.remove('hidden');
                    messageDiv.classList.add('bg-red-100', 'p-2', 'text-sm', 'text-red-700');
                });
        }

        // Logika untuk menampilkan modal Kirim Pesan
        function showSendMessageModal() {
            const messageDiv = document.getElementById('sendMessageMessage');
            messageDiv.classList.add('hidden');
            document.getElementById('sendMessageForm').reset();
            updateMessageForm();
            showModal('sendMessageModal');
        }

        // Fungsi untuk menyalin token
        function copyToken(token) {
            navigator.clipboard.writeText(token).then(() => {
                showToast("success", "Token berhasil disalin!", "Success!");
            }).catch(err => {
                console.error('Gagal menyalin token:', err);
                showToast("error", "Gagal menyalin token.", "Error!");
            });
        }

        // Fungsi untuk handle disconnect via AJAX
        function handleDisconnect(token) {
            showLoadingModal();

            fetch('{{ route('fonnte.disconnect-device') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        device_token: token
                    })
                })
                .then(response => response.json())
                .then(data => {
                    hideLoadingModal();
                    if (data.success) {
                        showToast("success", data.message, "Success!");
                        refreshDashboard();
                    } else {
                        showToast("error", data.error, "Error!");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    hideLoadingModal();
                    showToast("error", "Terjadi kesalahan saat memutus perangkat.", "Error!");
                });
        }

        // Event listener untuk confirm modal
        document.getElementById('actionBtnMessage').addEventListener('click', function() {
            const action = document.getElementById('confirmAction').value;
            const token = document.getElementById('confirmToken').value;

            hideConfirmModal();

            if (action === 'disconnect') {
                handleDisconnect(token);
            }
        });

        document.getElementById('closeBtnMessage').addEventListener('click', function() {
            hideConfirmModal();
        });

        // Menambahkan event listener untuk form
        document.getElementById('addDeviceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const messageDiv = document.getElementById('addDeviceMessage');

            showLoadingModal();

            fetch('{{ route('fonnte.add-device') }}', {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoadingModal();
                    if (data.success) {
                        hideModal('addDeviceModal');
                        showToast("success", data.message, "Success!");
                        refreshDashboard();
                    } else {
                        messageDiv.classList.remove('hidden');
                        messageDiv.classList.add('bg-red-100', 'p-3', 'text-sm', 'text-red-700');
                        messageDiv.innerHTML = data.error;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    hideLoadingModal();
                    showToast("error", "Terjadi kesalahan saat menambahkan perangkat.", "Error!");
                });
        });

        document.getElementById('sendMessageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const messageDiv = document.getElementById('sendMessageMessage');

            showLoadingModal();

            fetch('{{ route('fonnte.send-message') }}', {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoadingModal();
                    if (data.success) {
                        hideModal('sendMessageModal');
                        showToast("success", data.message, "Success!");
                    } else {
                        messageDiv.classList.remove('hidden');
                        messageDiv.classList.add('bg-red-100', 'p-3', 'text-sm', 'text-red-700');
                        messageDiv.innerHTML = data.error;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    hideLoadingModal();
                    showToast("error", "Terjadi kesalahan saat mengirim pesan.", "Error!");
                });
        });

        document.getElementById('deleteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const messageDiv = document.getElementById('deleteMessage');

            showLoadingModal();

            fetch('{{ route('fonnte.delete-device') }}', {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoadingModal();
                    if (data.success) {
                        hideModal('deleteModal');
                        showToast("success", data.message, "Success!");
                        refreshDashboard();
                    } else {
                        messageDiv.classList.remove('hidden');
                        messageDiv.classList.add('bg-red-100', 'p-2', 'text-sm', 'text-red-700');
                        messageDiv.innerHTML = data.error;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    hideLoadingModal();
                    showToast("error", "Terjadi kesalahan saat menghapus perangkat.", "Error!");
                });
        });

        // Panggil fungsi updateMessageForm saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            updateMessageForm();
        });
    </script>
@endpush
