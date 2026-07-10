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
            $table->string('theme_preset', 32)->default('blue')->after('is_published');
        });

        DB::table('games')
            ->whereIn('slug', ['elden-ring', 'dark-souls-2'])
            ->update(['theme_preset' => 'gold']);

        DB::table('games')
            ->where('slug', 'persona-3-reload')
            ->update(['theme_preset' => 'blue']);
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('theme_preset');
        });
    }
};
