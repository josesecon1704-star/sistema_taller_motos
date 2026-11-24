<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCotizacionServicio extends Model
{
    use HasFactory;

    protected $table = 'detalle_cotizacion_servicios';

    protected $fillable = [
        'cotizacion_id',
        'servicio_id',
        'precio_mano_obra',
        'observaciones',
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'cotizacion_id');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }
}
