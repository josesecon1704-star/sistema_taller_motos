<?php

namespace App\Models;

use App\Observers\BitacoraObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(BitacoraObserver::class)]
class Mantenimiento extends Model
{
    use HasFactory;

    protected $table = 'mantenimientos';

    protected $fillable = [
        'motocicleta_id',
        'tipo_mantenimiento',
        'proximo_km',
        'proxima_fecha',
        'estado',
    ];

    public function motocicleta()
    {
        return $this->belongsTo(Motocicleta::class);
    }

    public const ESTADOS = ['PENDIENTE', 'COMPLETADO', 'VENCIDO'];

    public function scopeDelCliente($query, $clienteId)
    {
        return $query->whereHas('motocicleta', fn ($m) => $m->where('cliente_id', $clienteId));
    }
}
