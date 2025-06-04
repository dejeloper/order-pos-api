<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->whereNull('deleted_at')->get();
        return response()->json($users, 200);
    }

    public function indexDisabled()
    {
        $users = User::onlyTrashed()->with('roles')->get();

        if ($users->isEmpty()) {
            return response()->json(['message' => 'No hay usuarios deshabilitados'], 200);
        }

        return response()->json($users, 200);
    }

    public function show($id)
    {
        $user = User::with('roles')->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($user, 200);
    }

    public function showByName($name)
    {
        $users = User::with('roles')->where('name', 'like', '%' . $name . '%')->get();

        if ($users->isEmpty()) {
            return response()->json(['message' => 'No se encontraron usuarios con ese nombre'], 404);
        }

        return response()->json($users, 200);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:10|max:100',
            'username' => 'required|string|min:4|max:100|unique:users',
            'password' => 'required|string|min:10|confirmed',
            'role' => 'nullable|string|exists:roles,name'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        if ($request->filled('role')) {
            $user->assignRole($request->role);
        }

        return response()->json($user->load('roles'), 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|min:10|max:100',
            'username' => 'sometimes|string|min:4|max:100|unique:users,username,' . $user->id,
            'password' => 'sometimes|string|min:10|confirmed',
            'role' => 'nullable|string|exists:roles,name'
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        if ($request->filled('role')) {
            $user->syncRoles([$request->role]);
        }

        return response()->json($user->load('roles'), 200);
    }

    public function destroy($id)
    {
        $user = User::whereNull('deleted_at')->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario eliminado no encontrado'], 404);
        }

        $user->restore();

        return response()->json(['message' => 'Usuario restaurado correctamente'], 200);
    }


    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario eliminado no encontrado'], 404);
        }

        $user->forceDelete();

        return response()->json(['message' => 'Usuario eliminado permanentemente'], 200);
    }

    public function showTrashed($id)
    {
        $user = User::onlyTrashed()->with('roles')->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario eliminado no encontrado'], 404);
        }

        return response()->json($user, 200);
    }

    public function syncPermissions(Request $request, $id)
    {
        $request->validate([
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string|exists:permissions,name',
            'assign' => 'required|boolean'
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        if ($request->assign) {
            $user->givePermissionTo($request->permissions);
        } else {
            $user->revokePermissionTo($request->permissions);
        }

        return response()->json([
            'message' => $request->assign
                ? 'Permisos asignados correctamente.'
                : 'Permisos revocados correctamente.',
            'user' => $user->load('permissions')
        ]);
    }
}
