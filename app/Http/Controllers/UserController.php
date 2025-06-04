<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function users()
    {
        $users = User::get();
        return response()->json($users, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Obtener todos los usuarios activos",
     *     tags={"Usuarios"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuarios activos",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     )
     * )
     */
    public function index()
    {
        $users = User::with('roles')->whereNull('deleted_at')->get();
        return response()->json($users, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/users/disabled",
     *     summary="Obtener usuarios deshabilitados",
     *     tags={"Usuarios"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuarios deshabilitados",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     )
     * )
     */
    public function indexDisabled()
    {
        $users = User::onlyTrashed()->with('roles')->get();

        if ($users->isEmpty()) {
            return response()->json(['message' => 'No hay usuarios deshabilitados'], 200);
        }

        return response()->json($users, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Obtener usuario por ID",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        $user = User::with('roles')->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($user, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/users/name/{name}",
     *     summary="Buscar usuarios por nombre",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="name",
     *         in="path",
     *         required=true,
     *         description="Nombre o parte del nombre",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuarios encontrados",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron usuarios"
     *     )
     * )
     */
    public function showByName($name)
    {
        $users = User::with('roles')->where('name', 'like', '%' . $name . '%')->get();

        if ($users->isEmpty()) {
            return response()->json(['message' => 'No se encontraron usuarios con ese nombre'], 404);
        }

        return response()->json($users, 200);
    }


    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Crear un nuevo usuario",
     *     tags={"Usuarios"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","username","password"},
     *             @OA\Property(property="name", type="string", example="Jhonatan Guerrero"),
     *             @OA\Property(property="username", type="string", example="jguerrero"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123"),
     *             @OA\Property(property="role", type="string", example="admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuario creado",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos"
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Actualizar usuario",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nuevo Nombre"),
     *             @OA\Property(property="username", type="string", example="nuevo_usuario"),
     *             @OA\Property(property="password", type="string", example="nuevaPassword123"),
     *             @OA\Property(property="password_confirmation", type="string", example="nuevaPassword123"),
     *             @OA\Property(property="role", type="string", example="admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario actualizado",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Eliminar usuario (soft delete)",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario eliminado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuario eliminado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $user = User::whereNull('deleted_at')->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/restore",
     *     summary="Restaurar usuario eliminado",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario restaurado correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario eliminado no encontrado"
     *     )
     * )
     */
    public function restore($id)
    {
        $user = User::onlyTrashed()->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario eliminado no encontrado'], 404);
        }

        $user->restore();

        return response()->json(['message' => 'Usuario restaurado correctamente'], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}/force",
     *     summary="Eliminar usuario permanentemente",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario eliminado permanentemente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario eliminado no encontrado"
     *     )
     * )
     */
    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario eliminado no encontrado'], 404);
        }

        $user->forceDelete();

        return response()->json(['message' => 'Usuario eliminado permanentemente'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/users/trashed/{id}",
     *     summary="Obtener usuario eliminado por ID",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario eliminado",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario eliminado encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario eliminado no encontrado"
     *     )
     * )
     */
    public function showTrashed($id)
    {
        $user = User::onlyTrashed()->with('roles')->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario eliminado no encontrado'], 404);
        }

        return response()->json($user, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/permissions",
     *     summary="Asignar o revocar permisos a un usuario",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"permissions","assign"},
     *             @OA\Property(
     *                 property="permissions",
     *                 type="array",
     *                 @OA\Items(type="string", example="edit-users")
     *             ),
     *             @OA\Property(
     *                 property="assign",
     *                 type="boolean",
     *                 example=true,
     *                 description="true para asignar, false para revocar"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permisos actualizados"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     )
     * )
     */
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
