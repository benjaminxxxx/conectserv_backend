<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function getDashboardStats()
    {
        // Total de profesionales registrados (usuarios activos con tipo 'profesional')
        $totalProfesionales = User::where('tipo_usuario', 'profesional')
            ->where('estado', 'activo')
            ->count();

        // Profesionales activos con verificación pendiente (verificado = 0)
        $verificacionPendiente = User::where('tipo_usuario', 'profesional')
            ->where('estado', 'activo')
            ->whereHas('profesional', function ($query) {
                $query->where('verificado', false); // false es equivalente a 0
            })
            ->count();

        // Profesionales activos y verificados (verificado = 1)
        $profesionalesActivos = User::where('tipo_usuario', 'profesional')
            ->where('estado', 'activo')
            ->whereHas('profesional', function ($query) {
                $query->where('verificado', true); // true es equivalente a 1
            })
            ->count();

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
