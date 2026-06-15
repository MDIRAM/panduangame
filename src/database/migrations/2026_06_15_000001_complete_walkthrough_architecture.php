<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('route_slug')->nullable()->unique()->after('slug');
            $table->string('subtitle')->nullable()->after('description');
            $table->json('highlights')->nullable()->after('subtitle');
            $table->boolean('is_featured')->default(false)->after('cover_image');
            $table->boolean('is_published')->default(true)->after('is_featured');
        });

        DB::table('games')->orderBy('id')->get()->each(function (object $game): void {
            DB::table('games')
                ->where('id', $game->id)
                ->update([
                    'route_slug' => $game->slug === 'persona-3-reload'
                        ? 'persona-3'
                        : $game->slug,
                ]);
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->dropUnique('chapters_slug_unique');
            $table->unique(['game_id', 'slug']);
            $table->index(['game_id', 'order']);
        });

        Schema::table('steps', function (Blueprint $table) {
            $table->index(['chapter_id', 'order']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('game_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->after('game_id')
                ->constrained()
                ->nullOnDelete();
        });

        DB::table('reviews')->orderBy('id')->get()->each(function (object $review): void {
            $gameId = DB::table('games')
                ->where('title', $review->game_title)
                ->value('id');

            if ($gameId) {
                DB::table('reviews')
                    ->where('id', $review->id)
                    ->update(['game_id' => $gameId]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('game_id');
        });

        Schema::table('steps', function (Blueprint $table) {
            $table->dropIndex(['chapter_id', 'order']);
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->dropIndex(['game_id', 'order']);
            $table->dropUnique(['game_id', 'slug']);
            $table->unique('slug');
        });

        Schema::table('games', function (Blueprint $table) {
            $table->dropUnique(['route_slug']);
            $table->dropColumn([
                'route_slug',
                'subtitle',
                'highlights',
                'is_featured',
                'is_published',
            ]);
        });
    }
};
