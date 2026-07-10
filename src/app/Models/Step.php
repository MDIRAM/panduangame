<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Step extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'step_title',
        'content',
        'image_url',
        'order',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function getResolvedImageUrlAttribute(): ?string
    {
        if (! $this->image_url) {
            return null;
        }

        if (str_starts_with($this->image_url, 'http')) {
            return $this->image_url;
        }

        if (is_file(public_path($this->image_url))) {
            return asset($this->image_url);
        }

        return Storage::disk('public')->url($this->image_url);
    }
}
