<div class="mx-auto max-w-md rounded-lg bg-white p-6 shadow-md">
    <h2 class="mb-6 text-center text-2xl font-bold">Verify Your Email</h2>

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
        <p class="mb-4">We've sent a verification code to your email:</p>
        <p class="font-semibold">{{ substr(Auth::user()->email, 0, 3) . '****' . substr(Auth::user()->email, strpos(Auth::user()->email, '@')) }}</p>
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
            <button wire:click="sendVerificationCode" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Resend Code
            </button>
        @endif
    </div>

    <div wire:loading class="text-center">
        <div class="inline-block h-5 w-5 animate-spin rounded-full border-b-2 border-t-2 border-gray-900"></div>
        <span class="ml-2">Processing...</span>
    </div>
</div>
