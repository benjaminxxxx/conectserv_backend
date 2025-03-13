<?php

namespace App\Http\Controllers;

use App\Models\Profesional;
use Illuminate\Http\Request;

class AuthSocialController extends Controller
{

    public function verificarGoogle(Request $request,$googleId)
    {
        // Buscar si el profesional ya existe en la base de datos
        $profesional = Profesional::where('google_id', $googleId)->first();

        if ($profesional) {
            return response()->json([
                'success' => true,
                'message' => 'El usuario ya está registrado con Google.',
                'data' => $profesional,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'error' => 'El usuario no está registrado con Google.',
        ], 404);
    }
    public function login(Request $request)
    {
        // Validar los parámetros de entrada
        $request->validate([
            'medio' => 'required|string|in:google,facebook,default',
            'id' => 'required|string', // Puede ser google_id, facebook_id o email
            'clave' => 'nullable|string', // Obligatorio solo si el medio es "default"
        ]);

        $profesional = null;

        // Buscar el usuario según el medio de autenticación
        switch ($request->medio) {
            case 'google':
                $profesional = Profesional::where('google_id', $request->id)->first();
                break;
            case 'facebook':
                $profesional = Profesional::where('facebook_id', $request->id)->first();
                break;
            case 'default':
                $profesional = Profesional::where('email', $request->id)->first();
                if (!$profesional || !password_verify($request->clave, $profesional->clave)) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Credenciales incorrectas.',
                    ], 401);
                }
                break;
        }

        // Verificar si el usuario existe
        if (!$profesional) {
            return response()->json([
                'success' => false,
                'error' => 'El usuario no está registrado.',
            ], 404);
        }

        // Retornar datos del usuario para la sesión
        return response()->json([
            'success' => true,
            'message' => 'Login exitoso.',
            'data' => [
                'id' => $profesional->id,
                'nombre' => $profesional->nombre,
                'apellido' => $profesional->apellido,
                'email' => $profesional->email,
                'whatsapp' => $profesional->whatsapp,
                'medio' => $request->medio, // Indica con qué método inició sesión
            ],
        ], 200);
    }

}
