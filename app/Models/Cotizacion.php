<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizacions'; // Indicamos la tabla correcta con 's'

    protected $fillable = [
        'nro_cotizacion',
        'version',
        'tipo',
        'fecha_emision',
        'fecha_respuesta',
        'estado',
        'total_mano_obra',
        'total_repuestos',
        'monto_total',
        'observaciones',
        'diagnostico_id',
        'orden_trabajo_id',
    ];

    // Relación con el Diagnóstico Técnico (CU18)
    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id');
    }

    // Relación con los servicios de mano de obra
    public function servicios()
    {
        return $this->hasMany(DetalleCotizacionServicio::class, 'cotizacion_id');
    }

    // Relación con los repuestos cotizados
    public function repuestos()
    {
        return $this->hasMany(DetalleCotizacionRepuesto::class, 'cotizacion_id');
    }
}
