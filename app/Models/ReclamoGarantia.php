<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReclamoGarantia extends Model
{
    protected $fillable = [
        'garantia_id',
        'orden_trabajo_id',
        'fecha',
        'km',
        'descripcion',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function garantia()
    {
        return $this->belongsTo(Garantia::class);
    }

    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'orden_trabajo_id');
    }

    public function ordenReparacion()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'orden_trabajo_id');
    }
}