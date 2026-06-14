<?php

namespace App\Http\Controllers\Api\Information;

use App\Http\Resources\InformationCommentResource;
use App\Models\Information\Information;
use App\Models\Information\InformationComment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InformationCommentController extends Controller
{
    /**
     * Display comments for an information.
     */
    public function index(Request $request, Information $information): AnonymousResourceCollection
    {
        $comments = $information->comments()
            ->with(['user', 'replies.user'])
            ->parents()
            ->latest()
            ->paginate($request->input('per_page', 20));

        return InformationCommentResource::collection($comments);
    }

    /**
     * Store a new comment.
     */
    public function store(Request $request, Information $information): InformationCommentResource
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:information_comments,id',
        ]);

        // Verify parent comment belongs to same information if provided
        if ($validated['parent_id'] ?? null) {
            $parentComment = InformationComment::findOrFail($validated['parent_id']);
            if ($parentComment->information_id !== $information->id) {
                abort(422, 'Parent comment does not belong to this information.');
            }
        }

        $comment = DB::transaction(function () use ($information, $validated) {
            $comment = $information->comments()->create([
                'user_id' => Auth::id(),
                'device_id' => session('device_id'),
                'content' => $validated['content'],
                'parent_id' => $validated['parent_id'] ?? null,
            ]);


            // Update parent comment replies count if this is a reply
            if ($comment->parent_id) {
                InformationComment::where('id', $comment->parent_id)
                    ->increment('replies_count');
            }

            return $comment;
        });

        $comment->load('user');

        // Broadcast event for realtime updates
        event(new \App\Events\Information\CommentCreated($comment, $information->id));

        return new InformationCommentResource($comment);
    }

    /**
     * Update a comment.
     */
    public function update(Request $request, InformationComment $comment): InformationCommentResource
    {
        $isOwner = false;
        if (Auth::check() && Auth::id() === $comment->user_id) {
            $isOwner = true;
        } elseif (!$comment->user_id && $comment->device_id === session('device_id')) {
            $isOwner = true;
        }

        if (!$isOwner) {
            abort(403, 'You are not authorized to update this comment.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update($validated);
        $comment->load('user');

        return new InformationCommentResource($comment);
    }

    /**
     * Delete a comment.
     */
    public function destroy(InformationComment $comment)
    {
        // Authorization
        // Authorization
        $isOwner = false;
        if (Auth::check() && Auth::id() === $comment->user_id) {
            $isOwner = true;
        } elseif (!$comment->user_id && $comment->device_id === session('device_id')) {
            $isOwner = true;
        }

        if (!$isOwner) {
            abort(403, 'You are not authorized to delete this comment.');
        }

        DB::transaction(function () use ($comment) {
            // Decrement parent replies count if this is a reply
            if ($comment->parent_id) {
                InformationComment::where('id', $comment->parent_id)
                    ->decrement('replies_count');
            }

            $comment->delete();
        });

        return response()->json([
            'message' => 'Comment deleted successfully.'
        ]);
    }

    /**
     * Get replies for a comment.
     */
    public function replies(Request $request, InformationComment $comment): AnonymousResourceCollection
    {
        $replies = $comment->replies()
            ->with('user')
            ->latest()
            ->paginate($request->input('per_page', 10));

        return InformationCommentResource::collection($replies);
    }
}
