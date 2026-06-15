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
    ];

    protected function casts(): array
    {
        return [
            'highlights' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
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
}
