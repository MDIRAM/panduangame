<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'parent_id',
        'chapter_title',
        'section_title',
        'overview',
        'overview_image',
        'cover_image',
        'slug',
        'source_url',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'overview' => 'array',
        ];
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Chapter::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Chapter::class, 'parent_id')->orderBy('order');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class)->orderBy('order');
    }

    public function walkthroughContributions(): HasMany
    {
        return $this->hasMany(WalkthroughContribution::class);
    }

    public function getOverviewImageUrlAttribute(): ?string
    {
        return $this->resolveImageUrl($this->overview_image);
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->resolveImageUrl($this->cover_image);
    }

    private function resolveImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        if (is_file(public_path($path))) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }
}
