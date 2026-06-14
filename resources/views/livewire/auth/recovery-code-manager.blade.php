<!-- resources/views/livewire/auth/recovery-code-manager.blade.php -->
<div class="glass-card rounded-2xl p-6 transition-all hover:shadow-lg">

    <div class="mb-6 flex items-center">
        <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-600">
            <i class="fas fa-shield-alt"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-900">Recovery Code</h3>
    </div>

    <div class="mb-6 text-sm">
        <p class="mb-2 text-gray-700">
            Recovery codes allow you to access your account if you lose your phone or can't receive verification codes.
        </p>
        <p class="mb-2 text-gray-700">
            <span class="font-semibold text-red-500">Important:</span> Keep these codes safe. Each code can only be used once.
        </p>
        @role('writer')
            @if ($lastGeneratedDate)
                <p class="mt-2 text-sm text-gray-500">
                    Last generated: {{ $lastGeneratedDate->format('F j, Y') }}
                    @if ($lastGeneratedDate->addMonth()->isFuture())
                        (New codes can be generated after {{ $lastGeneratedDate->addMonth()->format('F j, Y') }})
                    @endif
                </p>
            @endif
        @endrole
    </div>

    @if ($generationMessage)
        <div class="mb-6 rounded-md border border-green-200 bg-green-50 p-4 text-green-800">
            {{ $generationMessage }}
        </div>
    @endif

    <!-- Recovery Codes Display -->
    @if ($recoveryCodes && count($recoveryCodes) > 0)
        <div class="mb-6">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-base font-semibold">Your Recovery Codes</h3>
                <button wire:click="toggleShowCodes" wire:loading.attr="disabled" class="inline-flex items-center text-sm text-red-600 hover:text-red-800">
                    @if ($showCodes)
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                            <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                        </svg>
                        Hide codes
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                        </svg>
                        Show codes
                    @endif
                </button>
            </div>

            <div class="grid grid-cols-1 gap-4 font-mono">
                @foreach ($recoveryCodes as $code)
                    @if (!$code->hasUsed)
                        <div class="rounded-md border border-gray-200 bg-gray-50 p-3 text-center text-base">
                            @if ($showCodes)
                                {{ $code->code }}
                            @else
                                ••••-••••-•••••
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>

            <p class="mt-2 text-sm text-gray-500">
                {{ collect($recoveryCodes)->where('hasUsed', 0)->count() }} unused codes remaining
            </p>
        </div>
    @else
        <div class="mb-6 rounded-md border border-dashed border-gray-300 py-6 text-center">
            <p class="text-gray-500">You don't have any recovery codes yet</p>
        </div>
    @endif

    <!-- Generate Codes Button -->
    <div class="mt-6">
        @if ($canGenerateNewCodes)
            <button wire:click="initiateCodeGeneration" wire:loading.attr="disabled" class="inline-flex items-center rounded-md bg-red-600 px-5 py-2 text-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300" wire:loading.class="opacity-75">
                <span wire:loading.remove wire:target="initiateCodeGeneration">
                    Generate Recovery Codes
                </span>
                <span wire:loading wire:target="initiateCodeGeneration" class="flex items-center">
                    <svg aria-hidden="true" role="status" class="mr-2 inline h-4 w-4 animate-spin text-white" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"></path>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"></path>
                    </svg>
                    Processing...
                </span>
            </button>
            <p class="mt-1 text-sm text-gray-500">
                @if ($recoveryCodes && count($recoveryCodes) > 0)
                    This will delete your existing codes and generate new ones.
                @endif
            </p>
        @else
            <button class="cursor-not-allowed rounded-md bg-gray-300 px-4 py-2 text-gray-600" disabled>
                Generate New Recovery Codes
            </button>
            <p class="mt-1 text-sm text-gray-500">
                @if ($lastGeneratedDate)
                    You can generate new codes after {{ $lastGeneratedDate->addMonth()->format('F j, Y') }}
                @endif
            </p>
        @endif
    </div>

    <!-- Password Confirmation Modal -->
    @if ($showPasswordConfirmation)
        <div class="fixed inset-0 z-50 flex items-center justify-center rounded-2xl bg-black/50">
            <div class="transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:w-full sm:max-w-lg">
                <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">
                                Confirm Your Password
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    For security, please enter your password to generate new recovery codes.
                                </p>
                                <div class="mt-4">
                                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                    <div class="relative mt-1 rounded-md shadow-sm">
                                        <input type="password" id="password" wire:model.defer="password" class="@error('passwordError') border-red-300 @enderror block w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm" placeholder="Your current password">
                                        @if ($passwordError)
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    @if ($passwordError)
                                        <p class="mt-2 text-sm text-red-600">{{ $passwordError }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" wire:click="verifyPassword" wire:loading.attr="disabled" class="inline-flex items-center rounded-md bg-red-600 px-5 py-2 text-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300" wire:loading.class="opacity-75">
                        <span wire:loading.remove wire:target="verifyPassword">
                            Confirm
                        </span>
                        <span wire:loading wire:target="verifyPassword" class="flex items-center">
                            <svg aria-hidden="true" role="status" class="mr-2 inline h-4 w-4 animate-spin text-white" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"></path>
                                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                    <button type="button" wire:click="cancelPasswordConfirmation" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- OTP Verification Modal -->
    @if ($showOtpVerification)
        <div class="fixed inset-0 z-50 flex items-center justify-center rounded-2xl bg-black/50">
            <div class="mx-4 w-full max-w-md rounded-lg bg-white p-6">
                <h3 class="mb-4 text-lg font-semibold">Verify Your Identity</h3>
                @if ($showInputOtp)
                    <p class="mb-4 text-gray-600">
                        Please enter the OTP code sent to your {{ $otpChannel }}.
                    </p>
                @else
                    <p class="mb-4 text-gray-600">
                        For security, please verify your identity with a one-time code.
                    </p>
                @endif
                @if (!$showInputOtp)
                    <div class="mb-4">
                        <label class="mb-1 block text-sm font-medium text-gray-700">Verification Method</label>
                        <div class="items-center justify-center" x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="flex w-full items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <span>Send via {{ ucfirst($otpChannel) }}</span>
                                <svg :class="{ 'rotate-180': open }" class="ml-2 h-5 w-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="open" x-transition class="mt-2 space-y-2">
                                @if (Auth::user()->email_verified_at)
                                    <button wire:click="changeOtpChannel('email')" type="button" class="{{ $otpChannel == 'email' ? 'bg-red-100 text-red-800 border border-red-300' : 'bg-gray-100' }} flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        Send via Email
                                    </button>
                                @endif
                                @if (Auth::user()->phone_verified_at)
                                    <button wire:click="changeOtpChannel('whatsapp')" type="button" class="{{ $otpChannel == 'whatsapp' ? 'bg-red-100 text-red-800 border border-red-300' : 'bg-gray-100' }} flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        Send via WhatsApp
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if ($showInputOtp)
                    <div class="mb-4">
                        <label class="mb-1 block text-sm font-medium text-gray-700">Enter Verification Code</label>
                        <input type="text" wire:model="otpCode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                @endif

                <div>
                    @if (!$showInputOtp)
                        <div class="flex items-center justify-end">
                            <button wire:click="cancelOtpVerification" class="mr-3 mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:mt-0 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                            <button type="button" wire:click="sendOtp" wire:loading.attr="disabled" class="inline-flex items-center rounded-md bg-red-600 px-5 py-2 text-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300" wire:loading.class="opacity-75">
                                <span wire:loading.remove wire:target="sendOtp">
                                    Send OTP
                                </span>
                                <span wire:loading wire:target="sendOtp" class="flex items-center">
                                    <svg aria-hidden="true" role="status" class="mr-2 inline h-4 w-4 animate-spin text-white" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"></path>
                                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"></path>
                                    </svg>
                                    Sending...
                                </span>
                            </button>
                        </div>
                    @endif
                    @if ($showInputOtp)
                        <div class="mb-4 flex items-center">
                            <p class="mr-2">Not received? </p>
                            <div x-data="cooldownTimer" x-init="startTimer(@js($cooldown))">
                                <p x-show="$wire.showInputOtp">
                                    @if ($showCooldown)
                                        <span x-show="!showResend" x-text="`${formattedTime}`"></span>
                                        <button x-show="showResend" wire:click="$set('showInputOtp', false)" class="text-blue-500 hover:underline">
                                            Resend
                                        </button>
                                    @else
                                        <button wire:click="$set('showInputOtp', false)" class="text-blue-500 hover:underline">
                                            Resend
                                        </button>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-end">
                            <button wire:click="$set('showOtpVerification', false)" class="mr-3 mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:mt-0 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                            <button type="button" wire:click="verifyOtp" wire:loading.attr="disabled" class="inline-flex items-center rounded-md bg-red-600 px-5 py-2 text-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300" wire:loading.class="opacity-75">
                                <span wire:loading.remove wire:target="verifyOtp">
                                    Verify
                                </span>
                                <span wire:loading wire:target="verifyOtp" class="flex items-center">
                                    <svg aria-hidden="true" role="status" class="mr-2 inline h-4 w-4 animate-spin text-white" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"></path>
                                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"></path>
                                    </svg>
                                    Verifying...
                                </span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
<script>
    let cooldownTimer = null;

    // Use Alpine.js for the countdown
    document.addEventListener('alpine:init', () => {
        Alpine.data('cooldownTimer', () => ({
            time: 0,
            formattedTime: '',
            showResend: false,
            startTimer(initialTime) {
                this.time = Math.max(0, Math.floor(initialTime));
                this.updateFormattedTime();
                this.showResend = false;

                if (cooldownTimer) {
                    clearTimeout(cooldownTimer);
                }

                const updateTimer = () => {
                    if (this.time > 0) {
                        this.time--;
                        this.updateFormattedTime();
                        cooldownTimer = setTimeout(updateTimer, 1000);
                    } else {
                        this.showResend = true;
                    }
                };

                updateTimer();
            },
            updateFormattedTime() {
                const minutes = Math.max(0, Math.floor(this.time / 60));
                const seconds = Math.max(0, this.time % 60);

                if (minutes > 0) {
                    // For minutes and seconds
                    this.formattedTime = `${minutes}m ${seconds}s`;
                } else if (seconds > 0) {
                    // For seconds only
                    this.formattedTime = `${seconds}s`;
                } else {
                    this.formattedTime = '0s';
                }
            }
        }));
    });
</script>
