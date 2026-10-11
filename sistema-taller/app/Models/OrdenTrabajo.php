<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenTrabajo extends Model
{
    protected $fillable = [
        'nro_orden', 'tipo', 'fecha_ingreso', 'fecha_entrega_estimada',
        'fecha_entrega_real', 'km_ingreso', 'estado',
        'total_mano_obra', 'total_repuestos', 'total_final', 'observaciones',
    ];

    public function cotizaciones() { return $this->hasMany(Cotizacion::class); }
    public function tareas()       { return $this->hasMany(TareaServicio::class); }
    public function usosRepuesto() { return $this->hasMany(UsoRepuesto::class); }
    public function pagos()        { return $this->hasMany(Pago::class); }
    public function garantia()     { return $this->hasOne(Garantia::class); }
    public function reclamoOrigen(){ return $this->hasOne(ReclamoGarantia::class); }
}
