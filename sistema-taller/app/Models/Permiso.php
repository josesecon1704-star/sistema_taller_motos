<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $fillable = [
        'nombre',
        'modulo',
        'accion'
    ];

    public function roles(){
        return $this->belongsToMany(Rol::class, 'rol_permisos')->withTimestamps();
    }
}
