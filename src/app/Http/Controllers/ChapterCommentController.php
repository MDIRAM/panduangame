<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\ChapterComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChapterCommentController extends Controller
{
    public function store(Request $request, Chapter $chapter): RedirectResponse
    {
        $chapter->loadMissing('game');

        abort_unless($chapter->game->comments_enabled, 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $chapter->comments()->create([
            'user_id' => $request->user()->id,
            'body' => trim($validated['body']),
            'is_approved' => true,
        ]);

        return redirect()
            ->to($this->chapterUrl($chapter) . '#comments')
            ->with('comment_status', 'Komentar kamu sudah ditambahkan.');
    }

    public function destroy(Request $request, ChapterComment $comment): RedirectResponse
    {
        abort_unless(
            $request->user()->id === $comment->user_id || $request->user()->hasRole('super_admin'),
            403,
        );

        $comment->delete();

        return redirect()
            ->to($this->chapterUrl($comment->chapter) . '#comments')
            ->with('comment_status', 'Komentar sudah dihapus.');
    }

    private function chapterUrl(Chapter $chapter): string
    {
        $chapter->loadMissing('game');

        if ($chapter->game->slug === 'persona-3-reload') {
            return route('persona.story.show', ['mission' => $chapter->slug]);
        }

        return route('games.walkthrough.show', [
            'gameSlug' => $chapter->game->route_slug,
            'chapterSlug' => $chapter->slug,
        ]);
    }
}
