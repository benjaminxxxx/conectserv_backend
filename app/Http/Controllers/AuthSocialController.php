<?php

namespace App\Http\Controllers;

use App\Models\Profesional;
use App\Models\User;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Hash;
class AuthSocialController extends Controller
{

    public function verificarGoogle(Request $request, $googleId)
    {
        // Buscar si el profesional ya existe en la base de datos
        $user = User::where('google_id', $googleId)->with(['profesional'])->first();

        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'El usuario ya está registrado con Google.',
                'data' => $user,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'error' => 'El usuario no está registrado con Google.',
        ], 200);
    }
    public function login(Request $request)
    {
        $validator = validator($request->all(), [
            'medio' => 'required|string|in:google,whatsapp,default,facebook',
            'id' => 'required|string',
            'clave' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = null;
        $profesional = null;

        // Buscar el usuario según el método de autenticación
        switch ($request->medio) {
            case 'google':
                $user = User::where('google_id', $request->id)->first();
                break;
            case 'facebook':
                $user = User::where('facebook_id', $request->id)->first();
                break;
            case 'whatsapp':
                $profesional = Profesional::where('whatsapp', $request->id)->first();
                if ($profesional) {
                    $user = $profesional->user;
                }
                break;
            case 'default':
                $user = User::where('email', $request->id)->first();
                if (!$user || !Hash::check($request->clave, $user->password)) {
                    return response()->json(['success' => false, 'error' => 'Credenciales incorrectas.']);
                }
                break;
        }

        if (!$user) {
            return response()->json(['success' => false, 'error' => 'El usuario no está registrado.']);
        }

        // Si es un profesional, obtener su información y validar su estado
        if ($user->tipo_usuario === 'profesional') {
            $profesional = Profesional::where('user_id', $user->id)->first();
            if (!$profesional || in_array($profesional->estado, ['eliminado', 'bloqueado'])) {
                return response()->json(['success' => false, 'error' => 'El usuario no tiene acceso.']);
            }
        }

        // 🔥 **Generar el token JWT**
        $payload = [
            'sub' => $user->id,
            'nombre' => $user->name,
            'apellido' => $user->lastname,
            'email' => $user->email,
            'tipo_usuario' => $user->tipo_usuario,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24),
        ];

        $jwt = JWT::encode($payload, config('app.jwt_secret'), 'HS256');

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso.',
            'token' => $jwt,
            'data' => [
                'id' => $user->id,
                'nombre' => $user->name,
                'apellido' => $user->lastname,
                'email' => $user->email,
                'tipo_usuario' => $user->tipo_usuario,
                'profesional' => $profesional, // Si aplica
            ],
        ]);
    }

}
