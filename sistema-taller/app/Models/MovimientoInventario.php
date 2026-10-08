<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $fillable = [
        'repuesto_id', 'proveedor_id', 'uso_repuesto_id', 'tipo', 'motivo',
        'cantidad', 'costo_unitario', 'fecha_movimiento', 'observaciones',
    ];
}
