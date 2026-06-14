@extends('layouts.admin')

@section('page_title', Auth::user()->name . ' Profile')

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
                                <span class="mr-2 space-x-1 rounded bg-gradient-to-r from-blue-500 to-blue-700 px-2 py-1 text-sm text-white">
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
                            <span class="mr-2 space-x-1 rounded bg-gradient-to-r from-blue-500 to-blue-700 px-2 py-1 text-sm text-white shadow">
                                <i class="fad fa-globe mr-1"></i> {{ $tokoInfo['position'] }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <button class="hover-scale flex items-center rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-lg transition-all hover:bg-gray-50">
                    <i class="fas fa-pencil-alt text-primary-600 mr-2"></i> Edit Profile
                </button>
                <button class="hover-scale hover:from-primary-600 hover:to-primary-800 flex items-center rounded-lg bg-gradient-to-r from-red-500 to-red-700 px-4 py-2 text-sm font-medium text-white shadow-lg transition-all">
                    <i class="fas fa-envelope mr-2 text-white"></i> Message
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
                        <h3 class="text-xl font-semibold text-gray-900">User Details</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                        <div class="backdrop-blur-xs rounded-lg bg-blue-50/50 p-3">
                            <p class="mb-1 text-xs font-medium text-gray-500">Full Name</p>
                            <p class="font-medium text-gray-900">{{ $user->name }}</p>
                        </div>
                        <div class="backdrop-blur-xs rounded-lg bg-blue-50/50 p-3">
                            <p class="mb-1 text-xs font-medium text-gray-500">Username</p>
                            <p class="font-medium text-gray-900">{{ $user->username }}</p>
                        </div>
                        @if ($user->email)
                            <div class="backdrop-blur-xs rounded-lg bg-blue-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Email</p>
                                <p class="verification-badge verified font-medium text-gray-900">{{ $user->email }}</p>
                            </div>
                        @endif
                        <div class="backdrop-blur-xs rounded-lg bg-blue-50/50 p-3">
                            <p class="mb-1 text-xs font-medium text-gray-500">Phone Number</p>
                            <p class="verification-badge verified font-medium text-gray-900">{{ $formattedPhoneNumber }}</p>
                        </div>
                        @if ($formattedBirthDate)
                            <div class="backdrop-blur-xs rounded-lg bg-blue-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Date of Birth</p>
                                <p class="font-medium text-gray-900">{{ $formattedBirthDate }}</p>
                            </div>
                        @endif
                        @if ($user->gender)
                            <div class="backdrop-blur-xs rounded-lg bg-blue-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Gender</p>
                                <p class="font-medium capitalize text-gray-900">{{ $user->gender }}</p>
                            </div>
                        @endif
                        <div class="backdrop-blur-xs rounded-lg bg-blue-50/50 p-3">
                            <p class="mb-1 text-xs font-medium text-gray-500">Registration Date</p>
                            <p class="font-medium text-gray-900">{{ $formattedCreatedAt }}</p>
                        </div>
                        <div class="backdrop-blur-xs rounded-lg bg-blue-50/50 p-3">
                            <p class="mb-1 text-xs font-medium text-gray-500">Last Active</p>
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
                                <h3 class="text-xl font-semibold text-gray-900">Store Information</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Verified
                                </span>
                                <button class="hover-scale rounded-lg bg-blue-50/50 px-3 py-1 text-xs text-gray-600 shadow transition-all hover:bg-white">
                                    <i class="fas fa-pencil-alt mr-1"></i> Edit
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                            <div class="backdrop-blur-xs rounded-lg bg-blue-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Store Name</p>
                                <p class="font-medium text-gray-900">{{ $tokoInfo['name'] }}</p>
                            </div>
                            <div class="backdrop-blur-xs rounded-lg bg-blue-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Store ID</p>
                                <p class="font-medium text-gray-900">{{ $tokoInfo['id'] }}</p>
                            </div>
                            <div class="backdrop-blur-xs rounded-lg bg-blue-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Store Owner</p>
                                <p class="font-medium text-gray-900">{{ $tokoInfo['owner']->name }}</p>
                            </div>
                            <div class="backdrop-blur-xs rounded-lg bg-blue-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Store Employee</p>
                                <p class="font-medium text-gray-900">{{ $tokoInfo['employee_count'] }} User</p>
                            </div>
                            <div class="backdrop-blur-xs rounded-lg bg-blue-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Store Address</p>
                                <p class="font-medium text-gray-900">{{ $tokoInfo['address'] }}</p>
                            </div>
                            <div class="backdrop-blur-xs rounded-lg bg-blue-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">City</p>
                                <p class="font-medium text-gray-900">New York</p>
                            </div>
                            <div class="backdrop-blur-xs rounded-lg bg-blue-50/50 p-3">
                                <p class="mb-1 text-xs font-medium capitalize text-gray-500">Store Status</p>
                                <p class="verification-badge {{ $tokoInfo['status'] }} font-medium capitalize text-gray-900">{{ $tokoInfo['status'] }}</p>
                            </div>
                            <div class="backdrop-blur-xs rounded-lg bg-blue-50/50 p-3">
                                <p class="mb-1 text-xs font-medium text-gray-500">Store Registration Date</p>
                                <p class="font-medium text-gray-900">{{ $tokoInfo['formattedCreatedAt'] }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                @livewire('profile.manage-devices', ['user' => $user], key($user->id))
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
                            <div class="backdrop-blur-xs flex items-center rounded-lg bg-blue-50/50 p-3">
                                <i class="fas fa-check-circle mr-3 text-green-500"></i>
                                <span class="text-gray-700">{{ $formattedPermissions[$i]->formatted_name }}</span>
                            </div>
                        @endfor

                        <!-- Show remaining permissions when expanded -->
                        <template x-if="expanded">
                            <template x-for="formattedPermissions in @js($formattedPermissions->slice(11))">
                                <div class="backdrop-blur-xs flex items-center rounded-lg bg-blue-50/50 p-3">
                                    <i class="fas fa-check-circle mr-3 text-green-500"></i>
                                    <span class="text-gray-700" x-text="formattedPermissions.formatted_name"></span>
                                </div>
                            </template>
                        </template>

                        <!-- More/Close button -->
                        @if (count($formattedPermissions) > 11)
                            <div class="backdrop-blur-xs flex cursor-pointer items-center rounded-lg bg-blue-50/50 p-3 transition-colors hover:bg-blue-100/50" @click="expanded = !expanded">
                                <i class="mr-3 text-gray-500 transition-transform duration-200" :class="expanded ? 'fas fa-chevron-circle-up' : 'fas fa-chevron-circle-down'"></i>
                                <span class="text-gray-700" x-text="expanded ? 'Display Less' : 'Display More (' + {{ count($formattedPermissions) - 11 }} + ')'"></span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="glass-card rounded-2xl p-6 transition-all hover:shadow-lg">
                    <div class="mb-6 flex items-center">
                        <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <button class="hover-scale flex flex-col items-center justify-center rounded-xl bg-blue-50/50 p-3 text-center shadow transition-all hover:bg-white">
                            <i class="fas fa-lock mb-2 text-lg text-red-600"></i>
                            <span class="text-xs font-medium">Reset Password</span>
                        </button>
                        <button class="hover-scale flex flex-col items-center justify-center rounded-xl bg-blue-50/50 p-3 text-center shadow transition-all hover:bg-white">
                            <i class="fas fa-user-cog mb-2 text-lg text-red-600"></i>
                            <span class="text-xs font-medium">Permissions</span>
                        </button>
                        <button class="hover-scale flex flex-col items-center justify-center rounded-xl bg-blue-50/50 p-3 text-center shadow transition-all hover:bg-white">
                            <i class="fas fa-chart-pie mb-2 text-lg text-red-600"></i>
                            <span class="text-xs font-medium">Reports</span>
                        </button>
                        <button class="hover-scale flex flex-col items-center justify-center rounded-xl bg-blue-50/50 p-3 text-center shadow transition-all hover:bg-white">
                            <i class="fas fa-history mb-2 text-lg text-red-600"></i>
                            <span class="text-xs font-medium">Activity</span>
                        </button>
                        <button class="hover-scale flex flex-col items-center justify-center rounded-xl bg-blue-50/50 p-3 text-center shadow transition-all hover:bg-white">
                            <i class="fas fa-ban mb-2 text-lg text-red-600"></i>
                            <span class="text-xs font-medium">Suspend</span>
                        </button>
                        <button class="hover-scale flex flex-col items-center justify-center rounded-xl bg-blue-50/50 p-3 text-center shadow transition-all hover:bg-white">
                            <i class="fas fa-trash mb-2 text-lg text-red-600"></i>
                            <span class="text-xs font-medium">Delete</span>
                        </button>
                    </div>
                </div>

                <!-- Verification Status -->
                <div class="glass-card rounded-2xl p-6 transition-all hover:shadow-lg">
                    <div class="mb-6 flex items-center">
                        <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Verification Status</h3>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="backdrop-blur-xs flex items-center justify-between rounded-lg bg-blue-50/50 p-3">
                            <span class="text-gray-700">Email Verification</span>
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                @if (Auth::user()->email_verified_at)
                                    <i class="fas fa-check-circle mr-1"></i> Verified
                                @else
                                    <i class="fas fa-clock mr-1"></i> Pending
                                @endif
                            </span>
                        </div>
                        <div class="backdrop-blur-xs flex items-center justify-between rounded-lg bg-blue-50/50 p-3">
                            <span class="text-gray-700">Phone Verification</span>
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                @if (Auth::user()->isProfileCompleted)
                                    <i class="fas fa-check-circle mr-1"></i> Verified
                                @else
                                    <i class="fas fa-clock mr-1"></i> Pending
                                @endif
                            </span>
                        </div>
                        <div class="backdrop-blur-xs flex items-center justify-between rounded-lg bg-blue-50/50 p-3">
                            <span class="text-gray-700">Identity Verification</span>
                            <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800">
                                @if ($user->isProfileCompleted())
                                    <i class="fas fa-check-circle mr-1"></i> Verified
                                @else
                                    <i class="fas fa-clock mr-1"></i> Pending
                                @endif
                            </span>
                        </div>
                    </div>
                </div>


                <livewire:auth.recovery-code-manager />
                <!-- Login History -->

            </div>
        </div>
    </main>
@endsection
