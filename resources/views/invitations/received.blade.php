@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="overflow-hidden rounded-lg bg-white shadow-md">
            <div class="bg-blue-600 p-4">
                <h1 class="text-2xl font-bold text-white">Undangan Masuk</h1>
            </div>

            @if (session('success'))
                <div class="mb-4 border-l-4 border-green-500 bg-green-100 p-4 text-green-700" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 border-l-4 border-red-500 bg-red-100 p-4 text-red-700" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="p-6">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Daftar Undangan</h2>
                        <p class="text-sm text-gray-600">Undangan untuk bergabung dengan toko</p>
                    </div>

                    <div class="flex space-x-2">
                        <a href="{{ route('invitations.received') }}" class="rounded-lg bg-blue-100 px-3 py-1 text-sm font-medium text-blue-700">
                            Undangan Masuk
                        </a>
                        <a href="{{ route('invitations.sent') }}" class="rounded-lg bg-gray-100 px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-200">
                            Undangan Terkirim
                        </a>
                    </div>
                </div>

                @if (count($invitations) > 0)
                    <div class="space-y-4">
                        @foreach ($invitations as $invitation)
                            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-lg font-bold">{{ $invitation->toko_name }}</h3>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">{{ $invitation->inviter_name }}</span> mengundang Anda untuk bergabung sebagai
                                            <span class="font-medium">{{ $invitation->jabatan_name }}</span>
                                        </p>

                                        @if ($invitation->message)
                                            <div class="mt-2 rounded border border-blue-100 bg-white p-3">
                                                <p class="text-sm italic">{{ $invitation->message }}</p>
                                            </div>
                                        @endif

                                        <p class="mt-2 text-xs text-gray-500">
                                            Dikirim pada {{ \Carbon\Carbon::parse($invitation->created_at)->format('d M Y H:i') }}
                                        </p>
                                    </div>

                                    <div class="flex space-x-2">
                                        <form action="{{ route('invitations.accept', $invitation->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="rounded bg-green-500 px-4 py-2 text-sm text-white hover:bg-green-600">
                                                Terima
                                            </button>
                                        </form>

                                        <form action="{{ route('invitations.reject', $invitation->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="rounded bg-red-500 px-4 py-2 text-sm text-white hover:bg-red-600">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-lg bg-gray-50 p-8 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-700">Tidak Ada Undangan</h3>
                        <p class="mt-2 text-gray-500">Anda belum menerima undangan untuk bergabung dengan toko.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
