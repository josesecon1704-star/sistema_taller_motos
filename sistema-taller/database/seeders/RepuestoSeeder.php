<?php

namespace Database\Seeders;

use App\Models\MovimientoInventario;
use App\Models\Proveedor;
use App\Models\ProveedorRepuesto;
use App\Models\Repuesto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RepuestoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $andina = Proveedor::where('nit', '1020304050')->firstOr();
        $motoParts = Proveedor::where('nit', '1020304051')->first();

        // [codigo, nombre, categoria, precio_venta, stock, stock_minimo, proveedor, precio_compra]
        $repuestos = [
            ['ACE-M-15', 'Aceite Motul 1.5 L', 'lubricantes', 95, 8, 3, $andina, 75],
            ['ACE-M-10', 'Aceite Motul 1 L', 'lubricantes', 65, 12, 4, $andina, 50],
            ['PAS-FRE-01', 'Pastillas de freno delanteras', 'frenos', 60, 8, 3, $motoParts, 42],
            ['BUJ-NGK-01', 'Bujía NGK', 'motor', 15, 30, 10, $motoParts, 9],
            ['FIL-ACE-01', 'Filtro de aceite', 'motor', 35, 15, 5, $andina, 22],
            ['BAT-12V-01', 'Batería 12V', 'electrico', 280, 4, 2, $motoParts, 220],
        ];

        foreach ($repuestos as [$codigo, $nombre, $categoria, $precio, $stock, $minimo, $proveedor, $compra]) {
            $repuesto = Repuesto::firstOrCreate(['codigo' => $codigo], [
                'nombre' => $nombre,
                'categoria' => $categoria,
                'unidad_medida' => 'unidad',
                'precio_venta' => $precio,
                'stock_actual' => $stock,
                'stock_minimo' => $minimo,
                'estado' => 'activo',
            ]);

            if ($repuesto->wasRecentlyCreated) {
                ProveedorRepuesto::create([
                    'proveedor_id' => $proveedor->id,
                    'repuesto_id' => $repuesto->id,
                    'precio_compra' => $compra,
                    'codigo_proveedor' => 'P-' . $codigo,
                ]);

                MovimientoInventario::create([
                    'repuesto_id' => $repuesto->id,
                    'proveedor_id' => $proveedor->id,
                    'tipo' => 'entrada',
                    'motivo' => 'compra',
                    'cantidad' => $stock,
                    'costo_unitario' => $compra,
                    'fecha_movimiento' => now(),
                    'observaciones' => 'Stock inicial',
                ]);
            }
        }
    }
}
