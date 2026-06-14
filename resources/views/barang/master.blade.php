{{-- Updated master.blade.php --}}
@extends('layouts.admin')

@section('page_title', 'Master Data')

@section('content')
    <div x-data="{ selectedTab: '{{ session('tab', 'categoryDataTable') }}' }" class="w-full">
        <!-- Tab Content -->
        <div class="text-neutral-600">
            <x-table.tab-view :tabs="[
                'categoryDataTable' => [
                    'label' => 'Kategori',
                    'icon' => '',
                    'content' => view('components.table.table-data', [
                        'routeAdd' => route('master.category.store'),
                        'routeEdit' => route('master.category.edit', ':id'),
                        'dataTable' => $categoryDataTable,
                        'tableId' => 'category-table',
                        'title' => 'DATA KATEGORI',
                        'addForm' => true,
                        'formInputs' => [
                            [
                                'name' => 'name',
                                'type' => 'text',
                                'placeholder' => 'Masukkan Nama Kategori',
                                'value' => '',
                                'title' => 'Nama Kategori',
                            ],
                            [
                                'name' => 'description',
                                'type' => 'textarea',
                                'placeholder' => 'Masukkan Deskripsi Kategori',
                                'value' => '',
                                'title' => 'Deskripsi',
                            ],
                            [
                                'type' => 'file',
                                'name' => 'Image',
                                'title' => 'Image',
                                'maxFiles' => '1',
                                'maxSize' => '2',
                                'accepted-types' => 'image/*',
                            ],
                        ],
                    ])->render(),
                ],
                'subcategoryDataTable' => [
                    'label' => 'Sub Kategori',
                    'icon' => '',
                    'content' => view('components.table.table-data', [
                        'routeAdd' => route('master.subcategory.store'),
                        'routeEdit' => route('master.subcategory.edit', ':id'),
                        'dataTable' => $subcategoryDataTable,
                        'tableId' => 'subcategory-table',
                        'title' => 'DATA SUB KATEGORI',
                        'addForm' => true,
                        'formInputs' => [
                            [
                                'name' => 'name',
                                'type' => 'text',
                                'placeholder' => 'Masukkan Nama Sub Kategori',
                                'value' => '',
                                'title' => 'Nama Sub Kategori',
                            ],
                            [
                                'name' => 'description',
                                'type' => 'textarea',
                                'placeholder' => 'Masukkan Deskripsi Sub Kategori',
                                'value' => '',
                                'title' => 'Deskripsi',
                            ],
                            [
                                'type' => 'file',
                                'name' => 'Image',
                                'title' => 'Image',
                                'maxFiles' => '1',
                                'maxSize' => '2',
                                'accepted-types' => 'image/*',
                            ],
                        ],
                    ])->render(),
                ],
                'brandDataTable' => [
                    'label' => 'Brand',
                    'icon' => '',
                    'content' => view('components.table.table-data', [
                        'routeAdd' => route('master.brand.store'),
                        'routeEdit' => route('master.brand.edit', ':id'),
                        'dataTable' => $brandDataTable,
                        'tableId' => 'brand-table',
                        'title' => 'DATA BRAND',
                        'addForm' => true,
                        'formInputs' => [
                            [
                                'name' => 'name',
                                'type' => 'text',
                                'placeholder' => 'Masukkan Nama Brand',
                                'value' => '',
                                'title' => 'Nama Brand',
                            ],
                            [
                                'name' => 'description',
                                'type' => 'textarea',
                                'placeholder' => 'Masukkan Deskripsi Brand',
                                'value' => '',
                                'title' => 'Deskripsi',
                            ],
                            [
                                'type' => 'file',
                                'name' => 'Image',
                                'title' => 'Image',
                                'maxFiles' => '1',
                                'maxSize' => '2',
                                'accepted-types' => 'image/*',
                            ],
                        ],
                    ])->render(),
                ],
                'typeItemDataTable' => [
                    'label' => 'Tipe Barang',
                    'icon' => '',
                    'content' => view('components.table.table-data', [
                        'routeAdd' => route('master.type-item.store'),
                        'routeEdit' => route('master.type-item.edit', ':id'),
                        'dataTable' => $typeItemDataTable,
                        'tableId' => 'type-item-table',
                        'title' => 'Tipe Barang',
                        'addForm' => true,
                        'formInputs' => [
                            [
                                'name' => 'name',
                                'type' => 'text',
                                'placeholder' => 'Masukkan Nama Type',
                                'value' => '',
                                'title' => 'Nama Type',
                            ],
                            [
                                'name' => 'description',
                                'type' => 'textarea',
                                'placeholder' => 'Masukkan Deskripsi Type',
                                'value' => '',
                                'title' => 'Deskripsi',
                            ],
                            [
                                'type' => 'file',
                                'name' => 'Image',
                                'title' => 'Image',
                                'maxFiles' => '1',
                                'maxSize' => '2',
                                'accepted-types' => 'image/*',
                            ],
                        ],
                    ])->render(),
                ],
                'satuanItemDataTable' => [
                    'label' => 'Satuan',
                    'icon' => '',
                    'content' => view('components.table.table-data', [
                        'routeAdd' => route('master.satuan-item.store'),
                        'routeEdit' => route('master.satuan-item.edit', ':id'),
                        'dataTable' => $satuanItemDataTable,
                        'tableId' => 'satuan-item-table',
                        'title' => 'DATA SATUAN',
                        'addForm' => true,
                        'formInputs' => [
                            [
                                'name' => 'name',
                                'type' => 'text',
                                'placeholder' => 'Masukkan Nama Satuan',
                                'value' => '',
                                'title' => 'Nama Satuan',
                            ],
                            [
                                'name' => 'cut_name',
                                'type' => 'text',
                                'placeholder' => 'Masukkan Nama Potongan',
                                'value' => '',
                                'title' => 'Nama Potongan',
                            ],
                            [
                                'name' => 'type',
                                'type' => 'select',
                                'placeholder' => 'Masukkan Tipe',
                                'value' => '',
                                'title' => 'Tipe',
                                'options' => $satuanItemTypes,
                            ],
                            [
                                'name' => 'level -max 10',
                                'type' => 'number',
                                'placeholder' => 'Masukkan Level',
                                'value' => '1',
                                'title' => 'Level',
                                'max' => '10',
                            ],
                            [
                                'name' => 'selling',
                                'type' => 'select',
                                'placeholder' => 'Barang dengan satuan ini dijual?',
                                'value' => '',
                                'title' => 'Barang dengan satuan ini dijual?',
                                'options' => [
                                    [
                                        'value' => 'false',
                                        'label' => 'Tidak',
                                    ],
                                    [
                                        'value' => 'true',
                                        'label' => 'Ya',
                                    ],
                                ],
                            ],
                            [
                                'name' => 'description',
                                'type' => 'textarea',
                                'placeholder' => 'Masukkan Deskripsi Satuan',
                                'value' => '',
                                'title' => 'Deskripsi',
                            ],
                        ],
                    ])->render(),
                ],
            ]" />
        </div>
    </div>

    <!-- Updated Edit Modal -->
    <div id="edit-modal-category" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="modal-content absolute left-1/2 top-1/2 max-h-[90vh] w-1/2 max-w-2xl -translate-x-1/2 -translate-y-1/2 transform overflow-y-auto rounded-lg bg-white p-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-xl font-semibold">Edit Kategori</h3>
                <button type="button" onclick="closeEditModalCategory()" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="edit-category-form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="category_id" id="category_id" value="">
                @csrf
                @method('PUT')
                <div class="space-y-6 px-4 py-3">
                    <!-- Form Fields -->
                    <div>
                        <label for="edit-name" class="mb-2 block text-sm font-medium text-gray-700">Nama Kategori</label>
                        <input type="text" name="name" id="edit-name" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                    </div>

                    <div>
                        <label for="edit-description" class="mb-2 block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" id="edit-description" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                    </div>

                    <!-- Current Photo Display -->
                    <div id="current-photo-section" class="hidden">
                        <label class="mb-2 block text-sm font-medium text-gray-700">Foto Saat Ini</label>
                        <div class="relative inline-block">
                            <img id="current-photo-preview" src="" alt="Current Photo" class="h-32 w-32 rounded-lg border object-cover shadow-sm">
                            <button type="button" id="remove-current-photo" class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-sm text-white shadow-md hover:bg-red-600">
                                ×
                            </button>
                        </div>
                        <input type="hidden" id="remove-photo-flag" name="remove_photo" value="0">
                    </div>

                    <!-- File Upload Component -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Upload Foto Baru</label>
                        <x-form.file-upload id="edit-file-upload" max-files="1" accepted-types="image/*" max-size="2" name="files[]">
                        </x-form.file-upload>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3 rounded-b-lg bg-gray-50 px-4 py-3">
                    <button type="button" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" onclick="closeEditModalCategory()">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Update
                    </button>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                </div>
            </form>
        </div>
    </div>


    <div id="edit-satuan-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="modal-content absolute left-1/2 top-1/2 max-h-[90vh] w-1/2 max-w-2xl -translate-x-1/2 -translate-y-1/2 transform overflow-y-auto rounded-lg bg-white p-6">

        </div>
    </div>

    <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <!-- Modal Container -->
        <div id="delete-modal-container" class="animate-jump-in w-full max-w-md overflow-hidden rounded-lg bg-white shadow-xl transition-all duration-300">
            <div class="p-6">
                <div class="text-center">
                    <!-- Icon Container -->
                    <div id="iconContainer" class="my-6 flex justify-center">
                        <i id="modalIcon" class="fas fa-trash text-6xl text-red-500"></i>
                    </div>

                    <!-- Message -->
                    <h3 id="modalTitle" class="mb-2 text-2xl font-bold text-gray-800">Confirm Deletion</h3>
                    <p id="modalMessage" class="mb-6 text-gray-600">
                        Are you sure you want to delete this item? This action cannot be undone.
                    </p>
                    <input type="hidden" id="delete_id" name="delete_id">
                    <input type="hidden" id="delete_type" name="delete_type">
                    <!-- Buttons -->
                    <div id="actionButtons" class="flex justify-center space-x-4">
                        <button id="confirmBtn" onclick="confirmDelete()" class="cursor-pointer rounded-lg bg-red-500 px-6 py-2 font-bold text-white transition duration-200 hover:bg-red-600">
                            Delete
                        </button>
                        <button id="cancelBtn" onclick="closeDeleteModal()" class="cursor-pointer rounded-lg bg-gray-300 px-6 py-2 font-bold text-gray-800 transition duration-200 hover:bg-gray-400">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @keyframes checkmark-animation {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            50% {
                transform: scale(1.2);
                opacity: 1;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .checkmark-animate {
            animation: checkmark-animation 0.6s ease-out forwards;
        }

        .modal-enter {
            opacity: 0;
            transform: scale(0.9);
        }

        .modal-enter-active {
            opacity: 1;
            transform: scale(1);
            transition: all 0.3s ease;
        }

        .modal-exit {
            opacity: 1;
            transform: scale(1);
        }

        .modal-exit-active {
            opacity: 0;
            transform: scale(0.9);
            transition: all 0.3s ease;
        }
    </style>
@endpush

@push('scripts')
    {{-- DataTable Scripts --}}
    {!! $categoryDataTable->scripts() !!}
    {!! $satuanItemDataTable->scripts() !!}
    {!! $brandDataTable->scripts() !!}
    {!! $subcategoryDataTable->scripts() !!}
    {!! $typeItemDataTable->scripts() !!}
    <script>
        // Store original modal HTML to restore after loading
        let originalEditModalHTML = '';
        let originalDeleteModalHTML = '';

        const modalContainer = document.getElementById('modalContainer');
        const modalIcon = document.getElementById('modalIcon');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const actionButtons = document.getElementById('actionButtons');
        const confirmBtn = document.getElementById('confirmBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const iconContainer = document.getElementById('iconContainer');

        document.addEventListener('DOMContentLoaded', function() {
            // Store the original modal HTML
            const editModalCategory = document.getElementById('edit-modal-category');
            const deleteModalCategory = document.getElementById('delete-modal-container');
            if (editModalCategory) {
                originalEditModalHTML = editModalCategory.innerHTML;
            }
            if (deleteModalCategory) {
                originalDeleteModalHTML = deleteModalCategory.innerHTML;
            }

            $(document).on('click', '.delete-modal', function() {
                const deleteModal = document.getElementById('delete-modal');
                if (deleteModal) {
                    deleteModal.classList.remove('hidden');
                    deleteModal.classList.add('flex');
                }

                resetModal();
                const deleteId = document.getElementById('delete_id');
                if (deleteId) {
                    deleteId.value = $(this).data('id');
                }

                const deleteType = document.getElementById('delete_type');
                if (deleteType) {
                    deleteType.value = $(this).data('table');
                }
            });

            // Handle edit button click
            $(document).on('click', '.edit-category-modal', function() {
                const id = $(this).data('id');
                const editModal = document.getElementById('edit-modal-category');

                // Update form action URL
                const form = document.getElementById('edit-category-form');
                if (form) {
                    form.action = `{{ route('master.category.update', ['id' => ':id']) }}`.replace(':id', id);
                }

                // Show loading state
                showLoadingState();

                $.ajax({
                    url: `{{ route('master.category.edit', ['id' => ':id']) }}`.replace(':id', id),
                    type: 'GET',
                    success: function(response) {

                        // Hide loading state and restore modal
                        hideLoadingState();

                        // Update form action URL after modal content is restored
                        const form = document.getElementById('edit-category-form');
                        if (form) {
                            form.action = `{{ route('master.category.update', ['id' => ':id']) }}`.replace(':id', id);
                            // Update the category_id hidden input
                            document.getElementById('category_id').value = id;
                        }

                        // Fill form fields
                        $('#edit-name').val(response.data.name || '');
                        $('#edit-description').val(response.data.description || '');

                        // Handle current photo display
                        handleCurrentPhotoDisplay(response.data.photo);

                        // Reset file upload component
                        resetFileUpload();

                        // Show modal
                        showEditCategoryModal(editModal);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            response: xhr.responseText,
                            error: error
                        });

                        hideLoadingState();
                    }
                });
            });

            // Handle remove current photo
            $(document).on('click', '#remove-current-photo', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const currentPhotoSection = document.getElementById('current-photo-section');
                const removeFlag = document.getElementById('remove-photo-flag');

                if (currentPhotoSection && removeFlag) {
                    currentPhotoSection.classList.add('hidden');
                    removeFlag.value = '1';
                }
            });
        });

        // Function to handle current photo display
        function handleCurrentPhotoDisplay(photoUrl) {

            // Wait a bit for DOM to be ready after restoring modal content
            setTimeout(() => {
                const currentPhotoSection = document.getElementById('current-photo-section');
                const currentPhotoPreview = document.getElementById('current-photo-preview');
                const removeFlag = document.getElementById('remove-photo-flag');

                if (!currentPhotoSection || !currentPhotoPreview || !removeFlag) {
                    console.error("Photo display elements not found");
                    return;
                }

                if (photoUrl && photoUrl.trim() !== '' && photoUrl !== 'null' && photoUrl !== null) {
                    // Set the image source
                    currentPhotoPreview.src = photoUrl;

                    // Add error handler for image loading
                    currentPhotoPreview.onerror = function() {
                        console.error("Failed to load image:", photoUrl);
                        // Hide the section if image fails to load
                        currentPhotoSection.classList.add('hidden');
                    };

                    // Add load handler for successful image loading
                    currentPhotoPreview.onload = function() {
                        currentPhotoSection.classList.remove('hidden');
                    };

                    // Reset remove flag
                    removeFlag.value = '0';
                } else {
                    currentPhotoSection.classList.add('hidden');
                    removeFlag.value = '0';
                }
            }, 100);
        }

        // Function to show loading state
        function showLoadingState() {
            const modal = document.getElementById('edit-modal-category');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.innerHTML = `
            <div class="modal-content absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 transform rounded-lg bg-white p-6">
                <div class="flex items-center justify-center space-x-2">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    <span>Loading...</span>
                </div>
            </div>
        `;
            }
        }

        // Function to hide loading state and restore modal content
        function hideLoadingState() {
            const editModal = document.getElementById('edit-modal-category');
            if (editModal && originalEditModalHTML) {
                // Restore original modal content instead of reloading page
                editModal.innerHTML = originalEditModalHTML;
                editModal.classList.add('hidden');
                editModal.classList.remove('flex');
            }
        }

        // Function to show edit modal
        function showEditCategoryModal(editModal) {
            if (editModal) {
                editModal.classList.remove('hidden');
                editModal.classList.add('flex');
            }
        }

        function confirmDelete() {
            const deleteId = document.getElementById('delete_id');
            const deleteType = document.getElementById('delete_type');
            if (deleteId && deleteType) {
                const id = deleteId.value;
                const typeData = deleteType.value;

                confirmBtn.innerHTML = '<i class="fas fa-spinner animate-spin"></i> Deleting...';
                confirmBtn.disabled = true;
                cancelBtn.disabled = true;
                let urlData;
                switch (typeData) {
                    case 'category-table':
                        urlData = `{{ route('master.category.destroy', ['id' => ':id']) }}`.replace(':id', id);
                        break;
                    case 'satuan-item-table':
                        urlData = `{{ route('master.satuan-item.destroy', ['id' => ':id']) }}`.replace(':id', id);
                        break;
                }
                $.ajax({
                    url: urlData,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            modalIcon.className = 'fas fa-check-circle text-green-500 text-6xl checkmark-animate';
                            modalTitle.textContent = 'Success!';
                            modalMessage.textContent = response.message;
                            actionButtons.classList.add('hidden');
                            reloadTable(typeData);
                            setTimeout(closeDeleteModal, 3000);
                        } else {
                            modalIcon.className = 'fas fa-exclamation-circle animate__animated animate__rotateIn text-red-500 text-6xl';
                            modalTitle.textContent = 'Error!';
                            modalMessage.textContent = response.message;
                            actionButtons.classList.add('hidden');
                            confirmBtn.innerHTML = 'Delete';
                            confirmBtn.disabled = false;
                            cancelBtn.disabled = false;
                            setTimeout(closeDeleteModal, 3000);
                        }
                    },
                    error: function(error) {
                        modalIcon.className = 'fas fa-exclamation-circle animate__animated animate__rotateIn text-red-500 text-6xl';
                        modalTitle.textContent = 'Error!';
                        modalMessage.textContent = error.responseJSON.message || 'An error occurred';
                        actionButtons.classList.add('hidden');
                        confirmBtn.innerHTML = 'Delete';
                        confirmBtn.disabled = false;
                        cancelBtn.disabled = false;
                        setTimeout(closeDeleteModal, 3000);
                    }
                });
            }
        }

        // Function to close delete modal
        function closeDeleteModal() {
            const deleteModal = document.getElementById('delete-modal');
            if (deleteModal) {
                deleteModal.classList.add('hidden');
                deleteModal.classList.remove('flex');
            }
        }

        // Function to close edit modal
        function closeEditModalCategory() {
            const editModal = document.getElementById('edit-modal-category');
            const form = document.getElementById('edit-category-form');
            const currentPhotoSection = document.getElementById('current-photo-section');
            const removeFlag = document.getElementById('remove-photo-flag');

            if (editModal) {
                editModal.classList.add('hidden');
                editModal.classList.remove('flex');
            }

            // Reset form
            if (form) {
                form.reset();
                // Reset form action URL
                form.action = form.action.replace(/\/\d+$/, '/:id');
            }

            // Hide photo section and reset flag
            if (currentPhotoSection) {
                currentPhotoSection.classList.add('hidden');
            }
            if (removeFlag) {
                removeFlag.value = '0';
            }

            // Reset file upload
            resetFileUpload();
        }

        // Function to reset file upload component
        function resetFileUpload() {
            try {
                const fileUploadDiv = document.querySelector('#edit-file-upload');
                if (fileUploadDiv && fileUploadDiv._x_dataStack && fileUploadDiv._x_dataStack[0]) {
                    const alpineData = fileUploadDiv._x_dataStack[0];
                    if (alpineData && Array.isArray(alpineData.files)) {
                        // Clear existing files and previews
                        alpineData.files.forEach(file => {
                            if (file.preview) {
                                URL.revokeObjectURL(file.preview);
                            }
                        });
                        alpineData.files = [];
                        if (typeof alpineData.updateFileInput === 'function') {
                            alpineData.updateFileInput();
                        }
                    }
                }
            } catch (error) {
                console.warn("Could not reset file upload component:", error);
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            const editModal = document.getElementById('edit-modal-category');
            if (e.target === editModal) {
                closeEditModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
            }
        });

        // Rest of your existing functions remain the same...
        function updateSatuanItem(el) {
            const id = el.getAttribute("data-id");
            const selling = el.getAttribute("data-selling") === "true";
            $.post({
                url: "/barang/master/update-satuan-item",
                data: {
                    id: id,
                    selling: selling,
                },
                success: function(response) {
                    console.log(response.data);
                    reloadTable("satuan-item-table");
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function reloadTable(tableId) {
            if ($.fn.DataTable.isDataTable('#' + tableId)) {
                $('#' + tableId).DataTable().ajax.reload();
            }
        }

        // Debug function to check ajax errors
        function setupDataTableErrorHandling() {
            // Monitor all DataTable ajax errors
            $(document).on('error.dt', function(e, settings, techNote, message) {
                console.error('DataTable error:', {
                    table: settings.nTable.id,
                    message: message,
                    techNote: techNote,
                    settings: settings
                });
            });

            // Monitor general ajax errors
            $(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
                console.error('Ajax Error:', {
                    url: ajaxSettings.url,
                    status: jqXHR.status,
                    statusText: jqXHR.statusText,
                    response: jqXHR.responseText,
                    error: thrownError
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Setup error handling
            setupDataTableErrorHandling();

            // Initialize search functionality
            function initializeSearch() {
                document.querySelectorAll('.search-input').forEach(function(input) {
                    input.addEventListener('keyup', function() {
                        let tableId = input.getAttribute('data-table-id');
                        if ($.fn.DataTable.isDataTable('#' + tableId)) {
                            $('#' + tableId).DataTable().search(input.value).draw();
                        }
                    });
                });
            }

            // Initialize length menu
            function initializeLengthMenu() {
                document.querySelectorAll('#lengthMenu').forEach(function(select) {
                    select.addEventListener('change', function() {
                        let tableId = select.getAttribute('data-table-id');
                        if ($.fn.DataTable.isDataTable('#' + tableId)) {
                            $('#' + tableId).DataTable().page.len(select.value).draw();
                        }
                    });
                });
            }

            // Initialize reload buttons
            function initializeReload() {
                document.querySelectorAll('#buttonReload').forEach(function(button) {
                    button.addEventListener('click', function() {
                        let tableId = button.getAttribute('data-table-id');
                        reloadTable(tableId);
                    });
                });
            }

            // Wait for Alpine.js to initialize
            setTimeout(function() {
                initializeSearch();
                initializeLengthMenu();
                initializeReload();
            }, 100);
        });


        function resetModal() {
            modalIcon.className = 'fas fa-trash text-red-500 text-6xl';
            modalTitle.textContent = 'Confirm Deletion';
            modalMessage.textContent = 'Are you sure you want to delete this item? This action cannot be undone.';
            actionButtons.classList.remove('hidden');
            confirmBtn.textContent = 'Delete';
            confirmBtn.disabled = false;
            cancelBtn.disabled = false;
        }
    </script>
@endpush
