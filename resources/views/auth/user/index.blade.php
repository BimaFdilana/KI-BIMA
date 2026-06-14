@extends('layouts.admin')
@section('title', 'User Data')
@section('page_title', 'User Data')
@push('scripts')
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
    <div class="mx-auto">
        <div class="container mx-auto py-6">
            <div class="mb-6 flex items-center justify-between">
                <div class="flex space-x-2">
                    <span
                        class="inline-flex items-center py-2 text-center text-sm font-medium text-gray-500">Menampilkan</span>
                    <button id="dropdownDividerButton" data-dropdown-toggle="dropdownDivider"
                        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200"
                        type="button">
                        <span id="current-length"></span>
                        <svg class="ml-2.5 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <span class="inline-flex items-center py-2 text-center text-sm font-medium text-gray-500">Data
                        Perhalaman</span>
                </div>
                <div class="flex space-x-2">
                    <!-- Dropdown menu -->
                    <div id="dropdownDivider"
                        class="z-10 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow-sm">
                        <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownDividerButton">
                            <li><a data-length="10" class="block px-4 py-2 hover:bg-gray-100">10</a></li>
                            <li><a data-length="25" class="block px-4 py-2 hover:bg-gray-100">25</a></li>
                            <li><a data-length="50" class="block px-4 py-2 hover:bg-gray-100">50</a></li>
                            <li><a data-length="100" class="block px-4 py-2 hover:bg-gray-100">100</a></li>
                        </ul>
                    </div>
                    <div class="relative">
                        <button id="filterDropdownButton" data-dropdown-toggle="filterDropdown"
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200"
                            type="button">
                            <svg class="mr-2 h-4 w-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 20 18">
                                <path
                                    d="M18.85 1.1A1.99 1.99 0 0 0 17.063 0H2.937a2 2 0 0 0-1.566 3.242L6.99 9.868 7 14a1 1 0 0 0 .4.8l4 3A1 1 0 0 0 13 17v-7.132l5.63-6.626a1.99 1.99 0 0 0 .22-2.142Z" />
                            </svg>
                            Filter
                            <svg class="ml-2.5 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                        <!-- Dropdown menu -->
                        <div id="filterDropdown"
                            class="z-30 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow">
                            <ul class="py-2 text-sm text-gray-700" aria-labelledby="filterDropdownButton">
                                <li>
                                    <button id="statusDropdownButton" data-dropdown-toggle="statusDropdown"
                                        data-dropdown-placement="left-start" data-dropdown-trigger="hover" type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100"><i
                                            class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Filter by status</span>
                                    </button>
                                    <div id="statusDropdown"
                                        class="z-10 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow-sm">
                                        <ul class="py-2 text-sm text-gray-700" aria-labelledby="doubleDropdownButton">
                                            <li>
                                                <a href="{{ route('user.index') }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">All Users</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('user.index', ['filter' => ['status' => 'active']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Active Users</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('user.index', ['filter' => ['status' => 'suspended']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Suspended Users</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('user.index', ['filter' => ['status' => 'inactive']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Inactive Users</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('user.index', ['filter' => ['status' => 'deleted']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Deleted Users</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <button id="roleDropdownButton" data-dropdown-trigger="hover"
                                        data-dropdown-toggle="roleDropdown" data-dropdown-placement="left-start"
                                        type="button"
                                        class="flex w-full items-center justify-start px-4 py-2 hover:bg-gray-100"><i
                                            class="fa-solid fa-chevron-left text-xs text-gray-500"></i>
                                        <span class="ml-5">Filter by role</span>
                                    </button>
                                    <div id="roleDropdown"
                                        class="z-10 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow-sm">
                                        <ul class="py-2 text-sm text-gray-700" aria-labelledby="doubleDropdownButton">
                                            <li><a href="{{ route('user.index') }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">All Users</a></li>
                                            <li><a href="{{ route('user.index', ['filter' => ['role' => 'founder']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Founder Users</a></li>
                                            <li><a href="{{ route('user.index', ['filter' => ['role' => 'programmer']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Programmer Users</a></li>
                                            <li><a href="{{ route('user.index', ['filter' => ['role' => 'admin']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Admin Users</a></li>
                                            <li><a href="{{ route('user.index', ['filter' => ['role' => 'accounting']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Accounting Users</a></li>
                                            <li><a href="{{ route('user.index', ['filter' => ['role' => 'operator']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Operator Users</a></li>
                                            <li><a href="{{ route('user.index', ['filter' => ['role' => 'shop']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Shop Users</a></li>
                                            <li><a href="{{ route('user.index', ['filter' => ['role' => 'guest']]) }}"
                                                    class="block px-4 py-2 hover:bg-gray-100">Guest Users</a></li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="relative">
                        <button id="add-user-btn"
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-red-600 px-4 py-2 text-center text-sm font-medium text-white hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-200">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah User
                        </button>
                    </div>
                </div>
            </div>
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <div class="overflow-x-auto">
                    {!! $dataTable->table(['class' => 'w-full text-sm text-left text-gray-500']) !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add User -->
    <div id="add-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50">
        <div class="relative w-full max-w-md p-4">
            <div class="relative rounded-lg bg-white shadow">
                <div class="flex items-start justify-between rounded-t-lg border-b border-gray-200 p-4">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Tambah User
                    </h3>
                    <button id="close-modal-btn" type="button"
                        class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900">
                        <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-6 p-6">
                    <form id="user-form" class="space-y-4">
                        @csrf
                        <div>
                            <label for="username" class="mb-2 block text-sm font-medium text-gray-900">Username</label>
                            <div class="relative">
                                <input type="text" id="username" name="username"
                                    class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 pr-10 text-sm text-gray-900 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Masukan username">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg id="username-checking" class="hidden h-4 w-4 animate-spin text-gray-400"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4" fill="none"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <svg id="username-valid" class="hidden h-4 w-4 text-green-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <svg id="username-invalid" class="hidden h-4 w-4 text-red-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            </div>
                            <div id="username-error" class="mt-1 text-sm text-red-600"></div>
                        </div>
                        <!-- Phone Number Field -->
                        <div>
                            <label for="phone_number" class="mb-2 block text-sm font-medium text-gray-900">No Hp</label>
                            <div class="relative">
                                <input type="text" id="phone_number" name="phone_number"
                                    class="block w-full rounded-lg border-gray-300 bg-white p-2.5 pr-10 text-sm text-gray-900 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Masukan no hp">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg id="phone-checking" class="hidden h-4 w-4 animate-spin text-gray-400"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4" fill="none"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <svg id="phone-valid" class="hidden h-4 w-4 text-green-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <svg id="phone-invalid" class="hidden h-4 w-4 text-red-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            </div>
                            <div id="phone-error" class="mt-1 text-sm text-red-600"></div>
                        </div>
                        <!-- Password Field -->
                        <div>
                            <label for="password" class="mb-2 block text-sm font-medium text-gray-900">Password</label>
                            <div class="relative">
                                <input type="password" id="password" name="password"
                                    class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 pr-10 text-sm text-gray-900 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="••••••••">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg id="password-valid" class="hidden h-4 w-4 text-green-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <svg id="password-invalid" class="hidden h-4 w-4 text-red-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            </div>
                            <!-- Password Requirements -->
                            <div id="password-requirements" class="mt-2 hidden space-y-1">
                                <div id="length-requirement" class="flex items-center space-x-2">
                                    <i id="length-icon" class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span id="length-text" class="text-xs">Minimal 8 karakter</span>
                                </div>
                                <div id="uppercase-requirement" class="flex items-center space-x-2">
                                    <i id="uppercase-icon" class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span id="uppercase-text" class="text-xs">Huruf besar (A-Z)</span>
                                </div>
                                <div id="lowercase-requirement" class="flex items-center space-x-2">
                                    <i id="lowercase-icon" class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span id="lowercase-text" class="text-xs">Huruf kecil (a-z)</span>
                                </div>
                                <div id="number-requirement" class="flex items-center space-x-2">
                                    <i id="number-icon" class="fa-solid fa-circle-exclamation text-xs"></i>
                                    <span id="number-text" class="text-xs">Angka (0-9)</span>
                                </div>
                            </div>
                        </div>
                        <!-- Role Field -->
                        <div>
                            <label for="role" class="mb-2 block text-sm font-medium text-gray-900">Role</label>
                            <select id="role" name="role"
                                class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 shadow-sm focus:border-red-500 focus:ring-red-500">
                                <option value="">Pilih Role</option>
                                @role('founder|programmer')
                                    <option value="founder">Founder</option>
                                    <option value="programmer">Programmer</option>
                                    <option value="admin">Admin</option>
                                @endrole
                                <option value="accounting">Accounting</option>
                                <option value="operator">Operator</option>
                                <option value="shop">Shop</option>
                                <option value="guest">Guest</option>
                            </select>
                            <div id="role-error" class="mt-1 text-sm text-red-600"></div>
                        </div>
                    </form>
                </div>
                <div class="flex items-center justify-end space-x-2 rounded-b-lg border-t border-gray-200 p-6">
                    <button id="close-modal-bottom-btn" type="button"
                        class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus:z-10 focus:outline-none focus:ring-4 focus:ring-red-300">
                        Cancel
                    </button>
                    <button id="submit-user-btn" type="button"
                        class="rounded-lg bg-red-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300">
                        Tambah
                    </button>
                </div>
            </div>
        </div>
    </div>

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

    <div id="message-modal"
        class="z-60 fixed inset-0 hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50">
        <div class="relative w-full max-w-md">
            <div id="message-modal-container"
                class="animate-jump-in w-full max-w-md overflow-hidden rounded-lg bg-white shadow-xl transition-all duration-300">
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
                            <button id="confirmBtn" onclick="confirmAction()"
                                class="cursor-pointer rounded-lg bg-red-500 px-6 py-2 font-bold text-white transition duration-200 hover:bg-red-600">
                                Delete
                            </button>
                            <button id="closeMessageModalBtn" onclick="closeModal()"
                                class="cursor-pointer rounded-lg bg-gray-300 px-6 py-2 font-bold text-gray-800 transition duration-200 hover:bg-gray-400">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="edit-user-modal"
        class="fade-in fixed inset-0 z-40 flex hidden items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
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
            <form id="edit-user-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <!-- Modal Content -->
                <div class="p-6">
                    <!-- Display Validation Errors -->
                    <div id="edit-user-form-errors" class="mb-4 hidden rounded-md border border-red-200 bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 id="edit-user-form-errors-title" class="text-sm font-medium text-red-800">Please fix
                                    the following errors:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul id="edit-user-form-errors-list-items" class="list-disc space-y-1 pl-5">
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Section -->
                    <div class="mb-8 flex flex-col gap-6 md:flex-row">
                        <!-- Profile Picture -->
                        <div class="flex w-full flex-col items-center md:w-1/4">
                            <div id="profileImageContainer" class="relative mb-4">
                                <div id="profileImage"
                                    class="flex h-32 w-32 items-center justify-center rounded-full border-4 border-red-100 bg-gray-200 shadow-md">
                                    <i class="fas fa-user text-4xl text-gray-400"></i>
                                </div>
                                <button type="button" onclick="document.getElementById('profileUpload').click()"
                                    class="absolute bottom-0 right-0 rounded-full bg-red-500 p-2 text-white transition hover:bg-red-600">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            <label for="profileUpload"
                                class="file-input-label inline-flex cursor-pointer items-center rounded-lg bg-red-50 px-4 py-2 font-medium text-red-700 transition hover:bg-red-100">
                                <i class="fas fa-upload mr-2"></i>Upload New
                                <input type="file" id="profileUpload" name="profile_image" class="hidden"
                                    accept="image/*">
                            </label>
                            <p id="profileUploadError" class="mt-1 hidden text-xs text-red-600"></p>
                        </div>

                        <!-- Basic Info -->
                        <div class="w-full md:w-3/4">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Full Name <span
                                            class="text-red-600">*</span></label>
                                    <input id="nameEdit" name="name" type="text" value="" required
                                        class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <p id="nameError" class="mt-1 hidden text-xs text-red-600"></p>
                                </div>
                                <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Username <span
                                            class="text-red-600">*</span></label>
                                    <input id="usernameEdit" name="username" type="text" value="" required
                                        class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <p id="usernameError" class="mt-1 hidden text-xs text-red-600"></p>
                                </div>
                                <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Gender</label>
                                    <select id="genderSelectEdit" name="gender"
                                        class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <p id="genderError" class="mt-1 hidden text-xs text-red-600"></p>
                                </div>
                                <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Date of Birth</label>
                                    <input id="dateOfBirthEdit" name="date_of_birth" type="date" value=""
                                        class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <p id="dateOfBirthError" class="mt-1 hidden text-xs text-red-600"></p>
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
                                            <input id="phoneVerifiedEdit" type="checkbox" name="phone_verified_at"
                                                value="1">
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>
                                <input id="phoneNumberEdit" type="tel" value="" name="phone_number"
                                    class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>

                            <!-- Email -->
                            <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                <div class="mb-1 flex items-center justify-between">
                                    <label class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <div class="flex items-center">
                                        <span class="mr-2 text-xs text-gray-500">Verified</span>
                                        <label class="toggle-switch">
                                            <input id="emailVerifiedEdit" type="checkbox" name="email_verified_at"
                                                value="1">
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>
                                <input id="emailEdit" type="email" value="" name="email" required
                                    class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>

                            <!-- Address -->
                            <div class="card rounded-lg border border-gray-100 bg-white p-4 md:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Address</label>
                                <textarea id="addressEdit"
                                    class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500"
                                    rows="2" name="address"></textarea>
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
                                    <label class="block text-sm font-medium text-gray-700">Two-Factor
                                        Authentication</label>
                                    <label class="toggle-switch">
                                        <input id="twoFactorInfo" type="checkbox" name="two_factor_enabled"
                                            value="1">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Add an extra layer of security to your account</p>
                            </div>

                            <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-medium text-gray-700">Role & Permission</label>
                                    <div class="flex flex-wrap gap-1">
                                        <span id="roleUser"
                                            class="rounded bg-red-500 px-2 py-1 text-xs text-white">User</span>
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
                                    <div
                                        class="flex flex-1 flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-200 p-2">
                                        <div id="ktpImageContainer">
                                            <div id="ktpImage"
                                                class="mb-3 flex h-32 w-full items-center justify-center rounded bg-gray-100">
                                                <i class="fas fa-id-card text-2xl text-gray-400"></i>
                                            </div>
                                        </div>
                                        <label for="ktpUploadEdit"
                                            class="file-input-label inline-flex cursor-pointer items-center rounded-lg bg-red-50 px-4 py-2 font-medium text-red-700 transition hover:bg-red-100">
                                            <i class="fas fa-upload mr-2"></i>Upload KTP
                                            <input id="ktpUploadEdit" type="file" name="ktp_image" class="hidden"
                                                accept="image/*">
                                        </label>
                                        <p id="ktpImageError" class="mt-1 hidden text-xs text-red-600"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- KTP Details -->
                            <div class="w-full md:w-2/3">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                        <label class="mb-1 block text-sm font-medium text-gray-700">KTP Number</label>
                                        <input id="ktpNumberEdit" type="text" name="ktp_number" value=""
                                            class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <p id="ktpNumberError" class="mt-1 hidden text-xs text-red-600"></p>
                                    </div>
                                    <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                        <label class="mb-1 block text-sm font-medium text-gray-700">KTP Name</label>
                                        <input id="ktpNameEdit" type="text" name="ktp_name" value=""
                                            class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <p id="ktpNameError" class="mt-1 hidden text-xs text-red-600"></p>
                                    </div>
                                    <div class="card rounded-lg border border-gray-100 bg-white p-4 md:col-span-2">
                                        <label class="mb-1 block text-sm font-medium text-gray-700">KTP Address</label>
                                        <textarea id="ktpAddressEdit" name="ktp_address" rows="2"
                                            class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                                        <p id="ktpAddressError" class="mt-1 hidden text-xs text-red-600"></p>
                                    </div>
                                    <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                        <div class="mb-1 flex items-center justify-between">
                                            <label class="block text-sm font-medium text-gray-700">KTP Verified</label>
                                            <label class="toggle-switch">
                                                <input id="ktpVerifiedEdit" type="checkbox" name="ktp_verified"
                                                    value="1">
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
                                <select id="statusEdit" name="status"
                                    class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                                <p id="statusError" class="mt-1 hidden text-xs text-red-600"></p>
                            </div>
                            <div class="card rounded-lg border border-gray-100 bg-white p-4">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Quick Actions</label>
                                <div class="flex gap-2 text-sm">
                                    <button type="button" id="resetPasswordBtn"
                                        class="rounded-md bg-yellow-500 px-4 py-2 text-white transition hover:bg-yellow-600">
                                        <i class="fas fa-key mr-1"></i>Reset Password
                                    </button>
                                    <button type="button" id="deleteUserBtn"
                                        class="rounded-md bg-red-500 px-4 py-2 text-white transition hover:bg-red-600">
                                        <i class="fas fa-trash mr-1"></i>Delete Account
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="sticky bottom-0 z-10 flex justify-end border-t border-gray-200 bg-white px-6 py-4">
                    <button type="button" id="closeEditUserModal"
                        class="mr-3 rounded-lg bg-gray-200 px-6 py-2 font-medium text-gray-800 transition hover:bg-gray-300">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="button" id="saveEditUserBtn"
                        class="rounded-lg bg-red-600 px-6 py-2 font-medium text-white shadow-md transition hover:bg-red-700">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const modalIcon = document.getElementById('modalIcon');
        const actionButtons = document.getElementById('actionButtons');
        const confirmButton = document.getElementById('confirmBtn');
        const cancelButton = document.getElementById('closeMessageModalBtn');
        const messageModal = document.getElementById('message-modal');
        const messageModalContainer = document.getElementById('message-modal-container');

        cancelButton.addEventListener('click', closeModal);


        function showLoading() {
            $('#loadingOverlay').removeClass('hidden');
        }

        function hideLoading() {
            $('#loadingOverlay').addClass('hidden');
        }

        function closeModal() {
            modalTitle.textContent = '';
            modalMessage.textContent = '';
            modalIcon.className = '';
            actionButtons.classList.add('hidden');
            confirmButton.dataset.action = '';
            confirmButton.dataset.username = '';
            confirmButton.textContent = '';
            confirmButton.disabled = true;
            cancelButton.disabled = true;
            messageModal.classList.remove('flex');
            messageModal.classList.add('hidden');
        }

        $(document).ready(function() {
            // Inisialisasi Flowbite Modal
            const modalEl = document.getElementById('add-modal');
            const addUserModal = new Modal(modalEl);

            // Reset form dan validasi
            function resetForm() {
                $('#username').val('');
                $('#phone_number').val('');
                $('#password').val('');
                $('#role').val('');
                $('.feedback svg').hide();
                $('.error').text('');
                $('#password-requirements').addClass('hidden');
                $('#submit-user-btn').prop('disabled', true);
            }

            // Event tombol add user
            $('#add-user-btn').on('click', function() {
                addUserModal.show();
                resetForm();
            });

            // Tutup modal
            $('#close-modal-btn, #close-modal-bottom-btn').on('click', function() {
                addUserModal.hide();
            });

            // Validasi username
            $('#username').on('input', function() {
                let username = $(this).val();
                $('#username-error').text('');
                $('#username-checking, #username-valid, #username-invalid').hide();

                if (username.length < 3) {
                    $('#username-error').text('Username minimal 3 karakter');
                    $('#username-invalid').show();
                    return;
                }

                $('#username-checking').show();

                $.post("{{ route('user.check-username') }}", {
                    _token: "{{ csrf_token() }}",
                    username: username
                }, function(res) {
                    if (res.exists) {
                        $('#username-error').text('Username sudah digunakan');
                        $('#username-checking, #username-valid').hide();
                        $('#username-invalid').show();
                        $('#username').removeClass('focus:border-green-500 focus:ring-green-500');
                        $('#username').addClass('focus:border-red-500 focus:ring-red-500');
                    } else {
                        $('#username-checking').hide();
                        $('#username-valid').show();
                        validateForm();
                        $('#username').removeClass('focus:border-red-500 focus:ring-red-500');
                        $('#username').addClass('focus:border-green-500 focus:ring-green-500');
                    }
                });
            });

            // Validasi phone number
            $('#phone_number').on('input', function() {
                let phone = $(this).val();
                $('#phone-error').text('');
                $('#phone-checking, #phone-valid, #phone-invalid').hide();

                const phoneRegex = /^(\+62|62|0)[0-9]{9,13}$/;
                if (!phoneRegex.test(phone)) {
                    $('#phone-error').text('Format nomor HP tidak valid');
                    $('#phone-invalid').show();
                    return;
                }

                $('#phone-checking').show();

                $.post("{{ route('user.check-phone') }}", {
                    _token: "{{ csrf_token() }}",
                    phone_number: phone
                }, function(res) {
                    if (res.exists) {
                        $('#phone-error').text('Nomor HP sudah digunakan');
                        $('#phone-checking, #phone-valid').hide();
                        $('#phone-invalid').show();
                        $('#phone_number').removeClass(
                            'focus:border-green-500 focus:ring-green-500');
                        $('#phone_number').addClass('focus:border-red-500 focus:ring-red-500');
                    } else {
                        $('#phone-checking').hide();
                        $('#phone-valid').show();
                        validateForm();
                        $('#phone_number').removeClass('focus:border-red-500 focus:ring-red-500');
                        $('#phone_number').addClass('focus:border-green-500 focus:ring-green-500');
                    }
                });
            });

            // Validasi password
            $('#password').on('input', function() {
                let pwd = $(this).val();
                $('#password-valid, #password-invalid').hide();
                $('#password-requirements').toggleClass('hidden', pwd === '');

                let length = pwd.length >= 8,
                    uppercase = /[A-Z]/.test(pwd),
                    lowercase = /[a-z]/.test(pwd),
                    number = /[0-9]/.test(pwd);

                // Update requirement statuses
                $('#length-requirement').toggleClass('text-green-500 ', length).toggleClass('text-red-500',
                    !length);
                $('#length-icon').toggleClass('fa-circle-check', length).toggleClass(
                    'fa-circle-exclamation', !length);
                $('#uppercase-requirement').toggleClass('text-green-500', uppercase).toggleClass(
                    'text-red-500', !uppercase);
                $('#uppercase-icon').toggleClass('fa-circle-check', uppercase).toggleClass(
                    'fa-circle-exclamation', !uppercase);
                $('#lowercase-requirement').toggleClass('text-green-500 ', lowercase).toggleClass(
                    'text-red-500', !lowercase);
                $('#lowercase-icon').toggleClass('fa-circle-check', lowercase).toggleClass(
                    'fa-circle-exclamation', !lowercase);
                $('#number-requirement').toggleClass('text-green-500', number).toggleClass('text-red-500', !
                    number);
                $('#number-icon').toggleClass('fa-circle-check', number).toggleClass(
                    'fa-circle-exclamation', !number);

                // Check if all requirements are met
                if (length && uppercase && lowercase && number) {
                    // Show valid icon and hide requirements after 2 seconds
                    $('#password-valid').show();
                    $('#password-requirements').removeClass('hidden');
                    validateForm();
                    $('#password').removeClass('focus:border-red-500 focus:ring-red-500');
                    $('#password').addClass('focus:border-green-500 focus:ring-green-500');
                    $('#password-requirements').addClass('hidden');
                } else {
                    // Show invalid icon and keep requirements visible
                    $('#password-invalid').show();
                    $('#password-requirements').removeClass('hidden');
                    $('#password').removeClass('focus:border-green-500 focus:ring-green-500');
                    $('#password').addClass('focus:border-red-500 focus:ring-red-500');
                }
            });

            // Validasi role
            $('#role').on('change', function() {
                if ($(this).val()) {
                    validateForm();
                }
            });

            // Cek apakah semua validasi terpenuhi
            function validateForm() {
                let valid =
                    $('#username-valid').is(':visible') &&
                    $('#phone-valid').is(':visible') &&
                    $('#password-valid').is(':visible') &&
                    $('#role').val();

                $('#submit-user-btn').prop('disabled', !valid);
            }

            // Submit form
            $('#submit-user-btn').on('click', function() {
                let formData = $('#user-form').serialize();
                let confirmBtn = document.getElementById('submit-user-btn');
                let cancelBtn = document.getElementById('close-modal-bottom-btn');
                confirmBtn.innerHTML = '<i class="fas fa-spinner animate-spin"></i> Menyimpan...';
                confirmBtn.disabled = true;
                cancelBtn.disabled = true;
                $.ajax({
                    url: "{{ route('user.store') }}",
                    method: "POST",
                    data: formData,
                    success: function(res) {
                        addUserModal.hide();
                        window.LaravelDataTables['user-table'].ajax.reload();
                        confirmBtn.innerHTML = 'Tambah';
                        confirmBtn.disabled = false;
                        cancelBtn.disabled = false;
                        showMessageModal(res.message, 'success');
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.username) $('#username-error').text(errors.username[0]);
                        if (errors.phone_number) $('#phone-error').text(errors.phone_number[0]);
                        if (errors.role) $('#role-error').text(errors.role[0]);
                        confirmBtn.innerHTML = 'Tambah';
                        confirmBtn.disabled = false;
                        cancelBtn.disabled = false;
                        showMessageModal('Terjadi kesalahan saat menyimpan data', 'error');
                    }
                });
            });
        });
        // Initialize DataTable length dropdown
        $(function() {
            let table = $('#user-table').DataTable();

            function updateCurrentLength() {
                $('#current-length').text(table.page.len());
            }
            updateCurrentLength();
            $('#dropdownDivider li a').on('click', function(e) {
                e.preventDefault();
                let length = $(this).data('length');
                table.page.len(length).draw();
                updateCurrentLength();
            });
            table.on('length.dt', function() {
                updateCurrentLength();
            });
        });

        $(document).on('click', '.delete-user-modal', function() {
            let username = $(this).data('username');
            confirmDelete(username);
        });

        $(document).on('click', '.restore-user-modal', function() {
            let username = $(this).data('username');
            confirmRestore(username);
        });

        function confirmResetPassword(username) {
            modalTitle.textContent = 'Reset Password';
            modalMessage.textContent = 'Anda yakin ingin reset password user ' + username + '?';
            modalIcon.className =
                'fas fa-exclamation-triangle animate__animated animate__rotateIn text-yellow-500 text-6xl';
            actionButtons.classList.remove('hidden');
            confirmButton.dataset.action = 'reset-password';
            confirmButton.dataset.username = username;
            confirmButton.textContent = 'Reset Password';
            confirmButton.disabled = false;
            cancelButton.disabled = false;
            messageModal.classList.remove('hidden');
            messageModal.classList.add('flex');
        }

        function confirmRestore(username) {
            modalTitle.textContent = 'Restore User';
            modalMessage.textContent = 'Anda yakin ingin restore user ' + username + '?';
            modalIcon.className = 'fas fa-rotate animate__animated animate__rotateIn text-yellow-500 text-6xl';
            actionButtons.classList.remove('hidden');
            confirmButton.dataset.action = 'restore';
            confirmButton.dataset.username = username;
            confirmButton.textContent = 'Restore';
            confirmButton.disabled = false;
            cancelButton.disabled = false;
            messageModal.classList.remove('hidden');
            messageModal.classList.add('flex');
        }

        function confirmDelete(username) {
            modalTitle.textContent = 'Delete User';
            modalMessage.textContent = 'Anda yakin ingin delete user ' + username + '?';
            modalIcon.className = 'fas fa-trash animate__animated animate__rotateIn text-red-500 text-6xl';
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
            if (action === 'delete') {
                confirmBtn.innerHTML = '<i class="fas fa-spinner animate-spin"></i> Deleting...';
                confirmBtn.disabled = true;
                cancelButton.disabled = true;
                deleteUser(username);
            } else if (action === 'restore') {
                confirmBtn.innerHTML = '<i class="fas fa-spinner animate-spin"></i> Restoring...';
                confirmBtn.disabled = true;
                cancelButton.disabled = true;
                deleteUser(username);
            } else if (action === 'reset-password') {
                confirmBtn.innerHTML = '<i class="fas fa-spinner animate-spin"></i> Resetting Password...';
                confirmBtn.disabled = true;
                cancelButton.disabled = true;
                resetPassword(username);
            }
        }

        function reloadTable() {
            $('#user-table').DataTable().draw(false);
        }

        function deleteUser(username) {
            $.ajax({
                url: "{{ route('user.delete', ['username' => ':username']) }}".
                replace(':username', username),
                type: "POST",
                success: function(response) {
                    showMessageModal(response.message, 'success');
                    setTimeout(function() {
                        reloadTable();
                    }, 3000);
                },
                error: function(xhr) {
                    showMessageModal(xhr.responseJSON.message, 'error');
                }
            });
        }

        function resetPassword(username) {
            $.ajax({
                url: "{{ route('user.reset-password', ['username' => ':username']) }}".
                replace(':username', username),
                type: "POST",
                success: function(response) {
                    showMessageModal(response.message, 'success');
                    setTimeout(function() {
                        reloadTable();
                    }, 3000);
                },
                error: function(xhr) {
                    showMessageModal(xhr.responseJSON.message, 'error');
                }
            });
        }


        const editUserModal = document.getElementById('edit-user-modal');
        const editUserForm = document.getElementById('edit-user-form');
        const profileImageContainer = document.getElementById('profileImageContainer');
        const ktpImageContainer = document.getElementById('ktpImageContainer');
        const profileImage = document.getElementById('profileImage');


        $(document).on('click', '.edit-user-modal', function() {
            let username = $(this).data('username');
            openEditUserModal(username);
        });

        $(document).on('click', '#closeEditUserModal', function() {
            closeEditUserModal();
        });

        function closeEditUserModal() {
            editUserModal.classList.remove('flex');
            editUserModal.classList.add('hidden');
        }

        function openEditUserModal(username) {
            showLoading();

            $.ajax({
                url: "{{ route('user.show', ':user') }}".replace(':user', username),
                type: "GET",
                success: function(response) {
                    user = response.data;
                    showDataEditForm(user);
                    hideLoading();
                    editUserModal.classList.remove('hidden');
                    editUserModal.classList.add('flex');
                },
                error: function(xhr) {
                    hideLoading();
                    showMessageModal(xhr.responseJSON.message, 'error');
                }
            });
        }

        function formatDate(dateString) {
            let date = new Date(dateString);
            let year = date.getFullYear();
            let month = `0${date.getMonth() + 1}`.slice(-2);
            let day = `0${date.getDate()}`.slice(-2);

            return `${year}-${month}-${day}`;
        }

        function showDataEditForm(user) {
            // Basic info
            document.getElementById('nameEdit').value = user.name || '';
            document.getElementById('usernameEdit').value = user.username || '';
            document.getElementById('genderSelectEdit').value = user.gender || '';
            document.getElementById('dateOfBirthEdit').value = formatDate(user.date_of_birth);

            // Contact info
            document.getElementById('phoneNumberEdit').value = user.phone_number || '';
            document.getElementById('phoneVerifiedEdit').checked = user.phone_verified_at || false;
            document.getElementById('emailEdit').value = user.email || '';
            document.getElementById('emailVerifiedEdit').checked = user.email_verified_at || false;
            document.getElementById('addressEdit').value = user.address || '';

            // Security
            document.getElementById('twoFactorInfo').checked = user.two_factor_enabled || false;
            document.getElementById('roleUser').textContent = user.role_name || 'User';

            // KTP info
            if (user.ktp_image) {
                const ktpImageUrl = `{{ asset('storage/') }}/${user.ktp_image}`;
                updateKtpImage(ktpImageUrl);
            } else {
                updateKtpImage(null);
            }

            if (user.profile_photo_path) {
                const profileImageUrl = `{{ asset('storage/') }}/${user.profile_photo_path}`;
                updateProfileImage(profileImageUrl);
            } else {
                updateProfileImage(null);
            }
            document.getElementById('ktpNumberEdit').value = user.ktp_number || '';
            document.getElementById('ktpNameEdit').value = user.ktp_name || '';
            document.getElementById('ktpVerifiedEdit').checked = user.ktp_verified || false;
            document.getElementById('ktpAddressEdit').value = user.ktp_address || '';

            // Status
            document.getElementById('statusEdit').value = user.status || 'active';

            // Quick actions
            document.getElementById('resetPasswordBtn').onclick = function() {
                confirmResetPassword(user.username);
            };
            document.getElementById('deleteUserBtn').onclick = function() {
                confirmDelete(user.username);
            };

            document.getElementById('saveEditUserBtn').onclick = function() {
                saveEditUser(user.id);
            };
        }

        function enableEditUserForm() {
            document.getElementById('saveEditUserBtn').innerHTML = 'Save Changes';
            document.getElementById('saveEditUserBtn').disabled = false;
            document.getElementById('close-modal-bottom-btn').disabled = false;
        }

        function disableEditUserForm() {
            document.getElementById('saveEditUserBtn').innerHTML = '<i class="fas fa-spinner animate-spin"></i> Saving...';
            document.getElementById('saveEditUserBtn').disabled = true;
            document.getElementById('close-modal-bottom-btn').disabled = true;
        }

        function resetEditUserForm() {
            document.getElementById('edit-user-form').reset();
            document.getElementById('edit-user-form-errors').classList.add('hidden');
        }


        function saveEditUser(id) {
            showLoading();
            disableEditUserForm();
            clearValidationErrors();

            // Buat FormData dari form
            let formData = new FormData($('#edit-user-form')[0]);

            // PENTING: Hapus checkbox values yang otomatis diambil dari form
            formData.delete('phone_verified_at');
            formData.delete('email_verified_at');
            formData.delete('ktp_verified');
            formData.delete('two_factor_enabled');

            // Tambahkan manual dengan nilai 0 atau 1
            formData.append('phone_verified_at', $('#phoneVerifiedEdit').is(':checked') ? '1' : '0');
            formData.append('email_verified_at', $('#emailVerifiedEdit').is(':checked') ? '1' : '0');
            formData.append('ktp_verified', $('#ktpVerifiedEdit').is(':checked') ? '1' : '0');
            formData.append('two_factor_enabled', $('#twoFactorInfo').is(':checked') ? '1' : '0');

            formData.append('_method', 'PUT');

            // Debug: Lihat isi FormData
            console.log('=== FormData Debug ===');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            console.log('=== End Debug ===');

            $.ajax({
                url: "{{ route('user.update-api', ':id') }}".replace(':id', id),
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    hideLoading();
                    enableEditUserForm();
                    closeEditUserModal();
                    reloadTable();
                    showMessageModal(response.message, 'success');
                },
                error: function(xhr) {
                    console.log(xhr.responseJSON.errors);
                    hideLoading();
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        displayGeneralErrors(xhr.responseJSON.errors);
                        displayFieldErrors(xhr.responseJSON.errors);
                        enableEditUserForm();
                    } else {
                        showMessageModal(xhr.responseJSON.message ||
                            'An error occurred while updating the user.', 'error');
                        enableEditUserForm();
                    }
                }
            });
        }

        function closeEditUserModal() {
            editUserModal.classList.remove('flex');
            editUserModal.classList.add('hidden');
            clearValidationErrors();
            enableEditUserForm();
            resetEditUserForm();
        }


        function clearValidationErrors() {
            // Hide general error container
            const errorDiv = document.getElementById('edit-user-form-errors');
            if (errorDiv) {
                errorDiv.classList.add('hidden');
                errorDiv.innerHTML = '';
            }

            // Clear all field-specific errors
            const errorElements = document.querySelectorAll('[id$="Error"]');
            errorElements.forEach(element => {
                element.classList.add('hidden');
                element.textContent = '';
            });

            // Remove error styling from input fields
            const inputElements = document.querySelectorAll(
                '#edit-user-form input, #edit-user-form select, #edit-user-form textarea');
            inputElements.forEach(element => {
                element.classList.remove('border-red-500', 'bg-red-50');
                element.classList.add('border-gray-200', 'bg-gray-50');
            });
        }

        function displayGeneralErrors(errors) {
            const errorDiv = document.getElementById('edit-user-form-errors');
            errorDiv.innerHTML = '';
            errorDiv.classList.remove('hidden');
            Object.entries(errors).forEach(([field, messages]) => {
                messages.forEach(message => {
                    const errorItem = document.createElement('div');
                    errorItem.className = 'flex items-center text-sm text-red-600';
                    errorItem.innerHTML = `<svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="ml-3">${message}</span>`;
                    errorDiv.appendChild(errorItem);
                });
            });


        }

        function displayFieldErrors(errors) {
            // Field mapping for error display
            const fieldMapping = {
                'name': 'nameError',
                'username': 'usernameError',
                'email': 'emailError',
                'phone_number': 'phoneNumberError',
                'gender': 'genderError',
                'date_of_birth': 'dateOfBirthError',
                'address': 'addressError',
                'ktp_number': 'ktpNumberError',
                'ktp_name': 'ktpNameError',
                'ktp_address': 'ktpAddressError',
                'status': 'statusError',
                'profile_image': 'profileUploadError',
                'ktp_image': 'ktpImageError'
            };

            // Display errors for each field
            Object.entries(errors).forEach(([field, messages]) => {
                const errorElementId = fieldMapping[field];
                if (errorElementId) {
                    const errorElement = document.getElementById(errorElementId);
                    const inputElement = document.querySelector(`[name="${field}"]`);

                    if (errorElement && messages.length > 0) {
                        // Show error message
                        errorElement.textContent = messages[0]; // Show first error message
                        errorElement.classList.remove('hidden');

                        // Add error styling to input field
                        if (inputElement) {
                            inputElement.classList.remove('border-gray-200', 'bg-gray-50');
                            inputElement.classList.add('border-red-500', 'bg-red-50');
                        }
                    }
                }
            });
        }



        function updateKtpImage(ktpImageUrl) {
            const container = document.getElementById('ktpImageContainer');
            if (ktpImageUrl && ktpImageUrl !== null) {
                container.innerHTML =
                    `<img id="ktpImage" src="${ktpImageUrl}" alt="KTP Image" class="mb-3 h-auto max-h-40 w-full rounded object-contain">`;
            } else {
                container.innerHTML = `<div id="ktpImage" class="mb-3 flex h-32 w-full items-center justify-center rounded bg-gray-100">
            <i class="fas fa-id-card text-2xl text-gray-400"></i>
        </div>`;
            }
        }

        function updateProfileImage(profileImageUrl) {
            const container = document.getElementById('profileImageContainer');
            if (profileImageUrl && profileImageUrl !== null) {
                container.innerHTML =
                    `<img id="profileImage" src="${profileImageUrl}" alt="Profile" class="h-32 w-32 rounded-full border-4 border-red-100 object-cover shadow-md">`;
            } else {
                container.innerHTML = `<div id="profileImage" class="flex h-32 w-32 items-center justify-center rounded-full border-4 border-red-100 bg-gray-200 shadow-md">
            <i class="fas fa-user text-4xl text-gray-400"></i>
        </div>`;
            }
        }

        // File upload handlers
        document.getElementById('profileUpload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    updateProfileImage(e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('ktpUploadEdit').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    updateKtpImage(e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        function showMessageModal(message, type) {
            if (messageModal.classList.contains('hidden')) {
                messageModal.classList.remove('hidden');
                messageModal.classList.add('flex');
            }
            actionButtons.classList.add('hidden');
            modalTitle.textContent = type === 'success' ? 'Success' : 'Error';
            modalMessage.textContent = message;
            modalIcon.className = type === 'success' ?
                'fas fa-check-circle animate__animated animate__rotateIn text-6xl text-green-500' :
                'fas fa-circle-exclamation animate__animated animate__rotateIn text-6xl text-red-500';
            iconContainer.className = type === 'success' ? 'animate__animated animate__rotateIn my-6 flex justify-center' :
                'animate__animated animate__rotateIn my-6 flex justify-center';
            messageModalContainer.className = type === 'success' ?
                'animate-jump-in w-full max-w-md overflow-hidden rounded-lg bg-white shadow-xl transition-all duration-300' :
                'animate-jump-in w-full max-w-md overflow-hidden rounded-lg bg-white shadow-xl transition-all duration-300';
            setTimeout(function() {
                messageModal.classList.remove('flex');
                messageModal.classList.add('hidden');
            }, 3000);
        }
    </script>
@endpush
