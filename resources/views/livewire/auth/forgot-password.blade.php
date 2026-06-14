{{-- resources/views/livewire/auth/forgot-password.blade.php --}}
<div class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0">
    <div class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
        <h2 class="mt-4 text-center text-2xl font-bold">Forgot Password</h2>

        @if (session('error'))
            <div class="mt-4 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700">
                {{ session('error') }}
            </div>
        @endif

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
    </div>
</div>
