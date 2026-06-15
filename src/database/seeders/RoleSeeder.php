<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [];

        foreach ([
            'game',
            'chapter',
            'step',
            'contribution',
            'contribution_step',
            'user',
            'role',
            'activity',
        ] as $resource) {
            foreach (['view', 'view_any', 'create', 'update', 'delete', 'delete_any'] as $action) {
                $permissions[] = Permission::firstOrCreate([
                    'name' => $action.'_'.$resource,
                    'guard_name' => 'web',
                ]);
            }
        }

        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);
        $superAdmin->syncPermissions($permissions);
        $superAdmin->givePermissionTo(
            Permission::firstOrCreate(['name' => 'widget_OverlookWidget', 'guard_name' => 'web']),
            Permission::firstOrCreate(['name' => 'widget_AdminOverviewStats', 'guard_name' => 'web']),
            Permission::firstOrCreate(['name' => 'widget_LatestAccessLogs', 'guard_name' => 'web']),
        );

        Role::firstOrCreate([
            'name' => 'contributor',
            'guard_name' => 'web',
        ]);

        Role::firstOrCreate([
            'name' => 'member',
            'guard_name' => 'web',
        ]);

        Permission::query()
            ->where('guard_name', 'web')
            ->where('name', 'like', '%_review')
            ->delete();
    }
}
