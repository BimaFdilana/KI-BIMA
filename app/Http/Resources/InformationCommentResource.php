<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InformationCommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'replies_count' => $this->replies_count,
            'is_reply' => $this->isReply(),
            'is_owner' => $this->user_id
                ? ($request->user() && $request->user()->id === $this->user_id)
                : ($this->device_id === session('device_id')),

            'user' => new UserResource($this->whenLoaded('user')),
            'parent' => new InformationCommentResource($this->whenLoaded('parent')),
            'replies' => InformationCommentResource::collection($this->whenLoaded('replies')),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
