@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="overflow-hidden rounded-lg bg-white shadow-md">
            <div class="flex items-center justify-between bg-blue-600 p-4">
                <h1 class="text-2xl font-bold text-white">Daftar Pengguna</h1>
                <div>
                    <a href="{{ route('admin.users') }}" class="rounded-lg bg-white px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-100">
                        Kembali ke Panel Admin
                    </a>
                </div>
            </div>

            <div class="p-6">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Semua Pengguna</h2>
                        <p class="text-sm text-gray-600">Menampilkan {{ $users->total() }} pengguna</p>
                    </div>

                    <div class="flex space-x-2">
                        <a href="{{ route('admin.profiles.index') }}" class="rounded-lg bg-blue-100 px-3 py-1 text-sm font-medium text-blue-700">
                            Semua Profil
                        </a>
                        <a href="{{ route('admin.profiles.incomplete') }}" class="rounded-lg bg-gray-100 px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-200">
                            Profil Belum Lengkap
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full overflow-hidden rounded-lg bg-white">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">ID</th>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-left">Email</th>
                                <th class="px-4 py-3 text-left">Role</th>
                                <th class="px-4 py-3 text-left">Status Profil</th>
                                <th class="px-4 py-3 text-left">Terdaftar</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $user->id }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                                    <td class="px-4 py-3">{{ $user->email }}</td>
                                    <td class="px-4 py-3">
                                        @foreach ($user->roles as $role)
                                            <span class="rounded-full bg-blue-100 px-2 py-1 text-xs text-blue-800">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($user->profile_completed)
                                            <span class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-800">
                                                Lengkap
                                            </span>
                                        @else
                                            <span class="rounded-full bg-red-100 px-2 py-1 text-xs text-red-800">
                                                Belum Lengkap
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('profile.show.user', $user->id) }}" class="text-blue-600 hover:text-blue-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
