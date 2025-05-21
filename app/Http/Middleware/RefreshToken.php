<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class RefreshToken
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Autenticar usuario desde el token
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Continuar con la solicitud
            $response = $next($request);

            // Generar nuevo token (auto-renovación)
            $newToken = JWTAuth::refresh();
            $response->headers->set('Authorization', 'Bearer ' . $newToken);

            return $response;
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token invalid'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token not provided or invalid'], 401);
        }
    }
}
