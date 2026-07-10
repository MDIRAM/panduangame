<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContributionRequest;
use App\Http\Requests\UpdateContributionRequest;
use App\Models\Chapter;
use App\Models\Game;
use App\Models\WalkthroughContribution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContributionController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', WalkthroughContribution::class);

        return view('contributions.index', [
            'contributions' => auth()->user()
                ->walkthroughContributions()
                ->with(['chapter', 'game'])
                ->withCount('steps')
                ->latest()
                ->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', WalkthroughContribution::class);

        return view('contributions.create', [
            'chapters' => $this->publishedGameChapters(),
            'games' => $this->publishedGames(),
            'selectedChapterId' => $request->integer('chapter_id') ?: null,
            'selectedGameId' => $request->integer('game_id') ?: null,
        ]);
    }

    public function store(StoreContributionRequest $request): RedirectResponse
    {
        $contribution = $request->user()->walkthroughContributions()->create([
            ...$request->validated(),
            'slug' => $this->uniqueSlug($request->string('title')->toString()),
            'status' => WalkthroughContribution::STATUS_DRAFT,
        ]);

        return redirect()
            ->route('contributions.edit', $contribution)
            ->with('success', 'Draft walkthrough dibuat. Sekarang tambahkan langkah panduannya.');
    }

    public function edit(WalkthroughContribution $contribution): View
    {
        $this->authorize('view', $contribution);

        return view('contributions.edit', [
            'contribution' => $contribution->load(['chapter', 'game', 'steps']),
            'chapters' => $this->publishedGameChapters(),
            'games' => $this->publishedGames(),
        ]);
    }

    public function update(
        UpdateContributionRequest $request,
        WalkthroughContribution $contribution,
    ): RedirectResponse {
        $contribution->update([
            ...$request->validated(),
            'status' => WalkthroughContribution::STATUS_DRAFT,
            'moderation_notes' => null,
            'submitted_at' => null,
        ]);

        return back()->with('success', 'Informasi walkthrough berhasil diperbarui.');
    }

    public function destroy(WalkthroughContribution $contribution): RedirectResponse
    {
        $this->authorize('delete', $contribution);

        $contribution->delete();

        return redirect()
            ->route('contributions.index')
            ->with('success', 'Draft walkthrough dihapus.');
    }

    public function submit(WalkthroughContribution $contribution): RedirectResponse
    {
        $this->authorize('submit', $contribution);

        if (! $contribution->steps()->exists()) {
            return back()->withErrors([
                'submit' => 'Tambahkan minimal satu langkah sebelum mengirim walkthrough.',
            ]);
        }

        $contribution->update([
            'status' => WalkthroughContribution::STATUS_PENDING,
            'submitted_at' => now(),
            'moderation_notes' => null,
        ]);

        return redirect()
            ->route('contributions.index')
            ->with('success', 'Walkthrough dikirim ke admin untuk direview.');
    }

    private function publishedGames()
    {
        return Game::query()
            ->where('is_published', true)
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    private function publishedGameChapters()
    {
        return Chapter::query()
            ->whereHas('game', fn ($query) => $query->where('is_published', true))
            ->with('game:id,title')
            ->orderBy('game_id')
            ->orderBy('order')
            ->get(['id', 'game_id', 'parent_id', 'chapter_title', 'order']);
    }

    private function uniqueSlug(string $title): string
    {
        do {
            $slug = Str::slug($title) . '-' . Str::lower(Str::random(6));
        } while (WalkthroughContribution::where('slug', $slug)->exists());

        return $slug;
    }
}
