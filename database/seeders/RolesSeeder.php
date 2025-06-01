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
        $admin->syncPermissions(Permission::all());

        Role::firstOrCreate(['name' => 'coordinator', 'label' => 'Coodinador'])
            ->syncPermissions([
                'view_products',
                'create_products',
                'edit_products',
                'delete_products',
                'view_disabled_products',
                'view_trashed_product',
            ]);

        Role::firstOrCreate(['name' => 'auxiliar', 'label' => 'Auxiliar'])
            ->syncPermissions([
                'view_products',
                'create_products',
            ]);

        Role::firstOrCreate(['name' => 'visitor', 'label' => 'Visitante'])
            ->syncPermissions([
                'view_products',
                'view_disabled_products',
                'view_trashed_product',
            ]);
    }
}
