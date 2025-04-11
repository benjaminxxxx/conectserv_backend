<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'uuid' => Str::uuid(),
            'name' => 'Administrador',
            'email' => 'benuserxxx@gmail.com',
            'password' => Hash::make('12345678'),
            'google_id'=>'117770670773501178805',
            'tipo_usuario' => 'administrador',
        ]);

        $this->call([
            ServiceSeeder::class, // Asegúrate de que ServiceSeeder existe
        ]);
    }
}
