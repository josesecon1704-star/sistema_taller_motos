<?php

namespace App\Models;

use App\Observers\BitacoraObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(BitacoraObserver::class)]
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
