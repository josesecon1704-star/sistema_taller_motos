<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $fillable = [
        'nombre',
        'modulo',
        'accion'
    ];

    // Nombres legibles de cada módulo y acción para la pantalla
public const MODULOS = [
    'usuarios'       => 'Usuarios',
    'roles'          => 'Roles y permisos',
    'clientes'       => 'Clientes',
    'empleados'      => 'Empleados',
    'motocicletas'   => 'Motocicletas',
    'citas'          => 'Citas',
    'diagnosticos'   => 'Diagnósticos',
    'cotizaciones'   => 'Cotizaciones',
    'ordenes'        => 'Órdenes de trabajo',
    'servicios'      => 'Servicios',
    'repuestos'      => 'Repuestos',
    'proveedores'    => 'Proveedores',
    'pagos'          => 'Pagos',
    'garantias'      => 'Garantías',
    'mantenimientos' => 'Mantenimientos',
    'bitacora'       => 'Bitácora',
    'finanzas'       => 'Finanzas',
];

public const ACCIONES = [
    'crear'     => 'Crear',
    'mostrar'   => 'Ver',
    'modificar' => 'Editar',
    'eliminar'  => 'Eliminar',
];

    public function roles(){
        return $this->belongsToMany(Rol::class, 'rol_permisos')->withTimestamps();
    }
}
