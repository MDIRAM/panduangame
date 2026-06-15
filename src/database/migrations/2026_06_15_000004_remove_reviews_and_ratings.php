<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('reviews');

        DB::table('permissions')
            ->where('guard_name', 'web')
            ->where('name', 'like', '%_review')
            ->delete();
    }

    public function down(): void
    {
        // Review dan rating sengaja tidak dibuat kembali.
    }
};
