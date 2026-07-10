<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommunityGuideResource;
use App\Models\Game;
use App\Models\WalkthroughContribution;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommunityGuideController extends Controller
{
    public function index(Game $game): AnonymousResourceCollection
    {
        abort_unless($game->is_published, 404);

        $guides = $game->walkthroughContributions()
            ->published()
            ->with(['author', 'chapter'])
            ->withCount('steps')
            ->latest('published_at')
            ->get();

        return CommunityGuideResource::collection($guides);
    }

    public function show(WalkthroughContribution $contribution): CommunityGuideResource
    {
        abort_unless(
            $contribution->status === WalkthroughContribution::STATUS_PUBLISHED,
            404,
        );

        return new CommunityGuideResource(
            $contribution->load(['author', 'chapter', 'game', 'steps'])->loadCount('steps'),
        );
    }
}
