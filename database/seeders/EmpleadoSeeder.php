<?php

namespace Database\Seeders;

use App\Models\Empleado;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpleadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    
        $empleados = [
            ['ci' => '1000001', 'nombre' => 'Administrador del Sistema', 'telefono' => '70000001',
             'cargo' => 'administrador', 'especialidad' => null],
            ['ci' => '1000002', 'nombre' => 'Jefe de Taller', 'telefono' => '70000002',
             'cargo' => 'jefe_taller', 'especialidad' => null],
            ['ci' => '1000003', 'nombre' => 'Luis Mamani', 'telefono' => '70000003',
             'cargo' => 'mecanico', 'especialidad' => 'Motor'],
            ['ci' => '1000004', 'nombre' => 'Pedro Quispe', 'telefono' => '70000004',
             'cargo' => 'mecanico', 'especialidad' => 'Electricidad'],
        ];

        foreach ($empleados as $e) {
            Empleado::firstOrCreate(
                ['ci' => $e['ci']],
                $e + ['estado' => 'activo', 'fecha_contratacion' => now()->toDateString()]
            );
        }
    }
}
