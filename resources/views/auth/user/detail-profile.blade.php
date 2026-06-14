@extends('layouts.admin')

@section('page_title', Auth::user()->name . ' Profile')
@push('styles')
    <style>
        .peer:checked+label .w-4.h-4.border-2 {
            border-color: currentColor;
            background-color: currentColor;
        }

        .peer:checked+label .w-4.h-4.border-2 .w-2.h-2 {
            opacity: 1;
        }

        .peer:checked+label .fas.fa-check {
            opacity: 1;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .slide-up {
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.toggle-slider {
            background-color: #ef4444;
        }

        input:checked+.toggle-slider:before {
            transform: translateX(20px);
        }

        .file-input-label {
            transition: all 0.3s ease;
        }

        .file-input-label:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush
@section('content')
    <main class="mx-auto py-6">
        <!-- Profile Header -->
        <div class="mb-8 flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
            <div class="flex items-center gap-4">
                <div class="relative">

                    <x-user-avatar :user="$user" size="lg" />
                    <div class="absolute -bottom-1 -right-1 rounded-full bg-white p-0.5 shadow">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-green-500 text-xs text-white">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                    <div class="mt-1 flex items-center gap-2">
                        @if ($user->trashed())
                            <span class="mr-2 space-x-1 rounded bg-gradient-to-r from-red-500 to-red-700 px-2 py-1 text-sm capitalize text-white shadow">
                                <i class="fad fa-user-slash mr-1"></i>
                                Deleted
                            </span>
                        @else
                            @switch($user->status)
                                @case('active')
                                    <span class="mr-2 space-x-1 rounded bg-gradient-to-r from-green-500 to-green-700 px-2 py-1 text-sm capitalize text-white shadow">
                                        <i class="fad fa-user-check mr-1"></i>
                                        {{ $user->status }}
                                    </span>
                                @break

                                @case('inactive')
                                    <span class="mr-2 space-x-1 rounded bg-gradient-to-r from-yellow-500 to-yellow-700 px-2 py-1 text-sm capitalize text-white shadow">
                                        <i class="fad fa-user-slash mr-1"></i>
                                        {{ $user->status }}
                                    </span>
                                @break

                                @case('suspended')
                                    <span class="mr-2 space-x-1 rounded bg-gradient-to-r from-red-500 to-red-700 px-2 py-1 text-sm capitalize text-white shadow">
                                        <i class="fad fa-user-slash mr-1"></i>
                                        {{ $user->status }}
                                    </span>
                                @break

                                @default
                                    <span class="mr-2 space-x-1 rounded bg-gradient-to-r from-purple-500 to-purple-700 px-2 py-1 text-sm capitalize text-white">
                                        <i class="fad fa-user mr-1"></i>
                                        {{ $user->status }}
                                    </span>
                            @endswitch
                            @foreach ($user->roles as $role)
                                @if ($role->name == 'founder')
                                    <span class="mr-2 space-x-1 rounded bg-gradient-to-r from-purple-500 to-purple-700 px-2 py-1 text-sm text-white">
                                        <i class="fad fa-crown mr-1"></i> {{ ucfirst($role->name) }}
                                    </span>
                                @elseif($role->name == 'programmer')
                                    <span class="mr-2 space-x-1 rounded bg-gradient-to-r from-red-500 to-red-700 px-2 py-1 text-sm text-white">
                                        <i class="fad fa-code mr-1"></i> {{ ucfirst($role->name) }}
                                    </span>
                                @elseif($role->name == 'admin')
                                    <span class="mr-2 space-x-1 rounded bg-gradient-to-r from-red-500 to-red-700 px-2 py-1 text-sm text-white">
                                        <i class="fad fa-user-shield mr-1"></i> {{ ucfirst($role->name) }}
                                    </span>
                                @elseif($role->name == 'accounting')
                                    <span class="mr-2 space-x-1 rounded bg-gradient-to-r from-yellow-500 to-yellow-700 px-2 py-1 text-sm text-white">
                                        <i class="fad fa-calculator mr-1"></i> {{ ucfirst($role->name) }}
                                    </span>
                                @elseif($role->name == 'operator')
                                    <span class="mr-2 space-x-1 rounded bg-gradient-to-r from-green-500 to-green-700 px-2 py-1 text-sm text-white">
                                        <i class="fad fa-tools mr-1"></i> {{ ucfirst($role->name) }}
                                    </span>
                                @elseif($role->name == 'guest')
                                    <span class="mr-2 space-x-1 rounded bg-gradient-to-r from-gray-500 to-gray-700 px-2 py-1 text-sm text-white">
                                        <i class="fad fa-user mr-1"></i> {{ ucfirst($role->name) }}
                                    </span>
                                @elseif($role->name == 'shop')
                                    <span class="mr-2 space-x-1 rounded bg-gradient-to-r from-orange-500 to-orange-700 px-2 py-1 text-sm text-white">
                                        <i class="fad fa-store mr-1"></i> {{ ucfirst($role->name) }}
                                    </span>
                                @endif
                            @endforeach
                            @if ($tokoInfo)
                                <span class="mr-2 space-x-1 rounded bg-gradient-to-r from-red-500 to-red-700 px-2 py-1 text-sm text-white shadow">
                                    <i class="fad fa-globe mr-1"></i> {{ $tokoInfo['position'] }}
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <button id="openEditModal" class="hover-scale flex items-center rounded-lg bg-gradient-to-r from-red-500 to-red-700 px-4 py-2 text-sm font-medium text-white shadow-lg transition-all hover:bg-red-50">
                    <i class="fas fa-pencil-alt mr-2 text-white"></i> Edit Profile
                </button>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column -->
            <div class="space-y-6 lg:col-span-2">
                <!-- User Details Card -->
                <div class="glass-card rounded-2xl p-6 transition-all hover:shadow-lg">
                    <div class="mb-6 flex items-center">
                        <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-600">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Information {{ $user->name }}</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                        <div class="backdrop-blur-xs rounded-lg bg-red-50/50 p-3">
                            <p class="mb-1 text-xs font-medium text-gray-500">Nama Lengkap</p>
                            <p class="font-medium text-gray-900">{{ $user->name }}</p>
                        </div>
                        <div class="backdrop-blur-xs rounded-lg bg-red-50/50 p-3">
                            <p class="mb-1 text-xs font-medium text-gray-500">Nama Pengguna</p>
                            <p class="font-medium text-gray-900">{{ $user->username }}</p>
                        </div>
                        @if ($user->email)
                            <div class="backdrop-blur-xs rounded-lg bg-red-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Email</p>
                                <p class="verification-badge verified font-medium text-gray-900">{{ $user->email }}</p>
                            </div>
                        @endif
                        <div class="backdrop-blur-xs rounded-lg bg-red-50/50 p-3">
                            <p class="mb-1 text-xs font-medium text-gray-500">Nomor Telepon</p>
                            <p class="verification-badge verified font-medium text-gray-900">{{ $formattedPhoneNumber }}</p>
                        </div>
                        @if ($formattedBirthDate)
                            <div class="backdrop-blur-xs rounded-lg bg-red-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Tanggal Lahir</p>
                                <p class="font-medium text-gray-900">{{ $formattedBirthDate }}</p>
                            </div>
                        @endif
                        @if ($user->gender)
                            <div class="backdrop-blur-xs rounded-lg bg-red-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Jenis Kelamin</p>
                                <p class="font-medium capitalize text-gray-900">{{ $user->gender }}</p>
                            </div>
                        @endif
                        <div class="backdrop-blur-xs rounded-lg bg-red-50/50 p-3">
                            <p class="mb-1 text-xs font-medium text-gray-500">Tanggal Daftar</p>
                            <p class="font-medium text-gray-900">{{ $formattedCreatedAt }}</p>
                        </div>
                        <div class="backdrop-blur-xs rounded-lg bg-red-50/50 p-3">
                            <p class="mb-1 text-xs font-medium text-gray-500">Aktif Terakhir</p>
                            <p class="font-medium text-gray-900">{{ $user->getLastActiveFormatted() }}</p>
                        </div>
                    </div>
                </div>

                @if ($tokoInfo)
                    <!-- Store Info -->
                    <div class="glass-card rounded-2xl p-6 transition-all hover:shadow-lg">
                        <div class="mb-6 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600">
                                    <i class="fas fa-store"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900">Informasi Toko</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                    <i class="fas fa-trophy mr-1"></i> #{{ $tokoInfo['rank'] }}
                                </span>
                                <button class="hover-scale cursor-pointer rounded-lg bg-red-200/50 px-3 py-1 text-xs text-red-900 shadow transition-all hover:bg-red-50">
                                    <i class="fad fa-eye mr-1"></i> View
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                            <div class="backdrop-blur-xs rounded-lg bg-red-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Nama Toko</p>
                                <p class="font-medium text-gray-900">{{ $tokoInfo['name'] }}</p>
                            </div>
                            <div class="backdrop-blur-xs rounded-lg bg-red-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Pemilik Toko</p>
                                <p class="font-medium text-gray-900">{{ $tokoInfo['owner']->name }}</p>
                            </div>
                            <div class="backdrop-blur-xs rounded-lg bg-red-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Point</p>
                                <p class="font-medium text-gray-900">{{ $tokoInfo['ki_point'] }}</p>
                            </div>
                            <div class="backdrop-blur-xs rounded-lg bg-red-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Token</p>
                                <p class="font-medium text-gray-900">{{ $tokoInfo['token'] }}</p>
                            </div>
                            <div class="backdrop-blur-xs rounded-lg bg-red-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Karyawan</p>
                                <p class="font-medium text-gray-900">{{ $tokoInfo['employee_count'] }} User</p>
                            </div>
                            <div class="backdrop-blur-xs rounded-lg bg-red-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Alamat</p>
                                <p class="font-medium text-gray-900">{{ $tokoInfo['address'] }}</p>
                            </div>
                            <div class="backdrop-blur-xs rounded-lg bg-red-50/50 p-3">
                                <p class="mb-1 text-xs font-medium capitalize text-gray-500">Status</p>
                                <p class="verification-badge {{ $tokoInfo['status'] }} font-medium capitalize text-gray-900">{{ $tokoInfo['status'] }}</p>
                            </div>
                            <div class="backdrop-blur-xs rounded-lg bg-red-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Tanggal Daftar</p>
                                <p class="font-medium text-gray-900">{{ $tokoInfo['formattedCreatedAt'] }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                @livewire('profile.manage-devices-detail', ['user' => $user], key($user->id))

                <!-- Permissions -->
                <div class="glass-card rounded-2xl p-6 transition-all hover:shadow-lg" x-data="{ expanded: false }">
                    <div class="mb-6 flex items-center">
                        <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-600">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Permissions</h3>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-sm">
                        <!-- Show first 11 permissions -->
                        @for ($i = 0; $i < min(11, count($formattedPermissions)); $i++)
                            <div class="backdrop-blur-xs flex items-center rounded-lg bg-red-50/50 p-3">
                                <i class="fas fa-check-circle mr-3 text-green-500"></i>
                                <span class="text-gray-700">{{ $formattedPermissions[$i]->formatted_name }}</span>
                            </div>
                        @endfor

                        <!-- Show remaining permissions when expanded -->
                        <template x-if="expanded">
                            <template x-for="formattedPermissions in @js($formattedPermissions->slice(11))">
                                <div class="backdrop-blur-xs flex items-center rounded-lg bg-red-50/50 p-3">
                                    <i class="fas fa-check-circle mr-3 text-green-500"></i>
                                    <span class="text-gray-700" x-text="formattedPermissions.formatted_name"></span>
                                </div>
                            </template>
                        </template>

                        <!-- More/Close button -->
                        @if (count($formattedPermissions) > 11)
                            <div class="backdrop-blur-xs flex cursor-pointer items-center rounded-lg bg-red-50/50 p-3 transition-colors hover:bg-red-100/50" @click="expanded = !expanded">
                                <i class="mr-3 text-gray-500 transition-transform duration-200" :class="expanded ? 'fas fa-chevron-circle-up' : 'fas fa-chevron-circle-down'"></i>
                                <span class="text-gray-700" x-text="expanded ? 'Display Less' : 'Display More (' + {{ count($formattedPermissions) - 11 }} + ')'"></span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @can('edit.users')
                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="glass-card rounded-2xl p-6 transition-all hover:shadow-lg">
                        <div class="mb-6 flex items-center">
                            <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900">Aksi Cepat</h3>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            @if (!$user->trashed())
                                <button onclick="confirmPassword('{{ $user->username }}')" class="hover-scale flex cursor-pointer flex-col items-center justify-center rounded-xl bg-red-50/50 p-3 text-center shadow transition-all hover:bg-red-50">
                                    <i class="fas fa-lock mb-2 text-lg text-red-600"></i>
                                    <span class="text-xs font-medium">Reset Password</span>
                                </button>
                                <button id="openRolePermissionModal" class="hover-scale flex cursor-pointer flex-col items-center justify-center rounded-xl bg-red-50/50 p-3 text-center shadow transition-all hover:bg-red-50">
                                    <i class="fas fa-user-cog mb-2 text-lg text-red-600"></i>
                                    <span class="text-xs font-medium">Edit Role & Permission</span>
                                </button>
                                <button onclick="confirmSuspend('{{ $user->username }}')" class="hover-scale flex cursor-pointer flex-col items-center justify-center rounded-xl bg-red-50/50 p-3 text-center shadow transition-all hover:bg-red-50">
                                    <i class="fas fa-ban mb-2 text-lg text-red-600"></i>
                                    <span class="text-xs font-medium"> {{ $user->status === 'suspended' ? 'Unsuspend' : 'Suspend' }}</span>
                                </button>
                            @endif
                            @can('delete.users')
                                <button onclick="confirmDelete('{{ $user->username }}')" class="hover-scale flex cursor-pointer flex-col items-center justify-center rounded-xl bg-red-50/50 p-3 text-center shadow transition-all hover:bg-red-50">
                                    <i class="fas fa-trash mb-2 text-lg text-red-600"></i>
                                    <span class="text-xs font-medium">
                                        @if ($user->trashed())
                                            Restore
                                        @else
                                            Delete
                                        @endif
                                    </span>
                                </button>
                            @endcan
                        </div>
                    </div>
                @endcan

                <!-- Verification Status -->
                <div class="glass-card rounded-2xl p-6 transition-all hover:shadow-lg">
                    <div class="mb-6 flex items-center">
                        <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Verification Status</h3>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="backdrop-blur-xs flex items-center justify-between rounded-lg bg-red-50/50 p-3">
                            <span class="text-gray-700">Email Verification</span>
                            @if ($user->email_verified_at)
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Verified
                                </span>
                            @else
                                <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                            @endif
                            </span>
                        </div>
                        <div class="backdrop-blur-xs flex items-center justify-between rounded-lg bg-red-50/50 p-3">
                            <span class="text-gray-700">Phone Verification</span>
                            @if ($user->phone_verified_at)
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Verified
                                </span>
                            @else
                                <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                            @endif
                        </div>
                        <div class="backdrop-blur-xs flex items-center justify-between rounded-lg bg-red-50/50 p-3">
                            <span class="text-gray-700">Identity Verification</span>
                            @if ($user->isProfileCompleted())
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Verified
                                </span>
                            @else
                                <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="z-60 fixed inset-0 hidden bg-black/50 backdrop-blur-sm">
        <div class="flex min-h-screen items-center justify-center">
            <div class="rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-center space-x-3">
                    <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-red-600"></div>
                    <span class="font-medium text-gray-700">Memproses...</span>
                </div>
            </div>
        </div>
    </div>

    <div id="message-modal" class="z-60 fixed inset-0 hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50">
        <div class="relative w-full max-w-md">
            <div id="message-modal-container" class="animate-jump-in w-full max-w-md overflow-hidden rounded-lg bg-white shadow-xl transition-all duration-300">
                <div class="p-6">
                    <div class="text-center">
                        <!-- Icon Container -->
                        <div id="iconContainer" class="animate__animated animate__rotateIn my-6 flex justify-center">
                            <i id="modalIcon" class="fas fa-circle-exclamation text-6xl text-red-500"></i>
                        </div>

                        <!-- Message -->
                        <h3 id="modalTitle" class="mb-2 text-2xl font-bold text-gray-800">Something went wrong</h3>
                        <p id="modalMessage" class="mb-6 text-gray-600">
                            Please try again later.
                        </p>
                        <!-- Buttons -->
                        <div id="actionButtons" class="flex justify-center space-x-4">
                            <button id="confirmBtn" onclick="confirmAction()" class="cursor-pointer rounded-lg bg-red-500 px-6 py-2 font-bold text-white transition duration-200 hover:bg-red-600">
                                Delete
                            </button>
                            <button id="cancelBtn" onclick="closeModal()" class="cursor-pointer rounded-lg bg-gray-300 px-6 py-2 font-bold text-gray-800 transition duration-200 hover:bg-gray-400">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Backdrop -->
    <div id="rolePermissionModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm">
        <div class="flex min-h-screen items-center justify-center p-4">
            <!-- Modal Container -->
            <div class="max-h-[90vh] w-full max-w-4xl transform overflow-y-auto rounded-xl bg-white shadow-2xl transition-all">

                <!-- Modal Header -->
                <div class="rounded-t-lg bg-gradient-to-r from-red-600 via-red-400 to-red-600 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="mb-1 text-2xl font-bold text-white">
                                <i class="fas fa-user-shield mr-3"></i>
                                Kelola Role & Permission
                            </h2>
                            <p class="text-red-100">Atur peran dan izin pengguna</p>
                        </div>
                        <button id="closeModalRole" class="transform text-2xl text-white transition hover:scale-110 hover:text-gray-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-8">
                    <form id="rolePermissionForm">
                        <div class="space-y-8">

                            <!-- Role Selection -->
                            <div>
                                <h3 class="mb-4 flex items-center text-xl font-bold text-gray-800">
                                    <i class="fas fa-crown mr-3 text-yellow-500"></i>
                                    Pilih Role
                                </h3>
                                <div id="roleSelection" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                    <!-- Role cards will be populated by JavaScript -->
                                </div>
                            </div>

                            <!-- Toko Selection (Only for toko role) -->
                            <div id="tokoSection" class="hidden">
                                <div class="mb-4 flex items-center justify-between">
                                    <h3 class="flex items-center text-xl font-bold text-gray-800">
                                        <i class="fas fa-store mr-3 text-green-500"></i>
                                        Pilih Toko
                                    </h3>
                                    <div class="relative w-1/2">
                                        <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
                                            <i class="fas fa-search text-gray-500"></i>
                                        </div>
                                        <input type="text" id="toko-search" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 ps-10 text-sm text-gray-900 focus:border-red-500 focus:ring-red-500" placeholder="Search by name..." />
                                    </div>
                                </div>
                                <div id="tokoSelection" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                </div>
                            </div>

                            <!-- Jabatan Selection (Only for toko role) -->
                            <div id="jabatanSection" class="hidden">
                                <h3 class="mb-4 flex items-center text-xl font-bold text-gray-800">
                                    <i class="fas fa-briefcase mr-3 text-red-500"></i>
                                    Pilih Jabatan
                                </h3>
                                <div id="jabatanSelection" class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
                                    <!-- Jabatan cards will be populated by JavaScript -->
                                </div>
                            </div>

                            <!-- Permission Selection -->
                            <div>
                                <div class="mb-4 flex items-center justify-between">
                                    <h3 class="mb-4 flex items-center text-xl font-bold text-gray-800">
                                        <i class="fas fa-key mr-3 text-purple-500"></i>
                                        Pilih Permission (Multi Select)
                                    </h3>
                                    <div class="relative w-1/2">
                                        <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
                                            <i class="fas fa-search text-gray-500"></i>
                                        </div>
                                        <input type="text" id="permission-search" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 ps-10 text-sm text-gray-900 focus:border-purple-500 focus:ring-purple-500" placeholder="Search permission..." />
                                    </div>
                                </div>
                                <div id="permissionSelection" class="max-h-50 grid grid-cols-1 gap-3 overflow-y-auto sm:grid-cols-2 lg:grid-cols-3">
                                    <!-- Permission cards will be populated by JavaScript -->
                                </div>
                            </div>

                        </div>

                        <!-- Modal Footer -->
                        <div class="mt-8 flex justify-end gap-4 border-t border-gray-200 pt-6">
                            <button type="button" id="cancelBtnRole" class="rounded-xl bg-gray-100 px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-200">
                                <i class="fas fa-times mr-2"></i>
                                Batal
                            </button>
                            <button type="submit" class="transform rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-3 font-semibold text-white shadow-lg transition hover:scale-105 hover:from-green-600 hover:to-emerald-700">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- Edit User Modal -->
    <div id="modalBackdrop" class="fade-in fixed inset-0 z-40 flex hidden items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
        <!-- Modal Container -->
        <div class="slide-up max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-xl bg-white shadow-2xl">
            <!-- Modal Header -->
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white px-6 py-4">
                <h3 class="text-xl font-semibold text-gray-800">
                    <i class="fas fa-user-cog mr-2 text-red-600"></i>Edit User Details
                </h3>
                <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Form Start -->
            <form id="editUserForm" action="{{ route('user.update', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Modal Content -->
                <div class="p-6">
                    <!-- Display Validation Errors -->
                    @if ($errors->any())
                        <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        There were {{ $errors->count() }} errors with your submission
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc space-y-1 pl-5">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- User Profile Section -->
                    <div class="mb-8 flex flex-col gap-6 md:flex-row">
                        <!-- Profile Picture -->
                        <div class="flex w-full flex-col items-center md:w-1/4">
                            <div class="relative mb-4">
                                @if ($user->profile_photo_path)
                                    <img id="profileImage" src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Profile" class="h-32 w-32 rounded-full border-4 border-red-100 object-cover shadow-md">
                                @else
                                    <div id="profileImage" class="flex h-32 w-32 items-center justify-center rounded-full border-4 border-red-100 bg-gray-200 shadow-md">
                                        <i class="fas fa-user text-4xl text-gray-400"></i>
                                    </div>
                                @endif
                                <button type="button" onclick="document.getElementById('profileUpload').click()" class="absolute bottom-0 right-0 rounded-full bg-red-500 p-2 text-white transition hover:bg-red-600">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            <label for="profileUpload" class="file-input-label inline-flex cursor-pointer items-center rounded-lg bg-red-50 px-4 py-2 font-medium text-red-700 transition hover:bg-red-100">
                                <i class="fas fa-upload mr-2"></i>Upload New
                                <input type="file" id="profileUpload" name="profile_photo_path" class="hidden" accept="image/*">
                            </label>
                            @error('profile_photo_path')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Basic Info -->
                        <div class="w-full md:w-3/4">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Full Name *</label>
                                    <input name="name" type="text" value="{{ old('name', $user->name) }}" required class="@error('name') border-red-500 @enderror w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    @error('name')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Username *</label>
                                    <input name="username" type="text" value="{{ old('username', $user->username) }}" required class="@error('username') border-red-500 @enderror w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    @error('username')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Gender</label>
                                    <select name="gender" class="@error('gender') border-red-500 @enderror w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Date of Birth</label>
                                    <input name="date_of_birth" type="date" value="{{ old('date_of_birth', $user->date_of_birth) }}" class="@error('date_of_birth') border-red-500 @enderror w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    @error('date_of_birth')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info Section -->
                    <div class="mb-8">
                        <h4 class="mb-4 flex items-center text-lg font-medium text-gray-800">
                            <i class="fas fa-address-book mr-2 text-red-600"></i>Contact Information
                        </h4>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Phone Number -->
                            <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                <div class="mb-1 flex items-center justify-between">
                                    <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                                    <div class="flex items-center">
                                        <span class="mr-2 text-xs text-gray-500">Verified</span>
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="phone_verified" value="1" {{ $user->phone_verified_at ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>
                                <input type="tel" value="{{ old('phone_number', $user->phone_number) }}" name="phone_number" class="@error('phone_number') border-red-500 @enderror w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                                @error('phone_number')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                <div class="mb-1 flex items-center justify-between">
                                    <label class="block text-sm font-medium text-gray-700">Email Address *</label>
                                    <div class="flex items-center">
                                        <span class="mr-2 text-xs text-gray-500">Verified</span>
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="email_verified" value="1" {{ $user->email_verified_at ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>
                                <input type="email" value="{{ old('email', $user->email) }}" name="email" required class="@error('email') border-red-500 @enderror w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                                @error('email')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="card rounded-lg border border-gray-100 bg-white p-4 md:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Address</label>
                                <textarea class="@error('address') border-red-500 @enderror w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" rows="2" name="address">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Security Section -->
                    <div class="mb-8">
                        <h4 class="mb-4 flex items-center text-lg font-medium text-gray-800">
                            <i class="fas fa-shield-alt mr-2 text-red-600"></i>Security Settings
                        </h4>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-medium text-gray-700">Two-Factor Authentication</label>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="two_factor_enabled" value="1" {{ old('two_factor_enabled', $user->two_factor_enabled) ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Add an extra layer of security to your account</p>
                            </div>

                            <div id="openRoleModal" class="card rounded-lg border border-gray-100 bg-white p-4">
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-medium text-gray-700">Role & Permission</label>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($user->roles as $role)
                                            <span class="rounded bg-red-500 px-2 py-1 text-xs text-white">{{ $role->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Click to edit role & permission</p>
                            </div>
                        </div>
                    </div>

                    <!-- KTP Verification Section -->
                    <div class="mb-8">
                        <h4 class="mb-4 flex items-center text-lg font-medium text-gray-800">
                            <i class="fas fa-id-card mr-2 text-red-600"></i>KTP Details
                        </h4>

                        <div class="flex flex-col gap-6 md:flex-row">
                            <!-- KTP Image -->
                            <div class="w-full md:w-1/3">
                                <div class="card flex h-full flex-col rounded-lg border border-gray-100 bg-white p-4">
                                    <div class="flex flex-1 flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-200 p-2">
                                        @if ($user->ktp_image)
                                            <img id="ktpImage" src="{{ asset('storage/' . $user->ktp_image) }}" alt="KTP Image" class="mb-3 h-auto max-h-40 w-full rounded object-contain">
                                        @else
                                            <div id="ktpImage" class="mb-3 flex h-32 w-full items-center justify-center rounded bg-gray-100">
                                                <i class="fas fa-id-card text-2xl text-gray-400"></i>
                                            </div>
                                        @endif
                                        <label for="ktpUpload" class="file-input-label inline-flex cursor-pointer items-center rounded-lg bg-red-50 px-4 py-2 font-medium text-red-700 transition hover:bg-red-100">
                                            <i class="fas fa-upload mr-2"></i>Upload KTP
                                            <input type="file" name="ktp_image" id="ktpUpload" class="hidden" accept="image/*">
                                        </label>
                                        @error('ktp_image')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- KTP Details -->
                            <div class="w-full md:w-2/3">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                        <label class="mb-1 block text-sm font-medium text-gray-700">KTP Number</label>
                                        <input type="text" name="ktp_number" value="{{ old('ktp_number', $user->ktp_number) }}" class="@error('ktp_number') border-red-500 @enderror w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        @error('ktp_number')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                        <label class="mb-1 block text-sm font-medium text-gray-700">KTP Name</label>
                                        <input type="text" name="ktp_name" value="{{ old('ktp_name', $user->ktp_name) }}" class="@error('ktp_name') border-red-500 @enderror w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        @error('ktp_name')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="card rounded-lg border border-gray-100 bg-white p-4 md:col-span-2">
                                        <label class="mb-1 block text-sm font-medium text-gray-700">KTP Address</label>
                                        <textarea name="ktp_address" rows="2" class="@error('ktp_address') border-red-500 @enderror w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('ktp_address', $user->ktp_address) }}</textarea>
                                        @error('ktp_address')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                        <div class="mb-1 flex items-center justify-between">
                                            <label class="block text-sm font-medium text-gray-700">KTP Verified</label>
                                            <label class="toggle-switch">
                                                <input type="checkbox" name="ktp_verified" value="1" {{ old('ktp_verified', $user->ktp_verified) ? 'checked' : '' }}>
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Section -->
                    <div>
                        <h4 class="mb-4 flex items-center text-lg font-medium text-gray-800">
                            <i class="fas fa-user-tag mr-2 text-red-600"></i>Status & Quick Actions
                        </h4>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                <label class="mb-1 block text-sm font-medium text-gray-700">User Status</label>
                                <select name="status" class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                            </div>
                            <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Quick Actions</label>
                                <div class="flex justify-center gap-2 text-sm">
                                    <button onclick="resetPassword('{{ $user->username }}')" class="rounded-md bg-blue-500 px-4 py-2 text-white transition hover:bg-blue-600">
                                        <i class="fas fa-key mr-1"></i>Reset Password
                                    </button>
                                    <button onclick="confirmDelete('{{ $user->username }}')" class="rounded-md bg-red-500 px-4 py-2 text-white transition hover:bg-red-600">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="sticky bottom-0 z-10 flex justify-end border-t border-gray-200 bg-white px-6 py-4">
                    <button type="button" id="cancelBtn" class="mr-3 rounded-lg bg-gray-200 px-6 py-2 font-medium text-gray-800 transition hover:bg-gray-300">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="rounded-lg bg-red-600 px-6 py-2 font-medium text-white shadow-md transition hover:bg-red-700">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>


@endsection


@push('scripts')
    <script>
        const tokoSearch = document.getElementById('toko-search');
        const permissionSearch = document.getElementById('permission-search');
        let searchTimeout = null;

        const messageModal = document.getElementById('message-modal');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const modalIcon = document.getElementById('modalIcon');
        const iconContainer = document.getElementById('iconContainer');
        const messageModalContainer = document.getElementById('message-modal-container');
        const actionButtons = document.getElementById('actionButtons');
        const confirmButton = document.getElementById('confirmBtn');
        const cancelButton = document.getElementById('cancelBtn');

        $(document).ready(function() {
            const userId = {{ $user->id }}; // This should be dynamic based on the user being edited
            let modalData = null;

            // Modal controls
            $('#openRoleModal').click(function() {
                loadRolePermissionData();
            });

            $('#openRolePermissionModal').click(function() {
                loadRolePermissionData();
            });

            $('#closeModalRole, #cancelBtnRole').click(function() {
                closeModalRole();
            });

            // Load role and permission data
            function loadRolePermissionData() {
                showLoading();
                tokoSearch.addEventListener('input', function() {
                    filterTokos();
                });
                permissionSearch.addEventListener('input', function() {
                    filterPermissions();
                });
                $.ajax({
                    url: `/user/${userId}/role-permission-data`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            modalData = response.data;
                            populateModal();
                            $('#rolePermissionModal').removeClass('hidden');
                        } else {
                            showMessageModalOpen('Gagal memuat data', 'error');
                        }
                    },
                    error: function() {
                        showMessageModalOpen('Terjadi kesalahan saat memuat data', 'error');
                    },
                    complete: function() {
                        hideLoading();
                    }
                });
            }

            // Populate modal with data
            function populateModal() {
                populateRoles();
                populatePermissions();
                populateTokos();
                populateJabatan();
                handleRoleChange();
            }


            function filterTokos() {
                const searchTerm = tokoSearch.value.toLowerCase();
                clearTimeout(searchTimeout);
                const tokoContainer = $('#tokoSelection');
                tokoContainer.empty();
                tokoContainer.append(`
                    <div class="animate-pulse block p-4 border-2 rounded-xl border-gray-300">
                        <div class="flex-1">
                            <div class="h-4 bg-gray-300 rounded"></div>
                            <div class="h-4 bg-gray-300 rounded mt-1"></div>
                            <div class="h-4 bg-gray-300 rounded mt-1"></div>
                        </div>
                    </div>
                      <div class="animate-pulse block p-4 border-2 rounded-xl border-gray-300">
                        <div class="flex-1">
                            <div class="h-4 bg-gray-300 rounded"></div>
                            <div class="h-4 bg-gray-300 rounded mt-1"></div>
                            <div class="h-4 bg-gray-300 rounded mt-1"></div>
                        </div>
                    </div>
                `);
                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: `/user/tokosearch`,
                        method: 'POST',
                        data: {
                            search: searchTerm
                        },
                        success: function(response) {
                            if (response.data.length === 0) {
                                tokoContainer.empty();
                                tokoContainer.append(`
                                <div class="block text-center justify-center p-4 border-2 rounded-xl border-gray-300">
                        <div class="flex-1">
                            <div class="rounded mt-1 text-gray-500 text-sm">Tidak ada toko dengan pencarian ${searchTerm}</div>
                        </div>
                    </div>`);
                            } else {
                                modalData.tokos = response.data;
                                populateTokos();
                            }
                        }
                    });
                }, 1000);
            }

            function filterPermissions() {
                const searchTerm = permissionSearch.value.toLowerCase();
                clearTimeout(searchTimeout);
                const permissionContainer = $('#permissionSelection');
                permissionContainer.empty();
                permissionContainer.append(`
                    <div class="animate-pulse block p-4 border-2 rounded-xl border-gray-300">
                        <div class="flex-1">
                            <div class="h-4 bg-gray-300 rounded"></div>
                            <div class="h-4 bg-gray-300 rounded mt-1"></div>
                            <div class="h-4 bg-gray-300 rounded mt-1"></div>
                        </div>
                    </div>
                      <div class="animate-pulse block p-4 border-2 rounded-xl border-gray-300">
                        <div class="flex-1">
                            <div class="h-4 bg-gray-300 rounded"></div>
                            <div class="h-4 bg-gray-300 rounded mt-1"></div>
                            <div class="h-4 bg-gray-300 rounded mt-1"></div>
                        </div>
                    </div>
                `);
                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: `/user/permissionsearch`,
                        method: 'POST',
                        data: {
                            search: searchTerm
                        },
                        success: function(response) {
                            if (response.success) {
                                modalData.permissions = response.data;
                                populatePermissions();
                            } else {
                                showMessageModalOpen('Gagal memuat data', 'error');
                            }
                        },
                        error: function() {
                            showMessageModalOpen('Terjadi kesalahan saat memuat data', 'error');
                        },
                        complete: function() {
                            hideLoading();
                        }
                    });
                }, 1000);
            }

            // Populate roles
            function populateRoles() {
                const roleContainer = $('#roleSelection');
                roleContainer.empty();

                modalData.roles.forEach(role => {
                    const isSelected = role.name === modalData.current_role;
                    const roleCard = `
                    <div class="role-card relative">
                        <input type="radio" id="role_${role.id}" name="role" value="${role.name}" 
                               class="hidden peer" ${isSelected ? 'checked' : ''}>
                        <label for="role_${role.id}" 
                               class="block p-4 border-2 rounded-xl cursor-pointer transition-all duration-200
                                      peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:shadow-lg
                                      hover:border-red-300 hover:bg-red-25 border-gray-200 bg-white">
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-4 border-2 border-gray-300 rounded-full peer-checked:border-red-500 peer-checked:bg-red-500 relative">
                                    <div class="w-2 h-2 bg-white rounded-full absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100"></div>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 capitalize">${role.name}</h4>
                                    <p class="text-sm text-gray-500">Role ${role.name}</p>
                                </div>
                            </div>
                            <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                                <i class="fas fa-check text-red-500"></i>
                            </div>
                        </label>
                    </div>
                `;
                    roleContainer.append(roleCard);
                });
            }



            // Populate permissions
            function populatePermissions() {
                const permissionContainer = $('#permissionSelection');
                permissionContainer.empty();
                console.log(modalData.permissions);
                modalData.permissions.forEach(permission => {
                    const isSelected = modalData.current_permissions.includes(permission.id);
                    const permissionCard = `
                    <div class="permission-card">
                        <input type="checkbox" id="perm_${permission.id}" name="permissions[]" 
                               value="${permission.id}" class="hidden peer" ${isSelected ? 'checked' : ''}>
                        <label for="perm_${permission.id}" 
                               class="block p-3 border-2 rounded-lg cursor-pointer transition-all duration-200
                                      peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:shadow-md
                                      hover:border-purple-300 hover:bg-purple-25 border-gray-200 bg-white">
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 border-2 border-gray-300 rounded peer-checked:border-purple-500 peer-checked:bg-purple-500 flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700 capitalize">${permission.formatted_name}</span>
                            </div>
                        </label>
                    </div>
                `;
                    permissionContainer.append(permissionCard);
                });
            }

            // Populate tokos
            function populateTokos() {
                const tokoContainer = $('#tokoSelection');
                tokoContainer.empty();
                modalData.tokos.forEach(toko => {
                    const isSelected = toko.id === modalData.current_toko;
                    const tokoCard = `
                    <div class="toko-card">
                        <input type="radio" id="toko_${toko.id}" name="toko_id" value="${toko.id}" 
                               class="hidden peer" ${isSelected ? 'checked' : ''}>
                        <label for="toko_${toko.id}" 
                               class="block p-4 border-2 rounded-xl cursor-pointer transition-all duration-200
                                      peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:shadow-lg
                                      hover:border-green-300 hover:bg-green-25 border-gray-200 bg-white">
                            <div class="flex items-start space-x-3">
                                <div class="w-4 h-4 border-2 border-gray-300 rounded-full peer-checked:border-green-500 peer-checked:bg-green-500 mt-1 relative">
                                    <div class="w-2 h-2 bg-white rounded-full absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100"></div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">${toko.name}</h4>
                                    <p class="text-sm text-gray-500">${toko.address || 'No address'}</p>
                                    <span class="inline-block px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full mt-1">
                                        ${toko.status}
                                    </span>
                                </div>
                            </div>
                        </label>
                    </div>
                `;
                    tokoContainer.append(tokoCard);
                });
            }

            // Populate jabatan
            function populateJabatan() {
                const jabatanContainer = $('#jabatanSelection');
                jabatanContainer.empty();

                modalData.jabatan.forEach(jabatan => {
                    const isSelected = jabatan.id === modalData.current_jabatan;
                    const jabatanCard = `
                    <div class="jabatan-card">
                        <input type="radio" id="jabatan_${jabatan.id}" name="jabatan_id" value="${jabatan.id}" 
                               class="hidden peer" ${isSelected ? 'checked' : ''}>
                        <label for="jabatan_${jabatan.id}" 
                               class="block p-3 border-2 rounded-lg cursor-pointer transition-all duration-200
                                      peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:shadow-md
                                      hover:border-red-300 hover:bg-red-25 border-gray-200 bg-white">
                            <div class="text-center">
                                <div class="w-4 h-4 border-2 border-gray-300 rounded-full peer-checked:border-red-500 peer-checked:bg-red-500 mx-auto mb-2 relative">
                                    <div class="w-2 h-2 bg-white rounded-full absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700">${jabatan.name}</span>
                            </div>
                        </label>
                    </div>
                `;
                    jabatanContainer.append(jabatanCard);
                });
            }

            // Handle role change
            function handleRoleChange() {
                $('input[name="role"]').change(function() {
                    const selectedRole = $(this).val();

                    if (selectedRole === 'shop') {
                        $('#tokoSection, #jabatanSection').removeClass('hidden');
                    } else {
                        $('#tokoSection, #jabatanSection').addClass('hidden');
                        $('input[name="toko_id"], input[name="jabatan_id"]').prop('checked', false);
                    }
                });

                // Trigger change event for initially selected role
                $('input[name="role"]:checked').trigger('change');
            }

            // Form submission
            $('#rolePermissionForm').submit(function(e) {
                e.preventDefault();

                const formData = {
                    role: $('input[name="role"]:checked').val(),
                    permissions: $('input[name="permissions[]"]:checked').map(function() {
                        return $(this).val();
                    }).get(),
                    toko_id: $('input[name="toko_id"]:checked').val() || null,
                    jabatan_id: $('input[name="jabatan_id"]:checked').val() || null
                };

                showLoading();

                $.ajax({
                    url: `/user/${userId}/update-role-permission`,
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            closeModalRole();
                            showMessageModalOpen(response.message, 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 3000);
                        } else {
                            closeModalRole();
                            showMessageModalOpen(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Terjadi kesalahan';
                        closeModalRole();
                        showMessageModalOpen(message, 'error');
                    },
                    complete: function() {
                        hideLoading();
                    }
                });
            });

            // Helper functions
            function closeModalRole() {
                $('#rolePermissionModal').addClass('hidden');
            }

            function showLoading() {
                $('#loadingOverlay').removeClass('hidden');
            }

            function hideLoading() {
                $('#loadingOverlay').addClass('hidden');
            }

        });


        function confirmPassword(username) {
            modalTitle.textContent = 'Reset Password';
            modalMessage.textContent = 'Anda yakin ingin reset password user ' + username + '?';
            modalIcon.className = 'fas fa-exclamation-triangle animate__animated animate__rotateIn text-yellow-500 text-6xl';
            actionButtons.classList.remove('hidden');
            confirmButton.dataset.action = 'reset-password';
            confirmButton.dataset.username = username;
            confirmButton.textContent = 'Reset Password';
            confirmButton.disabled = false;
            cancelButton.disabled = false;
            messageModal.classList.remove('hidden');
            messageModal.classList.add('flex');
        }

        function confirmSuspend(username) {
            modalTitle.textContent = 'Suspend User';
            modalMessage.textContent = 'Anda yakin ingin suspend user ' + username + '?';
            modalIcon.className = 'fas fa-exclamation-triangle animate__animated animate__rotateIn text-yellow-500 text-6xl';
            actionButtons.classList.remove('hidden');
            confirmButton.dataset.action = 'suspend';
            confirmButton.dataset.username = username;
            confirmButton.textContent = 'Suspend';
            confirmButton.disabled = false;
            cancelButton.disabled = false;
            messageModal.classList.remove('hidden');
            messageModal.classList.add('flex');
        }


        function confirmDelete(username) {
            modalTitle.textContent = 'Delete User';
            modalMessage.textContent = 'Anda yakin ingin delete user ' + username + '?';
            modalIcon.className = 'fas fa-exclamation-triangle animate__animated animate__rotateIn text-yellow-500 text-6xl';
            actionButtons.classList.remove('hidden');
            confirmButton.dataset.action = 'delete';
            confirmButton.dataset.username = username;
            confirmButton.textContent = 'Delete';
            confirmButton.disabled = false;
            cancelButton.disabled = false;
            messageModal.classList.remove('hidden');
            messageModal.classList.add('flex');
        }

        function confirmAction() {
            const action = confirmButton.dataset.action;
            const username = confirmButton.dataset.username;
            if (action === 'reset-password') {
                confirmBtn.innerHTML = '<i class="fas fa-spinner animate-spin"></i> Reseting...';
                confirmBtn.disabled = true;
                cancelButton.disabled = true;
                resetPassword(username);
            } else if (action === 'delete') {
                confirmBtn.innerHTML = '<i class="fas fa-spinner animate-spin"></i> Deleting...';
                confirmBtn.disabled = true;
                cancelButton.disabled = true;
                deleteUser(username);
            } else if (action === 'suspend') {
                confirmBtn.innerHTML = '<i class="fas fa-spinner animate-spin"></i> Suspending...';
                confirmBtn.disabled = true;
                cancelButton.disabled = true;
                suspend(username);
            }
        }

        function closeModal() {
            messageModal.classList.remove('flex');
            messageModal.classList.add('hidden');
            actionButtons.classList.remove('hidden');
            confirmButton.disabled = false;
            cancelButton.disabled = false;
            confirmButton.textContent = 'Confirm';
            cancelButton.textContent = 'Cancel';
            modalTitle.textContent = '';
            modalMessage.textContent = '';
            modalIcon.className = 'fas fa-circle-exclamation text-6xl text-red-500';
        }

        function resetPassword(username) {
            $.ajax({
                url: "{{ route('user.reset-password', ['username' => $user->username]) }}",
                type: "POST",
                success: function(response) {
                    showMessageModal(response.message, 'success');
                },
                error: function(xhr) {
                    showMessageModal(xhr.responseJSON.message, 'error');
                }
            });
        }

        function deleteUser(username) {
            $.ajax({
                url: "{{ route('user.delete', ['username' => $user->username]) }}",
                type: "POST",
                success: function(response) {
                    showMessageModal(response.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                },
                error: function(xhr) {
                    showMessageModal(xhr.responseJSON.message, 'error');
                }
            });
        }

        function suspend(username) {
            $.ajax({
                url: "{{ route('user.suspend', ['username' => $user->username]) }}",
                type: "POST",
                success: function(response) {
                    showMessageModal(response.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                },
                error: function(xhr) {
                    showMessageModal(xhr.responseJSON.message, 'error');
                }
            });
        }

        function showMessageModalOpen(message, type) {
            messageModal.classList.remove('hidden');
            messageModal.classList.add('flex');
            showMessageModal(message, type);
        }

        function showMessageModal(message, type) {
            actionButtons.classList.add('hidden');
            modalTitle.textContent = type === 'success' ? 'Success' : 'Error';
            modalMessage.textContent = message;
            modalIcon.className = type === 'success' ? 'fas fa-check-circle animate__animated animate__rotateIn text-6xl text-green-500' : 'fas fa-circle-exclamation animate__animated animate__rotateIn text-6xl text-red-500';
            iconContainer.className = type === 'success' ? 'animate__animated animate__rotateIn my-6 flex justify-center' : 'animate__animated animate__rotateIn my-6 flex justify-center';
            messageModalContainer.className = type === 'success' ? 'animate-jump-in w-full max-w-md overflow-hidden rounded-lg bg-white shadow-xl transition-all duration-300' : 'animate-jump-in w-full max-w-md overflow-hidden rounded-lg bg-white shadow-xl transition-all duration-300';
            setTimeout(function() {
                messageModal.classList.remove('flex');
                messageModal.classList.add('hidden');
            }, 3000);
        }
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal functionality
            const modalBackdrop = document.getElementById('modalBackdrop');
            const closeModalBtn = document.getElementById('closeModal');
            const openModalBtn = document.getElementById('openEditModal');

            openModalBtn.addEventListener('click', () => {
                modalBackdrop.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });

            // Function to close modal
            function closeModal() {
                modalBackdrop.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            // Close modal events
            closeModalBtn.addEventListener('click', closeModal);

            modalBackdrop.addEventListener('click', (e) => {
                if (e.target === modalBackdrop) {
                    closeModal();
                }
            });

            // Image preview functionality
            const profileUpload = document.getElementById('profileUpload');
            const profileImage = document.getElementById('profileImage');
            const ktpUpload = document.getElementById('ktpUpload');
            const ktpImage = document.getElementById('ktpImage');

            profileUpload.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        if (profileImage.tagName === 'IMG') {
                            profileImage.src = event.target.result;
                        } else {
                            // Replace div with img element
                            const newImg = document.createElement('img');
                            newImg.id = 'profileImage';
                            newImg.src = event.target.result;
                            newImg.alt = 'Profile';
                            newImg.className = 'h-32 w-32 rounded-full border-4 border-red-100 object-cover shadow-md';
                            profileImage.parentNode.replaceChild(newImg, profileImage);
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });

            ktpUpload.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        if (ktpImage.tagName === 'IMG') {
                            ktpImage.src = event.target.result;
                        } else {
                            // Replace div with img element
                            const newImg = document.createElement('img');
                            newImg.id = 'ktpImage';
                            newImg.src = event.target.result;
                            newImg.alt = 'KTP Image';
                            newImg.className = 'mb-3 h-auto w-full rounded max-h-40 object-contain';
                            ktpImage.parentNode.replaceChild(newImg, ktpImage);
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endpush
