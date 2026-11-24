<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Motocicleta extends Model
{
    protected $fillable = [
        'cliente_id', 'placa', 'nro_chasis', 'marca', 'modelo', 'anio',
        'color', 'cilindrada', 'nro_motor', 'kilometraje', 'foto',
    ];
    
    public const MARCAS = [
        'Honda', 'Yamaha', 'Suzuki', 'Bajaj', 'TVS', 'KTM', 'Kawasaki',
        'Haojue', 'Zongshen', 'Loncin', 'Italika', 'AKT', 'Royal Enfield',
    ];

    protected function casts(): array
    {
        return [
            'anio'        => 'integer',
            'cilindrada'  => 'integer',
            'kilometraje' => 'integer',
        ];
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }

    public function citas(){ 
        return $this->hasMany(Cita::class); 
    }
    public function mantenimientos(){ 
        return $this->hasMany(Mantenimiento::class); 
    }
}
