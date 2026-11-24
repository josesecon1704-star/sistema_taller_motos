<?php

namespace App\Http\Controllers;

use App\Models\Mantenimiento;
use App\Models\Motocicleta;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MantenimientoController extends Controller
{
    public function index()
    {
        // Actualizar automáticamente a VENCIDO si la fecha límite ya pasó
        $mantenimientos = Mantenimiento::with('motocicleta')->get();
        
        foreach ($mantenimientos as $mant) {
            if ($mant->estado == 'PENDIENTE' && Carbon::now()->greaterThan($mant->proxima_fecha)) {
                $mant->update(['estado' => 'VENCIDO']);
            }
        }

        $mantenimientos = Mantenimiento::with('motocicleta')->orderBy('proxima_fecha', 'asc')->paginate(10);
        return view('mantenimientos.index', compact('mantenimientos'));
    }

    public function create()
    {
        $motocicletas = Motocicleta::all();
        return view('mantenimientos.create', compact('motocicletas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'motocicleta_id' => 'required|exists:motocicletas,id',
            'tipo_mantenimiento' => 'required|string|max:255',
            'proximo_km' => 'nullable|integer|min:0',
            'proxima_fecha' => 'required|date',
        ]);

        Mantenimiento::create([
            'motocicleta_id' => $request->motocicleta_id,
            'tipo_mantenimiento' => $request->tipo_mantenimiento,
            'proximo_km' => $request->proximo_km,
            'proxima_fecha' => $request->proxima_fecha,
            'estado' => 'PENDIENTE',
        ]);

        return redirect()->route('mantenimientos.index')->with('success', '¡Mantenimiento preventivo programado con éxito!');
    }

    public function update(Request $request, Mantenimiento $mantenimiento)
    {
        $mantenimiento->update(['estado' => $request->estado]);
        return redirect()->route('mantenimientos.index')->with('success', '¡Estado de mantenimiento actualizado!');
    }
}
