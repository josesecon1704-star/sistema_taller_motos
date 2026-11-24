<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
