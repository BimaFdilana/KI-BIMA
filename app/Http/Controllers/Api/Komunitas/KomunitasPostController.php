<?php

namespace App\Http\Controllers\Api\Komunitas;

use App\Http\Controllers\Controller;
use App\Models\Komunitas\KomunitasPost;
use App\Models\Komunitas\KomunitasLike;
use App\Models\Komunitas\KomunitasPostMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KomunitasPostController extends Controller
{
    /**
     * Get all posts with advanced filters and pagination
     * GET /api/komunitas/posts
     * 
     * Query Parameters:
     * - per_page (int): Items per page (default: 10)
     * - page (int): Page number (default: 1)
     * - search (string): Search in content
     * - sort_by (string): Sort field (created_at, likes_count, comments_count)
     * - sort_order (string): asc or desc (default: desc)
     * - user_id (int): Filter by user ID
     * - date_from (date): Filter by start date (Y-m-d)
     * - date_to (date): Filter by end date (Y-m-d)
     * - has_media (boolean): Filter posts with/without media
     * - min_likes (int): Minimum likes count
     * - max_likes (int): Maximum likes count
     * - trending (boolean): Get trending posts
     */
    public function getPosts(Request $request)
    {
        try {
            $validated = $request->validate([
                'per_page' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1',
                'search' => 'nullable|string|max:255',
                'sort_by' => 'nullable|in:created_at,likes_count,comments_count',
                'sort_order' => 'nullable|in:asc,desc',
                'user_id' => 'nullable|exists:users,id',
                'date_from' => 'nullable|date_format:Y-m-d',
                'date_to' => 'nullable|date_format:Y-m-d',
                'has_media' => 'nullable|boolean',
                'min_likes' => 'nullable|integer|min:0',
                'max_likes' => 'nullable|integer|min:0',
                'trending' => 'nullable|boolean',
            ]);

            $perPage = $validated['per_page'] ?? 10;
            $sortBy = $validated['sort_by'] ?? 'created_at';
            $sortOrder = $validated['sort_order'] ?? 'desc';

            $query = KomunitasPost::with(['user', 'media'])
                ->withCount([
                    'comments' => function ($q) {
                        $q->whereNull('parent_id');
                    },
                    'likes'
                ]);

            // Search filter
            if ($validated['search'] ?? null) {
                $query->where('content', 'like', "%{$validated['search']}%");
            }

            // User filter
            if ($validated['user_id'] ?? null) {
                $query->where('user_id', $validated['user_id']);
            }

            // Date range filter
            if ($validated['date_from'] ?? null) {
                $query->whereDate('created_at', '>=', $validated['date_from']);
            }
            if ($validated['date_to'] ?? null) {
                $query->whereDate('created_at', '<=', $validated['date_to']);
            }

            // Media filter
            if ($validated['has_media'] ?? null) {
                if ($validated['has_media']) {
                    $query->has('media');
                } else {
                    $query->doesntHave('media');
                }
            }

            // Likes count filter
            if ($validated['min_likes'] ?? null) {
                $query->where('likes_count', '>=', $validated['min_likes']);
            }
            if ($validated['max_likes'] ?? null) {
                $query->where('likes_count', '<=', $validated['max_likes']);
            }

            // Trending filter (posts from last 7 days with high engagement)
            if ($validated['trending'] ?? null) {
                $query->where('created_at', '>=', now()->subDays(7))
                    ->withCount('comments')
                    ->orderByRaw('(likes_count + comments_count) DESC');
            } else {
                // Normal sorting
                $query->orderBy($sortBy, $sortOrder);
            }

            $posts = $query->paginate($perPage);

            // Add like status for authenticated user
            if (Auth::check()) {
                $posts->getCollection()->transform(function ($post) {
                    $post->is_liked = KomunitasLike::where('user_id', Auth::id())
                        ->where('post_id', $post->id)
                        ->exists();
                    return $post;
                });
            }

            return $this->successResponse(
                $posts->map(fn($post) => $this->formatPost($post)),
                'Posts retrieved successfully',
                [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                    'from' => $posts->firstItem(),
                    'to' => $posts->lastItem(),
                ]
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get single post detail
     * GET /api/komunitas/posts/{id}
     */
    public function getPost($id)
    {
        try {
            $post = KomunitasPost::with([
                'user',
                'media',
                'allComments' => function ($query) {
                    $query->whereNull('parent_id')->latest();
                },
                'allComments.user',
                'allComments.replies.user',
                'allComments.likes',
                'allComments.replies.likes'
            ])
                ->withCount([
                    'comments' => function ($q) {
                        $q->whereNull('parent_id');
                    },
                    'likes'
                ])
                ->find($id);

            if (!$post) {
                return $this->errorResponse('Post tidak ditemukan', 404);
            }

            $is_liked = false;
            if (Auth::check()) {
                $is_liked = KomunitasLike::where('user_id', Auth::id())
                    ->where('post_id', $post->id)
                    ->exists();
            }

            return $this->successResponse([
                'id' => $post->id,
                'user' => [
                    'id' => $post->user->id,
                    'name' => $post->user->name,
                    'username' => $post->user->username,
                ],
                'content' => $post->content,
                'media' => $post->media->map(fn($m) => [
                    'id' => $m->id,
                    'file_path' => asset('storage/' . $m->file_path),
                    'type' => $m->type,
                ]),
                'likes_count' => $post->likes_count,
                'comments_count' => $post->comments_count,
                'is_liked' => $is_liked,
                'created_at' => $post->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $post->updated_at->format('Y-m-d H:i:s'),
            ], 'Post retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Create new post
     * POST /api/komunitas/posts
     * 
     * Body:
     * - content (required, string, 3-5000 chars)
     * - media (optional, array of files, max 5)
     */
    public function createPost(Request $request)
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string|min:3|max:5000',
                'media' => 'nullable|array|max:5',
                'media.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,webm,mov|max:50000',
            ]);

            $post = KomunitasPost::create([
                'user_id' => Auth::id(),
                'content' => $validated['content'],
            ]);

            // Handle media uploads
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $index => $file) {
                    $path = $file->store('komunitas/posts', 'public');
                    $mimeType = $file->getClientMimeType();
                    $type = str_starts_with($mimeType, 'video') ? 'video' : 'image';

                    KomunitasPostMedia::create([
                        'post_id' => $post->id,
                        'file_path' => $path,
                        'type' => $type,
                        'order' => $index,
                    ]);
                }
            }

            $post->load('user', 'media');

            return $this->successResponse(
                $this->formatPost($post),
                'Post berhasil dibuat',
                null,
                201
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update post
     * PUT /api/komunitas/posts/{id}
     */
    public function updatePost(Request $request, $id)
    {
        try {
            $post = KomunitasPost::find($id);

            if (!$post) {
                return $this->errorResponse('Post tidak ditemukan', 404);
            }

            if ($post->user_id !== Auth::id()) {
                return $this->errorResponse('Anda tidak memiliki akses untuk mengubah post ini', 403);
            }

            $validated = $request->validate([
                'content' => 'required|string|min:3|max:5000',
            ]);

            $post->update($validated);
            $post->load('user', 'media');

            return $this->successResponse(
                $this->formatPost($post),
                'Post berhasil diperbarui'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Delete post
     * DELETE /api/komunitas/posts/{id}
     */
    public function deletePost($id)
    {
        try {
            $post = KomunitasPost::find($id);

            if (!$post) {
                return $this->errorResponse('Post tidak ditemukan', 404);
            }

            if ($post->user_id !== Auth::id()) {
                return $this->errorResponse('Anda tidak memiliki akses untuk menghapus post ini', 403);
            }

            // Delete media files
            foreach ($post->media as $media) {
                if (Storage::disk('public')->exists($media->file_path)) {
                    Storage::disk('public')->delete($media->file_path);
                }
            }

            $post->delete();

            return $this->successResponse(
                null,
                'Post berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Like/Unlike post
     * POST /api/komunitas/posts/{postId}/like
     */
    public function toggleLike($postId)
    {
        try {
            $post = KomunitasPost::find($postId);

            if (!$post) {
                return $this->errorResponse('Post tidak ditemukan', 404);
            }

            $like = KomunitasLike::where('user_id', Auth::id())
                ->where('post_id', $postId)
                ->first();

            if ($like) {
                $like->delete();
                $post->decrement('likes_count');
                $action = 'unliked';
            } else {
                KomunitasLike::create([
                    'user_id' => Auth::id(),
                    'post_id' => $postId,
                ]);
                $post->increment('likes_count');
                $action = 'liked';
            }

            return $this->successResponse([
                'action' => $action,
                'likes_count' => $post->likes_count,
            ], "Post berhasil di-{$action}");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get user's own posts
     * GET /api/komunitas/posts/my-posts
     */
    public function getMyPosts(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);

            $posts = KomunitasPost::where('user_id', Auth::id())
                ->with(['user', 'media'])
                ->withCount([
                    'comments' => function ($q) {
                        $q->whereNull('parent_id');
                    },
                    'likes'
                ])
                ->latest()
                ->paginate($perPage);

            $posts->getCollection()->transform(function ($post) {
                $post->is_liked = KomunitasLike::where('user_id', Auth::id())
                    ->where('post_id', $post->id)
                    ->exists();
                return $post;
            });

            return $this->successResponse(
                $posts->map(fn($post) => $this->formatPost($post)),
                'My posts retrieved successfully',
                [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get user's liked posts
     * GET /api/komunitas/posts/liked
     */
    public function getLikedPosts(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);

            $posts = KomunitasPost::whereIn('id', function ($query) {
                $query->select('post_id')
                    ->from('komunitas_likes')
                    ->where('user_id', Auth::id());
            })
                ->with(['user', 'media'])
                ->withCount([
                    'comments' => function ($q) {
                        $q->whereNull('parent_id');
                    },
                    'likes'
                ])
                ->latest()
                ->paginate($perPage);

            $posts->getCollection()->transform(function ($post) {
                $post->is_liked = true;
                return $post;
            });

            return $this->successResponse(
                $posts->map(fn($post) => $this->formatPost($post)),
                'Liked posts retrieved successfully',
                [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Format post data for response
     */
    private function formatPost($post)
    {
        return [
            'id' => $post->id,
            'user' => [
                'id' => $post->user->id,
                'name' => $post->user->name,
                'username' => $post->user->username,
            ],
            'content' => $post->content,
            'media' => $post->media->map(fn($m) => [
                'id' => $m->id,
                'file_path' => asset('storage/' . $m->file_path),
                'type' => $m->type,
            ]),
            'likes_count' => $post->likes_count ?? 0,
            'comments_count' => $post->comments_count ?? 0,
            'is_liked' => $post->is_liked ?? false,
            'created_at' => $post->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $post->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Success response helper
     */
    private function successResponse($data = null, $message = 'Success', $pagination = null, $code = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($pagination !== null) {
            $response['pagination'] = $pagination;
        }

        return response()->json($response, $code);
    }

    /**
     * Error response helper
     */
    private function errorResponse($message, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
        ], $code);
    }
}
