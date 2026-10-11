<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Garantia extends Model
{
    protected $fillable = [
        'orden_trabajo_id',
        'fecha_inicio',
        'dias',
        'km',
        'cobertura',
        'estado',
    ];

    public function ordenTrabajo(){
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function reclamos() { 
        return $this->hasMany(ReclamoGarantia::class); 
    }
}
