<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repuesto extends Model
{
    protected $fillable = [
        'codigo', 'nombre', 'categoria','foto', 'unidad_medida',
        'precio_venta', 'stock_actual', 'stock_minimo', 'estado',
    ];
}
