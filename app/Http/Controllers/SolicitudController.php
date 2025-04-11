<?php

namespace App\Http\Controllers;

use App\Models\Solicitudes;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class SolicitudController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            // Validar los datos
            $validatedData = $request->validate([
                'servicio_id' => 'required|exists:services,id',
                'ubicacion_texto' => 'required|string',
                'latitud' => 'required|numeric|between:-90,90',
                'longitud' => 'required|numeric|between:-180,180',
                'descripcion' => 'required|string',
                'whatsapp' => 'required|string',
            ]);

            // Crear la solicitud
            $solicitud = Solicitudes::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Solicitud creada exitosamente',
                'data' => $solicitud,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(), // Devuelve los errores específicos de cada campo
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la solicitud',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
