<?php

namespace App\Models;

use App\Observers\BitacoraObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(BitacoraObserver::class)]
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
