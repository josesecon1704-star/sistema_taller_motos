<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Motocicleta extends Model
{
    protected $fillable = [
        'cliente_id', 'placa', 'nro_chasis', 'marca', 'modelo', 'anio',
        'color', 'cilindrada', 'nro_motor', 'kilometraje', 'foto',
    ];

    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }

    public function citas(){ 
        return $this->hasMany(Cita::class); 
    }
    public function mantenimientos(){ 
        return $this->hasMany(Mantenimiento::class); 
    }
}
