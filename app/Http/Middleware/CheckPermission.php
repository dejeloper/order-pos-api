<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$checkPermission): Response
    {
        $user = $request->user();

        // Verifica permisos
        foreach ($checkPermission as $value) {
            if ($user->hasPermissionTo($value) || $user->hasRole($value)) {
                return $next($request);
            }
        }

        return response()->json(['message' => 'Forbidden: insufficient role or permission'], 403);
    }
}
