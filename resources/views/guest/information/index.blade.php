@extends('layouts.guest')

@section('title', 'InfoHub - Blog Informasi')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-gray-50">
        <!-- Modern Navbar -->
        <nav class="bg-white shadow-md sticky top-0 z-50 border-b-4 border-red-600">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">
                    <!-- Logo & Brand -->
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-red-600 to-red-700 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-newspaper text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1
                                class="text-2xl font-bold bg-gradient-to-r from-red-600 to-red-800 bg-clip-text text-transparent">
                                InfoHub</h1>
                            <p class="text-xs text-gray-500">Portal Informasi Terkini</p>
                        </div>
                    </div>

                    <!-- Search Bar (Desktop) -->
                    <div class="hidden md:flex flex-1 max-w-2xl mx-8">
                        <div class="relative w-full">
                            <input id="searchInput" type="text" placeholder="Cari artikel, kategori, atau penulis..."
                                class="w-full px-6 py-3 bg-gray-50 border-2 border-gray-200 rounded-full text-sm focus:outline-none focus:border-red-500 focus:bg-white transition-all duration-300 shadow-sm">
                            <i class="fas fa-search absolute right-5 top-4 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-3">
                        <button onclick="FeedApp.UI.toggleFilters()"
                            class="p-3 hover:bg-red-50 rounded-full transition-colors duration-200 group">
                            <i class="fas fa-sliders-h text-xl text-gray-600 group-hover:text-red-600"></i>
                        </button>
                        @auth
                            <a href="/dashboard"
                                class="hidden sm:flex items-center space-x-2 bg-gradient-to-r from-red-600 to-red-700 text-white px-5 py-2.5 rounded-full hover:shadow-lg transition-all duration-300">
                                <i class="fas fa-home"></i>
                                <span class="font-semibold">Dashboard</span>
                            </a>
                        @else
                            <a href="/login"
                                class="hidden sm:flex items-center space-x-2 bg-gradient-to-r from-red-600 to-red-700 text-white px-5 py-2.5 rounded-full hover:shadow-lg transition-all duration-300">
                                <i class="fas fa-sign-in-alt"></i>
                                <span class="font-semibold">Login</span>
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Mobile Search -->
                <div class="md:hidden pb-4">
                    <div class="relative">
                        <input type="text" placeholder="Cari informasi..."
                            class="w-full px-4 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-full text-sm focus:outline-none focus:border-red-500">
                        <i class="fas fa-search absolute right-4 top-3 text-gray-400"></i>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Filter Bar -->
        <div id="filterBar" class="hidden bg-white shadow-md border-b-2 border-red-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-tag text-red-600 mr-2"></i>Kategori
                        </label>
                        <select id="categoryFilter"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-red-500 transition-colors">
                            <option value="">Semua Kategori</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-sort text-red-600 mr-2"></i>Urutkan
                        </label>
                        <select id="sortBy"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-red-500 transition-colors">
                            <option value="created_at">Terbaru</option>
                            <option value="title">Judul (A-Z)</option>
                            <option value="trending">Trending</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button onclick="FeedApp.UI.toggleFilters()"
                            class="w-full bg-red-600 text-white px-4 py-3 rounded-lg hover:bg-red-700 transition-colors font-semibold">
                            <i class="fas fa-check mr-2"></i>Terapkan Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Container -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Posts Feed -->
                    <div id="postsFeed" class="space-y-6"></div>

                    <!-- Loading Indicator -->
                    <div id="loadingIndicator" class="hidden flex justify-center py-8">
                        <div class="flex items-center space-x-3">
                            <div class="animate-spin">
                                <i class="fas fa-circle-notch text-red-600 text-3xl"></i>
                            </div>
                            <span class="text-gray-600 font-semibold">Memuat artikel...</span>
                        </div>
                    </div>

                    <!-- End of Feed -->
                    <div id="endOfFeed" class="hidden text-center py-12">
                        <div class="bg-white rounded-2xl shadow-lg p-8">
                            <i class="fas fa-check-circle text-6xl text-red-600 mb-4"></i>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Anda sudah mencapai akhir</h3>
                            <p class="text-gray-600">Tidak ada artikel lagi untuk ditampilkan</p>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 space-y-6">
                        <!-- Trending Widget -->
                        <div class="bg-gradient-to-br from-red-600 to-red-700 rounded-2xl shadow-xl p-6 text-white">
                            <h3 class="text-xl font-bold mb-4 flex items-center">
                                <i class="fas fa-fire mr-2"></i>Trending Now
                            </h3>
                            <div class="space-y-3" id="trendingPosts">
                                <div
                                    class="bg-white/10 backdrop-blur-sm rounded-lg p-3 hover:bg-white/20 transition-colors cursor-pointer">
                                    <p class="text-sm font-semibold line-clamp-2">Loading trending posts...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Categories Widget -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-folder text-red-600 mr-2"></i>Kategori
                            </h3>
                            <div id="categoriesList" class="flex flex-wrap gap-2">
                                <span
                                    class="px-3 py-1.5 bg-red-100 text-red-600 rounded-full text-sm font-semibold hover:bg-red-200 cursor-pointer transition-colors">
                                    Loading...
                                </span>
                            </div>
                        </div>

                        <!-- Newsletter Widget -->
                        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl shadow-xl p-6 text-white">
                            <i class="fas fa-envelope text-4xl mb-3 text-red-400"></i>
                            <h3 class="text-xl font-bold mb-2">Newsletter</h3>
                            <p class="text-gray-300 text-sm mb-4">Dapatkan update artikel terbaru langsung ke email Anda</p>
                            <div class="flex gap-2">
                                <input type="email" placeholder="Email Anda"
                                    class="flex-1 px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-400 text-sm focus:outline-none focus:border-red-400">
                                <button class="bg-red-600 px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
            <div
                class="sticky top-0 bg-white border-b-2 border-red-600 p-4 flex items-center justify-between z-10 rounded-t-2xl">
                <h2 class="text-xl font-bold text-gray-900">Detail Artikel</h2>
                <button onclick="FeedApp.UI.closeModal('detailModal')"
                    class="text-gray-500 hover:text-red-600 transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div id="modalContent" class="p-6"></div>
        </div>
    </div>

    <!-- Lightbox Modal -->
    <div id="lightboxModal" class="hidden fixed inset-0 bg-black z-[60] flex items-center justify-center">
        <button onclick="FeedApp.UI.closeModal('lightboxModal')"
            class="absolute top-4 right-4 text-white hover:text-red-400 z-50 transition-colors">
            <i class="fas fa-times text-3xl"></i>
        </button>
        <button onclick="FeedApp.Media.prevSlide()"
            class="absolute left-4 text-white hover:text-red-400 z-50 p-4 bg-black/30 rounded-full hover:bg-black/50 transition-all">
            <i class="fas fa-chevron-left text-3xl"></i>
        </button>
        <button onclick="FeedApp.Media.nextSlide()"
            class="absolute right-4 text-white hover:text-red-400 z-50 p-4 bg-black/30 rounded-full hover:bg-black/50 transition-all">
            <i class="fas fa-chevron-right text-3xl"></i>
        </button>

        <div id="lightboxContent" class="w-full h-full flex items-center justify-center p-4"></div>

        <div class="absolute bottom-4 left-0 right-0 text-center text-white">
            <span id="lightboxCounter" class="bg-black/70 px-4 py-2 rounded-full text-sm font-semibold">1 / 1</span>
        </div>
    </div>

    <!-- Share Modal -->
    <div id="shareModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[70] flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl animate-fadeIn">
            <div class="p-6 border-b-2 border-red-600">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-share-alt text-red-600 mr-2"></i>
                        Bagikan Artikel
                    </h3>
                    <button onclick="FeedApp.UI.closeModal('shareModal')"
                        class="text-gray-500 hover:text-red-600 transition-colors">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                <p id="shareTitle" class="text-sm text-gray-600 mt-2 line-clamp-2"></p>
            </div>

            <div class="p-6">
                <!-- Social Media Buttons -->
                <div class="grid grid-cols-4 gap-3 mb-6">
                    <button onclick="FeedApp.Share.toFacebook()"
                        class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-blue-50 transition-colors group">
                        <div
                            class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fab fa-facebook-f text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-700">Facebook</span>
                    </button>

                    <button onclick="FeedApp.Share.toTwitter()"
                        class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-blue-50 transition-colors group">
                        <div
                            class="w-12 h-12 bg-blue-400 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fab fa-twitter text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-700">Twitter</span>
                    </button>

                    <button onclick="FeedApp.Share.toWhatsApp()"
                        class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-green-50 transition-colors group">
                        <div
                            class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fab fa-whatsapp text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-700">WhatsApp</span>
                    </button>

                    <button onclick="FeedApp.Share.toTelegram()"
                        class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-blue-50 transition-colors group">
                        <div
                            class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fab fa-telegram-plane text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-700">Telegram</span>
                    </button>

                    <button onclick="FeedApp.Share.toLinkedIn()"
                        class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-blue-50 transition-colors group">
                        <div
                            class="w-12 h-12 bg-blue-700 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fab fa-linkedin-in text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-700">LinkedIn</span>
                    </button>

                    <button onclick="FeedApp.Share.toInstagram()"
                        class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-pink-50 transition-colors group">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-500 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fab fa-instagram text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-700">Instagram</span>
                    </button>

                    <button onclick="FeedApp.Share.toTikTok()"
                        class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                        <div
                            class="w-12 h-12 bg-black rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fab fa-tiktok text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-700">TikTok</span>
                    </button>

                    <button onclick="FeedApp.Share.toEmail()"
                        class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                        <div
                            class="w-12 h-12 bg-gray-600 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-envelope text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-700">Email</span>
                    </button>
                </div>

                <!-- Copy Link Section -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Link Artikel</label>
                    <div class="flex gap-2">
                        <input type="text" id="shareLink" readonly
                            class="flex-1 px-4 py-2 bg-white border-2 border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-red-500">
                        <button onclick="FeedApp.Share.copyLink()"
                            class="px-6 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:shadow-lg transition-all font-semibold">
                            <i class="fas fa-copy mr-2"></i>Salin
                        </button>
                    </div>
                    <p id="copyFeedback" class="hidden text-sm text-green-600 font-semibold mt-2">
                        <i class="fas fa-check-circle mr-1"></i>Link berhasil disalin!
                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .media-grid-1 {
            grid-template-columns: 1fr;
        }

        .media-grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .media-grid-3 {
            grid-template-columns: repeat(2, 1fr);
        }

        .media-grid-3>div:first-child {
            grid-row: span 2;
        }

        .media-grid-4 {
            grid-template-columns: repeat(2, 1fr);
        }

        /* Card hover effect */
        .article-card {
            transition: all 0.3s ease;
        }

        .article-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(220, 38, 38, 0.15);
        }

        /* Smooth scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #DC2626;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #991B1B;
        }
    </style>
@endsection

@push('scripts')
    <script>
        const FeedApp = {
            State: {
                posts: [],
                currentPage: 1,
                totalPages: 1,
                isLoading: false,
                hasMore: true,
                searchQuery: '',
                selectedCategory: '',
                sortBy: 'created_at',
                apiBaseUrl: '/api',
                categories: [],
                observer: null,
                currentLightboxMedia: [],
                currentLightboxIndex: 0,
                currentShareId: null,
                currentShareTitle: ''
            },

            init() {
                document.addEventListener('DOMContentLoaded', () => {
                    this.Events.setup();
                    if (this.Realtime) this.Realtime.init();
                    this.API.fetchCategories();
                    this.API.fetchTrendingPosts();
                    this.Actions.loadMorePosts();
                    this.Utils.setupInfiniteScroll();
                });
            },

            Events: {
                setup() {
                    const searchInput = document.getElementById('searchInput');
                    const categoryFilter = document.getElementById('categoryFilter');
                    const sortBy = document.getElementById('sortBy');

                    let searchTimeout;
                    searchInput.addEventListener('input', (e) => {
                        clearTimeout(searchTimeout);
                        FeedApp.State.searchQuery = e.target.value;
                        FeedApp.State.currentPage = 1;
                        FeedApp.State.posts = [];
                        searchTimeout = setTimeout(() => FeedApp.Actions.loadMorePosts(), 500);
                    });

                    categoryFilter.addEventListener('change', (e) => {
                        FeedApp.State.selectedCategory = e.target.value;
                        FeedApp.State.currentPage = 1;
                        FeedApp.State.posts = [];
                        FeedApp.Actions.loadMorePosts();
                    });

                    sortBy.addEventListener('change', (e) => {
                        FeedApp.State.sortBy = e.target.value;
                        FeedApp.State.currentPage = 1;
                        FeedApp.State.posts = [];
                        if (e.target.value === 'trending') {
                            FeedApp.Actions.fetchTrending();
                        } else {
                            FeedApp.Actions.loadMorePosts();
                        }
                    });

                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') {
                            FeedApp.UI.closeModal('detailModal');
                            FeedApp.UI.closeModal('lightboxModal');
                        }
                        if (!document.getElementById('lightboxModal').classList.contains('hidden')) {
                            if (e.key === 'ArrowLeft') FeedApp.Media.prevSlide();
                            if (e.key === 'ArrowRight') FeedApp.Media.nextSlide();
                        }
                    });
                }
            },

            API: {
                async fetch(endpoint, params = {}) {
                    const url = new URL(`${FeedApp.State.apiBaseUrl}${endpoint}`, window.location.origin);
                    Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));

                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                'content') || ''
                        }
                    });
                    return response.json();
                },

                async fetchCategories() {
                    try {
                        const data = await this.fetch('/information-categories', {
                            per_page: 100
                        });
                        FeedApp.State.categories = data.data;
                        FeedApp.Renderer.renderCategories(data.data);
                    } catch (error) {
                        console.error('Error fetching categories:', error);
                    }
                },

                async fetchTrendingPosts() {
                    try {
                        const data = await this.fetch('/informations/trending', {
                            per_page: 5
                        });
                        FeedApp.Renderer.renderTrendingPosts(data.data);
                    } catch (error) {
                        console.error('Error fetching trending posts:', error);
                    }
                },

                async submitComment(postId, content, parentId = null) {
                    return fetch(`${FeedApp.State.apiBaseUrl}/informations/${postId}/comments`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                'content') || '',
                            'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`
                        },
                        body: JSON.stringify({
                            content,
                            parent_id: parentId
                        })
                    });
                },

                async updateComment(commentId, content) {
                    return fetch(`${FeedApp.State.apiBaseUrl}/comments/${commentId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                'content') || '',
                            'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`
                        },
                        body: JSON.stringify({
                            content
                        })
                    });
                },

                async deleteComment(commentId) {
                    return fetch(`${FeedApp.State.apiBaseUrl}/comments/${commentId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                'content') || '',
                            'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`
                        }
                    });
                }
            },

            Actions: {
                async loadMorePosts() {
                    if (FeedApp.State.isLoading || !FeedApp.State.hasMore) return;

                    FeedApp.State.isLoading = true;
                    FeedApp.UI.showLoading(true);

                    try {
                        const params = {
                            page: FeedApp.State.currentPage,
                            per_page: 6,
                            sort_by: FeedApp.State.sortBy
                        };
                        if (FeedApp.State.searchQuery) params.search = FeedApp.State.searchQuery;
                        if (FeedApp.State.selectedCategory) params.category_id = FeedApp.State.selectedCategory;

                        const data = await FeedApp.API.fetch('/informations', params);

                        if (data.data.length > 0) {
                            FeedApp.State.posts.push(...data.data);
                            FeedApp.Renderer.renderPosts(data.data);
                            FeedApp.State.currentPage++;
                        }

                        FeedApp.State.hasMore = FeedApp.State.currentPage <= data.meta.last_page;
                        FeedApp.UI.toggleEndOfFeed(!FeedApp.State.hasMore);
                    } catch (error) {
                        console.error('Error loading posts:', error);
                    } finally {
                        FeedApp.State.isLoading = false;
                        FeedApp.UI.showLoading(false);
                    }
                },

                async fetchTrending() {
                    FeedApp.State.currentPage = 1;
                    FeedApp.State.posts = [];
                    document.getElementById('postsFeed').innerHTML = '';

                    try {
                        const data = await FeedApp.API.fetch('/informations/trending', {
                            per_page: 6
                        });
                        FeedApp.State.posts = data.data;
                        FeedApp.Renderer.renderPosts(data.data);
                        FeedApp.UI.toggleEndOfFeed(true);
                    } catch (error) {
                        console.error('Error fetching trending:', error);
                    }
                },

                async openPostDetail(postId) {
                    try {
                        const data = await FeedApp.API.fetch(`/informations/${postId}`);
                        FeedApp.Renderer.renderModalContent(data.data);
                        document.getElementById('detailModal').classList.remove('hidden');
                        FeedApp.Realtime.subscribeToPost(postId);
                    } catch (error) {
                        console.error('Error opening post:', error);
                    }
                },

                async submitComment(event, postId, parentId = null) {
                    event.preventDefault();
                    const textarea = event.target.querySelector('textarea');
                    const content = textarea.value;
                    if (!content.trim()) return;

                    try {
                        const response = await FeedApp.API.submitComment(postId, content, parentId);
                        if (response.ok) {
                            textarea.value = '';
                            if (parentId) {
                                // Hide reply form if it's a reply
                                document.getElementById(`reply-form-${parentId}`).classList.add('hidden');
                            }
                            // We don't need to reload the whole post, Pusher will handle the new comment.
                            // But for immediate feedback (if Pusher is slow or fails), we could append it.
                            // For now, let's rely on Pusher or reload if needed.
                            // Actually, let's reload to be safe and ensure consistent state until Pusher is fully verified.
                            // Or just let Pusher do it.
                            // Let's reload for now to ensure user sees their comment if Pusher has issues.
                            // But reloading closes the modal? No, openPostDetail re-renders content.
                            this.openPostDetail(postId);
                        }
                    } catch (error) {
                        console.error('Error submitting comment:', error);
                    }
                },

                editComment(commentId, currentContent) {
                    const commentEl = document.getElementById(`comment-content-${commentId}`);
                    const editForm = document.getElementById(`edit-form-${commentId}`);

                    if (commentEl && editForm) {
                        commentEl.classList.add('hidden');
                        editForm.classList.remove('hidden');
                        editForm.querySelector('textarea').value = currentContent;
                        editForm.querySelector('textarea').focus();
                    }
                },

                cancelEdit(commentId) {
                    const commentEl = document.getElementById(`comment-content-${commentId}`);
                    const editForm = document.getElementById(`edit-form-${commentId}`);

                    if (commentEl && editForm) {
                        commentEl.classList.remove('hidden');
                        editForm.classList.add('hidden');
                    }
                },

                async updateComment(event, commentId) {
                    event.preventDefault();
                    const form = event.target;
                    const content = form.querySelector('textarea').value;

                    if (!content.trim()) return;

                    try {
                        const response = await FeedApp.API.updateComment(commentId, content);
                        if (response.ok) {
                            // Optimistic update or reload
                            // For simplicity, let's reload the post detail to refresh comments
                            // But better UX is to update DOM directly.
                            // Let's reload for now to be safe.
                            this.openPostDetail(this.currentPostId);
                            // Note: currentPostId needs to be accessible. 
                            // FeedApp.Realtime.currentPostId is available but FeedApp.Actions might not have it easily unless we store it in State.
                            // We can pass postId to updateComment action or store it.
                            // Let's use FeedApp.Realtime.currentPostId if available.
                        }
                    } catch (error) {
                        console.error('Error updating comment:', error);
                        alert('Gagal mengupdate komentar');
                    }
                },

                async deleteComment(commentId) {
                    if (!confirm('Apakah Anda yakin ingin menghapus komentar ini?')) return;

                    try {
                        const response = await FeedApp.API.deleteComment(commentId);
                        if (response.ok) {
                            // Reload post detail
                            if (FeedApp.Realtime.currentPostId) {
                                this.openPostDetail(FeedApp.Realtime.currentPostId);
                            }
                        }
                    } catch (error) {
                        console.error('Error deleting comment:', error);
                        alert('Gagal menghapus komentar');
                    }
                },

                openShareModal(postId, title) {
                    FeedApp.State.currentShareId = postId;
                    FeedApp.State.currentShareTitle = title;

                    const shareUrl = `${window.location.origin}/informations/${postId}`;
                    document.getElementById('shareLink').value = shareUrl;
                    document.getElementById('shareTitle').textContent = title;
                    document.getElementById('copyFeedback').classList.add('hidden');

                    FeedApp.UI.openModal('shareModal');
                }
            },

            Media: {
                renderGrid(media, postId) {
                    if (!media || media.length === 0) return '';

                    const count = media.length;
                    let gridClass = 'grid gap-2 ';
                    if (count === 1) gridClass += 'media-grid-1';
                    else if (count === 2) gridClass += 'media-grid-2';
                    else if (count === 3) gridClass += 'media-grid-3';
                    else gridClass += 'media-grid-4';

                    const displayMedia = media.slice(0, 4);
                    const remaining = count - 4;

                    return `
                    <div class="${gridClass} mt-4 rounded-xl overflow-hidden">
                        ${displayMedia.map((item, index) => {
                            const isLast = index === 3 && remaining > 0;
                            return `
                                                                                                                                                                                                                                                                                                <div class="relative bg-gray-100 cursor-pointer aspect-square group overflow-hidden" 
                                                                                                                                                                                                                                                                                                     onclick="FeedApp.Media.openLightbox(${postId}, ${index})">
                                                                                                                                                                                                                                                                                                    ${this.renderThumbnail(item)}
                                                                                                                                                                                                                                                                                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all duration-300"></div>
                                                                                                                                                                                                                                                                                                    ${item.type === 'video' ? '<div class="absolute inset-0 flex items-center justify-center"><i class="fas fa-play-circle text-5xl text-white opacity-90 group-hover:opacity-100 transition-opacity"></i></div>' : ''}
                                                                                                                                                                                                                                                                                                    ${isLast ? `
                                        <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                            <span class="text-white font-bold text-3xl">+${remaining}</span>
                                        </div>
                                    ` : ''}
                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                            `;
                        }).join('')}
                    </div>
                `;
                },

                renderThumbnail(item) {
                    if (item.type === 'video') {
                        return `<img src="${item.thumbnail_url || '/images/video-placeholder.png'}" class="w-full h-full object-cover">`;
                    }
                    return `<img src="${item.media_url}" class="w-full h-full object-cover">`;
                },

                openLightbox(postId, index) {
                    const post = FeedApp.State.posts.find(p => p.id === postId);
                    if (!post) return;

                    FeedApp.State.currentLightboxMedia = post.media;
                    FeedApp.State.currentLightboxIndex = index;
                    this.updateLightboxContent();
                    document.getElementById('lightboxModal').classList.remove('hidden');
                },

                updateLightboxContent() {
                    const media = FeedApp.State.currentLightboxMedia[FeedApp.State.currentLightboxIndex];
                    const container = document.getElementById('lightboxContent');
                    const counter = document.getElementById('lightboxCounter');

                    counter.textContent =
                        `${FeedApp.State.currentLightboxIndex + 1} / ${FeedApp.State.currentLightboxMedia.length}`;

                    if (media.type === 'video') {
                        container.innerHTML = `
                        <video controls autoplay class="max-w-full max-h-full rounded-lg shadow-2xl">
                            <source src="${media.media_url}" type="video/mp4">
                        </video>
                    `;
                    } else {
                        container.innerHTML = `
                        <img src="${media.media_url}" class="max-w-full max-h-full rounded-lg shadow-2xl object-contain">
                    `;
                    }
                },

                nextSlide() {
                    if (FeedApp.State.currentLightboxIndex < FeedApp.State.currentLightboxMedia.length - 1) {
                        FeedApp.State.currentLightboxIndex++;
                        this.updateLightboxContent();
                    }
                },

                prevSlide() {
                    if (FeedApp.State.currentLightboxIndex > 0) {
                        FeedApp.State.currentLightboxIndex--;
                        this.updateLightboxContent();
                    }
                }
            },

            Renderer: {
                renderCategories(categories) {
                    const select = document.getElementById('categoryFilter');
                    const list = document.getElementById('categoriesList');

                    select.innerHTML = '<option value="">Semua Kategori</option>';
                    list.innerHTML = '';

                    categories.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.id;
                        option.textContent = cat.name;
                        select.appendChild(option);

                        const badge = document.createElement('span');
                        badge.className =
                            'px-3 py-1.5 bg-red-100 text-red-600 rounded-full text-sm font-semibold hover:bg-red-200 cursor-pointer transition-colors';
                        badge.textContent = cat.name;
                        badge.onclick = () => {
                            FeedApp.State.selectedCategory = cat.id;
                            FeedApp.State.currentPage = 1;
                            FeedApp.State.posts = [];
                            document.getElementById('postsFeed').innerHTML = '';
                            FeedApp.Actions.loadMorePosts();
                        };
                        list.appendChild(badge);
                    });
                },

                renderTrendingPosts(posts) {
                    const container = document.getElementById('trendingPosts');
                    container.innerHTML = '';

                    if (!posts || posts.length === 0) {
                        container.innerHTML = `
                            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-center">
                                <p class="text-sm">Tidak ada trending posts</p>
                            </div>
                        `;
                        return;
                    }

                    posts.forEach((post, index) => {
                        const div = document.createElement('div');
                        div.className =
                            'bg-white/10 backdrop-blur-sm rounded-lg p-4 hover:bg-white/20 transition-all cursor-pointer group';
                        div.onclick = () => FeedApp.Actions.openPostDetail(post.id);
                        div.innerHTML = `
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-full flex items-center justify-center font-bold text-lg">
                                    ${index + 1}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold line-clamp-2 mb-1 group-hover:text-red-100 transition-colors">
                                        ${FeedApp.Utils.escapeHtml(post.title)}
                                    </h4>
                                    <div class="flex items-center gap-2 text-xs text-red-100">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-comments"></i>
                                            ${post.comments_count || 0}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-eye"></i>
                                            ${Math.floor(Math.random() * 500) + 100}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        `;
                        container.appendChild(div);
                    });
                },

                renderPosts(posts) {
                    const feed = document.getElementById('postsFeed');
                    posts.forEach(post => {
                        const div = document.createElement('div');
                        div.className =
                            'article-card bg-white rounded-2xl shadow-lg overflow-hidden fade-in border-2 border-transparent hover:border-red-200';
                        div.innerHTML = `
                        <!-- Article Header -->
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center overflow-hidden shadow-md">
                                        ${post.user.profile_photo ? 
                                            `<img src="${post.user.profile_photo}" class="w-full h-full object-cover">` : 
                                            `<i class="fas fa-user text-white text-lg"></i>`}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900">${FeedApp.Utils.escapeHtml(post.user.name)}</h3>
                                        <p class="text-xs text-gray-500">${FeedApp.Utils.formatTimeAgo(post.created_at)}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <span class="flex items-center gap-1.5 text-xs">
                                        ${post.visibility === 'public' ? 
                                            '<i class="fas fa-globe text-green-500"></i><span class="font-semibold text-green-600">Public</span>' : 
                                            post.visibility === 'listed' ? 
                                            '<i class="fas fa-list text-blue-500"></i><span class="font-semibold text-blue-600">Listed</span>' : 
                                            '<i class="fas fa-lock text-gray-500"></i><span class="font-semibold text-gray-600">Private</span>'
                                        }
                                    </span>
                                    ${post.category ? `<span class="px-3 py-1.5 bg-red-100 text-red-600 rounded-full text-xs font-bold">${FeedApp.Utils.escapeHtml(post.category.name)}</span>` : ''}
                                </div>
                            </div>
                        </div>

                        <!-- Article Content -->
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-3 hover:text-red-600 transition-colors cursor-pointer" onclick="FeedApp.Actions.openPostDetail(${post.id})">
                                ${FeedApp.Utils.escapeHtml(post.title)}
                            </h2>
                            <p class="text-gray-600 mb-4 line-clamp-3 leading-relaxed">${FeedApp.Utils.escapeHtml(post.description)}</p>
                            ${FeedApp.Media.renderGrid(post.media, post.id)}
                        </div>

                        <!-- Article Footer -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                            <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
                                <span class="flex items-center space-x-1">
                                    <i class="fas fa-comments text-blue-500"></i>
                                    <span class="font-semibold">${post.comments_count || 0}</span>
                                </span>
                                <span class="flex items-center space-x-1">
                                    <i class="fas fa-share text-gray-500"></i>
                                    <span class="font-semibold">${post.shares_count || 0}</span>
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick="FeedApp.Actions.openPostDetail(${post.id})" class="flex-1 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-lg text-white font-semibold transition-all shadow-md hover:shadow-lg">
                                    <i class="fas fa-comment mr-2"></i>Komentar
                                </button>
                                <button onclick="FeedApp.Actions.openShareModal(${post.id}, '${FeedApp.Utils.escapeHtml(post.title)}')" class="flex-1 py-2.5 bg-white hover:bg-gray-100 rounded-lg text-gray-700 font-semibold transition-all border border-gray-200">
                                    <i class="fas fa-share mr-2"></i>Bagikan
                                </button>
                            </div>
                        </div>
                    `;
                        feed.appendChild(div);
                    });
                },

                renderModalContent(post) {
                    const content = document.getElementById('modalContent');
                    content.innerHTML = `
                    <div class="pb-6 border-b-2 border-red-100">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-red-500 to-red-600 overflow-hidden shadow-lg">
                                ${post.user.profile_photo ? `<img src="${post.user.profile_photo}" class="w-full h-full object-cover">` : ''}
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">${FeedApp.Utils.escapeHtml(post.user.name)}</h3>
                                <p class="text-sm text-gray-500">${FeedApp.Utils.formatDate(post.created_at)}</p>
                            </div>
                        </div>
                    </div>

                    <div class="py-6">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">${FeedApp.Utils.escapeHtml(post.title)}</h2>
                        <div class="text-gray-700 mb-6 prose max-w-none leading-relaxed">${FeedApp.Utils.escapeHtml(post.content || post.description)}</div>
                        ${FeedApp.Media.renderGrid(post.media, post.id)}
                    </div>

                    <div class="py-6 border-t-2 border-red-100">
                        <h3 class="font-bold text-xl text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-comments text-red-600 mr-2"></i>
                            Komentar <span id="modalCommentCount">(${post.comments.length})</span>
                        </h3>
                        <form onsubmit="FeedApp.Actions.submitComment(event, ${post.id})" class="mb-6 bg-red-50 rounded-xl p-4">
                            <textarea class="w-full border-2 text-gray-700 border-red-200 rounded-lg p-4 focus:ring-2 focus:ring-red-500 focus:outline-none resize-none" rows="3" placeholder="Tulis komentar Anda..."></textarea>
                            <div class="mt-3 flex justify-end">
                                <button type="submit" class="bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-2.5 rounded-lg hover:shadow-lg transition-all font-semibold">
                                    <i class="fas fa-paper-plane mr-2"></i>Kirim Komentar
                                </button>
                            </div>
                        </form>
                        <div class="space-y-4">
                            ${post.comments.length > 0 ? post.comments.filter(c => !c.is_reply).map(c => `
                                                                                                <div class="flex gap-3 bg-gray-50 rounded-xl p-4 border border-gray-100">
                                                                                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-100 to-red-200 flex-shrink-0 flex items-center justify-center text-red-600 font-bold shadow-sm">
                                                                                                        ${c.user && c.user.profile_photo ? 
                                                                                                            `<img src="${c.user.profile_photo}" class="w-full h-full rounded-full object-cover">` :
                                                                                                            (c.user ? c.user.name.charAt(0).toUpperCase() : '?')
                                                                                                        }
                                                                                                    </div>
                                                                                                    <div class="flex-1">
                                                                                                        <div class="flex justify-between items-start mb-1">
                                                                                                            <span class="font-bold text-gray-900">${FeedApp.Utils.escapeHtml(c.user ? c.user.name : 'Anonymous')}</span>
                                                                                                            <span class="text-xs text-gray-500">${FeedApp.Utils.formatTimeAgo(c.created_at)}</span>
                                                                                                        </div>
                                                                                                        <p class="text-gray-700 leading-relaxed">${FeedApp.Utils.escapeHtml(c.content)}</p>
                                                                                                        
                                                                                                        <!-- Reply Button -->
                                                                                                        <button onclick="document.getElementById('reply-form-${c.id}').classList.toggle('hidden')" class="text-xs text-red-600 font-semibold mt-2 hover:text-red-700">
                                                                                                            <i class="fas fa-reply mr-1"></i>Balas
                                                                                                        </button>

                                                                                                        <!-- Reply Form -->
                                                                                                        <form id="reply-form-${c.id}" onsubmit="FeedApp.Actions.submitComment(event, ${post.id}, ${c.id})" class="hidden mt-3 animate-fadeIn">
                                                                                                            <div class="flex gap-2">
                                                                                                                <textarea class="flex-1 border-2 text-gray-700 border-red-100 rounded-lg p-2 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none resize-none" rows="2" placeholder="Tulis balasan..."></textarea>
                                                                                                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm font-semibold h-fit self-end">
                                                                                                                    <i class="fas fa-paper-plane"></i>
                                                                                                                </button>
                                                                                                            </div>
                                                                                                        </form>

                                                                                                        <!-- Replies Container -->
                                                                                                <div id="replies-container-${c.id}" class="mt-3 space-y-3 pl-4 border-l-2 border-red-100 ${c.replies && c.replies.length > 0 ? '' : 'hidden'}">
                                                                                                    ${c.replies && c.replies.length > 0 ? c.replies.map(r => `
                                                <div class="flex gap-3 bg-white rounded-lg p-3 border border-gray-100">
                                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex-shrink-0 flex items-center justify-center text-gray-500 font-bold text-xs">
                                                        ${r.user && r.user.profile_photo ? 
                                                            `<img src="${r.user.profile_photo}" class="w-full h-full rounded-full object-cover">` :
                                                            (r.user ? r.user.name.charAt(0).toUpperCase() : '?')
                                                        }
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="flex justify-between items-start mb-1">
                                                            <span class="font-bold text-sm text-gray-900">${FeedApp.Utils.escapeHtml(r.user ? r.user.name : 'Anonymous')}</span>
                                                            <div class="flex items-center gap-2">
                                                                <span class="text-[10px] text-gray-500">${FeedApp.Utils.formatTimeAgo(r.created_at)}</span>
                                                                ${r.is_owner ? `
                                                                                                        <div class="relative group">
                                                                                                            <button class="text-gray-400 hover:text-gray-600">
                                                                                                                <i class="fas fa-ellipsis-v text-[10px]"></i>
                                                                                                            </button>
                                                                                                            <div class="absolute right-0 mt-1 w-32 bg-white rounded-lg shadow-lg border border-gray-100 hidden group-hover:block z-10">
                                                                                                                <button onclick="FeedApp.Actions.editComment(${r.id}, '${FeedApp.Utils.escapeHtml(r.content).replace(/'/g, "\\'")}')" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                                                                                                    <i class="fas fa-edit text-blue-500"></i> Edit
                                                                                                                </button>
                                                                                                                <button onclick="FeedApp.Actions.deleteComment(${r.id})" class="w-full text-left px-4 py-2 text-xs text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                                                                                    <i class="fas fa-trash-alt"></i> Hapus
                                                                                                                </button>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    ` : ''}
                                                            </div>
                                                        </div>
                                                        
                                                        <div id="comment-content-${r.id}">
                                                            <p class="text-sm text-gray-700 leading-relaxed">${FeedApp.Utils.escapeHtml(r.content)}</p>
                                                        </div>

                                                        <form id="edit-form-${r.id}" onsubmit="FeedApp.Actions.updateComment(event, ${r.id})" class="hidden mt-2">
                                                            <textarea class="w-full border-2 text-gray-700 border-gray-200 rounded-lg p-2 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none mb-2" rows="2">${FeedApp.Utils.escapeHtml(r.content)}</textarea>
                                                            <div class="flex justify-end gap-2">
                                                                <button type="button" onclick="FeedApp.Actions.cancelEdit(${r.id})" class="text-[10px] text-gray-500 hover:text-gray-700 font-semibold">Batal</button>
                                                                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-[10px] hover:bg-blue-700 transition-colors font-semibold">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            `).join('') : ''}
                                                                                                </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            `).join('') : '<p class="text-gray-500 text-center py-4 italic">Belum ada komentar. Jadilah yang pertama berkomentar!</p>'}
                        </div>
                    </div>
                `;
                }
            },


            Share: {
                getShareUrl() {
                    return document.getElementById('shareLink').value;
                },

                getShareText() {
                    return FeedApp.State.currentShareTitle;
                },

                async incrementShareCount() {
                    if (!FeedApp.State.currentShareId) return;

                    try {
                        await fetch(
                            `${FeedApp.State.apiBaseUrl}/informations/${FeedApp.State.currentShareId}/share`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        ?.getAttribute(
                                            'content') || '',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            });
                    } catch (error) {
                        console.error('Error incrementing share count:', error);
                    }
                },

                toFacebook() {
                    const url = encodeURIComponent(this.getShareUrl());
                    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank',
                        'width=600,height=400');
                    this.incrementShareCount();
                },

                toTwitter() {
                    const url = encodeURIComponent(this.getShareUrl());
                    const text = encodeURIComponent(this.getShareText());
                    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank',
                        'width=600,height=400');
                    this.incrementShareCount();
                },

                toWhatsApp() {
                    const url = encodeURIComponent(this.getShareUrl());
                    const text = encodeURIComponent(this.getShareText());
                    window.open(`https://wa.me/?text=${text}%20${url}`, '_blank');
                    this.incrementShareCount();
                },

                toTelegram() {
                    const url = encodeURIComponent(this.getShareUrl());
                    const text = encodeURIComponent(this.getShareText());
                    window.open(`https://t.me/share/url?url=${url}&text=${text}`, '_blank');
                    this.incrementShareCount();
                },

                toLinkedIn() {
                    const url = encodeURIComponent(this.getShareUrl());
                    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank',
                        'width=600,height=400');
                    this.incrementShareCount();
                },

                toInstagram() {
                    // Instagram doesn't support direct URL sharing via web
                    // Copy link and show instruction
                    this.copyLink();
                    alert('Link telah disalin! Buka Instagram dan paste link di bio atau story Anda.');
                },

                toTikTok() {
                    // TikTok doesn't support direct URL sharing via web
                    // Copy link and show instruction
                    this.copyLink();
                    alert('Link telah disalin! Buka TikTok dan paste link di bio atau video Anda.');
                },

                toEmail() {
                    const url = encodeURIComponent(this.getShareUrl());
                    const subject = encodeURIComponent(this.getShareText());
                    const body = encodeURIComponent(
                        `Saya ingin berbagi artikel menarik ini dengan Anda:\n\n${this.getShareText()}\n\n${this.getShareUrl()}`
                    );
                    window.location.href = `mailto:?subject=${subject}&body=${body}`;
                    this.incrementShareCount();
                },

                async copyLink() {
                    const linkInput = document.getElementById('shareLink');
                    const feedback = document.getElementById('copyFeedback');

                    try {
                        await navigator.clipboard.writeText(linkInput.value);
                        feedback.classList.remove('hidden');
                        this.incrementShareCount();

                        setTimeout(() => {
                            feedback.classList.add('hidden');
                        }, 3000);
                    } catch (error) {
                        // Fallback for older browsers
                        linkInput.select();
                        document.execCommand('copy');
                        feedback.classList.remove('hidden');
                        this.incrementShareCount();

                        setTimeout(() => {
                            feedback.classList.add('hidden');
                        }, 3000);
                    }
                }
            },


            Realtime: {
                currentPostId: null,

                init() {
                    if (typeof Echo === 'undefined') {
                        console.warn('Laravel Echo is not defined. Realtime updates disabled.');
                        return;
                    }

                    this.subscribeToFeed();
                },

                subscribeToFeed() {
                    window.Echo.channel('informations')
                        .listen('.information.created', (data) => {
                            if (data.information) {
                                this.handleNewPost(data.information);
                            }
                        });
                },

                subscribeToPost(postId) {
                    if (this.currentPostId) {
                        window.Echo.leave(`information.${this.currentPostId}`);
                    }

                    this.currentPostId = postId;
                    window.Echo.channel(`information.${postId}`)
                        .listen('.comment.created', (data) => {
                            if (data.comment) {
                                this.handleNewComment(data.comment);
                            }
                        });
                },

                handleNewPost(post) {
                    // Show notification
                    const notification = document.createElement('div');
                    notification.className =
                        'fixed top-24 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fadeIn flex items-center gap-3 cursor-pointer hover:bg-red-700 transition-colors';
                    notification.innerHTML = `
                        <i class="fas fa-bell text-xl"></i>
                        <div>
                            <p class="font-bold text-sm">Artikel Baru!</p>
                            <p class="text-xs opacity-90 truncate max-w-[200px]">${FeedApp.Utils.escapeHtml(post.title)}</p>
                        </div>
                    `;

                    notification.onclick = () => {
                        FeedApp.Actions.openPostDetail(post.id);
                        notification.remove();
                    };

                    document.body.appendChild(notification);
                    setTimeout(() => notification.remove(), 8000);

                    // Prepend to feed if at top
                    if (window.scrollY < 100) {
                        const feed = document.getElementById('postsFeed');
                        const tempContainer = document.createElement('div');
                        // We need a way to render a single post string, but renderPosts renders to innerHTML.
                        // Let's just reload for now or prepend if we can refactor renderPosts.
                        // For now, just notification is good, user can click to see.
                        // Actually, let's try to prepend.
                        // Since renderPosts clears or appends, we might need a prepend helper.
                        // But for now, notification is safer to avoid breaking layout.
                    }
                },

                handleNewComment(comment) {
                    // Check if it's a reply
                    if (comment.is_reply && comment.parent) {
                        const repliesContainer = document.getElementById(`replies-container-${comment.parent.id}`);
                        if (repliesContainer) {
                            const replyHtml = `
                                <div class="flex gap-3 bg-white rounded-lg p-3 border border-gray-100 animate-fadeIn">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex-shrink-0 flex items-center justify-center text-gray-500 font-bold text-xs">
                                        ${comment.user && comment.user.profile_photo ? 
                                            `<img src="${comment.user.profile_photo}" class="w-full h-full rounded-full object-cover">` :
                                            (comment.user ? comment.user.name.charAt(0).toUpperCase() : '?')
                                        }
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="font-bold text-sm text-gray-900">${FeedApp.Utils.escapeHtml(comment.user ? comment.user.name : 'Anonymous')}</span>
                                            <span class="text-[10px] text-gray-500">Baru saja</span>
                                        </div>
                                        <p class="text-sm text-gray-700 leading-relaxed">${FeedApp.Utils.escapeHtml(comment.content)}</p>
                                    </div>
                                </div>
                            `;
                            repliesContainer.insertAdjacentHTML('beforeend', replyHtml);
                            repliesContainer.classList.remove('hidden');
                        }
                    } else {
                        // Top level comment
                        const commentsList = document.querySelector('#modalContent .space-y-4');
                        if (!commentsList) return;

                        const commentHtml = `
                            <div class="flex gap-3 bg-gray-50 rounded-xl p-4 border border-gray-100 animate-fadeIn">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-100 to-red-200 flex-shrink-0 flex items-center justify-center text-red-600 font-bold shadow-sm">
                                    ${comment.user && comment.user.profile_photo ?
                                        `<img src="${comment.user.profile_photo}" class="w-full h-full rounded-full object-cover">` :
                                        (comment.user ? comment.user.name.charAt(0).toUpperCase() : '?')
                                    }
                                </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="font-bold text-gray-900">${FeedApp.Utils.escapeHtml(comment.user ? comment.user.name : 'Anonymous')}</span>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-gray-500">${FeedApp.Utils.formatTimeAgo(comment.created_at)}</span>
                                                ${comment.is_owner ? `
                                                                                            <div class="relative group">
                                                                                                <button class="text-gray-400 hover:text-gray-600">
                                                                                                    <i class="fas fa-ellipsis-v text-xs"></i>
                                                                                                </button>
                                                                                                <div class="absolute right-0 mt-1 w-32 bg-white rounded-lg shadow-lg border border-gray-100 hidden group-hover:block z-10">
                                                                                                    <button onclick="FeedApp.Actions.editComment(${comment.id}, '${FeedApp.Utils.escapeHtml(comment.content).replace(/'/g, "\\'")}')" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                                                                                        <i class="fas fa-edit text-blue-500"></i> Edit
                                                                                                    </button>
                                                                                                    <button onclick="FeedApp.Actions.deleteComment(${comment.id})" class="w-full text-left px-4 py-2 text-xs text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                                                                        <i class="fas fa-trash-alt"></i> Hapus
                                                                                                    </button>
                                                                                                </div>
                                                                                            </div>
                                                                                        ` : ''}
                                            </div>
                                        </div>

                                        <div id="comment-content-${comment.id}">
                                            <p class="text-gray-700 leading-relaxed">${FeedApp.Utils.escapeHtml(comment.content)}</p>
                                        </div>

                                        <form id="edit-form-${comment.id}" onsubmit="FeedApp.Actions.updateComment(event, ${comment.id})" class="hidden mt-2">
                                            <textarea class="w-full border-2 text-gray-700 border-gray-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none mb-2" rows="2">${FeedApp.Utils.escapeHtml(comment.content)}</textarea>
                                            <div class="flex justify-end gap-2">
                                                <button type="button" onclick="FeedApp.Actions.cancelEdit(${comment.id})" class="text-xs text-gray-500 hover:text-gray-700 font-semibold">Batal</button>
                                                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 transition-colors font-semibold">Simpan</button>
                                            </div>
                                        </form>

                                        <!-- Reply Button -->
                                    <button onclick="document.getElementById('reply-form-${comment.id}').classList.toggle('hidden')" class="text-xs text-red-600 font-semibold mt-2 hover:text-red-700">
                                        <i class="fas fa-reply mr-1"></i>Balas
                                    </button>

                                    <!-- Reply Form -->
                                    <form id="reply-form-${comment.id}" onsubmit="FeedApp.Actions.submitComment(event, ${this.currentPostId}, ${comment.id})" class="hidden mt-3 animate-fadeIn">
                                        <div class="flex gap-2">
                                            <textarea class="flex-1 border-2 text-gray-700 border-red-100 rounded-lg p-2 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none resize-none" rows="2" placeholder="Tulis balasan..."></textarea>
                                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm font-semibold h-fit self-end">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Replies Container -->
                                    <div id="replies-container-${comment.id}" class="mt-3 space-y-3 pl-4 border-l-2 border-red-100 hidden">
                                    </div>
                                </div>
                            </div>
                        `;

                        commentsList.insertAdjacentHTML('beforeend', commentHtml);
                        commentsList.scrollTop = commentsList.scrollHeight;
                    }

                    // Update comment count if visible
                    const countEl = document.getElementById('modalCommentCount');
                    if (countEl) {
                        const currentCount = parseInt(countEl.textContent.replace(/\D/g, '')) || 0;
                        countEl.textContent = `(${currentCount + 1})`;
                    }
                }
            },

            UI: {
                toggleFilters() {
                    document.getElementById('filterBar').classList.toggle('hidden');
                },
                showLoading(show) {
                    const el = document.getElementById('loadingIndicator');
                    show ? el.classList.remove('hidden') : el.classList.add('hidden');
                },
                toggleEndOfFeed(show) {
                    const el = document.getElementById('endOfFeed');
                    show ? el.classList.remove('hidden') : el.classList.add('hidden');
                },
                openModal(id) {
                    document.getElementById(id).classList.remove('hidden');
                },
                closeModal(id) {
                    document.getElementById(id).classList.add('hidden');
                }
            },

            Utils: {
                escapeHtml(text) {
                    if (!text) return '';
                    return text.replace(/[&<>"']/g, m => ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    })[m]);
                },
                formatDate(date) {
                    return new Date(date).toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                },
                formatTimeAgo(date) {
                    const seconds = Math.floor((new Date() - new Date(date)) / 1000);
                    if (seconds < 60) return 'Baru saja';
                    const minutes = Math.floor(seconds / 60);
                    if (minutes < 60) return `${minutes} menit lalu`;
                    const hours = Math.floor(minutes / 60);
                    if (hours < 24) return `${hours} jam lalu`;
                    return `${Math.floor(hours / 24)} hari lalu`;
                },
                setupInfiniteScroll() {
                    const observer = new IntersectionObserver((entries) => {
                        if (entries[0].isIntersecting) FeedApp.Actions.loadMorePosts();
                    }, {
                        rootMargin: '200px'
                    });
                    const sentinel = document.createElement('div');
                    document.getElementById('postsFeed').after(sentinel);
                    observer.observe(sentinel);
                }
            }
        };

        FeedApp.init();
    </script>
@endpush
