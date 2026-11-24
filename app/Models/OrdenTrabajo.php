<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenTrabajo extends Model
{
    protected $table = 'orden_trabajos';

    protected $fillable = [
        'nro_orden', 
        'cotizacion_id', 
        'diagnostico_id', 
        'tipo', 
        'fecha_ingreso', 
        'fecha_entrega_estimada',
        'fecha_entrega_real', 
        'km_ingreso', 
        'estado',
        'monto_total',
        'fecha_inicio',
        'total_mano_obra', 
        'total_repuestos', 
        'total_final', 
        'observaciones',
    ];

    // Relación belongsTo en singular
    public function cotizacion() 
    { 
        return $this->belongsTo(Cotizacion::class, 'cotizacion_id'); 
    }

    // Relación requerida por el controlador y la vista del CU24
    public function tareasServicio() 
    { 
        return $this->hasMany(TareaServicio::class, 'orden_trabajo_id'); 
    }

    // Alias por compatibilidad si lo usas en otros módulos
    public function tareas()         
    { 
        return $this->hasMany(TareaServicio::class, 'orden_trabajo_id'); 
    }

    public function usosRepuesto() { return $this->hasMany(UsoRepuesto::class, 'orden_trabajo_id'); }
    public function pagos()          { return $this->hasMany(Pago::class, 'orden_trabajo_id'); }
    public function garantia()     { return $this->hasOne(Garantia::class, 'orden_trabajo_id'); }
    public function reclamoOrigen(){ return $this->hasOne(ReclamoGarantia::class, 'orden_trabajo_id'); }
}
