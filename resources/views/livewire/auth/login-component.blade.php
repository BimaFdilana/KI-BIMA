<div style="background-image: url({{ asset('/storage/assets_images/images/carousel/bg1.png') }}); background-size: cover;" class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-red-50 to-indigo-100 p-4">
    <!-- Loading bar -->
    <div class="absolute left-0 top-0 z-50 h-1 w-full">
        <div wire:loading class="h-1 w-full origin-left transform animate-pulse bg-red-500"></div>
    </div>

    <!-- Logo outside card -->
    <div class="mb-6 rounded-full bg-white shadow-lg">
        <i class="fa-solid fa-lock p-3 px-4 text-2xl text-red-600"></i>
    </div>

    <div class="w-full max-w-md">
        <!-- Main login card -->
        <div class="rounded-xl bg-white p-8 shadow-xl transition-all duration-300">
            <!-- Login header -->
            <h2 class="mb-6 text-center text-2xl font-bold text-gray-900">Login</h2>

            <!-- Alerts -->
            @if (session('success'))
                <div class="mb-4 rounded-md border-l-4 border-green-500 bg-green-50 p-4 text-green-700">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-md border-l-4 border-red-500 bg-red-50 p-4 text-red-700">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <form wire:submit="login" class="space-y-6">
                <div class="space-y-4">
                    <!-- Username field -->
                    <div>
                        <label for="username" class="mb-1 block text-sm font-medium text-gray-700">Username, Email, or Phone</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input wire:model="username" id="username" name="username" type="text" autocomplete="username" required class="block w-full rounded-lg border border-gray-300 py-3 pl-10 pr-3 text-gray-900 placeholder-gray-500 focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="Enter your username or email">
                        </div>
                        @error('username')
                            <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password field -->
                    <div>
                        <label for="password" class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input wire:model="password" id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-lg border border-gray-300 py-3 pl-10 pr-3 text-gray-900 placeholder-gray-500 focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="••••••••">
                        </div>
                        @error('password')
                            <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Forgot Password and Login button row -->
                <div class="flex items-center justify-between">
                    <div class="text-sm">
                        <a href="#" class="font-medium text-red-600 hover:text-red-500">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit" class="inline-flex items-center rounded-lg bg-red-600 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300" wire:loading.class="opacity-75" wire:loading.attr="disabled" wire:target="login">
                        <span wire:loading.remove wire:target="login">
                            Login
                        </span>
                        <span wire:loading wire:target="login">
                            <svg aria-hidden="true" role="status" class="mr-2 inline h-4 w-4 animate-spin text-white" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"></path>
                                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"></path>
                            </svg>
                            Loading...
                        </span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Verification Modal -->
    <x-modal wire:model="showVerificationModal">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="rounded-xl bg-white p-6 shadow-xl">
                <div class="mb-6 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                        </svg>
                    </div>
                    <h3 class="mt-3 text-lg font-medium text-gray-900">
                        Verify Your Device
                    </h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Please enter the verification code sent to your {{ $channel == 'whatsapp' ? 'WhatsApp' : ($channel == 'sms' ? 'phone via SMS' : 'email') }}.
                    </p>
                </div>

                <!-- Alert messages -->
                @if (session('message'))
                    <div class="mb-4 rounded-md border-red-500 bg-red-50 p-4 text-red-700">
                        <p>{{ session('message') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-md border-red-500 bg-red-50 p-4 text-red-700">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                <div class="mt-6">
                    <!-- OTP Input -->
                    <div class="mb-5">
                        <div class="flex justify-center space-x-2">
                            @foreach (range(0, 5) as $index)
                                <input wire:model="otp.{{ $index }}" wire:key="otp-{{ $index }}" x-data x-on:keydown.backspace="$el.value === '' && $event.target.previousElementSibling?.focus()" x-on:input="$event.target.value.length === 1 && $event.target.nextElementSibling?.focus()" type="text" maxlength="1" class="h-12 w-10 rounded-lg border border-gray-300 text-center text-lg font-medium text-gray-900 focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                            @endforeach
                        </div>
                    </div>

                    <!-- Remember device option -->
                    <div class="mb-6 flex items-center">
                        <input wire:model="remember" id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Remember 30 Days
                        </label>
                    </div>

                    <!-- Verification button and "Didn't receive code" row -->
                    <div class="mb-4 flex items-center justify-between">
                        <button wire:click="sendVerificationCode" type="button" class="text-sm text-red-600 hover:text-red-500" wire:loading.class="opacity-75" wire:loading.attr="disabled" wire:target="sendVerificationCode">
                            <span wire:loading.remove wire:target="sendVerificationCode">Didn't receive code?</span>
                            <span wire:loading wire:target="sendVerificationCode" class="flex items-center">
                                <svg class="-ml-1 mr-1 h-4 w-4 animate-spin text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sending...
                            </span>
                        </button>

                        <button wire:click="verifyCode" type="button" class="rounded-lg bg-red-600 px-5 py-2 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500" wire:loading.class="opacity-75" wire:loading.attr="disabled" wire:target="verifyCode">
                            <span wire:loading.remove wire:target="verifyCode">Verify</span>
                            <span wire:loading wire:target="verifyCode" class="flex items-center">
                                <svg class="-ml-1 mr-2 h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Verifying...
                            </span>
                        </button>
                    </div>

                    <!-- Alternate methods dropdown -->
                    <div class="mt-6">
                        <div x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="flex w-full items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                                <span>Use another method</span>
                                <svg :class="{ 'rotate-180': open }" class="ml-2 h-5 w-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="mt-2 space-y-2">
                                <!-- Alternative verification methods -->
                                <button wire:click="switchChannel('email')" type="button" class="flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Send via Email
                                </button>

                                <button wire:click="switchChannel('sms')" type="button" class="flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    Send via SMS
                                </button>

                                <button wire:click="toggleRecoveryCodeModal" type="button" class="flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                    Use Recovery Code
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Cooldown timer -->
                    @if ($showCooldown)
                        <div class="mt-4 text-center text-sm text-gray-600" x-data="countdown({{ $cooldown }})">
                            Resend code in:
                            <span class="font-semibold" x-text="formattedTime()"></span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </x-modal>

    <!-- Recovery Code Modal -->
    <x-modal wire:model="showRecoveryCodeModal">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="rounded-xl bg-white p-6 shadow-xl">
                <div class="mb-6 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h3 class="mt-3 text-lg font-medium text-gray-900">
                        Enter Recovery Code
                    </h3>
                    <p class="mt-2 text-sm text-gray-600">
                        If you can't access your verification methods, use the recovery code provided when you set up two-factor authentication.
                    </p>
                </div>

                @if (session('error'))
                    <div class="mb-4 rounded-md border-l-4 border-red-500 bg-red-50 p-4 text-red-700">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                <div class="mt-6">
                    <div class="space-y-6">
                        <div>
                            <label for="recovery-code" class="mb-1 block text-sm font-medium text-gray-700">Recovery code</label>
                            <input wire:model="recoveryCode" id="recovery-code" type="text" class="block w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-500 focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="Enter your recovery code">
                        </div>

                        <div class="flex items-center justify-between">
                            <button wire:click="toggleRecoveryCodeModal" type="button" class="text-sm text-red-600 hover:text-red-500">
                                Back to verification code
                            </button>

                            <button wire:click="verifyRecoveryCode" type="button" class="rounded-lg border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all duration-300 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500" wire:loading.class="opacity-75" wire:loading.attr="disabled" wire:target="verifyRecoveryCode">
                                <span wire:loading.remove wire:target="verifyRecoveryCode">Verify</span>
                                <span wire:loading wire:target="verifyRecoveryCode" class="flex items-center">
                                    <svg class="-ml-1 mr-2 h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Verifying...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-modal>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('countdown', (seconds) => ({
                seconds: seconds,
                interval: null,
                init() {
                    this.startCountdown();

                    // Listen for Livewire event to restart countdown
                    window.addEventListener('start-countdown', (event) => {
                        this.seconds = event.detail.seconds;
                        this.startCountdown();
                    });
                },
                startCountdown() {
                    clearInterval(this.interval);
                    this.interval = setInterval(() => {
                        if (this.seconds > 0) {
                            this.seconds--;
                        } else {
                            clearInterval(this.interval);
                            // Tell Livewire the countdown is finished
                            Livewire.dispatch('cooldownFinished');
                        }
                    }, 1000);
                },
                formattedTime() {
                    const mins = Math.floor(this.seconds / 60);
                    const secs = this.seconds % 60;
                    return `${mins}:${secs.toString().padStart(2, '0')}`;
                }
            }));
        });
    </script>
</div>
