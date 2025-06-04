<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $permissions = [
            // Role permissions
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            // Permission permissions
            'view_permissions',
            'create_permissions',
            'edit_permissions',
            'delete_permissions',
            // User permissions
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'restore_users',
            'force_delete_users',
            'view_disabled_users',
            'view_trashed_users',
            'view_users_by_name',
            'edit_users_permissions',
            // Product permissions
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'restore_products',
            'force_delete_products',
            'view_disabled_products',
            'view_trashed_product'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
