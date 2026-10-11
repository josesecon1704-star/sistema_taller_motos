<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
