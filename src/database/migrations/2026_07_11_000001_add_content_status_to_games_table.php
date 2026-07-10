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
            $table->string('content_status', 32)->default('ongoing')->after('theme_preset');
        });

        DB::table('games')
            ->where('slug', 'persona-3-reload')
            ->update(['content_status' => 'complete']);

        DB::table('games')
            ->whereIn('slug', ['elden-ring', 'dark-souls-2'])
            ->update(['content_status' => 'ongoing']);
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('content_status');
        });
    }
};
