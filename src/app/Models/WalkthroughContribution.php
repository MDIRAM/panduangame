<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class WalkthroughContribution extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING = 'pending';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'game_id',
        'title',
        'slug',
        'summary',
        'status',
        'moderation_notes',
        'submitted_at',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (WalkthroughContribution $contribution): void {
            if ($contribution->status === self::STATUS_PUBLISHED) {
                $contribution->published_at ??= now();
            } else {
                $contribution->published_at = null;
            }
        });

        static::deleting(function (WalkthroughContribution $contribution): void {
            $contribution->steps()
                ->whereNotNull('image_path')
                ->pluck('image_path')
                ->each(fn (string $path) => Storage::disk('public')->delete($path));
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(ContributionStep::class)->orderBy('order');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function isEditableByAuthor(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_REJECTED,
        ], true);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending review',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }
}
