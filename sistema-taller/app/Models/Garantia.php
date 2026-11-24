<?php

namespace App\Models;

use App\Observers\BitacoraObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(BitacoraObserver::class)]
class Garantia extends Model
{
    protected $fillable = [
        'orden_trabajo_id',
        'fecha_inicio',
        'dias',
        'km',
        'cobertura',
        'estado',
    ];

    public const ESTADOS = ['vigente', 'vencida', 'anulada'];

    // Condiciones por defecto al entregar una orden
    public const DIAS_POR_DEFECTO = 90;
    public const KM_POR_DEFECTO = 5000;

    protected $casts = [
        'fecha_inicio' => 'date',
    ];

    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function reclamos()
    {
        return $this->hasMany(ReclamoGarantia::class);
    }

    /** El cliente se obtiene a través de la orden (la tabla no guarda cliente_id). */
    public function getClienteAttribute(): ?Cliente
    {
        return $this->ordenTrabajo?->cliente;
    }

    public function fechaVencimiento()
    {
        return $this->fecha_inicio?->copy()->addDays((int) $this->dias);
    }

    public function estaVigente(): bool
    {
        return $this->estado === 'vigente'
            && $this->fecha_inicio
            && now()->lte($this->fechaVencimiento());
    }

    public function scopeDelCliente($query, $clienteId)
    {
        return $query->whereHas('ordenTrabajo', fn ($o) => $o->delCliente($clienteId));
    }
}
