<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repuesto extends Model
{
    protected $fillable = [
        'codigo', 'nombre', 'categoria','foto', 'unidad_medida',
        'precio_venta', 'stock_actual', 'stock_minimo', 'estado',
    ];

    public function usos()               { return $this->hasMany(UsoRepuesto::class); }
    public function detallesCotizacion() { return $this->hasMany(DetalleCotizacionRepuesto::class); }
    public function movimientos()        { return $this->hasMany(MovimientoInventario::class); }

    public function proveedores(){
        return $this->belongsToMany(Proveedor::class, 'proveedor_repuestos')
            ->withPivot('precio_compra', 'codigo_proveedor')
            ->withTimestamps();
    }
}
