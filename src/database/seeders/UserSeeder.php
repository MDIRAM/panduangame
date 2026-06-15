<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminPassword = config('app.admin_password');

        if (app()->environment('production') && blank($adminPassword)) {
            throw new RuntimeException(
                'ADMIN_PASSWORD wajib diisi sebelum menjalankan seeder di production.',
            );
        }

        $user = User::updateOrCreate(
            ['email' => config('app.admin_email')],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($adminPassword ?: 'password'),
            ],
        );
        $user->syncRoles(['super_admin']);

        if (! app()->environment('production')) {
            $user = User::query()
                ->whereIn('email', ['contributor@admin.com', 'user@admin.com'])
                ->first() ?? new User();

            $user->fill([
                'name' => 'Contributor User',
                'email' => 'contributor@admin.com',
                'password' => Hash::make('password'),
            ])->save();

            $user->syncRoles(['contributor']);

            $member = User::updateOrCreate(
                ['email' => 'member@admin.com'],
                [
                    'name' => 'Member User',
                    'password' => Hash::make('password'),
                ],
            );

            $member->syncRoles(['member']);
        }
    }
}
