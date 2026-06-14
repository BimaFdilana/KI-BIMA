<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InformationMediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'media_url' => $this->media_url,
            'thumbnail_url' => $this->thumbnail_url,
            'alt_text' => $this->alt_text,
            'order' => $this->order,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
