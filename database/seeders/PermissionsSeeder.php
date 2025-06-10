<?php

namespace Database\Seeders;

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
            ['name' => 'view_roles', 'label' => 'Ver Roles', 'type' => 'Role'],
            ['name' => 'create_roles', 'label' => 'Crear Roles', 'type' => 'Role'],
            ['name' => 'edit_roles', 'label' => 'Editar Roles', 'type' => 'Role'],
            ['name' => 'delete_roles', 'label' => 'Eliminar Roles', 'type' => 'Role'],
            // Permission permissions
            ['name' => 'view_permissions', 'label' => 'Ver Permisos', 'type' => 'Permission'],
            ['name' => 'create_permissions', 'label' => 'Crear Permisos', 'type' => 'Permission'],
            ['name' => 'edit_permissions', 'label' => 'Editar Permisos', 'type' => 'Permission'],
            ['name' => 'delete_permissions', 'label' => 'Eliminar Permisos', 'type' => 'Permission'],
            // User permissions
            ['name' => 'view_users', 'label' => 'Ver Usuarios', 'type' => 'User'],
            ['name' => 'create_users', 'label' => 'Crear Usuarios', 'type' => 'User'],
            ['name' => 'edit_users', 'label' => 'Editar Usuarios', 'type' => 'User'],
            ['name' => 'delete_users', 'label' => 'Eliminar Usuarios', 'type' => 'User'],
            ['name' => 'restore_users', 'label' => 'Restaurar Usuarios', 'type' => 'User'],
            ['name' => 'force_delete_users', 'label' => 'Eliminar Usuarios Permanentemente', 'type' => 'User'],
            ['name' => 'view_disabled_users', 'label' => 'Ver Usuarios Deshabilitados', 'type' => 'User'],
            ['name' => 'view_trashed_users', 'label' => 'Ver Usuarios Eliminados', 'type' => 'User'],
            ['name' => 'view_users_by_name', 'label' => 'Buscar Usuarios por Nombre', 'type' => 'User'],
            ['name' => 'edit_users_permissions', 'label' => 'Editar Permisos de Usuario', 'type' => 'User'],
            // Product permissions
            ['name' => 'view_products', 'label' => 'Ver Productos', 'type' => 'Product'],
            ['name' => 'create_products', 'label' => 'Crear Productos', 'type' => 'Product'],
            ['name' => 'edit_products', 'label' => 'Editar Productos', 'type' => 'Product'],
            ['name' => 'delete_products', 'label' => 'Eliminar Productos', 'type' => 'Product'],
            ['name' => 'restore_products', 'label' => 'Restaurar Productos', 'type' => 'Product'],
            ['name' => 'force_delete_products', 'label' => 'Eliminar Productos Permanentemente', 'type' => 'Product'],
            ['name' => 'view_disabled_products', 'label' => 'Ver Productos Deshabilitados', 'type' => 'Product'],
            ['name' => 'view_trashed_product', 'label' => 'Ver Productos Eliminados', 'type' => 'Product'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                [
                    'name' => $permission['name'],
                    'label' => $permission['label'] ?? null,
                    'type' => $permission['type'] ?? null,
                ]
            );
        }
    }
}
