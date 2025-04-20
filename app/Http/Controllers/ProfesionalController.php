<?php

namespace App\Http\Controllers;

use App\Models\Profesional;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
class ProfesionalController extends Controller
{
    public function uploadDocs(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'imagen_identidad_frontal' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:2048'],
            'imagen_identidad_dorso' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:2048'],
            'imagen_real' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:2048'],
        ]);

        $user = User::with('profesional')->find($request->user_id);

        if (!$user || !$user->profesional) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el profesional asociado al usuario.',
            ], 404);
        }

        $profesional = $user->profesional;
        $paths = [];
        $fecha = now();
        $carpeta = $fecha->year . '/' . $fecha->month;

        foreach (['imagen_identidad_frontal', 'imagen_identidad_dorso', 'imagen_real'] as $campo) {
            if ($request->hasFile($campo)) {
                $file = $request->file($campo);
                $extension = $file->getClientOriginalExtension();
                $filename = Str::random(16) . '.' . $extension;

                $path = $file->storeAs($carpeta, $filename, 'public');
                $fullPath = $path;

                // Eliminar archivo anterior si existe
                if (!empty($profesional->{$campo}) && Storage::disk('public')->exists($profesional->{$campo})) {
                    Storage::disk('public')->delete($profesional->{$campo});
                }

                $paths[$campo] = $fullPath;
            } else {
                // Si no se subió nuevo archivo, conservar el anterior
                $paths[$campo] = $profesional->{$campo};
            }
        }

        // Actualizar el modelo Profesional
        $profesional->update($paths);

        return response()->json([
            'success' => true,
            'message' => 'Documentos subidos correctamente.',
            'data' => $paths,
        ]);
    }
    public function deleteDocument(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'documento' => 'required|string|in:frontal,dorso,real',
        ]);

        try {
            $userId = $request->input('user_id');
            $documento = $request->input('documento');

            $profesional = Profesional::where('user_id', $userId)->first();

            if (!$profesional) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profesional no encontrado.',
                ], 404);
            }

            $campo = null;

            switch ($documento) {
                case 'frontal':
                    $campo = 'imagen_identidad_frontal';
                    break;
                case 'dorso':
                    $campo = 'imagen_identidad_dorso';
                    break;
                case 'real':
                    $campo = 'imagen_real';
                    break;
            }

            if ($campo && $profesional->$campo && \Storage::exists($profesional->$campo)) {
                \Storage::delete($profesional->$campo);
            }

            $profesional->$campo = null;
            $profesional->save();

            return response()->json([
                'success' => true,
                'message' => 'Documento eliminado correctamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el documento.',
            ], 500);
        }
    }

    public function getUploadedDocs($id)
    {
        try {

            $user = User::with('profesional')->find($id);

            if (!$user || !$user->profesional) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró el profesional asociado al usuario.',
                ], 404);
            }

            $profesional = $user->profesional;

            $archivos = [
                'imagen_identidad_frontal' => $profesional->imagen_identidad_frontal ? Storage::disk('public')->url($profesional->imagen_identidad_frontal) : null,
                'imagen_identidad_dorso' => $profesional->imagen_identidad_dorso ? Storage::disk('public')->url($profesional->imagen_identidad_dorso) : null,
                'imagen_real' => $profesional->imagen_real ? Storage::disk('public')->url($profesional->imagen_real) : null,
            ];

            return response()->json([
                'success' => true,
                'data' => $archivos,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la solicitud',
                'error' => $e->getMessage(),
            ], 500);
        }


    }

    public function verificarImagenes($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Profesional no encontrado.',
            ], 404);
        }

        $faltantes = [];

        $profesional = $user->profesional;
        if (!$profesional) {
            return response()->json([
                'success' => false,
                'message' => 'Profesional no encontrado.',
            ], 404);
        }

        if (!$profesional->imagen_identidad_frontal) {
            $faltantes[] = 'Imagen frontal de cédula de identidad';
        }

        if (!$profesional->imagen_identidad_dorso) {
            $faltantes[] = 'Imagen dorso de cédula de identidad';
        }

        if (!$profesional->imagen_real) {
            $faltantes[] = 'Imagen real del rostro';
        }

        if (count($faltantes) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Faltan los siguientes campos: ' . implode(', ', $faltantes),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Todas las imágenes están completas.',
        ]);
    }


    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'ubicacion_texto' => 'nullable|string',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'nombre' => 'nullable|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'edad' => 'nullable|integer|min:18',
            'email' => 'nullable|email|unique:users,email',
            'whatsapp' => 'nullable|string|unique:profesionales,whatsapp',
            'clave' => 'nullable|string|min:6',
            'google_id' => 'nullable|string|unique:users,google_id',
            'facebook_id' => 'nullable|string|unique:users,facebook_id',
            'servicios' => 'required|array', // Asegúrate de que se envíe un array de servicios
            'servicios.*' => 'exists:servicios,id', // Cada servicio debe existir en la tabla servicios
        ], [
            'servicio_id.exists' => 'El servicio seleccionado no existe.',
            'email.unique' => 'El email ya está siendo utilizado.',
            'whatsapp.unique' => 'El número de WhatsApp ya está siendo utilizado.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        DB::beginTransaction();

        try {
            $user = new User();
            $user->fill([
                'name' => $request->nombre,
                'lastname' => $request->apellido,
                'email' => $request->email,
                'password' => Hash::make($request->clave),
                'tipo_usuario' => 'profesional',
                'google_id' => $request->google_id ?? null,
                'facebook_id' => $request->facebook_id ?? null,
            ]);
            $user->save();

            $profesional = new Profesional();
            $profesional->fill([
                'user_id' => $user->id,
                'ubicacion_texto' => $request->ubicacion_texto ?? '',
                'latitud' => $request->latitud ?? 0.0,
                'longitud' => $request->longitud ?? 0.0,
                'whatsapp' => $request->whatsapp ?? '',
                'estado' => 'noverificado',
            ]);
            $profesional->save();

            // Asociar los servicios al profesional
            $profesional->servicios()->sync($request->servicios);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profesional registrado con éxito',
            ]);

        } catch (QueryException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }


}
