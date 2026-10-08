<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenTrabajo extends Model
{
    protected $fillable = [
        'nro_orden', 'tipo', 'fecha_ingreso', 'fecha_entrega_estimada',
        'fecha_entrega_real', 'km_ingreso', 'estado',
        'total_mano_obra', 'total_repuestos', 'total_final', 'observaciones',
    ];
}
