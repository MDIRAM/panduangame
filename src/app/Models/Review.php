<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_title',
        'guide_type',
        'title',
        'excerpt',
        'views',
        'rating',
    ];
}
