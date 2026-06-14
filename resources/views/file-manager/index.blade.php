@extends('layouts.admin')


@section('nav_title', 'File Manager')

@section('sub_title', 'File Manager')
@section('page_title', ucwords($breadcrumb[count($breadcrumb) - 1]['name']))

@push('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }

        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
@endpush

@section('content')
    <div class="py-6 mx-auto" x-data="fileManager()">
        <div class="container mx-auto py-8">
            <!-- Header -->
            <div class="mb-6 rounded-lg bg-white p-6 shadow-md">
                <!-- Storage Statistics -->
                <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-500">
                                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-600">Total Files</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['totalFiles'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-500">
                                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-600">Total Size</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['totalSize'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-500">
                                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-600">Disk Used</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['diskUsage']['used'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-500">
                                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-600">Disk Usage</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['diskUsage']['percentage'] }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Disk Usage Bar -->
                <div class="mb-6">
                    <div class="mb-2 flex justify-between text-sm text-gray-600">
                        <span>Storage Usage</span>
                        <span>{{ $stats['diskUsage']['used'] }} of {{ $stats['diskUsage']['total'] }}</span>
                    </div>
                    <div class="h-3 w-full rounded-full bg-gray-200">
                        <div class="h-3 rounded-full bg-gradient-to-r from-red-500 to-red-600 transition-all duration-300"
                            style="width: {{ $stats['diskUsage']['percentage'] }}%"></div>
                    </div>
                </div>

                <!-- File Extensions -->
                <div class="rounded-lg bg-gray-50 p-4">
                    <h3 class="mb-3 text-lg font-semibold text-gray-800">File Types</h3>
                    <div class="grid grid-cols-2 gap-2 md:grid-cols-4 lg:grid-cols-6">
                        @foreach ($stats['extensionCounts'] as $extension => $count)
                            <div class="rounded-md border border-red-100 bg-white p-2 text-center">
                                <div class="text-xs font-medium text-red-600">{{ strtoupper($extension ?: 'NO EXT') }}</div>
                                <div class="text-lg font-bold text-gray-900">{{ $count }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Navigation & Actions -->
            <div class="mb-6 rounded-lg bg-white p-6 shadow-md">
                <!-- Breadcrumb -->
                <nav class="mb-4 flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        @foreach ($breadcrumb as $crumb)
                            <li class="inline-flex items-center">
                                @if (!$loop->last)
                                    <a href="?disk={{ $disk }}&path={{ $crumb['path'] }}"
                                        class="inline-flex items-center text-sm font-medium text-red-600 hover:text-red-700">
                                        {{ $crumb['name'] }}
                                    </a>
                                    @if (!$loop->last)
                                        <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                @else
                                    <span
                                        class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $crumb['name'] }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2">
                    <button @click="showUploadModal = true"
                        class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white ring-red-300 transition duration-150 ease-in-out hover:bg-red-700 focus:border-red-900 focus:outline-none focus:ring active:bg-red-900 disabled:opacity-25">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                            </path>
                        </svg>
                        Upload File
                    </button>

                    <button @click="showFolderModal = true"
                        class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white ring-red-300 transition duration-150 ease-in-out hover:bg-red-700 focus:border-red-900 focus:outline-none focus:ring active:bg-red-900 disabled:opacity-25">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        New Folder
                    </button>

                    <select x-model="currentDisk" @change="changeDisk()"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="public">Public Storage</option>
                        <option value="local">Local Storage</option>
                    </select>
                </div>
            </div>

            <!-- File List -->
            <div class="overflow-hidden rounded-lg bg-white shadow-md">
                <div class="min-w-full" id="file-list" :class="{ 'loading': loading }">
                    <div class="border-b border-red-200 bg-red-50 px-6 py-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-red-900">Files & Folders</h3>
                            <div class="text-sm text-red-600" x-show="items.length > 0">
                                Showing <span x-text="items.length"></span> items
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-200">
                        <template x-for="item in items" :key="item.path">
                            <div class="px-6 py-4 transition-colors duration-150 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex min-w-0 flex-1 items-center">
                                        <!-- File/Folder Icon -->
                                        <div class="mr-4 flex-shrink-0">
                                            <div x-show="item.type === 'directory'"
                                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100">
                                                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-5l-2-2H5a2 2 0 00-2 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div x-show="item.type === 'file'"
                                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100">
                                                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- File Info -->
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center">
                                                <button x-show="item.type === 'directory'"
                                                    @click="navigateToFolder(item.path)"
                                                    class="truncate text-sm font-medium text-red-600 hover:text-red-700">
                                                    <span x-text="item.name"></span>
                                                </button>
                                                <span x-show="item.type === 'file'"
                                                    class="truncate text-sm font-medium text-gray-900"
                                                    x-text="item.name"></span>

                                                <span x-show="item.extension"
                                                    class="ml-2 inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800"
                                                    x-text="item.extension.toUpperCase()"></span>
                                            </div>

                                            <div class="mt-1 flex items-center space-x-4 text-xs text-gray-500">
                                                <span x-show="item.size" x-text="item.size"></span>
                                                <span x-show="item.modified" x-text="item.modified"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="ml-4 flex items-center space-x-2">
                                        <button x-show="item.type === 'file'" @click="downloadFile(item)"
                                            class="p-2 text-gray-400 transition-colors hover:text-red-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-4-4m4 4l4-4m-4-10v4"></path>
                                            </svg>
                                        </button>

                                        <button x-show="item.type === 'file'" @click="replaceFile(item)"
                                            class="p-2 text-gray-400 transition-colors hover:text-blue-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </button>

                                        <button @click="deleteItem(item)"
                                            class="p-2 text-gray-400 transition-colors hover:text-red-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Loading indicator -->
                    <div x-show="loading" class="px-6 py-8 text-center">
                        <div class="inline-flex items-center">
                            <svg class="-ml-1 mr-3 h-5 w-5 animate-spin text-red-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="text-red-600">Loading more files...</span>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div x-show="!loading && items.length === 0" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-5l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No files</h3>
                        <p class="mt-1 text-sm text-gray-500">This folder is empty.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Modal -->
        <div x-show="showUploadModal" x-cloak
            class="fixed inset-0 z-50 h-full w-full overflow-y-auto bg-gray-600 bg-opacity-50">
            <div class="relative top-20 mx-auto w-96 rounded-md border bg-white p-5 shadow-lg">
                <div class="mt-3">
                    <h3 class="mb-4 text-center text-lg font-medium text-gray-900">Upload File</h3>
                    <form @submit.prevent="uploadFile">
                        <div class="mb-4">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Select File</label>
                            <input type="file" x-ref="uploadInput" required
                                class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-full file:border-0 file:bg-red-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-red-700 hover:file:bg-red-100">
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" @click="showUploadModal = false"
                                class="rounded-md bg-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="submit" :disabled="uploading"
                                class="rounded-md bg-red-600 px-4 py-2 text-white transition hover:bg-red-700 disabled:opacity-50">
                                <span x-show="!uploading">Upload</span>
                                <span x-show="uploading">Uploading...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Create Folder Modal -->
        <div x-show="showFolderModal" x-cloak
            class="fixed inset-0 z-50 h-full w-full overflow-y-auto bg-gray-600 bg-opacity-50">
            <div class="relative top-20 mx-auto w-96 rounded-md border bg-white p-5 shadow-lg">
                <div class="mt-3">
                    <h3 class="mb-4 text-center text-lg font-medium text-gray-900">Create New Folder</h3>
                    <form @submit.prevent="createFolder">
                        <div class="mb-4">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Folder Name</label>
                            <input type="text" x-model="newFolderName" required
                                class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-red-500 focus:outline-none focus:ring-red-500">
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" @click="showFolderModal = false"
                                class="rounded-md bg-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="submit" :disabled="creating"
                                class="rounded-md bg-red-600 px-4 py-2 text-white transition hover:bg-red-700 disabled:opacity-50">
                                <span x-show="!creating">Create</span>
                                <span x-show="creating">Creating...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Replace File Modal -->
        <div x-show="showReplaceModal" x-cloak
            class="fixed inset-0 z-50 h-full w-full overflow-y-auto bg-gray-600 bg-opacity-50">
            <div class="relative top-20 mx-auto w-96 rounded-md border bg-white p-5 shadow-lg">
                <div class="mt-3">
                    <h3 class="mb-4 text-center text-lg font-medium text-gray-900">Replace File</h3>
                    <p class="mb-4 text-center text-sm text-gray-600">
                        Replacing: <span class="font-medium" x-text="selectedFile?.name"></span>
                    </p>
                    <form @submit.prevent="replaceFileSubmit">
                        <div class="mb-4">
                            <label class="mb-2 block text-sm font-medium text-gray-700">Select New File</label>
                            <input type="file" x-ref="replaceInput" required
                                class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-full file:border-0 file:bg-red-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-red-700 hover:file:bg-red-100">
                            <p class="mt-1 text-xs text-gray-500">File extension must match the original file</p>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" @click="showReplaceModal = false"
                                class="rounded-md bg-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="submit" :disabled="replacing"
                                class="rounded-md bg-red-600 px-4 py-2 text-white transition hover:bg-red-700 disabled:opacity-50">
                                <span x-show="!replacing">Replace</span>
                                <span x-show="replacing">Replacing...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Notification -->
        <div x-show="notification.show" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2" class="fixed right-4 top-4 z-50">
            <div class="pointer-events-auto w-full max-w-sm rounded-lg shadow-lg"
                :class="notification.type === 'success' ? 'bg-green-500' : 'bg-red-500'">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg x-show="notification.type === 'success'" class="h-6 w-6 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <svg x-show="notification.type === 'error'" class="h-6 w-6 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <p class="text-sm font-medium text-white" x-text="notification.message"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function fileManager() {
            return {
                items: @json($items['items']),
                loading: false,
                currentPage: {{ $items['currentPage'] }},
                hasMore: @json($items['hasMore']),
                currentDisk: '{{ $disk }}',
                currentPath: '{{ $path }}',
                showUploadModal: false,
                showFolderModal: false,
                showReplaceModal: false,
                uploading: false,
                creating: false,
                replacing: false,
                newFolderName: '',
                selectedFile: null,
                notification: {
                    show: false,
                    message: '',
                    type: 'success'
                },

                init() {
                    // Setup infinite scroll
                    window.addEventListener('scroll', () => {
                        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 1000) {
                            this.loadMore();
                        }
                    });

                },

                async loadMore() {
                    if (this.loading || !this.hasMore) return;

                    this.loading = true;

                    try {
                        const response = await axios.get(window.location.href, {
                            params: {
                                page: this.currentPage + 1,
                                disk: this.currentDisk,
                                path: this.currentPath
                            },
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        this.items.push(...response.data.items);
                        this.currentPage = response.data.currentPage;
                        this.hasMore = response.data.hasMore;
                    } catch (error) {
                        showToast('error', 'Failed to load more files', 'Network Error');
                    } finally {
                        this.loading = false;
                    }
                },

                navigateToFolder(path) {
                    window.location.href = `?disk=${this.currentDisk}&path=${encodeURIComponent(path)}`;
                },

                changeDisk() {
                    window.location.href = `?disk=${this.currentDisk}&path=`;
                },

                async uploadFile() {
                    if (!this.$refs.uploadInput.files[0]) return;

                    this.uploading = true;
                    const formData = new FormData();
                    formData.append('file', this.$refs.uploadInput.files[0]);
                    formData.append('disk', this.currentDisk);
                    formData.append('path', this.currentPath);

                    try {
                        await axios.post('/file-manager/upload', formData);
                        showToast('success', 'File uploaded successfully!', 'File Manager');
                        this.showUploadModal = false;
                        this.refreshPage();
                    } catch (error) {
                        showToast('error', error.response?.data?.message || 'Upload failed', 'File Manager');
                    } finally {
                        this.uploading = false;
                    }
                },

                async createFolder() {
                    if (!this.newFolderName.trim()) return;

                    this.creating = true;

                    try {
                        await axios.post('/file-manager/create-folder', {
                            name: this.newFolderName,
                            disk: this.currentDisk,
                            path: this.currentPath
                        });

                        showToast('success', 'Folder created successfully!', 'File Manager');
                        this.showFolderModal = false;
                        this.newFolderName = '';
                        this.refreshPage();
                    } catch (error) {
                        showToast('error', error.response?.data?.message || 'Failed to create folder', 'File Manager');
                    } finally {
                        this.creating = false;
                    }
                },

                replaceFile(file) {
                    this.selectedFile = file;
                    this.showReplaceModal = true;
                },

                async replaceFileSubmit() {
                    if (!this.$refs.replaceInput.files[0]) return;

                    this.replacing = true;
                    const formData = new FormData();
                    formData.append('file', this.$refs.replaceInput.files[0]);
                    formData.append('disk', this.currentDisk);
                    formData.append('path', this.selectedFile.path);

                    try {
                        await axios.post('/file-manager/replace', formData);
                        showToast('success', 'File replaced successfully!', 'File Manager');
                        this.showReplaceModal = false;
                        this.refreshPage();
                    } catch (error) {
                        showToast('error', error.response?.data?.message || 'Replace failed', 'File Manager');
                    } finally {
                        this.replacing = false;
                    }
                },

                async deleteItem(item) {
                    if (!confirm(`Are you sure you want to delete ${item.name}?`)) return;

                    try {
                        await axios.delete('/file-manager/delete', {
                            data: {
                                disk: this.currentDisk,
                                path: item.path,
                                type: item.type
                            }
                        });

                        showToast('success', `${item.type === 'directory' ? 'Folder' : 'File'} deleted successfully!`,
                            'File Manager');
                        this.refreshPage();
                    } catch (error) {
                        console.log(error);
                        showToast('error', error.response?.data?.message || 'Delete failed', 'File Manager');
                    }
                },

                downloadFile(file) {
                    const url = `/file-manager/download?disk=${this.currentDisk}&path=${encodeURIComponent(file.path)}`;
                    window.open(url, '_blank');
                },

                refreshPage() {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                },

            }
        }
    </script>
@endpush
