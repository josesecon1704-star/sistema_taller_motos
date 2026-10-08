<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $fillable = [
        'cliente_id', 'motocicleta_id', 'empleado_id',
        'fecha_hora', 'motivo', 'estado', 'observaciones',
    ];
}
