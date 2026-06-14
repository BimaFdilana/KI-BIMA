@extends('layouts.admin')

@section('title', 'WhatsApp Devices')

@section('content')
    <div class="mx-auto py-6">
        <div class="container mx-auto py-6">

            <div class="mb-8 flex items-center justify-between rounded-2xl bg-white p-6 shadow-lg">
                <div class="flex items-center space-x-4">
                    <div class="rounded-full bg-blue-500/10 p-3">
                        <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">
                        All Devices
                    </h1>
                </div>
                <a href="{{ route('devices.create') }}"
                    class="inline-flex items-center rounded-full bg-slate-700 px-6 py-3 text-sm font-semibold text-white shadow-lg transition-transform duration-200 hover:scale-105 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New Device
                </a>
            </div>

            @php
                $totalDevices = count($devices);
                $connectedDevices = collect($devices)->where('status', 'connect')->count();
                $disconnectedDevices = $totalDevices - $connectedDevices;
            @endphp
            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
                <div
                    class="flex items-center space-x-4 rounded-2xl bg-white p-6 shadow-md transition-transform duration-200 hover:scale-105">
                    <div class="rounded-full bg-gray-200 p-3 text-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-500">Total Devices</p>
                        <h2 class="text-3xl font-bold text-gray-900">{{ $totalDevices }}</h2>
                    </div>
                </div>

                <div
                    class="flex items-center space-x-4 rounded-2xl bg-white p-6 shadow-md transition-transform duration-200 hover:scale-105">
                    <div class="rounded-full bg-green-100 p-3 text-green-600">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-500">Connected</p>
                        <h2 class="text-3xl font-bold text-green-600">{{ $connectedDevices }}</h2>
                    </div>
                </div>

                <div
                    class="flex items-center space-x-4 rounded-2xl bg-white p-6 shadow-md transition-transform duration-200 hover:scale-105">
                    <div class="rounded-full bg-red-100 p-3 text-red-600">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-500">Disconnected</p>
                        <h2 class="text-3xl font-bold text-red-600">{{ $disconnectedDevices }}</h2>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-800 transition-opacity duration-300 ease-in-out"
                    role="alert" x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show">
                    <div class="flex items-center">
                        <svg class="mr-3 h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <p class="font-bold">Success!</p>
                        <p class="ml-2">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div id="notification" class="mb-6 hidden rounded-md border-l-4 border-green-500 bg-green-100 p-4"
                role="alert">
                <p id="notificationMessage" class="font-semibold"></p>
            </div>

            <div class="overflow-hidden rounded-2xl shadow-xl ring-1 ring-black ring-opacity-5" x-data="{ isOpen: false, qrCode: '', loading: false }">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">#
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Quota</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($devices as $index => $device)
                            <tr class="transition-colors duration-150 hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="whitespace-nowrap px-6 py-4 font-medium text-gray-900">{{ $device['name'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $device['device'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $device['quota'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($device['status'] === 'connect')
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800">
                                            <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            Connected
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-800">
                                            <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            Disconnected
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button
                                            class="flex items-center rounded-lg bg-blue-500 px-4 py-2 text-white transition-colors duration-200 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                            onclick="copyToClipboard('{{ $device['token'] }}')">
                                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2.5a1.5 1.5 0 011.5 1.5v6.5m-6-4.5V17a2 2 0 002 2h2a2 2 0 002-2v-2">
                                                </path>
                                            </svg>
                                            Copy
                                        </button>
                                        @if ($device['status'] === 'connect')
                                            <button
                                                class="flex items-center rounded-lg bg-slate-500 px-4 py-2 text-white transition-colors duration-200 hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2"
                                                onclick="openSendMessageModal('{{ $device['token'] }}')">
                                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.692C5.642 13.987 8.165 12 11 12h2.53a4.5 4.5 0 01-.133-1.895L15 14l-3.244-3.244m2.625-5.91a1 1 0 101.414 1.414L15 8.586l1.293-1.293a1 1 0 10-1.414-1.414L14 7.414l-1.293-1.293z">
                                                    </path>
                                                </svg>
                                                Send
                                            </button>
                                            <button
                                                class="disconnectButton flex items-center rounded-lg bg-red-500 px-4 py-2 text-white transition-colors duration-200 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                                data-device-token="{{ $device['token'] }}"
                                                onclick="disconnectDevice('{{ $device['token'] }}')">
                                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                    </path>
                                                </svg>
                                                Disconnect
                                                <svg class="disconnectSpinner ml-2 hidden h-5 w-5 animate-spin text-white"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                </svg>
                                            </button>
                                        @else
                                            <button @click="activateDevice('{{ $device['token'] }}', $el)"
                                                class="flex items-center rounded-lg bg-green-500 px-4 py-2 text-white transition-colors duration-200 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                                    </path>
                                                </svg>
                                                Connect
                                            </button>
                                        @endif
                                        <button
                                            class="flex items-center rounded-lg bg-red-500 px-4 py-2 text-white transition-colors duration-200 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                            onclick="confirmDelete('{{ $device['token'] }}', '{{ $device['name'] }}')">
                                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @include('devices.partials.modal-qr-code')
            @include('devices.partials.modal-confirmation-delete')
            @include('devices.partials.modal-otp-delete')
            @include('devices.partials.modal-send-message')

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let deviceIdToDelete = null;

        function activateDevice(deviceToken, buttonElement) {
            const alpineContext = Alpine.$data(buttonElement.closest('[x-data]'));
            alpineContext.loading = true;
            alpineContext.isOpen = true;

            fetch('{{ route('devices.activate') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        token: deviceToken
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        alpineContext.qrCode =
                            `<img src="data:image/png;base64,${data.url}" alt="QR Code" class="w-64 h-64 mx-auto" />`;
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while activating the device.');
                })
                .finally(() => {
                    alpineContext.loading = false;
                });
        }

        function disconnectDevice(deviceToken) {
            const disconnectButton = document.querySelector(`.disconnectButton[data-device-token="${deviceToken}"]`);
            const disconnectSpinner = disconnectButton.querySelector('.disconnectSpinner');
            const originalText = disconnectButton.textContent;

            disconnectButton.disabled = true;
            disconnectSpinner.classList.remove('hidden');
            disconnectButton.classList.add('flex', 'items-center', 'justify-center');
            disconnectButton.childNodes[0].nodeValue = 'Disconnecting...';

            fetch('{{ route('devices.disconnect') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        token: deviceToken
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        showNotification('Device successfully disconnected.');
                        setTimeout(() => location.reload(), 2000);
                    } else if (data.error) {
                        alert('Failed to disconnect device: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while disconnecting the device.');
                })
                .finally(() => {
                    disconnectButton.disabled = false;
                    disconnectSpinner.classList.add('hidden');
                    disconnectButton.childNodes[0].nodeValue = originalText.trim();
                    disconnectButton.classList.remove('flex', 'items-center', 'justify-center');
                });
        }

        function confirmDelete(deviceId, deviceName) {
            deviceIdToDelete = deviceId;
            document.getElementById('confirmDeleteMessage').innerText =
                `Are you sure you want to delete the device "${deviceName}"?`;
            document.getElementById('confirmDeleteModal').classList.remove('hidden');
        }

        function closeConfirmDeleteModal() {
            document.getElementById('confirmDeleteModal').classList.add('hidden');
            deviceIdToDelete = null;
        }

        function deleteDevice(otp = null) {
            const errorContainer = document.getElementById('errorContainerOTP');
            const errorMessage = document.getElementById('errorMessageOTP');
            const form = document.getElementById('otpAuthorizationForm');
            const deleteButton = form.querySelector('button');

            if (otp) {
                deleteButton.disabled = true;
                deleteButton.textContent = 'Deleting...';

                axios.delete('/devices/' + deviceIdToDelete, {
                        data: {
                            otp: otp
                        }
                    })
                    .then((response) => {
                        window.location.reload();
                    })
                    .catch((error) => {
                        errorMessage.textContent = error.response.data.error;
                        errorContainer.classList.remove('hidden');
                        deleteButton.disabled = false;
                        deleteButton.textContent = 'Verify & Delete';
                    });
                return;
            }

            if (deviceIdToDelete) {
                document.getElementById('otpDeleteAuthorization').classList.remove('hidden');
                document.getElementById('confirmDeleteModal').classList.add('hidden');
            }
        }

        function openSendMessageModal(deviceToken) {
            document.getElementById('deviceToken').value = deviceToken;
            document.getElementById('sendMessageModal').classList.remove('hidden');
            clearError();
        }

        function closeSendMessageModal() {
            document.getElementById('sendMessageModal').classList.add('hidden');
            clearError();
        }

        function closeOtpDeleteAuthorization() {
            document.getElementById('otpDeleteAuthorization').classList.add('hidden');
            clearError();
        }

        function clearError() {
            const errorContainer = document.getElementById('errorContainer');
            const errorMessage = document.getElementById('errorMessage');
            if (errorContainer && errorMessage) {
                errorContainer.classList.add('hidden');
                errorMessage.textContent = '';
            }
        }

        document.getElementById('otpAuthorizationForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            deleteDevice(formData.get('otp'));
        });

        document.getElementById('sendMessageForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            const deviceToken = formData.get('device_token');
            const sendButton = document.getElementById('sendMessageButton');
            const buttonText = document.getElementById('buttonText');
            const spinner = document.getElementById('spinner');

            buttonText.textContent = 'Sending...';
            spinner.classList.remove('hidden');
            sendButton.disabled = true;

            try {
                const response = await fetch('/ajax/send-message', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Authorization': `Bearer ${deviceToken}`,
                    },
                    body: formData,
                });

                const result = await response.json();

                if (response.ok) {
                    showNotification('Pesan berhasil dikirim!');
                    closeSendMessageModal();
                } else {
                    showError(result.error || 'Gagal mengirim pesan.');
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Terjadi kesalahan. Coba lagi.');
            } finally {
                buttonText.textContent = 'Send';
                spinner.classList.add('hidden');
                sendButton.disabled = false;
            }
        });

        function showNotification(message) {
            const notification = document.getElementById('notification');
            const notificationMessage = document.getElementById('notificationMessage');
            if (notification && notificationMessage) {
                notificationMessage.innerText = message;
                notification.classList.remove('hidden');
                setTimeout(() => {
                    notification.classList.add('hidden');
                }, 3000);
            }
        }

        function showError(message) {
            const errorContainer = document.getElementById('errorContainer');
            const errorMessage = document.getElementById('errorMessage');
            errorMessage.textContent = message;
            errorContainer.classList.remove('hidden');
        }

        function copyToClipboard(token) {
            navigator.clipboard.writeText(token).then(() => {
                showNotification('Token copied to clipboard.');
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }
    </script>
@endpush
