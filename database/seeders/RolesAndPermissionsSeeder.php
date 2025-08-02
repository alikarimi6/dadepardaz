<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void

    {
        foreach (config('roles.permissions') as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        foreach (config('roles.roles') as $roleName => $permissions) {
            $role = Role::query()->firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions);
        }

        foreach (config('roles.users') as $userId => $roleName) {
            $user = User::query()->find($userId);
            if ($user) {
                $user->assignRole($roleName);
            }
        }
    }

}

