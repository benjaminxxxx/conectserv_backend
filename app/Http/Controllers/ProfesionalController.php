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
        // Validación manual sin redirección automática
        $validator = validator($request->all(), [
            'servicio_id' => 'required|exists:services,id',
            'ubicacion_texto' => 'required|string',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'edad' => 'required|integer|min:18',
            'whatsapp' => 'required|string|unique:profesionals,whatsapp',
            'clave' => 'required|string|min:6',
        ]);

        // Si la validación falla, devolvemos los errores
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Intentamos crear el profesional
            $profesional = Profesional::create([
                'servicio_id' => $request->servicio_id,
                'ubicacion_texto' => $request->ubicacion_texto,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'edad' => $request->edad,
                'whatsapp' => $request->whatsapp,
                'clave' => Hash::make($request->clave),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profesional creado con éxito',
                'data' => $profesional
            ], 201);

        } catch (QueryException $e) {
            // Si el error es por número de WhatsApp duplicado
            if ($e->getCode() == 23000) {
                return response()->json([
                    'success' => false,
                    'message' => 'El número de WhatsApp ya está registrado.',
                    'error_code' => 'DUPLICATE_WHATSAPP'
                ], 409);
            }

            // Si el servicio no existe o fue eliminado
            if ($e->getCode() == 1452) {
                return response()->json([
                    'success' => false,
                    'message' => 'El servicio seleccionado no está disponible.',
                    'error_code' => 'SERVICE_NOT_FOUND'
                ], 404);
            }

            // Si ocurre un error inesperado
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un problema interno.',
                'error' => $e->getMessage(),
                'error_code' => 'INTERNAL_ERROR'
            ], 500);
        }
    }
}
