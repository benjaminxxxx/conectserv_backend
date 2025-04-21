<?php

namespace App\Http\Controllers\Admin;

use App\Models\Profesional;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class ProfesionalController extends Controller
{
    public function aprobar($uuid)
    {
        try {
            $usuario = User::where('uuid', $uuid)->firstOrFail();
            $profesional = Profesional::where('user_id', $usuario->id)->firstOrFail();

            $profesional->verificado = true;
            $profesional->save();

            return response()->json([
                'success' => true,
                'message' => 'Profesional aprobado correctamente.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function eliminar($uuid)
    {
        try {
            $usuario = User::where('uuid', $uuid)->firstOrFail();

            // Eliminar profesional relacionado
            $profesional = Profesional::where('user_id', $usuario->id)->first();
            if ($profesional) {
                $profesional->servicios()->detach(); // Elimina relación muchos a muchos
                $profesional->delete();
            }

            // Eliminar usuario si es necesario
            $usuario->delete();

            return response()->json([
                'success' => true,
                'message' => 'Profesional eliminado correctamente.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function listar(Request $request, $page = 1, $profesion = null)
    {
        try {
            $perPage = $request->get('per_page', 5); // Por defecto 10

            $query = Profesional::with(['user', 'servicios']);

            // Si se envía una profesión (servicio), filtramos
            if ($profesion) {

                $query->whereHas('servicios', function ($q) use ($profesion) {
                    $q->where('servicios.id', $profesion);
                });
            }


            $profesionales = $query->paginate($perPage, ['*'], 'page', $page);
            // Mapeamos cada item manteniendo los datos de paginación
            $data = $profesionales->getCollection()->map(function ($profesional) {
                return [
                    'uuid' => $profesional->user->uuid,
                    'nombres' => $profesional->user->name,
                    'apellidos' => $profesional->user->lastname,
                    'ubicacion_texto' => $profesional->ubicacion_texto,
                    'profesiones' => implode(', ', $profesional->servicios->pluck('nombre')->toArray()),
                    'latitud' => $profesional->latitud,
                    'longitud' => $profesional->longitud,
                    'whatsapp' => $profesional->whatsapp,
                    'verificado' => $profesional->verificado,
                    'imagen_identidad_frontal' => $profesional->url_imagen_identidad_frontal,
                    'imagen_identidad_dorso' => $profesional->url_imagen_identidad_dorso,
                    'imagen_real' => $profesional->url_imagen_real,
                    'created_at' => Carbon::parse($profesional->created_at)->format('d-m-Y'),
                ];
            });

            // Reemplazamos la colección original por la transformada
            $profesionales->setCollection($data);

            return response()->json([
                'success' => true,
                'data' => $profesionales
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
