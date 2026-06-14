@extends('layouts.admin')

@section('title', 'Detail Pos Infaq')

@section('content')
    <div class="container-fluid px-4 py-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('infaq.list.index') }}"
                    class="p-2 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="fad fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $infaqList->name }}</h1>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fad fa-tag mr-1"></i> {{ $infaqList->category_label }}
                        </span>
                        @if ($infaqList->is_active)
                            <span class="px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                <i class="fad fa-check-circle mr-1"></i> Aktif
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-md text-xs font-medium bg-red-100 text-red-800">
                                <i class="fad fa-times-circle mr-1"></i> Nonaktif
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                @can('edit.infaq')
                    <a href="{{ route('infaq.list.edit', $infaqList->id) }}"
                        class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-700 font-semibold rounded-lg hover:bg-yellow-200 transition-all">
                        <i class="fad fa-pen mr-2"></i> Edit
                    </a>
                @endcan
                @can('create.infaq')
                    <a href="{{ route('infaq.image.create', ['infaq_list_id' => $infaqList->id]) }}"
                        class="inline-flex items-center px-4 py-2 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 transition-all shadow-md">
                        <i class="fad fa-image mr-2"></i> Tambah Gambar
                    </a>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Info & Images -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Main Info Card -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Deskripsi Pos Infaq</h3>
                        <div class="prose max-w-none text-gray-600">
                            {!! nl2br(e($infaqList->description)) !!}
                        </div>
                    </div>
                </div>

                <!-- Images Gallery -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Galeri Gambar</h3>
                        <span class="text-sm text-gray-500">{{ $infaqList->infaqImages->count() }} Gambar</span>
                    </div>
                    <div class="p-6">
                        @if ($infaqList->infaqImages->count() > 0)
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach ($infaqList->infaqImages as $image)
                                    <div
                                        class="relative group aspect-video rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                        <img src="{{ asset($image->image_path) }}" alt="Infaq Image"
                                            class="w-full h-full object-cover">
                                        <div
                                            class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all flex items-center justify-center space-x-2">
                                            <button onclick="viewImage('{{ asset($image->image_path) }}')"
                                                class="p-2 bg-white rounded-full text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
                                                <i class="fad fa-search-plus"></i>
                                            </button>
                                            @if ($image->is_main)
                                                <span
                                                    class="absolute top-2 left-2 px-2 py-0.5 bg-yellow-400 text-white text-[10px] font-bold rounded shadow-sm">UTAMA</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                                <i class="fad fa-images text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500">Belum ada gambar ditambahkan</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Stats & Progress -->
            <div class="space-y-6">
                <!-- Progress Card -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6 bg-gradient-to-br from-red-500 to-red-600 text-white">
                        <h3 class="text-lg font-bold mb-4">Progress Donasi</h3>
                        @php
                            $totalDonations = $infaqList->total_donations;
                            $percentage =
                                $infaqList->dana_dibutuhkan > 0
                                    ? min(100, ($totalDonations / $infaqList->dana_dibutuhkan) * 100)
                                    : 0;
                        @endphp
                        <div class="space-y-4">
                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-red-100 text-xs uppercase font-bold tracking-wider">Terkumpul</p>
                                    <p class="text-2xl font-bold">Rp {{ number_format($totalDonations, 0, ',', '.') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-red-100 text-xs uppercase font-bold tracking-wider">Target</p>
                                    <p class="text-lg font-semibold opacity-90">Rp
                                        {{ number_format($infaqList->dana_dibutuhkan, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <div class="w-full bg-white/20 rounded-full h-3 overflow-hidden">
                                <div class="bg-white h-full rounded-full transition-all duration-1000"
                                    style="width: {{ $percentage }}%"></div>
                            </div>

                            <div class="flex justify-between text-sm font-medium">
                                <span>{{ number_format($percentage, 1) }}% Tercapai</span>
                                <span>{{ $infaqList->donors_count }} Donatur</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 border-t border-gray-100">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Dibuat pada:</span>
                            <span
                                class="font-medium text-gray-700">{{ $infaqList->created_at->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Donations -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900">Donasi Terakhir</h3>
                    </div>
                    <div class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
                        @forelse($infaqList->infaqHistories()->where('status', 'completed')->latest()->take(10)->get() as $history)
                            <div class="p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-semibold text-gray-900">{{ $history->donor_name }}</span>
                                    <span class="text-xs font-bold text-green-600">Rp
                                        {{ number_format($history->amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between text-[10px] text-gray-500">
                                    <span>{{ $history->created_at->diffForHumans() }}</span>
                                    <span
                                        class="px-1.5 py-0.5 bg-gray-100 rounded">{{ $history->payment_method_label }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500 italic text-sm">
                                Belum ada donasi masuk
                            </div>
                        @endforelse
                    </div>
                    @if ($infaqList->infaqHistories()->count() > 10)
                        <div class="p-3 bg-gray-50 text-center border-t border-gray-100">
                            <a href="{{ route('infaq.history.index', ['infaq_list_id' => $infaqList->id]) }}"
                                class="text-xs font-bold text-red-600 hover:text-red-700">Lihat Semua Riwayat</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Image Viewer Modal -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 p-4">
        <div class="flex items-center justify-center h-full">
            <div class="relative max-w-4xl max-h-full">
                <button onclick="closeImageModal()" class="absolute -top-10 right-0 text-white hover:text-gray-300">
                    <i class="fad fa-times text-3xl"></i>
                </button>
                <img id="modalImage" src="" alt="Preview" class="max-w-full max-h-screen rounded-lg">
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function viewImage(imageUrl) {
                document.getElementById('modalImage').src = imageUrl;
                document.getElementById('imageModal').classList.remove('hidden');
            }

            function closeImageModal() {
                document.getElementById('imageModal').classList.add('hidden');
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeImageModal();
            });
        </script>
    @endpush
@endsection
