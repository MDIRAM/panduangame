<div wire:poll.3s>
    @if ($chapter->game->comments_enabled)
        <section class="comments-section" id="comments" aria-label="Comments for {{ $chapter->chapter_title }}">
            <div class="comments-header">
                <div>
                    <p class="comments-kicker">Discussion</p>
                    <h2>Comments</h2>
                </div>
                <span>{{ $comments->count() }} comment{{ $comments->count() === 1 ? '' : 's' }}</span>
            </div>

            @if (session('comment_status'))
                <p class="comment-status" role="status">{{ session('comment_status') }}</p>
            @endif

            @auth
                <form wire:submit.prevent="storeComment" class="comment-form">
                    <label for="comment-body">Join the discussion</label>
                    <textarea
                        id="comment-body"
                        wire:model.defer="body"
                        rows="4"
                        maxlength="2000"
                        placeholder="Tulis komentar atau catatan tentang halaman walkthrough ini..."
                        required
                    ></textarea>
                    @error('body')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                    <button type="submit" wire:loading.attr="disabled" wire:target="storeComment">
                        <span wire:loading.remove wire:target="storeComment">Submit comment</span>
                        <span wire:loading wire:target="storeComment">Sending...</span>
                    </button>
                </form>
            @else
                <div class="comment-login-card">
                    <div>
                        <h3>Login to join the discussion</h3>
                        <p>Guest bisa membaca komentar. Login dulu kalau mau ikut menulis komentar di halaman ini.</p>
                    </div>
                    <div class="comment-login-actions">
                        <a href="{{ route('login') }}">Login to comment</a>
                        <a href="{{ route('register') }}">Create account</a>
                    </div>
                </div>
            @endauth

            <div class="comment-list">
                @forelse ($comments as $comment)
                    <article class="comment-card" wire:key="comment-{{ $comment->id }}">
                        <div class="comment-avatar" aria-hidden="true">
                            @if ($comment->user?->avatar_url)
                                <img src="{{ asset('storage/' . $comment->user->avatar_url) }}" alt="">
                            @else
                                {{ strtoupper(\Illuminate\Support\Str::substr($comment->user?->name ?? 'U', 0, 1)) }}
                            @endif
                        </div>
                        <div class="comment-body">
                            <div class="comment-meta">
                                <div>
                                    <strong>{{ $comment->user?->name ?? 'Unknown user' }}</strong>
                                    <time datetime="{{ $comment->created_at->toIso8601String() }}">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </time>
                                </div>
                                @auth
                                    @if (auth()->id() === $comment->user_id || auth()->user()->hasRole('super_admin'))
                                        <button
                                            type="button"
                                            class="comment-delete-form"
                                            wire:click="deleteComment({{ $comment->id }})"
                                            wire:confirm="Delete this comment?"
                                            aria-label="Delete comment"
                                        >
                                            Delete
                                        </button>
                                    @endif
                                @endauth
                            </div>
                            <p>{{ $comment->body }}</p>
                        </div>
                    </article>
                @empty
                    <div class="comments-empty">
                        <h3>Belum ada komentar</h3>
                        <p>Jadilah yang pertama membahas halaman walkthrough ini.</p>
                    </div>
                @endforelse
            </div>
        </section>
    @endif
</div>
