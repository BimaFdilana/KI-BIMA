<?php

namespace App\Http\Controllers\Api\Komunitas;

use App\Http\Controllers\Controller;
use App\Models\Komunitas\KomunitasPost;
use App\Models\Komunitas\KomunitasComment;
use App\Models\Komunitas\KomunitasCommentLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KomunitasCommentController extends Controller
{
    /**
     * Get comments for specific post with advanced filters
     * GET /api/komunitas/posts/{postId}/comments
     * 
     * Query Parameters:
     * - per_page (int): Items per page (default: 10)
     * - page (int): Page number (default: 1)
     * - sort_by (string): Sort field (created_at, likes_count)
     * - sort_order (string): asc or desc (default: desc)
     * - user_id (int): Filter by user ID
     * - min_likes (int): Minimum likes count
     * - max_likes (int): Maximum likes count
     * - include_replies (boolean): Include nested replies (default: true)
     */
    public function getComments($postId, Request $request)
    {
        try {
            $post = KomunitasPost::find($postId);

            if (!$post) {
                return $this->errorResponse('Post tidak ditemukan', 404);
            }

            $validated = $request->validate([
                'per_page' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1',
                'sort_by' => 'nullable|in:created_at,likes_count',
                'sort_order' => 'nullable|in:asc,desc',
                'user_id' => 'nullable|exists:users,id',
                'min_likes' => 'nullable|integer|min:0',
                'max_likes' => 'nullable|integer|min:0',
                'include_replies' => 'nullable|boolean',
            ]);

            $perPage = $validated['per_page'] ?? 10;
            $sortBy = $validated['sort_by'] ?? 'created_at';
            $sortOrder = $validated['sort_order'] ?? 'desc';
            $includeReplies = $validated['include_replies'] ?? true;

            $query = KomunitasComment::where('post_id', $postId)
                ->whereNull('parent_id')
                ->with([
                    'user',
                    'likes'
                ]);

            if ($includeReplies) {
                $query->with([
                    'replies' => function ($q) {
                        $q->latest();
                    },
                    'replies.user',
                    'replies.likes'
                ]);
            }

            // User filter
            if ($validated['user_id'] ?? null) {
                $query->where('user_id', $validated['user_id']);
            }

            // Likes count filter
            if ($validated['min_likes'] ?? null) {
                $query->where('likes_count', '>=', $validated['min_likes']);
            }
            if ($validated['max_likes'] ?? null) {
                $query->where('likes_count', '<=', $validated['max_likes']);
            }

            // Sorting
            $query->orderBy($sortBy, $sortOrder);

            $comments = $query->paginate($perPage);

            return $this->successResponse(
                $comments->map(fn($comment) => $this->formatComment($comment)),
                'Comments retrieved successfully',
                [
                    'current_page' => $comments->currentPage(),
                    'last_page' => $comments->lastPage(),
                    'per_page' => $comments->perPage(),
                    'total' => $comments->total(),
                    'from' => $comments->firstItem(),
                    'to' => $comments->lastItem(),
                ]
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get single comment with all replies
     * GET /api/komunitas/comments/{commentId}
     */
    public function getComment($commentId)
    {
        try {
            $comment = KomunitasComment::with([
                'user',
                'replies' => function ($q) {
                    $q->latest();
                },
                'replies.user',
                'likes'
            ])
                ->find($commentId);

            if (!$comment) {
                return $this->errorResponse('Comment tidak ditemukan', 404);
            }

            return $this->successResponse(
                $this->formatComment($comment),
                'Comment retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Create comment or reply
     * POST /api/komunitas/posts/{postId}/comments
     * 
     * Body:
     * - content (required, string, 1-2000 chars)
     * - parent_id (optional, int): If this is a reply to another comment
     */
    public function createComment(Request $request, $postId)
    {
        try {
            $post = KomunitasPost::find($postId);

            if (!$post) {
                return $this->errorResponse('Post tidak ditemukan', 404);
            }

            $validated = $request->validate([
                'content' => 'required|string|min:1|max:2000',
                'parent_id' => 'nullable|integer|exists:komunitas_comments,id',
            ]);

            // Validate parent comment belongs to same post
            if ($validated['parent_id'] ?? null) {
                $parentComment = KomunitasComment::where('id', $validated['parent_id'])
                    ->where('post_id', $postId)
                    ->first();

                if (!$parentComment) {
                    return $this->errorResponse('Parent comment tidak ditemukan di post ini', 404);
                }
            }

            $comment = KomunitasComment::create([
                'user_id' => Auth::id(),
                'post_id' => $postId,
                'parent_id' => $validated['parent_id'] ?? null,
                'content' => $validated['content'],
            ]);

            $post->increment('comments_count');
            $comment->load('user', 'replies', 'likes');

            return $this->successResponse(
                $this->formatComment($comment),
                'Comment berhasil ditambahkan',
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
     * Update comment
     * PUT /api/komunitas/comments/{commentId}
     */
    public function updateComment(Request $request, $commentId)
    {
        try {
            $comment = KomunitasComment::find($commentId);

            if (!$comment) {
                return $this->errorResponse('Comment tidak ditemukan', 404);
            }

            if ($comment->user_id !== Auth::id()) {
                return $this->errorResponse('Anda tidak memiliki akses untuk mengubah comment ini', 403);
            }

            $validated = $request->validate([
                'content' => 'required|string|min:1|max:2000',
            ]);

            $comment->update($validated);
            $comment->load('user', 'replies', 'likes');

            return $this->successResponse(
                $this->formatComment($comment),
                'Comment berhasil diperbarui'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Delete comment (soft delete)
     * DELETE /api/komunitas/comments/{commentId}
     */
    public function deleteComment($commentId)
    {
        try {
            $comment = KomunitasComment::find($commentId);

            if (!$comment) {
                return $this->errorResponse('Comment tidak ditemukan', 404);
            }

            if ($comment->user_id !== Auth::id()) {
                return $this->errorResponse('Anda tidak memiliki akses untuk menghapus comment ini', 403);
            }

            $post = $comment->post;

            // Count child comments if this is parent comment
            $childCommentsCount = KomunitasComment::where('parent_id', $commentId)->count();

            // Decrement post comments count
            $post->decrement('comments_count', 1 + $childCommentsCount);

            $comment->delete();

            return $this->successResponse(
                null,
                'Comment berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Like/Unlike comment
     * POST /api/komunitas/comments/{commentId}/like
     */
    public function toggleLike($commentId)
    {
        try {
            $comment = KomunitasComment::find($commentId);

            if (!$comment) {
                return $this->errorResponse('Comment tidak ditemukan', 404);
            }

            $like = KomunitasCommentLike::where('user_id', Auth::id())
                ->where('comment_id', $commentId)
                ->first();

            if ($like) {
                $like->delete();
                $comment->decrement('likes_count');
                $action = 'unliked';
            } else {
                KomunitasCommentLike::create([
                    'user_id' => Auth::id(),
                    'comment_id' => $commentId,
                ]);
                $comment->increment('likes_count');
                $action = 'liked';
            }

            return $this->successResponse([
                'action' => $action,
                'likes_count' => $comment->likes_count,
            ], "Comment berhasil di-{$action}");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get replies for specific comment
     * GET /api/komunitas/comments/{commentId}/replies
     */
    public function getReplies($commentId, Request $request)
    {
        try {
            $comment = KomunitasComment::find($commentId);

            if (!$comment) {
                return $this->errorResponse('Comment tidak ditemukan', 404);
            }

            $validated = $request->validate([
                'per_page' => 'nullable|integer|min:1|max:100',
                'sort_order' => 'nullable|in:asc,desc',
            ]);

            $perPage = $validated['per_page'] ?? 10;
            $sortOrder = $validated['sort_order'] ?? 'desc';

            $replies = KomunitasComment::where('parent_id', $commentId)
                ->with(['user', 'likes'])
                ->orderBy('created_at', $sortOrder)
                ->paginate($perPage);

            return $this->successResponse(
                $replies->map(fn($reply) => $this->formatComment($reply)),
                'Replies retrieved successfully',
                [
                    'current_page' => $replies->currentPage(),
                    'last_page' => $replies->lastPage(),
                    'per_page' => $replies->perPage(),
                    'total' => $replies->total(),
                ]
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get user's comments
     * GET /api/komunitas/comments/user/{userId}
     */
    public function getUserComments($userId, Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);

            $comments = KomunitasComment::where('user_id', $userId)
                ->whereNull('parent_id')
                ->with(['user', 'post', 'likes'])
                ->latest()
                ->paginate($perPage);

            return $this->successResponse(
                $comments->map(fn($comment) => [
                    ...$this->formatComment($comment),
                    'post' => [
                        'id' => $comment->post->id,
                        'content' => substr($comment->post->content, 0, 100) . '...',
                    ]
                ]),
                'User comments retrieved successfully',
                [
                    'current_page' => $comments->currentPage(),
                    'last_page' => $comments->lastPage(),
                    'per_page' => $comments->perPage(),
                    'total' => $comments->total(),
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get liked comments by user
     * GET /api/komunitas/comments/liked
     */
    public function getLikedComments(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);

            $comments = KomunitasComment::whereIn('id', function ($query) {
                $query->select('comment_id')
                    ->from('komunitas_comment_likes')
                    ->where('user_id', Auth::id());
            })
                ->with(['user', 'post', 'likes'])
                ->latest()
                ->paginate($perPage);

            return $this->successResponse(
                $comments->map(fn($comment) => $this->formatComment($comment)),
                'Liked comments retrieved successfully',
                [
                    'current_page' => $comments->currentPage(),
                    'last_page' => $comments->lastPage(),
                    'per_page' => $comments->perPage(),
                    'total' => $comments->total(),
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Format comment data for response
     */
    private function formatComment($comment)
    {
        $is_liked = false;
        if (Auth::check()) {
            $is_liked = KomunitasCommentLike::where('user_id', Auth::id())
                ->where('comment_id', $comment->id)
                ->exists();
        }

        return [
            'id' => $comment->id,
            'post_id' => $comment->post_id,
            'parent_id' => $comment->parent_id,
            'user' => [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
                'username' => $comment->user->username,
            ],
            'content' => $comment->content,
            'likes_count' => $comment->likes_count ?? 0,
            'is_liked' => $is_liked,
            'replies_count' => $comment->replies ? $comment->replies->count() : 0,
            'replies' => $comment->replies ? $comment->replies->map(fn($reply) => $this->formatComment($reply)) : [],
            'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $comment->updated_at->format('Y-m-d H:i:s'),
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
