<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermisosController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/roles",
     *     summary="Listar todos los roles con sus permisos",
     *     tags={"Roles"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de roles",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return response()->json($roles);
    }

    /**
     * @OA\Get(
     *     path="/api/roles/{id}",
     *     summary="Obtener un rol por ID con sus permisos",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol encontrado",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        $role = Role::with('permissions')->find($id);
        if (!$role) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }
        return response()->json($role);
    }

    /**
     * @OA\Post(
     *     path="/api/roles",
     *     summary="Crear un nuevo rol",
     *     tags={"Roles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="admin"),
     *             @OA\Property(
     *                 property="permissions",
     *                 type="array",
     *                 @OA\Items(type="string", example="edit-users")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rol creado",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos"
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/roles/{id}",
     *     summary="Actualizar un rol",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="editor"),
     *             @OA\Property(
     *                 property="permissions",
     *                 type="array",
     *                 @OA\Items(type="string", example="edit-users")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol actualizado",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/roles/{id}",
     *     summary="Eliminar un rol",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol eliminado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Rol eliminado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/permissions",
     *     summary="Listar todos los permisos",
     *     tags={"Permisos"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de permisos",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function permissionsIndex()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    /**
     * @OA\Post(
     *     path="/api/permissions",
     *     summary="Crear un nuevo permiso",
     *     tags={"Permisos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="edit-users")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Permiso creado",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos"
     *     )
     * )
     */
    public function permissionsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        $permission = Permission::create(['name' => $request->name]);

        return response()->json($permission, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/permissions/{id}",
     *     summary="Obtener un permiso por ID",
     *     tags={"Permisos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del permiso",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permiso encontrado",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Permiso no encontrado"
     *     )
     * )
     */
    public function permissionsShow($id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json(['message' => 'Permiso no encontrado'], 404);
        }
        return response()->json($permission);
    }

    /**
     * @OA\Put(
     *     path="/api/permissions/{id}",
     *     summary="Actualizar un permiso",
     *     tags={"Permisos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del permiso",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="edit-users")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permiso actualizado",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Permiso no encontrado"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/permissions/{id}",
     *     summary="Eliminar un permiso",
     *     tags={"Permisos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del permiso",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permiso eliminado correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Permiso no encontrado"
     *     )
     * )
     */
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
