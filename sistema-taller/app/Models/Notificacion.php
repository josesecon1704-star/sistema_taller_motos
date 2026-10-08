<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $fillable = [
        'user_id', 'tipo', 'mensaje', 'leida'
    ];
}
