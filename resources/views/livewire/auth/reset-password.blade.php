{{-- resources/views/livewire/auth/reset-password.blade.php --}}
<div class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0">
    <div class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
        <h2 class="mt-4 text-center text-2xl font-bold">Reset Password</h2>

        @if (session('error'))
            <div class="mt-4 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if (session('message'))
            <div class="mt-4 rounded border border-blue-400 bg-blue-100 px-4 py-3 text-blue-700">
                {{ session('message') }}
            </div>
        @endif

        @if ($step === 1)
            {{-- Step 1: Find user --}}
            <form wire:submit="findUser" class="mt-6">
                <div class="mt-4">
                    <label for="username" class="block text-sm font-medium text-gray-700">
                        Username, Email or Phone Number
                    </label>
                    <input wire:model="username" id="username" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" placeholder="Enter your username, email or phone number">
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8 flex items-center justify-between">
                    <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                        Back to login
                    </a>
                    <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white ring-indigo-300 transition duration-150 ease-in-out hover:bg-indigo-500 focus:border-indigo-900 focus:outline-none focus:ring active:bg-indigo-900 disabled:opacity-25">
                        Continue
                    </button>
                </div>
            </form>
        @elseif ($step === 2)
            {{-- Step 2: Verify identity --}}
            <div class="mt-6">
                <div class="mb-4 text-sm text-gray-600">
                    Please verify your identity to reset your password.
                </div>

                @if ($useRecoveryCode)
                    {{-- Recovery code form --}}
                    <form wire:submit="verifyRecoveryCode" class="space-y-4">
                        <div>
                            <label for="recovery_code" class="block text-sm font-medium text-gray-700">Recovery Code</label>
                            <input wire:model="recovery_code" id="recovery_code" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" placeholder="Enter your recovery code">
                            @error('recovery_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-between">
                            <button type="button" wire:click="toggleRecoveryCode" class="text-sm text-indigo-600 hover:text-indigo-500">
                                Use verification code instead
                            </button>
                            <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white ring-indigo-300 transition duration-150 ease-in-out hover:bg-indigo-500 focus:border-indigo-900 focus:outline-none focus:ring active:bg-indigo-900 disabled:opacity-25">
                                Verify
                            </button>
                        </div>
                    </form>
                @else
                    {{-- OTP form --}}
                    <div class="mb-4">
                        <label class="mb-2 block text-sm font-medium text-gray-700">Verification Method</label>
                        <div class="flex space-x-2">
                            <button wire:click="switchChannel('whatsapp')" class="{{ $channel === 'whatsapp' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded-md px-4 py-2">
                                WhatsApp
                            </button>
                            <button wire:click="switchChannel('sms')" class="{{ $channel === 'sms' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded-md px-4 py-2">
                                SMS
                            </button>
                            <button wire:click="switchChannel('email')" class="{{ $channel === 'email' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded-md px-4 py-2" {{ !$user->email_verified_at ? 'disabled' : '' }}>
                                Email
                            </button>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="mb-2 block text-sm font-medium text-gray-700">Enter 5-digit code</label>
                        <div class="flex justify-between">
                            @foreach (range(0, 4) as $index)
                                <input wire:model.live="otp.{{ $index }}" wire:key="otp-{{ $index }}" type="text" maxlength="1" class="h-12 w-12 rounded-md border-gray-300 text-center text-xl shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" x-on:keydown.backspace="$event.target.value === '' && $event.target.previousElementSibling?.focus()">
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4 flex justify-between">
                        <button type="button" wire:click="toggleRecoveryCode" class="text-sm text-indigo-600 hover:text-indigo-500">
                            Use recovery code instead
                        </button>

                        @if ($showCooldown)
                            <span class="text-sm text-gray-500">
                                Resend in <span x-data="{ countdown: {{ $cooldown }} }" x-init="setInterval(() => { if (countdown > 0) countdown--; if (countdown === 0) $wire.cooldownFinished(); }, 1000)" x-text="countdown"></span>s
                            </span>
                        @else
                            <button type="button" wire:click="sendVerificationCode" class="text-sm text-indigo-600 hover:text-indigo-500">
                                Resend code
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        @elseif ($step === 3)
            {{-- Step 3: Set new password --}}
            <form wire:submit="resetPassword" class="mt-6">
                <div class="mt-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        New Password
                    </label>
                    <input wire:model="password" id="password" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Confirm Password
                    </label>
                    <input wire:model="password_confirmation" id="password_confirmation" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8 flex items-center justify-end">
                    <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white ring-indigo-300 transition duration-150 ease-in-out hover:bg-indigo-500 focus:border-indigo-900 focus:outline-none focus:ring active:bg-indigo-900 disabled:opacity-25">
                        Reset Password
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
