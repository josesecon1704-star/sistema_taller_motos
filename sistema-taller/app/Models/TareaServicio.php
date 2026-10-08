<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TareaServicio extends Model
{
    protected $fillable = [
        'orden_trabajo_id', 'servicio_id', 'empleado_id', 'precio_mano_obra',
        'estado', 'fecha_inicio', 'fecha_fin', 'observaciones',
    ];
}
