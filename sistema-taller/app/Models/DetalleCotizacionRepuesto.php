<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCotizacionRepuesto extends Model
{
    use HasFactory;

    protected $table = 'detalle_cotizacion_repuestos';

    protected $fillable = [
        'cotizacion_id',
        'repuesto_id',
        'cantidad',
        'precio_unitario',
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'cotizacion_id');
    }

    public function repuesto()
    {
        return $this->belongsTo(Repuesto::class, 'repuesto_id');
    }
}
