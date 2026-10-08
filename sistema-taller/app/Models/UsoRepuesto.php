<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsoRepuesto extends Model
{
    protected $fillable = [
        'orden_trabajo_id', 'repuesto_id', 'cantidad', 'precio_unitario',
    ];
}
