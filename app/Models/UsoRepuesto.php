<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsoRepuesto extends Model
{
    protected $fillable = [
        'orden_trabajo_id',
        'repuesto_id',
        'cantidad',
        'precio_unitario',
    ];

    public function ordenTrabajo(){
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function repuesto(){
        return $this->belongsTo(Repuesto::class);
    }

    public function movimiento() { 
        return $this->hasOne(MovimientoInventario::class); 
    }

}
