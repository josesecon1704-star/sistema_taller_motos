<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $fillable = [
        'nombre',
        'ci',
        'telefono',
        'cargo',
        'especialidad',
        'estado',
        'fecha_contratacion',
    ];

    public function usuario(){
        return $this->hasOne(User::class);
    }

    public function citas(){
        return $this->hasMany(Cita::class);
    }
    
    public function diagnosticos(){
        return $this->hasMany(Diagnostico::class);
    }

    public function tareas(){
        return $this->hasMany(TareaServicio::class);
    }
}
