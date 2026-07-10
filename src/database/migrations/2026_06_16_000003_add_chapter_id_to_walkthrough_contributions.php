<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('walkthrough_contributions', function (Blueprint $table) {
            $table->foreignId('chapter_id')
                ->nullable()
                ->after('game_id')
                ->constrained()
                ->nullOnDelete();

            $table->index(['chapter_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('walkthrough_contributions', function (Blueprint $table) {
            $table->dropIndex(['chapter_id', 'status']);
            $table->dropConstrainedForeignId('chapter_id');
        });
    }
};
