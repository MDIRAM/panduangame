<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivateContributionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'status' => $this->status,
            'moderation_notes' => $this->moderation_notes,
            'game' => $this->whenLoaded('game', fn () => [
                'id' => $this->game->id,
                'title' => $this->game->title,
                'slug' => $this->game->route_slug,
            ]),
            'chapter' => $this->whenLoaded('chapter', fn () => [
                'id' => $this->chapter->id,
                'title' => $this->chapter->chapter_title,
                'slug' => $this->chapter->slug,
            ]),
            'steps_count' => $this->whenCounted('steps'),
            'steps' => ContributionStepResource::collection($this->whenLoaded('steps')),
            'submitted_at' => $this->submitted_at?->toISOString(),
            'published_at' => $this->published_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
