<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $fillable = [
        'nombre', 'categoria', 'descripcion', 'precio_base', 'estado'
    ];

    
    public function tareas()          { return $this->hasMany(TareaServicio::class); }
    public function detallesCotizacion() { return $this->hasMany(DetalleCotizacionServicio::class); }
    public function mantenimientos()  { return $this->hasMany(Mantenimiento::class); }

}
