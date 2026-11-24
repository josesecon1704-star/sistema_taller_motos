<?php

namespace App\Http\Controllers;

use App\Models\Garantia;
use App\Models\Notificacion;
use App\Models\ReclamoGarantia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

/** CU24 - Registrar reclamo de garantía. */
class ReclamoGarantiaController extends Controller
{
    public const ESTADOS = ['pendiente', 'aprobado', 'rechazado'];

    public static function middleware(): array
    {
        return [
            new Middleware('permiso:garantias,crear', only: ['store']),
            new Middleware('permiso:garantias,modificar', only: ['cambiarEstado']),
        ];
    }

    public function store(Request $request, Garantia $garantia): RedirectResponse
    {
        $request->validate([
            'descripcion_problema' => 'required|string|max:255',
            'km'                   => 'nullable|integer|min:0',
        ]);

        if ($garantia->estado !== 'vigente') {
            return back()->withErrors(['error' => 'No se puede registrar el reclamo porque la garantía no está vigente.']);
        }

        if (now()->greaterThan($garantia->fechaVencimiento())) {
            $garantia->update(['estado' => 'vencida']);

            return back()->withErrors(['error' => 'El plazo de cobertura (' . $garantia->dias . ' días) ya venció.']);
        }

        // Kilómetros recorridos desde la entrega de la orden
        $garantia->load('ordenTrabajo');
        $kmReclamo = $request->input('km');
        $kmIngreso = (int) $garantia->ordenTrabajo?->km_ingreso;

        if (! is_null($kmReclamo) && $kmIngreso && ((int) $kmReclamo - $kmIngreso) > (int) $garantia->km) {
            return back()->withErrors(['error' => 'El reclamo excede el límite de ' . $garantia->km . ' km de la garantía.']);
        }

        ReclamoGarantia::create([
            'garantia_id' => $garantia->id,
            'descripcion' => $request->descripcion_problema,
            'fecha'       => now(),
            'km'          => (int) ($kmReclamo ?? 0),
            'estado'      => 'pendiente',
        ]);

        return back()->with('success', 'Reclamo de garantía registrado.');
    }

    /** Aprobar o rechazar un reclamo. */
    public function cambiarEstado(Request $request, ReclamoGarantia $reclamo): RedirectResponse
    {
        $request->validate(['estado' => ['required', Rule::in(self::ESTADOS)]]);

        $reclamo->update(['estado' => $request->estado]);

        $reclamo->load('garantia.ordenTrabajo.diagnostico.motocicleta.cliente');
        Notificacion::aCliente($reclamo->garantia?->ordenTrabajo?->cliente, 'garantia',
            "Tu reclamo de garantía fue {$request->estado}.");

        return back()->with('success', 'Reclamo actualizado.');
    }
}
