<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate(['name' => 'developer', 'label' => 'Desarrollador']);
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin', 'label' => 'Administrador']);
        $admin->syncPermissions(
            [
                'view_roles',
                'view_permissions',
                'view_users',
                'create_users',
                'edit_users',
                'delete_users',
                'view_disabled_users',
                'view_users_by_name',
                'edit_users_permissions',
                'view_products',
                'create_products',
                'edit_products',
                'delete_products',
                'view_disabled_products',
            ]
        );

        Role::firstOrCreate(['name' => 'coordinator', 'label' => 'Coodinador'])
            ->syncPermissions([
                'view_users',
                'create_users',
                'edit_users',
                'view_users_by_name',
                'view_products',
                'create_products',
                'edit_products',
            ]);

        Role::firstOrCreate(['name' => 'auxiliar', 'label' => 'Auxiliar'])
            ->syncPermissions([
                'view_products',
                'create_products',
            ]);

        Role::firstOrCreate(['name' => 'visitor', 'label' => 'Visitante'])
            ->syncPermissions([
                'view_products',
            ]);
    }
}
