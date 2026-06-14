<?php

namespace App\Http\Controllers\Api\Information;

use App\Http\Resources\InformationResource;
use App\Models\Information\Information;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Jenssegers\Agent\Agent;

class InformationController extends Controller
{
    /**
     * Display all information with smart search and pagination
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Information::query()
            ->with(['user', 'category', 'media', 'comments'])
            ->published()
            ->public();

        // Filter by category
        if ($request->filled('category_id')) {
            $query->byCategory($request->input('category_id'));
        }

        // Smart search functionality
        if ($request->filled('search')) {
            $query = $this->smartSearch($query, $request->input('search'));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $allowedSorts = ['created_at', 'updated_at', 'title'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $perPage = min($request->input('per_page', 15), 100); // Max 100 per page
        $informations = $query->paginate($perPage);

        return InformationResource::collection($informations);
    }

    /**
     * Smart search across multiple fields and relations
     */
    private function smartSearch($query, $searchTerm)
    {
        $searchTerm = trim($searchTerm);

        return $query->where(function ($q) use ($searchTerm) {
            // Search dalam fields utama information
            $q->where('title', 'like', "%{$searchTerm}%")
                ->orWhere('description', 'like', "%{$searchTerm}%")
                ->orWhere('content', 'like', "%{$searchTerm}%")

                // Search dalam category name
                ->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
                    $categoryQuery->where('name', 'like', "%{$searchTerm}%");
                })

                // Search dalam user (author)
                ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                    $userQuery->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('email', 'like', "%{$searchTerm}%");
                })

                // Search dalam comments
                ->orWhereHas('comments', function ($commentQuery) use ($searchTerm) {
                    $commentQuery->where('content', 'like', "%{$searchTerm}%");
                });
        });
    }

    /**
     * Advanced search dengan filter lebih detail
     */
    public function advancedSearch(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:information_categories,id',
            'author_id' => 'nullable|exists:users,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'has_comments' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Information::query()
            ->with(['user', 'category', 'media', 'comments'])
            ->published()
            ->public();

        // Search term
        if ($request->filled('search')) {
            $query = $this->smartSearch($query, $request->input('search'));
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->byCategory($request->input('category_id'));
        }

        // Filter by author
        if ($request->filled('author_id')) {
            $query->where('user_id', $request->input('author_id'));
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Filter informasi dengan comments
        if ($request->filled('has_comments') && $request->boolean('has_comments')) {
            $query->has('comments');
        }

        $perPage = min($request->input('per_page', 15), 100);
        $informations = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return InformationResource::collection($informations);
    }

    /**
     * Search suggestions/autocomplete
     */
    public function searchSuggestions(Request $request)
    {
        $searchTerm = trim($request->input('search', ''));

        if (strlen($searchTerm) < 2) {
            return response()->json([
                'suggestions' => []
            ]);
        }

        $titles = Information::query()
            ->select('title')
            ->where('title', 'like', "%{$searchTerm}%")
            ->published()
            ->public()
            ->distinct()
            ->limit(5)
            ->pluck('title');

        $categories = Information::query()
            ->select('information_categories.name')
            ->join('information_categories', 'informations.category_id', '=', 'information_categories.id')
            ->where('information_categories.name', 'like', "%{$searchTerm}%")
            ->published()
            ->public()
            ->distinct()
            ->limit(5)
            ->pluck('name');

        $authors = Information::query()
            ->select('users.name')
            ->join('users', 'informations.user_id', '=', 'users.id')
            ->where('users.name', 'like', "%{$searchTerm}%")
            ->published()
            ->public()
            ->distinct()
            ->limit(5)
            ->pluck('name');

        return response()->json([
            'suggestions' => [
                'titles' => $titles,
                'categories' => $categories,
                'authors' => $authors,
            ]
        ]);
    }

    public function store(Request $request): InformationResource
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'category_id' => 'nullable|exists:information_categories,id',
            'visibility' => ['required', Rule::in(['public', 'private'])],
            'is_published' => 'boolean',
        ]);

        $information = Auth::user()->informations()->create($validated);
        $information->load(['user', 'category', 'media']);

        // Broadcast event for realtime updates
        event(new \App\Events\Information\InformationCreated($information));

        return new InformationResource($information);
    }

    public function show(Information $information): InformationResource
    {
        // Generate deterministic device ID based on device characteristics
        if (!session()->has('device_id')) {
            $agent = new Agent();
            $deviceId = md5($agent->device() . $agent->platform() . request()->ip());
            session(['device_id' => $deviceId]);
        }

        if (
            $information->visibility === 'private' &&
            (!Auth::check() || Auth::id() !== $information->user_id)
        ) {
            abort(403, 'This information is private.');
        }

        $information->load(['user', 'category', 'media', 'comments.user', 'comments.replies.user']);

        return new InformationResource($information);
    }

    public function update(Request $request, Information $information): InformationResource
    {
        if (Auth::id() !== $information->user_id) {
            abort(403, 'You are not authorized to update this information.');
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'content' => 'nullable|string',
            'category_id' => 'nullable|exists:information_categories,id',
            'visibility' => ['sometimes', 'required', Rule::in(['public', 'private'])],
            'is_published' => 'boolean',
        ]);

        $information->update($validated);
        $information->load(['user', 'category', 'media']);

        return new InformationResource($information);
    }

    public function incrementShareCount(Information $information): InformationResource
    {
        $information->increment('shares_count');

        return new InformationResource($information);
    }

    public function destroy(Information $information)
    {
        if (Auth::id() !== $information->user_id) {
            abort(403, 'You are not authorized to delete this information.');
        }

        $information->delete();

        return response()->json([
            'message' => 'Information deleted successfully.'
        ]);
    }

    public function trending(Request $request): AnonymousResourceCollection
    {
        $perPage = min($request->input('per_page', 10), 50);

        $informations = Information::query()
            ->with(['user', 'category', 'media'])
            ->published()
            ->public()
            ->withCount('comments')
            ->orderByDesc('comments_count')
            ->paginate($perPage);

        return InformationResource::collection($informations);
    }

    public function myInformations(Request $request): AnonymousResourceCollection
    {
        $perPage = min($request->input('per_page', 15), 100);

        $informations = Auth::user()
            ->informations()
            ->with(['category', 'media', 'comments'])
            ->latest()
            ->paginate($perPage);

        return InformationResource::collection($informations);
    }
}
