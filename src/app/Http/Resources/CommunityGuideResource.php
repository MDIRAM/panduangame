<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommunityGuideResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'author' => $this->whenLoaded('author', fn () => [
                'id' => $this->author->id,
                'name' => $this->author->name,
            ]),
            'game' => $this->whenLoaded('game', fn () => [
                'title' => $this->game->title,
                'slug' => $this->game->route_slug,
            ]),
            'steps_count' => $this->whenCounted('steps'),
            'steps' => ContributionStepResource::collection($this->whenLoaded('steps')),
            'published_at' => $this->published_at?->toISOString(),
        ];
    }
}
