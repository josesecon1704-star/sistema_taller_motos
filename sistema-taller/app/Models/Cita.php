<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $fillable = [
        'cliente_id',
        'motocicleta_id',
        'empleado_id',
        'fecha_hora',
        'motivo',
        'estado',
        'observaciones',
    ];

    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }

    public function motocicleta(){
        return $this->belongsTo(Motocicleta::class);
    }

    public function empleado(){
        return $this->belongsTo(Empleado::class);
    }

    public function diagnosticos() { 
        return $this->hasMany(Diagnostico::class); 
    }

}
