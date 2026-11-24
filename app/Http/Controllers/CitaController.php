<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Motocicleta;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $citas = Cita::with(['cliente', 'motocicleta'])->orderBy('fecha_hora', 'desc')->paginate(10);
        return view('citas.index', compact('citas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $motocicletas = Motocicleta::all();
        return view('citas.create', compact('motocicletas'));
    }

    /**
     * Store a newly created resource in storage (CU15).
     */
    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'Hora' => 'required',
            'motivo' => 'required|string|max:255',
            'idMotocicleta' => 'required|exists:motocicletas,id',
        ]);

        $fechaHora = $request->fecha . ' ' . $request->Hora . ':00';

        // Validar si ya hay cita a la misma hora para evitar saturación (máximo 3)
        $saturado = Cita::where('fecha_hora', $fechaHora)
                        ->where('estado', '<>', 'CANCELADA')
                        ->count();

        if ($saturado >= 3) {
            return back()->withErrors(['Hora' => 'Este horario ya está saturado en el taller (máximo 3 citas). Por favor elija otra hora.'])->withInput();
        }

        // Obtener la moto para sacar el cliente_id asociado
        $moto = Motocicleta::find($request->idMotocicleta);

        Cita::create([
            'fecha_hora' => $fechaHora,
            'motivo' => $request->motivo,
            'estado' => 'PENDIENTE',
            'cliente_id' => $moto->cliente_id ?? null,
            'motocicleta_id' => $request->idMotocicleta,
        ]);

        return redirect()->route('citas.index')->with('success', '¡Cita agendada con éxito!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cita $cita)
    {
        return view('citas.show', compact('cita'));
    }

    /**
     * Show the form for editing the specified resource (CU16 - Reprogramar/Gestionar).
     */
    public function edit(Cita $cita)
    {
        $motocicletas = Motocicleta::all();
        return view('citas.edit', compact('cita', 'motocicletas'));
    }

    /**
     * Update the specified resource in storage (CU16 - Actualizar, Reprogramar o Cancelar).
     */
    public function update(Request $request, Cita $cita)
    {
        $request->validate([
            'fecha' => 'required|date',
            'Hora' => 'required',
            'motivo' => 'required|string|max:255',
            'estado' => 'required|in:PENDIENTE,COMPLETADA,CANCELADA',
            'motocicleta_id' => 'required|exists:motocicletas,id',
        ]);

        $nuevaFechaHora = $request->fecha . ' ' . $request->Hora . ':00';

        // Validar saturación si la fecha/hora cambia y no está cancelada
        if ($cita->fecha_hora !== $nuevaFechaHora && $request->estado !== 'CANCELADA') {
            $saturado = Cita::where('fecha_hora', $nuevaFechaHora)
                            ->where('id', '<>', $cita->id)
                            ->where('estado', '<>', 'CANCELADA')
                            ->count();

            if ($saturado >= 3) {
                return back()->withErrors(['Hora' => 'Este horario ya está saturado en el taller. Elija otra hora.'])->withInput();
            }
        }

        $moto = Motocicleta::find($request->motocicleta_id);

        $cita->update([
            'fecha_hora' => $nuevaFechaHora,
            'motivo' => $request->motivo,
            'estado' => $request->estado,
            'cliente_id' => $moto->cliente_id ?? $cita->cliente_id,
            'motocicleta_id' => $request->motocicleta_id,
        ]);

        return redirect()->route('citas.index')->with('success', '¡Cita gestionada y actualizada correctamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cita $cita)
    {
        $cita->delete();
        return redirect()->route('citas.index')->with('success', '¡Cita eliminada con éxito!');
    }
}
