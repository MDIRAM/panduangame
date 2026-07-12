<?php

namespace App\Livewire;

use App\Models\Chapter;
use App\Models\ChapterComment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChapterComments extends Component
{
    public Chapter $chapter;

    public string $body = '';

    public function mount(Chapter $chapter): void
    {
        $this->chapter = $chapter->loadMissing('game');
    }

    public function storeComment(): void
    {
        abort_unless(Auth::check(), 403);

        $this->chapter->loadMissing('game');
        abort_unless($this->chapter->game->comments_enabled, 403);

        $validated = $this->validate([
            'body' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $this->chapter->comments()->create([
            'user_id' => Auth::id(),
            'body' => trim($validated['body']),
            'is_approved' => true,
        ]);

        $this->reset('body');
        session()->flash('comment_status', 'Komentar kamu sudah ditambahkan.');
    }

    public function deleteComment(int $commentId): void
    {
        $comment = ChapterComment::query()
            ->where('chapter_id', $this->chapter->id)
            ->findOrFail($commentId);

        $user = Auth::user();

        abort_unless(
            $user && ($user->id === $comment->user_id || $user->hasRole('super_admin')),
            403,
        );

        $comment->delete();
        session()->flash('comment_status', 'Komentar sudah dihapus.');
    }

    public function render()
    {
        return view('livewire.chapter-comments', [
            'comments' => $this->chapter->comments()
                ->where('is_approved', true)
                ->with('user')
                ->latest()
                ->get(),
        ]);
    }
}
