<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolPermiso extends Model
{
    protected $fillable = [
        'rol_id', 'permiso_id'
    ];
}
