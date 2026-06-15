<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->route_slug,
            'description' => $this->description,
            'subtitle' => $this->subtitle,
            'highlights' => $this->highlights ?? [],
            'cover_image' => $this->cover_image,
            'cover_url' => $this->cover_url,
            'is_featured' => $this->is_featured,
            'chapters_count' => $this->whenCounted('chapters'),
            'chapters' => ChapterResource::collection($this->whenLoaded('chapters')),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
