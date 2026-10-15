<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\BitacoraObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(BitacoraObserver::class)]
class Cliente extends Model
{
    protected $fillable = [
        'nombre',
        'tipo_documento',
        'numero_documento',
        'telefono',
        'direccion',
        'tipo',
    ];

    public const TIPOS_DOCUMENTO = ['ci', 'nit', 'pasaporte'];

    public const TIPOS = ['particular', 'empresa'];

    public function usuario()
    {
        return $this->hasOne(User::class);
    }
    public function motocicletas()
    {
        return $this->hasMany(Motocicleta::class);
    }
    public function citas()
    {
        return $this->hasMany(Cita::class);
    }
}
