<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Garantia extends Model
{
    protected $fillable = [
        'orden_trabajo_id',
        'cliente_id',
        'fecha_inicio',
        'dias',
        'km',
        'cobertura',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
    ];

    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function reclamos()
    {
        return $this->hasMany(ReclamoGarantia::class);
    }
}