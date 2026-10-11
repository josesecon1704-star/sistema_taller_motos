<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    public function usuarios(){
        return $this->hasMany(User::class);
    }

    public function permisos(){
        return $this->belongsToMany(Permiso::class, 'rol_permisos')->withTimestamps();
    }
}
