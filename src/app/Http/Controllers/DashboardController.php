<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Game;
use App\Models\Step;
use App\Models\WalkthroughContribution;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'gameCount' => Game::where('is_published', true)->count(),
            'chapterCount' => Chapter::count(),
            'stepCount' => Step::count(),
            'contributionCount' => auth()->user()->walkthroughContributions()->count(),
            'pendingContributionCount' => auth()->user()
                ->walkthroughContributions()
                ->where('status', WalkthroughContribution::STATUS_PENDING)
                ->count(),
            'latestChapters' => Chapter::query()
                ->with('game')
                ->withCount('steps')
                ->latest('updated_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
