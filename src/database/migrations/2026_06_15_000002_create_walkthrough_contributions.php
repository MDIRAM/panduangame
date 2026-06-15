<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('walkthrough_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->string('status')->default('draft');
            $table->text('moderation_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['game_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('contribution_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('walkthrough_contribution_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->string('image_path')->nullable();
            $table->unsignedInteger('order')->default(1);
            $table->timestamps();

            $table->index(['walkthrough_contribution_id', 'order'], 'contribution_steps_order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contribution_steps');
        Schema::dropIfExists('walkthrough_contributions');
    }
};
