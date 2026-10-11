<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $fillable = [
        'nombre', 'nit', 'telefono', 'email', 'direccion', 'estado',
    ];

    public function movimientos() { return $this->hasMany(MovimientoInventario::class); }
    public function repuestos()
    {
        return $this->belongsToMany(Repuesto::class, 'proveedor_repuestos')
            ->withPivot('precio_compra', 'codigo_proveedor')
            ->withTimestamps();
    }
}
