<?php

namespace App\Http\Controllers\Api\Information;

use App\Http\Resources\InformationCategoryResource;
use App\Models\Information\InformationCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class InformationCategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $categories = InformationCategory::query()
            ->withCount('informations')
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate($request->input('per_page', 15));

        return InformationCategoryResource::collection($categories);
    }

    /**
     * Display the specified category.
     */
    public function show(InformationCategory $category): InformationCategoryResource
    {
        $category->loadCount('informations');

        return new InformationCategoryResource($category);
    }

    /**
     * Get informations by category.
     */
    public function informations(Request $request, InformationCategory $category): AnonymousResourceCollection
    {
        $informations = $category->informations()
            ->with(['user', 'media'])
            ->published()
            ->public()
            ->latest()
            ->paginate($request->input('per_page', 15));

        return \App\Http\Resources\InformationResource::collection($informations);
    }
}
