<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'accion', 'tabla_afectada', 'registro_id',
        'valor_anterior', 'valor_nuevo',
    ];
}
