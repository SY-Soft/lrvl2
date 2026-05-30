<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'tickets.view-own',
            'tickets.view-assigned',
            'tickets.view-all',
            'tickets.create',
            'tickets.comment',
            'tickets.change-status',
            'tickets.assign',
            'tickets.manage',
            'users.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        Role::findOrCreate('user')->syncPermissions([
            'tickets.view-own',
            'tickets.view-assigned',
            'tickets.create',
            'tickets.comment',
        ]);

        Role::findOrCreate('support')->syncPermissions([
            'tickets.view-assigned',
            'tickets.comment',
            'tickets.change-status',
        ]);

        Role::findOrCreate('manager')->syncPermissions($permissions);
        Role::findOrCreate('admin')->syncPermissions($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
