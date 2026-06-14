<!-- Modal for Confirmation -->
<div id="confirmDeleteModal" class="fixed inset-0 z-50 hidden bg-gray-500 bg-opacity-75" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <h2 class="text-lg font-bold">Confirm Delete</h2>
            <p id="confirmDeleteMessage">Are you sure you want to delete this device?</p>
            <div class="mt-4 flex justify-end">
                <button class="rounded bg-red-500 px-4 py-2 text-white hover:bg-red-600" onclick="deleteDevice()">Delete</button>
                <button class="ml-2 rounded border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-100" onclick="closeConfirmDeleteModal()">Cancel</button>
            </div>
        </div>
    </div>
</div>
