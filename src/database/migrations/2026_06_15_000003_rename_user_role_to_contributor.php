<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $legacyRole = DB::table('roles')
            ->where('name', 'user')
            ->where('guard_name', 'web')
            ->first();

        $contributorRole = DB::table('roles')
            ->where('name', 'contributor')
            ->where('guard_name', 'web')
            ->first();

        if ($legacyRole && ! $contributorRole) {
            DB::table('roles')
                ->where('id', $legacyRole->id)
                ->update(['name' => 'contributor']);
        }

        DB::table('users')
            ->where('email', 'user@admin.com')
            ->update([
                'name' => 'Contributor User',
                'email' => 'contributor@admin.com',
            ]);
    }

    public function down(): void
    {
        DB::table('roles')
            ->where('name', 'contributor')
            ->where('guard_name', 'web')
            ->update(['name' => 'user']);

        DB::table('users')
            ->where('email', 'contributor@admin.com')
            ->update([
                'name' => 'User Account',
                'email' => 'user@admin.com',
            ]);
    }
};
