<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    protected $fillable = [
        'diagnostico_id',
        'orden_trabajo_id',
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
    ];

    public function diagnostico(){
        return $this->belongsTo(Diagnostico::class);
    }

    public function ordenTrabajo(){
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function detallesServicio(){ 
        return $this->hasMany(DetalleCotizacionServicio::class); 
    }


    public function detallesRepuesto(){ 
        return $this->hasMany(DetalleCotizacionRepuesto::class); 
    }
}
