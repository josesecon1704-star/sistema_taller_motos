<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\BitacoraObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(BitacoraObserver::class)]
class Servicio extends Model
{
    protected $fillable = [
        'nombre', 'categoria', 'descripcion', 'precio_base', 'estado'
    ];

    public const CATEGORIAS = [
        'mantenimiento', 'frenos', 'motor', 'electrico', 'suspension', 'otros',
    ]   ;

    
    public function tareas()          { return $this->hasMany(TareaServicio::class); }
    public function detallesCotizacion() { return $this->hasMany(DetalleCotizacionServicio::class); }
    public function mantenimientos()  { return $this->hasMany(Mantenimiento::class); }

}
