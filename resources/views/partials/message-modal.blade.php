{{-- resources/views/partials/message-modal.blade.php --}}
<div id="message-modal" class="z-60 fixed inset-0 hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50">
    <div class="relative w-full max-w-md">
        <div id="message-modal-container" class="animate-jump-in w-full max-w-md overflow-hidden rounded-lg bg-white shadow-xl transition-all duration-300">
            <div class="p-6">
                <div class="text-center">
                    {{-- Icon Container --}}
                    <div id="iconContainer" class="animate__animated animate__rotateIn my-6 flex justify-center">
                        <i id="modalIcon" class="fas fa-circle-exclamation text-6xl text-red-500"></i>
                    </div>

                    {{-- Message Content --}}
                    <h3 id="modalTitle" class="mb-2 text-2xl font-bold text-gray-800">
                        Something went wrong
                    </h3>
                    <p id="modalMessage" class="mb-6 text-gray-600">
                        Please try again later.
                    </p>

                    {{-- Action Buttons --}}
                    <div id="actionButtons" class="flex justify-center space-x-4">
                        <button id="confirmBtn" onclick="confirmAction()" class="cursor-pointer rounded-lg bg-red-500 px-6 py-2 font-bold text-white transition duration-200 hover:bg-red-600 focus:outline-none focus:ring-4 focus:ring-red-300">
                            Delete
                        </button>
                        <button id="closeMessageModalBtn" onclick="closeModal()" class="cursor-pointer rounded-lg bg-gray-300 px-6 py-2 font-bold text-gray-800 transition duration-200 hover:bg-gray-400 focus:outline-none focus:ring-4 focus:ring-gray-200">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
