<?php

namespace App\Models;

use App\Observers\BitacoraObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(BitacoraObserver::class)]
class Cita extends Model
{
    protected $fillable = [
        'cliente_id',
        'motocicleta_id',
        'empleado_id',
        'fecha_hora',
        'motivo',
        'estado',
        'observaciones',
    ];

    public const ESTADOS = [
        'programada' => 'Programada',
        'confirmada' => 'Confirmada',
        'completada' => 'Completada',
        'cancelada'  => 'Cancelada',
        'no_asistio' => 'No asistió',
    ];

    // Estados en los que la cita sigue pendiente de atención
    public const ACTIVOS = ['programada', 'confirmada'];

    // A qué estados puede pasar una cita desde cada estado
    public const TRANSICIONES = [
        'programada' => ['confirmada', 'cancelada'],
        'confirmada' => ['completada', 'no_asistio', 'cancelada'],
        'completada' => [],
        'cancelada'  => [],
        'no_asistio' => [],
    ];

    // Máximo de citas activas en la misma fecha y hora
    public const CUPO_POR_HORARIO = 3;

    protected function casts(): array
    {
        return ['fecha_hora' => 'datetime'];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function motocicleta()
    {
        return $this->belongsTo(Motocicleta::class);
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class);
    }

    public function scopeDelCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function esActiva(): bool
    {
        return in_array($this->estado, self::ACTIVOS, true);
    }

    public function puedeCambiarA(string $estado): bool
    {
        return in_array($estado, self::TRANSICIONES[$this->estado] ?? [], true);
    }
}
