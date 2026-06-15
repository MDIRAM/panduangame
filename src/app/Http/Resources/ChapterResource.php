<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChapterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'game' => new GameResource($this->whenLoaded('game')),
            'title' => $this->chapter_title,
            'slug' => $this->slug,
            'section_title' => $this->section_title,
            'overview' => $this->overview ?? [],
            'overview_image' => $this->overview_image,
            'cover_image' => $this->cover_image,
            'source_url' => $this->source_url,
            'order' => $this->order,
            'steps_count' => $this->whenCounted('steps'),
            'steps' => StepResource::collection($this->whenLoaded('steps')),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
