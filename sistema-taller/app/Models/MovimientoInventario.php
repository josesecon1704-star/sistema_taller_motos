<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $fillable = [
        'repuesto_id',
        'proveedor_id',
        'uso_repuesto_id',
        'tipo',
        'motivo',
        'cantidad',
        'costo_unitario',
        'fecha_movimiento',
        'observaciones',
    ];

    public function repuesto(){
        return $this->belongsTo(Repuesto::class);
    }

    public function proveedor(){
        return $this->belongsTo(Proveedor::class);
    }

    public function usoRepuesto(){
        return $this->belongsTo(UsoRepuesto::class);
    }
}
