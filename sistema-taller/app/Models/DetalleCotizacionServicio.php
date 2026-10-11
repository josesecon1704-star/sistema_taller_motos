<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCotizacionServicio extends Model
{
    protected $fillable = [
        'cotizacion_id',
        'servicio_id',
        'precio_mano_obra',
        'observaciones',
    ];

    public function cotizacion(){
        return $this->belongsTo(Cotizacion::class);
    }

    public function servicio(){
        return $this->belongsTo(Servicio::class);
    }
}
