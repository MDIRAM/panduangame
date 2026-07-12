<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'route_slug',
        'description',
        'subtitle',
        'highlights',
        'cover_image',
        'is_featured',
        'is_published',
        'theme_preset',
        'content_status',
        'comments_enabled',
    ];

    protected function casts(): array
    {
        return [
            'highlights' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'comments_enabled' => 'boolean',
        ];
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    public function walkthroughContributions(): HasMany
    {
        return $this->hasMany(WalkthroughContribution::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(GameFavorite::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(GameRating::class);
    }

    public static function contentStatuses(): array
    {
        return [
            'complete' => 'Complete',
            'ongoing' => 'Ongoing',
            'upcoming' => 'Upcoming',
        ];
    }

    public function getContentStatusLabelAttribute(): string
    {
        return self::contentStatuses()[$this->content_status] ?? 'Ongoing';
    }

    public function getCoverUrlAttribute(): ?string
    {
        if (! $this->cover_image) {
            return null;
        }

        if (str_starts_with($this->cover_image, 'http')) {
            return $this->cover_image;
        }

        if (is_file(public_path($this->cover_image))) {
            return asset($this->cover_image);
        }

        return Storage::disk('public')->url($this->cover_image);
    }

    public function getThemeAttribute(): array
    {
        return match ($this->theme_preset) {
            'gold' => [
                'class' => 'theme-gold',
                'accent' => '#d9b45b',
                'accent_soft' => '#27271f',
                'background' => '#090b0b',
                'background_glow' => 'rgba(127, 104, 49, 0.2)',
                'border' => '#403d32',
            ],
            'red' => [
                'class' => 'theme-red',
                'accent' => '#fb7185',
                'accent_soft' => '#3a1822',
                'background' => '#0c0b12',
                'background_glow' => 'rgba(185, 28, 28, 0.2)',
                'border' => '#4b2b35',
            ],
            'green' => [
                'class' => 'theme-green',
                'accent' => '#34d399',
                'accent_soft' => '#142d24',
                'background' => '#07110d',
                'background_glow' => 'rgba(16, 185, 129, 0.16)',
                'border' => '#26483d',
            ],
            'neutral' => [
                'class' => 'theme-neutral',
                'accent' => '#cbd5e1',
                'accent_soft' => '#202936',
                'background' => '#090d14',
                'background_glow' => 'rgba(148, 163, 184, 0.13)',
                'border' => '#334155',
            ],
            default => [
                'class' => 'theme-blue',
                'accent' => '#38bdf8',
                'accent_soft' => '#1c2d46',
                'background' => '#080d18',
                'background_glow' => 'rgba(24, 55, 91, 0.28)',
                'border' => '#27364b',
            ],
        };
    }
}
