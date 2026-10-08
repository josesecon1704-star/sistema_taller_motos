<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proveedores = [
            ['nombre' => 'Repuestos Andina', 'nit' => '1020304050', 'telefono' => '22000001',
             'email' => 'ventas@andina.com', 'direccion' => 'Av. Buenos Aires 100'],
            ['nombre' => 'Moto Parts La Paz', 'nit' => '1020304051', 'telefono' => '22000002',
             'email' => 'contacto@motoparts.com', 'direccion' => 'Calle Max Paredes 250'],
        ];

        foreach ($proveedores as $p) {
            Proveedor::firstOrCreate(['nit' => $p['nit']], $p + ['estado' => 'activo']);
        }
    }
}
