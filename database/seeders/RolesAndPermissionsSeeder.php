<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void

    {
//        todo: ref role/permission factory and get them from configs
        $permissions = [
            'approve by supervisor',
            'reject by supervisor',
            'approve by owner',
            'reject by owner',
            'view payments',
            'make payment',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        $supervisor = Role::query()->firstOrCreate(['name' => 'supervisor']);
        $owner = Role::query()->firstOrCreate(['name' => 'owner']);

        $supervisor->syncPermissions([
            'approve by supervisor',
            'reject by supervisor',
            'view payments',
        ]);

        $owner->syncPermissions([
            'approve by owner',
            'reject by owner',
            'make payment',
            'view payments',
        ]);

        $admin1 = User::query()->find((2));
        $admin1->assignRole('supervisor');

        $admin2 = User::query()->find((1));
        $admin2->assignRole('owner');
    }

}

