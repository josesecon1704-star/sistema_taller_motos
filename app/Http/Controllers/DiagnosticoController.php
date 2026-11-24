<?php

namespace App\Http\Controllers;

use App\Models\Diagnostico;
use App\Models\Motocicleta;
use App\Models\Cita;
use App\Models\Empleado;
use Illuminate\Http\Request;

class DiagnosticoController extends Controller
{
    public function index()
    {
        $diagnosticos = Diagnostico::with('motocicleta')->latest()->paginate(10);
        return view('diagnosticos.index', compact('diagnosticos'));
    }

    public function create()
    {
        $motocicletas = Motocicleta::all();
        return view('diagnosticos.create', compact('motocicletas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'motocicleta_id' => 'required|exists:motocicletas,id',
            'kilometraje_ingreso' => 'required|integer|min:0',
            'estado_motor' => 'required|string',
            'notas_motor' => 'nullable|string',
            'estado_frenos' => 'required|string',
            'notas_frenos' => 'nullable|string',
            'estado_electrico' => 'required|string',
            'notas_electrico' => 'nullable|string',
            'diagnostico_general' => 'required|string',
        ]);

        Diagnostico::create([
            'motocicleta_id' => $request->motocicleta_id,
            'kilometraje_ingreso' => $request->kilometraje_ingreso,
            'estado_motor' => $request->estado_motor,
            'notas_motor' => $request->notas_motor,
            'estado_frenos' => $request->estado_frenos,
            'notas_frenos' => $request->notas_frenos,
            'estado_electrico' => $request->estado_electrico,
            'notas_electrico' => $request->notas_electrico,
            'diagnostico_general' => $request->diagnostico_general,
            'fecha_diagnostico' => now()->toDateString(),
            'descripcion_falla' => $request->descripcion_falla ?? 'Revisión y diagnóstico técnico general por sistemas',
            'cita_id' => Cita::first()->id ?? 1,
            'empleado_id' => Empleado::first()->id ?? 1, // <-- Asignamos un empleado por defecto para PostgreSQL
        ]);

        return redirect()->route('diagnosticos.index')->with('success', '¡Diagnóstico técnico registrado con éxito!');
    }
}
