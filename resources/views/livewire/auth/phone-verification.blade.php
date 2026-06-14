<div class="mx-auto max-w-md rounded-lg bg-white p-6 shadow-md">
    <h2 class="mb-6 text-center text-2xl font-bold">Verify Your Phone Number</h2>

    @if (session()->has('message'))
        <div class="mb-4 rounded-md bg-blue-100 p-4 text-blue-700">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 rounded-md bg-red-100 p-4 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6 text-center">
        <p class="mb-4">We've sent a verification code to your phone:</p>
        <p class="font-semibold">{{ substr(Auth::user()->phone_number, 0, 4) . '****' . substr(Auth::user()->phone_number, -4) }}</p>
    </div>

    <form wire:submit.prevent="verify">
        <div class="mb-6">
            <label class="mb-2 block text-center">Enter the 5-digit code</label>
            <x-otp-input name="otp" length="5" />
        </div>
    </form>

    <div class="mb-6 text-center">
        @if ($showCooldown)
            <p class="mb-2 text-sm text-gray-600">You can request a new code in:</p>
            <x-countdown-timer :seconds="$cooldown" event="cooldownFinished" />
        @else
            <p class="mb-2 text-sm text-gray-600">Didn't receive the code?</p>
            <div class="flex justify-center space-x-4">
                <button wire:click="switchChannel('whatsapp')" class="{{ $channel === 'whatsapp' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-800' }} rounded-md px-4 py-2 text-sm">
                    Try WhatsApp
                </button>
                <button wire:click="switchChannel('sms')" class="{{ $channel === 'sms' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800' }} rounded-md px-4 py-2 text-sm">
                    Try SMS
                </button>
            </div>
        @endif
    </div>

    <div wire:loading class="text-center">
        <div class="inline-block h-5 w-5 animate-spin rounded-full border-b-2 border-t-2 border-gray-900"></div>
        <span class="ml-2">Processing...</span>
    </div>
</div>
