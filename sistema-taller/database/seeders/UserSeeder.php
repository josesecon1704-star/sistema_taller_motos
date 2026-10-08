<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cuentas = [
            ['email' => 'admin@taller.com',   'ci' => '1000001', 'rol' => 'Administrador'],
            ['email' => 'jefe@taller.com',    'ci' => '1000002', 'rol' => 'Jefe de Taller'],
            ['email' => 'luis@taller.com',    'ci' => '1000003', 'rol' => 'Mecanico'],
            ['email' => 'pedro@taller.com',   'ci' => '1000004', 'rol' => 'Mecanico'],
        ];

        foreach ($cuentas as $c) {
            User::firstOrCreate(['email' => $c['email']], [
                'password' => Hash::make('password'),
                'estado' => 'activo',
                'rol_id' => Rol::where('nombre', $c['rol'])->value('id'),
                'empleado_id' => Empleado::where('ci', $c['ci'])->value('id'),
            ]);
        }

        // Cliente de prueba, para probar el rol Cliente
        $cliente = Cliente::firstOrCreate(
            ['tipo_documento' => 'ci', 'numero_documento' => '7654321'],
            ['nombre' => 'Cliente de Prueba', 'telefono' => '70000010', 'tipo' => 'particular']
        );

        User::firstOrCreate(['email' => 'cliente@taller.com'], [
            'password' => Hash::make('password'),
            'estado' => 'activo',
            'rol_id' => Rol::where('nombre', 'Cliente')->value('id'),
            'cliente_id' => $cliente->id,
        ]);
    }
}
