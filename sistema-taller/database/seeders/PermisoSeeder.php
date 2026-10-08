<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $crud = ['crear', 'mostrar', 'modificar', 'eliminar'];

        $modulos = [
            'usuarios' => $crud, 'roles' => $crud, 'clientes' => $crud,
            'empleados' => $crud, 'motocicletas' => $crud, 'citas' => $crud,
            'diagnosticos' => $crud, 'cotizaciones' => $crud, 'ordenes' => $crud,
            'servicios' => $crud, 'repuestos' => $crud, 'proveedores' => $crud,
            'pagos' => $crud, 'garantias' => $crud, 'mantenimientos' => $crud,
            'bitacora' => ['mostrar'], 'finanzas' => ['mostrar'],
        ];

        $verbos = ['crear' => 'Crear', 'mostrar' => 'Ver', 'modificar' => 'Editar', 'eliminar' => 'Eliminar'];

        foreach ($modulos as $modulo => $acciones) {
            foreach ($acciones as $accion) {
                Permiso::firstOrCreate(
                    ['modulo' => $modulo, 'accion' => $accion],
                    ['nombre' => $verbos[$accion] . ' ' . $modulo]
                );
            }
        }

        // Administrador: todo
        $this->asignar('Administrador', Permiso::pluck('id'));

        // Jefe de Taller: todo menos usuarios, roles, bitácora y eliminar
        $this->asignar('Jefe de Taller',
            Permiso::whereNotIn('modulo', ['usuarios', 'roles', 'bitacora'])
                ->where('accion', '!=', 'eliminar')->pluck('id'));

        // Mecánico
        $this->asignar('Mecanico', $this->idsPara([
            'diagnosticos' => ['crear', 'mostrar', 'modificar'],
            'ordenes' => ['mostrar', 'modificar'],
            'cotizaciones' => ['mostrar'],
            'citas' => ['mostrar'], 'clientes' => ['mostrar'], 'motocicletas' => ['mostrar'],
            'servicios' => ['mostrar'], 'repuestos' => ['mostrar'], 'mantenimientos' => ['mostrar'],
        ]));

        // Cliente (ver solo lo propio se controla en el código, con policies)
        $this->asignar('Cliente', $this->idsPara([
            'citas' => ['crear', 'mostrar'],
            'motocicletas' => ['crear', 'mostrar', 'modificar'],
            'cotizaciones' => ['mostrar', 'modificar'],
            'ordenes' => ['mostrar'], 'pagos' => ['mostrar'],
            'garantias' => ['mostrar'], 'mantenimientos' => ['mostrar'],
        ]));
    }

    private function idsPara(array $modulos)
    {
        return Permiso::where(function ($q) use ($modulos) {
            foreach ($modulos as $modulo => $acciones) {
                $q->orWhere(fn ($w) => $w->where('modulo', $modulo)->whereIn('accion', $acciones));
            }
        })->pluck('id');
    }

    private function asignar(string $nombreRol, $ids): void
    {
        $rolId = Rol::where('nombre', $nombreRol)->value('id');

        $filas = collect($ids)->map(fn ($id) => [
            'rol_id' => $rolId, 'permiso_id' => $id,
            'created_at' => now(), 'updated_at' => now(),
        ])->all();

        DB::table('rol_permisos')->insertOrIgnore($filas);
    }
    
}
