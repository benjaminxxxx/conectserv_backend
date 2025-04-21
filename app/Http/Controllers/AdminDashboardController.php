<?php

namespace App\Http\Controllers;

use App\Models\Profesional;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function getDashboardStats()
    {
        // Total de profesionales registrados (usuarios activos con tipo 'profesional')
        $totalProfesionales = Profesional::get()->count();

        // Profesionales activos con verificación pendiente (verificado = 0)
        $verificacionPendiente = Profesional::where('verificado', false)->count();

        // Profesionales activos y verificados (verificado = 1)
        $profesionalesActivos = Profesional::where('verificado', true)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'profesionales_registrados' => $totalProfesionales ?? 0,
                'verificacion_pendiente' => $verificacionPendiente ?? 0,
                'profesionales_activos' => $profesionalesActivos ?? 0,
            ]
        ]);
    }
}
