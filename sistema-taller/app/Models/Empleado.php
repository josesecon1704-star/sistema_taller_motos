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

    public const CARGOS = [
    'administrador' => 'Administrador',
    'jefe_taller'   => 'Jefe de Taller',
    'mecanico'      => 'Mecánico',
];

    // Nombre del rol (tabla rols) que corresponde a cada cargo
    public const ROL_POR_CARGO = [
        'administrador' => 'Administrador',
        'jefe_taller'   => 'Jefe de Taller',
        'mecanico'      => 'Mecanico',
    ];

    protected function casts(): array
    {
        return ['fecha_contratacion' => 'date'];
    }

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
