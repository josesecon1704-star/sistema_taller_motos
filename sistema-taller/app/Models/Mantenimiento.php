<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    protected $fillable = [
        'motocicleta_id',
        'servicio_id',
        'intervalo_km',
        'intervalo_mes',
        'ultimo_km',
        'proximo_km',
        'ultima_fecha',
        'proxima_fecha',
        'estado',
    ];

    public function motocicleta(){
        return $this->belongsTo(Motocicleta::class);
    }

    public function servicio(){
        return $this->belongsTo(Servicio::class);
    }
}
