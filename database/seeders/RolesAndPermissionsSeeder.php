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
            Permission::query()->firstOrCreate(['name' => $permission , 'guard_name' => 'sanctum']);
        }

        foreach (config('roles.roles') as $roleName => $permissions) {
            $role = Role::query()->firstOrCreate(['name' => $roleName , 'guard_name' => 'sanctum']);
            $role->syncPermissions($permissions);
        }

        foreach (config('roles.users') as $userId => $roleName) {
            $user = User::query()->find($userId);
            if ($user) {
                $user->guard_name = 'sanctum';
                $user->assignRole($roleName);
            }
        }
    }

}

