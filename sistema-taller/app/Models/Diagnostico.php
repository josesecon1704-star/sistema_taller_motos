<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    use HasFactory;

    protected $table = 'diagnosticos';

    protected $fillable = [
        'motocicleta_id',
        'kilometraje_ingreso',
        'estado_motor',
        'notas_motor',
        'estado_frenos',
        'notas_frenos',
        'estado_electrico',
        'notas_electrico',
        'diagnostico_general',
        'fecha_diagnostico',
        'descripcion_falla',
        'cita_id',
        'empleado_id', // <-- Añadido aquí
    ];

    public function motocicleta()
    {
        return $this->belongsTo(Motocicleta::class);
    }
}
