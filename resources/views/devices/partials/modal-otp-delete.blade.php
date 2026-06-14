<div id="otpDeleteAuthorization" class="fixed inset-0 z-50 hidden bg-gray-500 bg-opacity-75" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <h2 class="text-lg font-bold">Otorisasi</h2>
            <p id="confirmDeleteMessage">Please insert the OTP code that sent to your registered number.</p>
            <div id="errorContainerOTP" class="mb-4 hidden">
                <p class="font-medium text-red-500" id="errorMessageOTP"></p>
            </div>
            <form id="otpAuthorizationForm">
                <input class="w-full rounded border px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" name="otp" required />
                <div class="mt-4 flex justify-end">
                    <button class="rounded bg-red-500 px-4 py-2 text-white hover:bg-red-600" type="submit">Confirm</button>
                    <button class="ml-2 rounded border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-100" type="button" onclick="closeOtpDeleteAuthorization()">Cancel</button>
                </div>
        </div>
        </form>
    </div>
</div>
