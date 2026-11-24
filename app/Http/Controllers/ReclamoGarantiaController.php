<?php

namespace App\Http\Controllers;

use App\Models\Garantia;
use App\Models\ReclamoGarantia;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReclamoGarantiaController extends Controller
{
    public function store(Request $request, Garantia $garantia)
    {
        $request->validate([
            'descripcion_problema' => 'required|string|max:500',
            'km' => 'nullable|integer|min:0',
        ]);

        // Validar que la garantía exista y esté activa
        if (strtolower((string) $garantia->estado) !== 'activa') {
            return redirect()->back()->withErrors([
                'error' => 'No se puede registrar el reclamo porque la garantía no se encuentra activa.',
            ]);
        }

        // Validar vigencia por tiempo (días desde fecha_inicio)
        $diasCobertura = (int) ($garantia->dias ?? 90);
        if ($garantia->fecha_inicio && $diasCobertura > 0) {
            $fechaVencimiento = Carbon::parse($garantia->fecha_inicio)->addDays($diasCobertura);
            if (now()->greaterThan($fechaVencimiento)) {
                return redirect()->back()->withErrors([
                    'error' => 'No se puede registrar el reclamo porque el plazo de cobertura (' . $diasCobertura . ' días) ha vencido.',
                ]);
            }
        }

        // Validar límite de kilometraje si se envía un kilometraje en el reclamo
        $kmLimite = $garantia->km ?? null;
        $kmReclamo = $request->input('km');
        if (!is_null($kmLimite) && !is_null($kmReclamo) && (int) $kmReclamo > (int) $kmLimite) {
            return redirect()->back()->withErrors([
                'error' => 'No se puede registrar el reclamo porque excede el límite de kilometraje (' . $kmLimite . ' km).',
            ]);
        }

        ReclamoGarantia::create([
            'garantia_id' => $garantia->id,
            'descripcion' => $request->descripcion_problema,
            'fecha' => now(),
            'km' => (int) ($kmReclamo ?? 0),
            'estado' => 'pendiente',
        ]);

        return redirect()->back()->with('success', 'Reclamo de garantía registrado exitosamente sin costo adicional.');
    }
}