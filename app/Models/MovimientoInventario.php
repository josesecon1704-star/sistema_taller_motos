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

    public const MOVIMIENTOS = [
        'compra'       => ['entrada', 'compra', 'Compra a proveedor'],
        'devolucion'   => ['entrada', 'devolucion', 'Devolución'],
        'ajuste_mas'   => ['entrada', 'ajuste', 'Ajuste (aumenta)'],
        'merma'        => ['salida', 'merma', 'Merma / pérdida'],
        'ajuste_menos' => ['salida', 'ajuste', 'Ajuste (disminuye)'],
    ];

    protected function casts(): array
    {
        return ['fecha_movimiento' => 'datetime'];
    }

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
