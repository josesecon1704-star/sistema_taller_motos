<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $servicios = [
            ['Cambio de aceite', 'mantenimiento', 80],
            ['Cambio de filtro de aceite', 'mantenimiento', 40],
            ['Lubricación y ajuste de cadena', 'mantenimiento', 50],
            ['Revisión general', 'mantenimiento', 100],
            ['Cambio de pastillas de freno', 'frenos', 120],
            ['Ajuste de frenos', 'frenos', 60],
            ['Cambio de bujía', 'motor', 40],
            ['Sincronización de carburador', 'motor', 150],
            ['Reparación de motor', 'motor', 900],
            ['Revisión eléctrica', 'electrico', 80],
            ['Cambio de batería', 'electrico', 50],
            ['Cambio de amortiguadores', 'suspension', 200],
            ['Cambio de llanta', 'otros', 60],
        ];

        foreach ($servicios as [$nombre, $categoria, $precio]) {
            Servicio::firstOrCreate(['nombre' => $nombre], [
                'categoria' => $categoria,
                'precio_base' => $precio,
                'estado' => 'activo',
            ]);
        }
    }
}
