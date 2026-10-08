<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $fillable = [
        'nombre', 'ci', 'telefono', 'cargo',
        'especialidad', 'estado', 'fecha_contratacion',
    ];
}
