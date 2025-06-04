<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermisosController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return response()->json($roles);
    }

    public function show($id)
    {
        $role = Role::with('permissions')->find($id);
        if (!$role) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }
        return response()->json($role);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json($role->load('permissions'), 201);
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        $request->validate([
            'name' => 'string|unique:roles,name,' . $id,
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        if ($request->has('name')) {
            $role->name = $request->name;
        }

        $role->save();

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json($role->load('permissions'));
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        $role->revokePermissionTo($role->permissions);
        $role->delete();

        return response()->json(['message' => 'Rol eliminado correctamente']);
    }

    public function permissionsIndex()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    public function permissionsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        $permission = Permission::create(['name' => $request->name]);

        return response()->json($permission, 201);
    }

    public function permissionsShow($id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json(['message' => 'Permiso no encontrado'], 404);
        }
        return response()->json($permission);
    }

    public function permissionsUpdate(Request $request, $id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json(['message' => 'Permiso no encontrado'], 404);
        }

        $request->validate([
            'name' => 'string|unique:permissions,name,' . $id,
        ]);

        if ($request->has('name')) {
            $permission->name = $request->name;
        }
        $permission->save();

        return response()->json($permission);
    }

    public function permissionsDestroy($id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json(['message' => 'Permiso no encontrado'], 404);
        }
        $permission->delete();

        return response()->json(['message' => 'Permiso eliminado correctamente']);
    }
}
