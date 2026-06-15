<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ContributionStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'walkthrough_contribution_id',
        'title',
        'content',
        'image_path',
        'order',
    ];

    public function contribution(): BelongsTo
    {
        return $this->belongsTo(WalkthroughContribution::class, 'walkthrough_contribution_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : null;
    }
}
