<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Order POS API",
 *     description="Order POS API es una solución backend desarrollada en Laravel para la gestión integral de puntos de venta. Actualmente permite administrar usuarios y productos, y en el futuro abarcará todo el ciclo de ventas, cobranza, pagos, clientes, reportes, inventario, facturación y más.",
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     ),
 *     @OA\Contact(
 *         url="https://dejeloper.com",
 *         email="jhonatanguerrero@outlook.com",
 *         name="Jhonatan Guerrero"
 *     )
 * )
 */

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:10|max:100',
            'username' => 'required|string|min:4|max:100|unique:users',
            'password' => 'required|string|min:10|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        User::create([
            'name' => $request->get('name'),
            'username' => $request->get('username'),
            'password' => bcrypt($request->get('password')),
        ]);

        return response()->json(['message' => 'User created successfully'], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Iniciar sesión y obtener token JWT",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username","password"},
     *             @OA\Property(property="username", type="string", example="jguerrero"),
     *             @OA\Property(property="password", type="string", example="password12345")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login exitoso, retorna token y datos de usuario"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciales inválidas"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:4|max:100',
            'password' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $credentials = $request->only('username', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            $user = JWTAuth::user();

            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'role' => $user->getRoleNames()->first(),
                ],
            ], 200);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token', $e], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/me",
     *     summary="Obtener usuario autenticado",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Datos del usuario autenticado"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token inválido o usuario no encontrado"
     *     )
     * )
     */
    public function getUser()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            return response()->json([
                'user' => $user,
                'role' => $user->getRoleNames()->first(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User not found or token invalid'], 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Cerrar sesión (invalidar token)",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sesión cerrada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     )
     * )
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/refresh",
     *     summary="Refrescar token JWT",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Nuevo token JWT"
     *     )
     * )
     */
    public function refresh()
    {
        return $this->respondWithToken(JWTAuth::refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json(['token' => $token], 200);
    }
}
