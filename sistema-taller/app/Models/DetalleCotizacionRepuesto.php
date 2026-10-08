<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCotizacionRepuesto extends Model
{
    protected $fillable = [
        'cotizacion_id', 'repuesto_id', 'cantidad', 'precio_unitario',
    ];
}
