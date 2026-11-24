<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Diagnostico;
use App\Models\Motocicleta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

/** CU17 - Registrar diagnóstico. */
class DiagnosticoController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:diagnosticos,mostrar', only: ['index']),
            new Middleware('permiso:diagnosticos,crear', only: ['create', 'store']),
        ];
    }

    public function index(): View
    {
        $diagnosticos = Diagnostico::with(['motocicleta.cliente', 'empleado', 'cita'])->latest()->paginate(10);

        return view('diagnosticos.index', compact('diagnosticos'));
    }

    public function create(): View
    {
        $motocicletas = Motocicleta::with('cliente')->orderBy('marca')->get();

        return view('diagnosticos.create', compact('motocicletas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'motocicleta_id'      => 'required|exists:motocicletas,id',
            'kilometraje_ingreso' => 'required|integer|min:0',
            'estado_motor'        => 'required|string|max:255',
            'notas_motor'         => 'nullable|string',
            'estado_frenos'       => 'required|string|max:255',
            'notas_frenos'        => 'nullable|string',
            'estado_electrico'    => 'required|string|max:255',
            'notas_electrico'     => 'nullable|string',
            'diagnostico_general' => 'required|string',
            'descripcion_falla'   => 'nullable|string',
        ]);

        $moto = Motocicleta::findOrFail($datos['motocicleta_id']);

        if ($datos['kilometraje_ingreso'] < (int) $moto->kilometraje) {
            return back()->withInput()->withErrors([
                'kilometraje_ingreso' => "El kilometraje no puede ser menor al registrado de la moto ({$moto->kilometraje} km).",
            ]);
        }

        // La cita pendiente de esta moto (si la hay) queda atendida con este diagnóstico
        $cita = Cita::where('motocicleta_id', $moto->id)
            ->whereIn('estado', Cita::ACTIVOS)
            ->orderBy('fecha_hora')
            ->first();

        Diagnostico::create($datos + [
            'descripcion_falla' => $datos['descripcion_falla'] ?? 'Revisión y diagnóstico técnico general por sistemas',
            'fecha_diagnostico' => now()->toDateString(),
            'cita_id'           => $cita?->id,
            'empleado_id'       => $request->user()->empleado_id, // quien registra el diagnóstico
        ]);

        $cita?->update(['estado' => 'completada']);
        $moto->update(['kilometraje' => $datos['kilometraje_ingreso']]);

        return redirect()->route('diagnosticos.index')->with('success', 'Diagnóstico técnico registrado con éxito.');
    }
}
