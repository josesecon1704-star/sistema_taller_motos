<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    protected $fillable = [
        'diagnostico_id', 'orden_trabajo_id', 'nro_cotizacion', 'version',
        'tipo', 'fecha_emision', 'fecha_respuesta', 'estado',
        'total_mano_obra', 'total_repuestos', 'monto_total', 'observaciones',
    ];
}
