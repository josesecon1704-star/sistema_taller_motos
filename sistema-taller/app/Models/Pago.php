<?php

namespace App\Models;

use App\Observers\BitacoraObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(BitacoraObserver::class)]
class Pago extends Model
{
    protected $fillable = [
        'orden_trabajo_id',
        'nro_comprobante',
        'fecha_pago',
        'monto',
        'metodo',
        'tipo',
        'estado',
        'observaciones',
    ];

    public function ordenTrabajo(){
        return $this->belongsTo(OrdenTrabajo::class);
    }
}
