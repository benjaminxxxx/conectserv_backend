<?php

namespace App\Http\Controllers;

use App\Models\Profesional;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfesionalController extends Controller
{
    public function store(Request $request)
    {
        // Validación sin redirección automática
        $validator = validator($request->all(), [
            'servicio_id' => 'nullable|exists:services,id',
            'ubicacion_texto' => 'nullable|string',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'nombre' => 'nullable|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'edad' => 'nullable|integer|min:18',
            'email' => 'nullable|email|unique:profesionals,email',
            'whatsapp' => 'nullable|string|unique:profesionals,whatsapp',
            'clave' => 'nullable|string|min:6',
            'google_id' => 'nullable|string|unique:profesionals,google_id',
            'facebook_id' => 'nullable|string|unique:profesionals,facebook_id',
        ]);

        // Si la validación falla, devolver errores
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Crear instancia de Profesional y asignar valores con fill()
            $profesional = new Profesional();
            $profesional->fill([
                'servicio_id' => $request->servicio_id ?? null,
                'ubicacion_texto' => $request->ubicacion_texto ?? '',
                'latitud' => $request->latitud ?? 0.0,
                'longitud' => $request->longitud ?? 0.0,
                'nombre' => $request->nombre ?? '',
                'apellido' => $request->apellido ?? '',
                'edad' => $request->edad ?? 0,
                'email' => $request->email ?? '',
                'whatsapp' => $request->whatsapp ?? '',
                'clave' => $request->clave ? Hash::make($request->clave) : '',
                'google_id' => $request->google_id ?? null,
                'facebook_id' => $request->facebook_id ?? null,
            ]);
            $profesional->save();

            return response()->json([
                'success' => true,
                'message' => 'Profesional registrado con éxito',
                'data' => $profesional
            ], 201);

        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json([
                    'success' => false,
                    'message' => 'El email o número de WhatsApp ya está registrado.',
                    'error_code' => 'DUPLICATE_ENTRY'
                ], 409);
            }

            if ($e->getCode() == 1452) {
                return response()->json([
                    'success' => false,
                    'message' => 'El servicio seleccionado no está disponible.',
                    'error_code' => 'SERVICE_NOT_FOUND'
                ], 404);
            }

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un problema interno.',
                'error' => $e->getMessage(),
                'error_code' => 'INTERNAL_ERROR'
            ], 500);
        }
    }

}
