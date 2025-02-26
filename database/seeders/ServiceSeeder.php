<?php

namespace Database\Seeders;

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
        $services = [
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

        foreach ($services as $service) {
            Service::create(['name' => $service]);
        }
    }
}
