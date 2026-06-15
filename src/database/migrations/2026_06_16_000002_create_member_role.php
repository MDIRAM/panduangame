<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->updateOrInsert(
            [
                'name' => 'member',
                'guard_name' => 'web',
            ],
            [
                'updated_at' => now(),
                'created_at' => now(),
            ],
        );
    }

    public function down(): void
    {
        DB::table('roles')
            ->where('name', 'member')
            ->where('guard_name', 'web')
            ->delete();
    }
};
