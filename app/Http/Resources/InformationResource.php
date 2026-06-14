<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InformationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->when($request->routeIs('api.informations.show'), $this->content),
            'visibility' => $this->visibility,
            'is_published' => $this->is_published,
            'shares_count' => $this->shares_count,

            'user' => new UserResource($this->whenLoaded('user')),
            'category' => new InformationCategoryResource($this->whenLoaded('category')),
            'media' => InformationMediaResource::collection($this->whenLoaded('media')),
            'comments' => InformationCommentResource::collection($this->whenLoaded('comments')),
            'comments_count' => $this->whenLoaded('comments', function () {
                return $this->comments->count();
            }, 0),
            'comments_count' => $this->comments_count,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
