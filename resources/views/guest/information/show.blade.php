@extends('layouts.guest')

@section('title', 'Detail Informasi')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-gray-50">
        <!-- Modern Navbar -->
        <nav class="bg-white shadow-md sticky top-0 z-50 border-b-4 border-red-600">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">
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
                    <div class="flex items-center space-x-4">
                        <a href="/informations"
                            class="flex items-center space-x-2 text-gray-700 hover:text-red-600 transition-colors">
                            <i class="fas fa-arrow-left"></i>
                            <span class="hidden sm:inline font-semibold">Kembali</span>
                        </a>
                        @auth
                            <a href="/dashboard"
                                class="hidden sm:flex items-center space-x-2 bg-gradient-to-r from-red-600 to-red-700 text-white px-5 py-2.5 rounded-full hover:shadow-lg transition-all">
                                <i class="fas fa-home"></i>
                                <span class="font-semibold">Dashboard</span>
                            </a>
                        @else
                            <a href="/login"
                                class="hidden sm:flex items-center space-x-2 bg-gradient-to-r from-red-600 to-red-700 text-white px-5 py-2.5 rounded-full hover:shadow-lg transition-all">
                                <i class="fas fa-sign-in-alt"></i>
                                <span class="font-semibold">Login</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Breadcrumb -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center space-x-2 text-sm">
                    <a href="/informations"
                        class="text-gray-600 hover:text-red-600 transition-colors flex items-center space-x-1">
                        <i class="fas fa-home"></i>
                        <span>Beranda</span>
                    </a>
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    <span class="text-red-600 font-semibold">Detail Artikel</span>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Loading State -->
            <div id="loadingState" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <div class="skeleton h-96 w-full rounded-xl mb-6"></div>
                        <div class="skeleton h-10 w-3/4 rounded mb-4"></div>
                        <div class="skeleton h-6 w-1/2 rounded mb-6"></div>
                        <div class="space-y-3">
                            <div class="skeleton h-4 w-full rounded"></div>
                            <div class="skeleton h-4 w-full rounded"></div>
                            <div class="skeleton h-4 w-3/4 rounded"></div>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-1">
                    <div class="skeleton h-64 w-full rounded-2xl"></div>
                </div>
            </div>

            <!-- Article Content -->
            <div id="articleContent" class="hidden grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <article class="lg:col-span-2 space-y-6">
                    <!-- Featured Media -->
                    <div id="mediaSection" class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div
                            class="relative h-96 bg-gradient-to-br from-red-600 to-red-700 flex items-center justify-center">
                            <div class="text-white text-center p-8">
                                <i class="fas fa-images text-6xl mb-4 opacity-80"></i>
                                <p class="text-lg font-semibold" id="mediaCount">Loading media...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Article Header & Content -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <!-- Meta Info -->
                        <div id="articleMeta" class="flex flex-wrap items-center gap-4 mb-6 pb-6 border-b-2 border-red-100">
                        </div>

                        <!-- Title -->
                        <h1 id="articleTitle" class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6 leading-tight"></h1>

                        <!-- Description -->
                        <p id="articleDescription" class="text-xl text-gray-600 mb-8 leading-relaxed font-medium"></p>

                        <!-- Content -->
                        <div id="articleContentBody" class="prose prose-lg max-w-none mb-8 text-gray-700 leading-relaxed">
                            <style>
                                #articleContentBody p {
                                    margin-bottom: 1.5rem;
                                }

                                #articleContentBody h2 {
                                    font-size: 1.875rem;
                                    font-weight: bold;
                                    margin-top: 2rem;
                                    margin-bottom: 1rem;
                                    color: #DC2626;
                                }

                                #articleContentBody h3 {
                                    font-size: 1.5rem;
                                    font-weight: bold;
                                    margin-top: 1.5rem;
                                    margin-bottom: 0.75rem;
                                    color: #EF4444;
                                }

                                #articleContentBody ul,
                                #articleContentBody ol {
                                    margin-left: 2rem;
                                    margin-bottom: 1.5rem;
                                }

                                #articleContentBody li {
                                    margin-bottom: 0.5rem;
                                }

                                #articleContentBody blockquote {
                                    border-left: 4px solid #DC2626;
                                    padding-left: 1rem;
                                    font-style: italic;
                                    color: #4B5563;
                                }
                            </style>
                        </div>

                        <!-- Social Stats -->
                        <div id="socialStats" class="border-t-2 border-red-100 pt-6 flex flex-wrap gap-6"></div>

                        <!-- Share Buttons -->
                        <div class="border-t-2 border-red-100 mt-8 pt-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-share-alt text-red-600 mr-2"></i>Bagikan Artikel
                            </h3>
                            <div class="flex flex-wrap gap-3">
                                <a href="#"
                                    class="flex items-center space-x-2 bg-blue-600 text-white px-5 py-3 rounded-xl hover:bg-blue-700 transition-all shadow-md hover:shadow-lg">
                                    <i class="fab fa-facebook text-xl"></i>
                                    <span class="font-semibold">Facebook</span>
                                </a>
                                <a href="#"
                                    class="flex items-center space-x-2 bg-blue-400 text-white px-5 py-3 rounded-xl hover:bg-blue-500 transition-all shadow-md hover:shadow-lg">
                                    <i class="fab fa-twitter text-xl"></i>
                                    <span class="font-semibold">Twitter</span>
                                </a>
                                <a href="#"
                                    class="flex items-center space-x-2 bg-green-600 text-white px-5 py-3 rounded-xl hover:bg-green-700 transition-all shadow-md hover:shadow-lg">
                                    <i class="fab fa-whatsapp text-xl"></i>
                                    <span class="font-semibold">WhatsApp</span>
                                </a>
                                <button
                                    class="flex items-center space-x-2 bg-gray-700 text-white px-5 py-3 rounded-xl hover:bg-gray-800 transition-all shadow-md hover:shadow-lg">
                                    <i class="fas fa-link text-xl"></i>
                                    <span class="font-semibold">Salin Link</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-8 flex items-center">
                            <i class="fas fa-comments text-red-600 mr-3"></i>
                            Komentar (<span id="commentCount">0</span>)
                        </h2>

                        <!-- Add Comment Form -->
                        <div id="addCommentForm"
                            class="mb-8 bg-gradient-to-br from-red-50 to-orange-50 rounded-xl p-6 border-2 border-red-200">
                            <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-pen text-red-600 mr-2"></i>Tulis Komentar
                            </h3>
                            <form onsubmit="submitComment(event)">
                                <textarea id="commentContent" placeholder="Bagikan pendapat Anda tentang artikel ini..." rows="4"
                                    class="w-full px-5 py-4 border-2 border-red-200 rounded-xl focus:outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100 resize-none transition-all"
                                    required></textarea>
                                <div class="mt-4 flex justify-between items-center">
                                    <p class="text-sm text-gray-600">
                                        <i class="fas fa-info-circle text-red-600 mr-1"></i>
                                        Komentar Anda akan ditampilkan setelah dikirim
                                    </p>
                                    <button type="submit"
                                        class="bg-gradient-to-r from-red-600 to-red-700 text-white px-8 py-3 rounded-xl hover:shadow-lg transition-all disabled:opacity-50 font-bold"
                                        id="submitCommentBtn">
                                        <i class="fas fa-paper-plane mr-2"></i>Kirim Komentar
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Comments List -->
                        <div id="commentsList" class="space-y-6"></div>
                    </div>
                </article>

                <!-- Sidebar -->
                <aside class="lg:col-span-1 space-y-6">
                    <!-- Author Widget -->
                    <div id="authorWidget"
                        class="hidden bg-gradient-to-br from-red-600 to-red-700 text-white rounded-2xl shadow-xl p-6 sticky top-24">
                        <h3 class="text-lg font-bold mb-6 flex items-center">
                            <i class="fas fa-user-circle mr-2 text-2xl"></i>Tentang Penulis
                        </h3>
                        <div class="text-center">
                            <div
                                class="w-20 h-20 mx-auto mb-4 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center overflow-hidden border-4 border-white/30">
                                <i class="fas fa-user-circle text-5xl opacity-80"></i>
                            </div>
                            <h4 id="authorName" class="font-bold text-xl mb-2"></h4>
                            <p id="authorEmail" class="text-red-100 text-sm mb-6"></p>
                            <a href="#"
                                class="inline-block bg-white text-red-600 px-6 py-3 rounded-xl font-bold hover:bg-red-50 transition-all shadow-lg">
                                <i class="fas fa-user mr-2"></i>Lihat Profil
                            </a>
                        </div>
                    </div>

                    <!-- Info Widget -->
                    <div id="infoWidget" class="hidden bg-white rounded-2xl shadow-lg p-6 sticky top-96">
                        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-info-circle text-red-600 mr-2"></i>Informasi Artikel
                        </h3>
                        <div id="infoContent" class="space-y-4 text-sm"></div>
                    </div>

                    <!-- Related Posts Widget -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-newspaper text-red-600 mr-2"></i>Artikel Terkait
                        </h3>
                        <div class="space-y-4">
                            <div class="flex gap-3 p-3 hover:bg-red-50 rounded-xl transition-colors cursor-pointer">
                                <div class="w-16 h-16 bg-red-100 rounded-lg flex-shrink-0"></div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-sm text-gray-900 line-clamp-2 mb-1">Loading related
                                        posts...</h4>
                                    <p class="text-xs text-gray-500">Just now</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            <!-- Not Found State -->
            <div id="notFoundState" class="hidden">
                <div class="bg-white rounded-2xl shadow-lg p-16 text-center">
                    <i class="fas fa-exclamation-circle text-8xl text-red-300 mb-6"></i>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Artikel Tidak Ditemukan</h2>
                    <p class="text-gray-600 mb-8">Maaf, artikel yang Anda cari tidak dapat ditemukan atau telah dihapus.
                    </p>
                    <a href="/informations"
                        class="inline-flex items-center space-x-2 bg-gradient-to-r from-red-600 to-red-700 text-white px-8 py-4 rounded-xl hover:shadow-lg transition-all font-bold">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .prose {
            max-width: none;
        }
    </style>
@endsection

@push('scripts')
    <script>
        const state = {
            information: null,
            informationId: '{{ $id ?? 0 }}',
            apiBaseUrl: '/api',
            isAuthenticated: false
        };

        document.addEventListener('DOMContentLoaded', function() {
            state.isAuthenticated = !!document.querySelector('meta[name="auth-token"]');

            if (state.informationId) {
                fetchInformation();
            } else {
                showNotFound();
            }
        });

        async function fetchInformation() {
            try {
                const response = await fetch(`${state.apiBaseUrl}/informations/${state.informationId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    showNotFound();
                    return;
                }

                const data = await response.json();
                state.information = data.data;

                renderArticle();
                hideLoading();
                document.getElementById('articleContent').classList.remove('hidden');
            } catch (error) {
                console.error('Error fetching information:', error);
                showNotFound();
            }
        }

        function renderArticle() {
            const info = state.information;

            // Render Meta Info
            const metaHtml = `
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center overflow-hidden shadow-md">
                    ${info.user.profile_photo ? `<img src="${info.user.profile_photo}" class="w-full h-full object-cover">` : `<i class="fas fa-user text-white"></i>`}
                </div>
                <div>
                    <p class="font-bold text-gray-900">${escapeHtml(info.user.name)}</p>
                    <p class="text-sm text-gray-500">${formatDate(info.created_at)}</p>
                </div>
            </div>
            ${info.category ? `
                    <span class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-full text-sm font-bold shadow-md">
                        <i class="fas fa-tag mr-1"></i>${escapeHtml(info.category.name)}
                    </span>
                ` : ''}
        `;
            document.getElementById('articleMeta').innerHTML = metaHtml;

            // Render Title & Description
            document.getElementById('articleTitle').textContent = info.title;
            document.getElementById('articleDescription').textContent = info.description;

            // Render Content
            if (info.content) {
                document.getElementById('articleContentBody').innerHTML = escapeHtml(info.content).replace(/\n/g, '<br>');
            }

            // Render Media
            if (info.media && info.media.length > 0) {
                const mediaSection = document.getElementById('mediaSection');
                const firstMedia = info.media[0];

                if (firstMedia.type === 'video') {
                    mediaSection.innerHTML = `
                        <video controls class="w-full h-96 object-cover">
                            <source src="${firstMedia.media_url}" type="video/mp4">
                        </video>
                    `;
                } else {
                    mediaSection.innerHTML = `
                        <img src="${firstMedia.media_url}" class="w-full h-96 object-cover" alt="${escapeHtml(info.title)}">
                    `;
                }
                document.getElementById('mediaCount').textContent = `${info.media.length} Media`;
            }

            // Render Social Stats
            const statsHtml = `
            <div class="flex items-center space-x-3 bg-red-50 px-5 py-3 rounded-xl">
                <i class="fas fa-eye text-red-600 text-2xl"></i>
                <div>
                    <p class="text-2xl font-bold text-gray-900">${Math.floor(Math.random() * 1000) + 100}</p>
                    <p class="text-xs text-gray-600">Views</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 bg-blue-50 px-5 py-3 rounded-xl">
                <i class="fas fa-comments text-blue-600 text-2xl"></i>
                <div>
                    <p class="text-2xl font-bold text-gray-900">${info.comments.length}</p>
                    <p class="text-xs text-gray-600">Comments</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 bg-pink-50 px-5 py-3 rounded-xl">
                <i class="fas fa-heart text-pink-600 text-2xl"></i>
                <div>
                    <p class="text-2xl font-bold text-gray-900">${Math.floor(Math.random() * 500) + 10}</p>
                    <p class="text-xs text-gray-600">Likes</p>
                </div>
            </div>
        `;
            document.getElementById('socialStats').innerHTML = statsHtml;

            // Render Comments
            document.getElementById('commentCount').textContent = info.comments.length;
            renderComments(info.comments);

            // Render Info Widget
            const infoHtml = `
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <span class="text-gray-600 font-semibold">Status</span>
                <span class="bg-green-100 text-green-800 px-4 py-1.5 rounded-full text-xs font-bold">
                    <i class="fas fa-check-circle mr-1"></i>Published
                </span>
            </div>
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <span class="text-gray-600 font-semibold">Visibilitas</span>
                <span class="bg-blue-100 text-blue-800 px-4 py-1.5 rounded-full text-xs font-bold capitalize">
                    <i class="fas fa-globe mr-1"></i>${info.visibility}
                </span>
            </div>
            <div class="pb-4 border-b border-gray-200">
                <span class="text-gray-600 font-semibold block mb-2">Kategori</span>
                <span class="text-red-600 font-bold text-lg">
                    ${info.category ? escapeHtml(info.category.name) : 'Tidak ada kategori'}
                </span>
            </div>
            <div class="pb-4">
                <span class="text-gray-600 font-semibold block mb-3">Penulis</span>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <i class="fas fa-user-circle text-red-600 text-2xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">${escapeHtml(info.user.name)}</p>
                        <p class="text-xs text-gray-500">${escapeHtml(info.user.email)}</p>
                    </div>
                </div>
            </div>
        `;
            document.getElementById('infoContent').innerHTML = infoHtml;
            document.getElementById('infoWidget').classList.remove('hidden');

            // Render Author Widget
            document.getElementById('authorName').textContent = info.user.name;
            document.getElementById('authorEmail').textContent = info.user.email;
            document.getElementById('authorWidget').classList.remove('hidden');

            // Setup Comment Form
            if (!state.isAuthenticated) {
                const form = document.getElementById('addCommentForm');
                form.innerHTML = `
                <div class="p-6 bg-yellow-50 border-2 border-yellow-300 rounded-xl text-center">
                    <i class="fas fa-lock text-yellow-600 text-3xl mb-3"></i>
                    <p class="text-gray-700 font-semibold mb-4">Anda harus login untuk menambahkan komentar</p>
                    <a href="/login" class="inline-block bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-3 rounded-xl font-bold hover:shadow-lg transition-all">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login Sekarang
                    </a>
                </div>
            `;
            }
        }

        function renderComments(comments) {
            const container = document.getElementById('commentsList');
            container.innerHTML = '';

            if (comments.length === 0) {
                container.innerHTML = `
                <div class="text-center py-12 bg-gray-50 rounded-xl">
                    <i class="fas fa-comments text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Komentar</h3>
                    <p class="text-gray-600">Jadilah yang pertama berkomentar pada artikel ini!</p>
                </div>
            `;
                return;
            }

            comments.forEach(comment => {
                const commentDiv = document.createElement('div');
                commentDiv.className =
                    'bg-gray-50 rounded-xl p-6 border-l-4 border-red-600 hover:shadow-md transition-shadow';
                commentDiv.innerHTML = `
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white font-bold shadow-md">
                            ${comment.user.name.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">${escapeHtml(comment.user.name)}</h4>
                            <p class="text-xs text-gray-500">${formatDate(comment.created_at)}</p>
                        </div>
                    </div>
                </div>
                <p class="text-gray-700 leading-relaxed mb-4">${escapeHtml(comment.content)}</p>

                ${comment.replies && comment.replies.length > 0 ? `
                        <div class="mt-6 space-y-4 pl-6 border-l-2 border-red-300">
                            ${comment.replies.map(reply => `
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 rounded-full bg-red-200 flex items-center justify-center text-red-700 font-bold text-sm">
                                        ${reply.user.name.charAt(0).toUpperCase()}
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-gray-800 text-sm">${escapeHtml(reply.user.name)}</h5>
                                        <p class="text-xs text-gray-500">${formatDate(reply.created_at)}</p>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-700 text-sm leading-relaxed">${escapeHtml(reply.content)}</p>
                        </div>
                    `).join('')}
                        </div>
                    ` : ''}

                <button onclick="toggleReplyForm(this, ${comment.id})" class="mt-4 text-sm text-red-600 hover:text-red-700 font-bold transition-colors">
                    <i class="fas fa-reply mr-1"></i>Balas Komentar
                </button>
            `;
                container.appendChild(commentDiv);
            });
        }

        async function submitComment(event) {
            event.preventDefault();

            const content = document.getElementById('commentContent').value;
            const btn = document.getElementById('submitCommentBtn');

            if (!content.trim()) return;

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';

            try {
                const response = await fetch(
                    `${state.apiBaseUrl}/informations/${state.informationId}/comments`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                'content') || '',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`
                        },
                        body: JSON.stringify({
                            content
                        })
                    }
                );

                if (response.ok) {
                    document.getElementById('commentContent').value = '';
                    fetchInformation(); // Refresh comments
                } else {
                    alert('Gagal mengirim komentar');
                }
            } catch (error) {
                console.error('Error submitting comment:', error);
                alert('Error: ' + error.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Kirim Komentar';
            }
        }

        function toggleReplyForm(btn, commentId) {
            console.log('Reply to comment:', commentId);
            // Implement reply form toggle
        }

        function hideLoading() {
            document.getElementById('loadingState').classList.add('hidden');
        }

        function showNotFound() {
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('articleContent').classList.add('hidden');
            document.getElementById('notFoundState').classList.remove('hidden');
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    </script>
@endpush
