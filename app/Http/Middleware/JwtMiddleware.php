<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['success' => false, 'error' => 'Token no proporcionado.'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $jwt = str_replace('Bearer ', '', $token);
            $decoded = JWT::decode($jwt, new Key(config('app.jwt_secret'), 'HS256'));

            // Buscar al usuario en la base de datos
            $user = User::find($decoded->sub);

            if (!$user) {
                return response()->json(['success' => false, 'error' => 'Usuario no encontrado.'], Response::HTTP_UNAUTHORIZED);
            }

            // Guardar el usuario en la request para que los controladores lo usen
            $request->attributes->add(['user' => $user]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Token inválido o expirado.'], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
