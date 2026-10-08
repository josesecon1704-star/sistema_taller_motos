<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $fillable = [
        'nombre', 'categoria', 'descripcion', 'precio_base', 'estado'
    ];
}
