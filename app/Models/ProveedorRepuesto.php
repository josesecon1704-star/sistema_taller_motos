<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProveedorRepuesto extends Model
{
    protected $fillable = [
        'proveedor_id', 'repuesto_id', 'precio_compra', 'codigo_proveedor',
    ];
}
