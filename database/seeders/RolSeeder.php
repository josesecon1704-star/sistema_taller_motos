<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rol::create(['nombre' => 'Administrador', 'descripcion' => 'Tiene acceso completo al sistema.']);
        Rol::create(['nombre' => 'Jefe de Taller', 'descripcion' => 'Supervisa las operaciones y gestiona información.']);
        Rol::create(['nombre' => 'Mecanico', 'descripcion' => 'Ejecuta tareas y diagnósticos']);
        Rol::create(['nombre' => 'Cliente', 'descripcion' => 'Usuario que Consulta sus motos y agenda su citas']);
    }
}
