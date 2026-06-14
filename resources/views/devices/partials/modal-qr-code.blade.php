<!-- Modal untuk menampilkan QR Code -->
<div x-show="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-500 bg-opacity-75 p-4">
    <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
        <!-- Keterangan di bagian atas modal -->
        <div class="mb-4">
            <p class="mb-7 text-center text-lg font-bold">To use WhatsApp on your computer:</p>
            <ol class="ml-4 list-inside list-decimal text-sm text-gray-700">
                <li>Open WhatsApp on your phone</li>
                <li>Tap Menu or Settings and select Linked Devices</li>
                <li>Point your phone to this screen to capture the code</li>
                <li>After your smartphone show success message, you can try to refresh this page and voila the device already can send message now</li>
            </ol>
        </div>

        <!-- QR Code atau loading message -->
        <div x-text="loading ? 'Loading...' : ''"></div>
        <div x-show="qrCode" x-html="qrCode" class="flex items-center justify-center p-4"></div>

        <!-- Container tombol close dengan posisi kanan bawah -->
        <div class="mt-4 flex justify-end">
            <button @click="isOpen = false; qrCode = '';" class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
                Close
            </button>
        </div>
    </div>
</div>
