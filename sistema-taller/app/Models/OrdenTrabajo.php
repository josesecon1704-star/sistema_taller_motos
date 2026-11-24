<?php

namespace App\Models;

use App\Observers\BitacoraObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(BitacoraObserver::class)]
class OrdenTrabajo extends Model
{
    protected $table = 'orden_trabajos';

    protected $fillable = [
        'nro_orden',
        'cotizacion_id',
        'diagnostico_id',
        'tipo',
        'fecha_ingreso',
        'fecha_entrega_estimada',
        'fecha_entrega_real',
        'km_ingreso',
        'estado',
        'monto_total',
        'fecha_inicio',
        'total_mano_obra',
        'total_repuestos',
        'total_final',
        'observaciones',
    ];

    // Relación belongsTo en singular
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'cotizacion_id');
    }

    // Relación requerida por el controlador y la vista del CU24
    public function tareasServicio()
    {
        return $this->hasMany(TareaServicio::class, 'orden_trabajo_id');
    }

    // Alias por compatibilidad si lo usas en otros módulos
    public function tareas()
    {
        return $this->hasMany(TareaServicio::class, 'orden_trabajo_id');
    }

    public function usosRepuesto() { return $this->hasMany(UsoRepuesto::class, 'orden_trabajo_id'); }
    public function pagos()          { return $this->hasMany(Pago::class, 'orden_trabajo_id'); }
    public function garantia()     { return $this->hasOne(Garantia::class, 'orden_trabajo_id'); }
    public function reclamoOrigen(){ return $this->hasOne(ReclamoGarantia::class, 'orden_trabajo_id'); }

    public const ESTADOS = [
        'activa'     => 'Activa',
        'en_proceso' => 'En proceso',
        'completada' => 'Completada',
        'entregada'  => 'Entregada',
    ];

    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id');
    }

    /** Motocicleta de la orden (desde su diagnóstico). */
    public function getMotocicletaAttribute(): ?Motocicleta
    {
        return $this->diagnostico?->motocicleta ?? $this->cotizacion?->diagnostico?->motocicleta;
    }

    /** Cliente dueño de la motocicleta de la orden. */
    public function getClienteAttribute(): ?Cliente
    {
        return $this->motocicleta?->cliente;
    }

    /** Total a cobrar: el de la orden o, si no se guardó, el de su cotización. */
    public function totalACobrar(): float
    {
        return (float) ($this->monto_total > 0 ? $this->monto_total : ($this->cotizacion?->monto_total ?? 0));
    }

    public function scopeDelCliente($query, $clienteId)
    {
        return $query->whereHas('diagnostico.motocicleta', fn ($m) => $m->where('cliente_id', $clienteId));
    }
}
