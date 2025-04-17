<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $servicios = [
            'Refrigeracion',
            'Aire acondicionado',
            'Electricidad',
            'Carpinteria',
            'Muebles sobre medida',
            'Plomeria',
            'Pintura',
            'Colocacion de pisos',
            'Albañileria general',
            'Jardineria y limpieza',
            'Remodelaciones y fachada',
            'Limpieza de piscinas'
        ];

        foreach ($servicios as $servicio) {
            Servicio::create(['nombre' => $servicio]);
        }
    }
}
