<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $fillable = [
        'user_id', 'tipo', 'mensaje', 'leida',
    ];

    protected function casts(): array
    {
        return ['leida' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Avisa a la cuenta de un cliente (si tiene cuenta). */
    public static function aCliente(?Cliente $cliente, string $tipo, string $mensaje): void
    {
        $userId = $cliente?->usuario?->id;

        if ($userId) {
            self::create(['user_id' => $userId, 'tipo' => $tipo, 'mensaje' => $mensaje]);
        }
    }

    /** Avisa a la cuenta de un empleado (si tiene cuenta). */
    public static function aEmpleado(?Empleado $empleado, string $tipo, string $mensaje): void
    {
        $userId = $empleado?->usuario?->id;

        if ($userId) {
            self::create(['user_id' => $userId, 'tipo' => $tipo, 'mensaje' => $mensaje]);
        }
    }

    /** Avisa a todas las cuentas activas de un rol (por ejemplo, 'Jefe de Taller'). */
    public static function aRol(string $rol, string $tipo, string $mensaje): void
    {
        User::where('estado', 'activo')
            ->whereHas('rol', fn ($r) => $r->where('nombre', $rol))
            ->pluck('id')
            ->each(fn ($id) => self::create(['user_id' => $id, 'tipo' => $tipo, 'mensaje' => $mensaje]));
    }
}
