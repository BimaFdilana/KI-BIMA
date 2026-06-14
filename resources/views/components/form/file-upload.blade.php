@props([
    'maxFiles' => 5, // Batas jumlah file default
    'acceptedTypes' => 'image/*', // Jenis file yang diterima default
    'maxSize' => 5, // Ukuran file maksimal default (dalam MB)
])

<div x-data="fileUpload({
    maxFiles: {{ $maxFiles }},
    acceptedTypes: '{{ $acceptedTypes }}',
    maxSize: {{ $maxSize }}
})" class="mx-auto max-w-2xl" x-bind:data-max-files="{{ $maxFiles }}" x-bind:data-accepted-types="'{{ $acceptedTypes }}'" x-bind:data-max-size="{{ $maxSize }}">

    <!-- Upload Container -->
    <div class="space-y-4 rounded-lg border-2 border-dashed border-gray-300 p-6 shadow-sm">
        <div x-ref="dropZone" x-bind:class="{ 'border-dashed border-4 border-blue-500': dragging, 'border-gray-300': !dragging }" class="relative cursor-pointer" @drop.prevent="handleDrop" @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @click="triggerFileInput">
            <input type="file" name="files[]" multiple x-ref="fileInput" :accept="acceptedTypes" class="hidden" @change="handleFileInput">
            <div class="text-center">
                <p class="mb-3 text-lg text-gray-500">Drag & Drop files here or click to select files</p>
                <p class="text-sm text-gray-400">Accepted file types: <span x-text="acceptedTypesText"></span></p>
                <p class="text-sm text-gray-400">Max file size: <span x-text="maxSize"></span> MB. Max files: <span x-text="maxFiles"></span></p>
            </div>

            <!-- Error Message if file limit is exceeded -->
            <p x-show="files.length > maxFiles" class="mt-2 text-sm text-red-500">You can only upload up to <span x-text="maxFiles"></span> files.</p>

            <!-- Error Message if file size is too large -->
            <p x-show="fileTooLarge" class="mt-2 text-sm text-red-500">File is too large. Max size is <span x-text="maxSize"></span> MB.</p>

            <!-- Error Message if file type is not accepted -->
            <p x-show="fileTypeNotAccepted" class="mt-2 text-sm text-red-500">File type is not accepted. Accepted types are: <span x-text="acceptedTypesText"></span>.</p>
        </div>

        <!-- File Previews -->
        <div class="mt-4 grid grid-cols-3 gap-4" x-show="files.length > 0">
            <template x-for="(file, index) in files" :key="index">
                <div class="space-y-2">
                    <div x-show="file.type.startsWith('image')" class="relative">
                        <img :src="file.preview" alt="" class="h-24 w-full rounded-md object-cover">
                        <button @click="removeFile(index)" class="absolute right-2 top-2 rounded-full bg-gray-500 p-1 text-white">×</button>
                    </div>
                    <div x-show="!file.type.startsWith('image')" class="relative rounded-md bg-gray-100 p-4 text-center">
                        <p class="text-sm text-gray-600">File: <span x-text="file.name"></span></p>
                        <button @click="removeFile(index)" class="absolute right-2 top-2 rounded-full bg-gray-500 p-1 text-white">×</button>
                    </div>
                    <div class="text-xs text-gray-500" x-text="file.sizeText"></div>
                </div>
            </template>
        </div>

        <!-- Progress bar & estimations -->
        <div x-show="uploading" class="mt-4 space-y-2">
            <div class="flex items-center justify-between text-sm">
                <span>Estimated time: <span x-text="uploadTime"></span></span>
                <span class="text-xs text-gray-400" x-text="files.length + ' file(s)'"></span>
            </div>
            <div class="h-2 w-full rounded-full bg-gray-200">
                <div x-bind:style="'width: ' + progress + '%'" class="h-2 rounded-full bg-blue-500"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('fileUpload', (props) => ({
                maxFiles: props.maxFiles || 5,
                acceptedTypes: props.acceptedTypes || 'image/*',
                maxSize: props.maxSize || 5, // in MB
                files: [],
                dragging: false,
                fileTooLarge: false,
                fileTypeNotAccepted: false,
                uploading: false,
                progress: 0,
                uploadTime: '0s',

                // Computed property untuk menampilkan accepted types dalam format yang lebih readable
                get acceptedTypesText() {
                    if (this.acceptedTypes === 'image/*') {
                        return 'Images';
                    } else if (this.acceptedTypes === '*') {
                        return 'All files';
                    } else {
                        return this.acceptedTypes.replace(/\*/g, 'all').replace(/,/g, ', ');
                    }
                },

                handleDrop(event) {
                    this.dragging = false;
                    const files = event.dataTransfer.files;
                    this.addFiles(files);
                },

                handleFileInput(event) {
                    const selectedFiles = event.target.files;
                    this.addFiles(selectedFiles);
                },

                addFiles(files) {
                    Array.from(files).forEach((file) => {
                        // Cek jika file dengan nama dan ekstensi yang sama sudah ada
                        const fileExists = this.files.some(f => f.name === file.name && f.type === file.type);

                        if (fileExists) {
                            // Jika sudah ada, tidak perlu menambahkan file ini
                            console.log('File sudah ada: ' + file.name);
                            return;
                        }

                        // Jika maxFiles = 1 dan sudah ada file, perbarui file yang lama
                        if (this.maxFiles === 1 && this.files.length > 0) {
                            // Perbarui file yang ada dengan file yang baru
                            this.files[0] = this.createFileWithPreview(file);
                            this.updateFileInput();
                            return;
                        }

                        // Jika sudah mencapai maxFiles dan ada file yang lebih banyak dari maxFiles, 
                        // ganti file terakhir yang ada di array
                        if (this.files.length >= this.maxFiles) {
                            // Ganti file yang terakhir dengan file yang baru
                            this.files[this.files.length - 1] = this.createFileWithPreview(file);
                            this.updateFileInput();
                            return;
                        }

                        // Cek ukuran file
                        if (file.size / 1024 / 1024 > this.maxSize) {
                            this.fileTooLarge = true;
                            setTimeout(() => this.fileTooLarge = false, 3000);
                            return;
                        }
                        this.fileTooLarge = false;

                        // Cek jenis file
                        if (!this.isFileTypeAccepted(file.type)) {
                            this.fileTypeNotAccepted = true;
                            setTimeout(() => this.fileTypeNotAccepted = false, 3000);
                            return;
                        }
                        this.fileTypeNotAccepted = false;

                        // Menambahkan file baru jika belum ada
                        this.files.push(this.createFileWithPreview(file));

                        // Memasukkan file ke dalam input file yang tersembunyi
                        this.updateFileInput();
                    });
                },

                isFileTypeAccepted(fileType) {
                    if (this.acceptedTypes === '*') return true;

                    const acceptedTypesArray = this.acceptedTypes.split(',').map(type => type.trim());

                    return acceptedTypesArray.some(acceptedType => {
                        if (acceptedType.includes('*')) {
                            const baseType = acceptedType.split('/')[0];
                            return fileType.startsWith(baseType + '/');
                        }
                        return fileType === acceptedType;
                    });
                },

                createFileWithPreview(file) {
                    return {
                        file,
                        preview: URL.createObjectURL(file),
                        name: file.name,
                        sizeText: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                        type: file.type
                    };
                },

                updateFileInput() {
                    // Perbarui input file setelah menambah atau menghapus file
                    const input = this.$refs.fileInput;
                    const dataTransfer = new DataTransfer();
                    Array.from(this.files).forEach(f => {
                        dataTransfer.items.add(f.file);
                    });
                    input.files = dataTransfer.files;
                },

                removeFile(index) {
                    // Revoke object URL to prevent memory leaks
                    URL.revokeObjectURL(this.files[index].preview);
                    this.files.splice(index, 1);

                    // Perbarui input file setelah menghapus file
                    this.updateFileInput();
                },

                triggerFileInput() {
                    this.$refs.fileInput.click();
                },

                // Method untuk simulasi upload (opsional)
                async simulateUpload() {
                    if (this.files.length === 0) return;

                    this.uploading = true;
                    this.progress = 0;

                    const totalSize = this.files.reduce((sum, file) => sum + file.file.size, 0);
                    const estimatedTime = Math.ceil(totalSize / (1024 * 1024)); // rough estimation
                    this.uploadTime = estimatedTime + 's';

                    // Simulate upload progress
                    const interval = setInterval(() => {
                        this.progress += 10;
                        if (this.progress >= 100) {
                            clearInterval(interval);
                            this.uploading = false;
                            this.progress = 0;
                        }
                    }, 200);
                }
            }));
        });
    </script>
@endpush
