<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    protected $fillable = [
        'cita_id', 'empleado_id', 'fecha_diagnostico',
        'tipo', 'descripcion_falla', 'estado',
    ];
}
