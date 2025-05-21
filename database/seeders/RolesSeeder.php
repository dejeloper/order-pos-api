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
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        Role::firstOrCreate(['name' => 'coordinator'])->syncPermissions([
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'view_disabled_products',
            'view_trashed_product',
        ]);

        Role::firstOrCreate(['name' => 'auxiliar'])->syncPermissions([
            'view_products',
            'create_products',
        ]);

        Role::firstOrCreate(['name' => 'visitor'])->syncPermissions([
            'view_products',
            'view_disabled_products',
            'view_trashed_product',
        ]);
    }
}
