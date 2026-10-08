<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'orden_trabajo_id', 'nro_comprobante', 'fecha_pago', 'monto',
        'metodo', 'tipo', 'estado', 'observaciones',
    ];
}
