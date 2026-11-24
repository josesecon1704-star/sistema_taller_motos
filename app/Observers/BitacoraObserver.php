<?php

namespace App\Observers;

use App\Models\Bitacora;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BitacoraObserver
{
    /** Campos que nunca se guardan en la bitácora */
    private const OCULTOS = ['password', 'remember_token', 'created_at', 'updated_at', 'ultimo_acceso'];

    public function created(Model $modelo): void
    {
        $this->registrar($modelo, 'crear', null, $modelo->getAttributes());
    }

    public function updated(Model $modelo): void
    {
        $nuevo = $this->limpiar($modelo->getChanges());

        // Si solo cambiaron campos ocultos, no hay nada que registrar
        if (empty($nuevo)) {
            return;
        }

        $anterior = array_intersect_key($this->limpiar($modelo->getOriginal()), $nuevo);

        // Si solo cambió el estado, se registra como cambio de estado
        $accion = array_keys($nuevo) === ['estado'] ? 'cambio_estado' : 'editar';

        $this->registrar($modelo, $accion, $anterior, $nuevo);
    }

    public function deleted(Model $modelo): void
    {
        $this->registrar($modelo, 'eliminar', $modelo->getAttributes(), null);
    }

    private function limpiar(array $datos): array
    {
        return array_diff_key($datos, array_flip(self::OCULTOS));
    }

    private function registrar(Model $modelo, string $accion, ?array $anterior, ?array $nuevo): void
    {
        // Los seeders y comandos de consola no se registran
        if (app()->runningInConsole()) {
            return;
        }

        $anterior = $anterior ? $this->limpiar($anterior) : null;
        $nuevo = $nuevo ? $this->limpiar($nuevo) : null;

        Bitacora::create([
            'user_id'        => Auth::id(),
            'accion'         => $accion,
            'tabla_afectada' => $modelo->getTable(),
            'registro_id'    => $modelo->getKey(),
            'valor_anterior' => $anterior ? json_encode($anterior, JSON_UNESCAPED_UNICODE) : null,
            'valor_nuevo'    => $nuevo ? json_encode($nuevo, JSON_UNESCAPED_UNICODE) : null,
        ]);
    }
}