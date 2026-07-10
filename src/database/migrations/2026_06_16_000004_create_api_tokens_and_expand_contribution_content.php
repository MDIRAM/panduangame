<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name')->default('api-token');
            $table->string('token', 64)->unique();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'expires_at']);
        });

        Schema::table('contribution_steps', function (Blueprint $table) {
            $table->longText('content')->change();
        });
    }

    public function down(): void
    {
        Schema::table('contribution_steps', function (Blueprint $table) {
            $table->text('content')->change();
        });

        Schema::dropIfExists('api_tokens');
    }
};
