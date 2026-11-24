<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Motocicleta;
use App\Models\Notificacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** CU15 - Gestionar cita (agendar, reprogramar, confirmar y cancelar). */
class CitaController extends Controller
{
    public static function middleware(): array
    {
        return [
            // cancelar: el cliente cancela las suyas; se controla dentro del método
            new Middleware('permiso:citas,mostrar', only: ['index', 'show', 'cancelar']),
            new Middleware('permiso:citas,crear', only: ['create', 'store']),
            new Middleware('permiso:citas,modificar', only: ['edit', 'update', 'cambiarEstado']),
        ];
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $citas = Cita::with(['cliente', 'motocicleta', 'empleado'])
            ->when($user->esCliente(), fn ($q) => $q->delCliente($user->cliente_id))
            ->when($request->estado, fn ($q, $v) => $q->where('estado', $v))
            ->when($request->fecha, fn ($q, $v) => $q->whereDate('fecha_hora', $v))
            ->orderByDesc('fecha_hora')
            ->paginate(10)
            ->withQueryString();

        return view('citas.index', compact('citas'));
    }

    public function create(Request $request): View
    {
        return view('citas.create', [
            'motocicletas' => $this->motosVisibles($request),
            'empleados'    => $request->user()->esCliente() ? collect() : $this->mecanicos(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $datos = $request->validate([
            'motocicleta_id' => ['required', Rule::exists('motocicletas', 'id')
                ->when($user->esCliente(), fn ($r) => $r->where('cliente_id', $user->cliente_id))],
            'fecha'          => ['required', 'date', 'after_or_equal:today'],
            'hora'           => ['required', 'date_format:H:i'],
            'motivo'         => ['required', 'string', 'max:255'],
            'empleado_id'    => ['nullable', Rule::exists('empleados', 'id')->where('estado', 'activo')],
        ], [
            'motocicleta_id.exists' => 'La motocicleta elegida no es válida.',
        ]);

        $fechaHora = Carbon::parse($datos['fecha'] . ' ' . $datos['hora']);

        if ($fechaHora->isPast()) {
            return back()->withInput()->withErrors(['hora' => 'La fecha y hora deben ser posteriores a este momento.']);
        }

        if ($error = $this->validarDisponibilidad($datos['motocicleta_id'], $fechaHora)) {
            return back()->withInput()->withErrors($error);
        }

        $moto = Motocicleta::findOrFail($datos['motocicleta_id']);

        $cita = Cita::create([
            'cliente_id'     => $moto->cliente_id,
            'motocicleta_id' => $moto->id,
            'empleado_id'    => $user->esCliente() ? null : ($datos['empleado_id'] ?? null),
            'fecha_hora'     => $fechaHora,
            'motivo'         => $datos['motivo'],
            'estado'         => 'programada',
        ]);

        Notificacion::aRol('Jefe de Taller', 'cita', "Nueva cita de {$moto->cliente?->nombre} para el {$fechaHora->format('d/m/Y H:i')}.");

        return redirect()->route('citas.show', $cita)
            ->with('success', 'Cita registrada. El taller la confirmará.');
    }

    public function show(Request $request, Cita $cita): View
    {
        $this->soloSuya($request, $cita);

        $cita->load(['cliente', 'motocicleta', 'empleado']);

        return view('citas.show', compact('cita'));
    }

    public function edit(Cita $cita): View|RedirectResponse
    {
        if (! $cita->esActiva()) {
            return redirect()->route('citas.show', $cita)
                ->with('error', 'Solo se pueden editar citas programadas o confirmadas.');
        }

        $cita->load(['cliente', 'motocicleta']);

        return view('citas.edit', ['cita' => $cita, 'empleados' => $this->mecanicos()]);
    }

    /** Reprogramar (cambiar fecha y hora), cambiar motivo o asignar mecánico. */
    public function update(Request $request, Cita $cita): RedirectResponse
    {
        if (! $cita->esActiva()) {
            return redirect()->route('citas.show', $cita)
                ->with('error', 'Solo se pueden editar citas programadas o confirmadas.');
        }

        $datos = $request->validate([
            'fecha'         => ['required', 'date'],
            'hora'          => ['required', 'date_format:H:i'],
            'motivo'        => ['required', 'string', 'max:255'],
            'empleado_id'   => ['nullable', Rule::exists('empleados', 'id')->where('estado', 'activo')],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        $fechaHora = Carbon::parse($datos['fecha'] . ' ' . $datos['hora']);
        $reprogramada = ! $fechaHora->equalTo($cita->fecha_hora);

        if ($reprogramada) {
            if ($fechaHora->isPast()) {
                return back()->withInput()->withErrors(['hora' => 'La nueva fecha y hora deben ser posteriores a este momento.']);
            }

            if ($error = $this->validarDisponibilidad($cita->motocicleta_id, $fechaHora, $cita->id)) {
                return back()->withInput()->withErrors($error);
            }
        }

        $cita->update([
            'fecha_hora'    => $fechaHora,
            'motivo'        => $datos['motivo'],
            'empleado_id'   => $datos['empleado_id'] ?? null,
            'observaciones' => $datos['observaciones'] ?? null,
        ]);

        if ($reprogramada) {
            Notificacion::aCliente($cita->cliente, 'cita', "Tu cita fue reprogramada para el {$fechaHora->format('d/m/Y H:i')}.");
        }

        return redirect()->route('citas.show', $cita)->with('success', 'Cita actualizada.');
    }

    /** Confirmar, completar, marcar "no asistió" o cancelar (personal del taller). */
    public function cambiarEstado(Request $request, Cita $cita): RedirectResponse
    {
        $request->validate(['estado' => ['required', Rule::in(array_keys(Cita::ESTADOS))]]);

        $nuevo = $request->estado;

        if (! $cita->puedeCambiarA($nuevo)) {
            return back()->with('error', 'Una cita "' . Cita::ESTADOS[$cita->estado]
                . '" no puede pasar a "' . Cita::ESTADOS[$nuevo] . '".');
        }

        $cita->update(['estado' => $nuevo]);

        if (in_array($nuevo, ['confirmada', 'cancelada'], true)) {
            Notificacion::aCliente($cita->cliente, 'cita',
                "Tu cita del {$cita->fecha_hora->format('d/m/Y H:i')} fue " . mb_strtolower(Cita::ESTADOS[$nuevo]) . '.');
        }

        return back()->with('success', 'Estado actualizado: ' . Cita::ESTADOS[$nuevo] . '.');
    }

    /** El cliente cancela sus propias citas; el personal, con permiso de modificar. */
    public function cancelar(Request $request, Cita $cita): RedirectResponse
    {
        $this->soloSuya($request, $cita);

        $user = $request->user();
        abort_if(! $user->esCliente() && ! $user->tienePermiso('citas', 'modificar'), 403);

        if (! $cita->puedeCambiarA('cancelada')) {
            return back()->with('error', 'Esta cita ya no se puede cancelar.');
        }

        $cita->update(['estado' => 'cancelada']);

        if ($user->esCliente()) {
            Notificacion::aRol('Jefe de Taller', 'cita', "{$cita->cliente?->nombre} canceló su cita del {$cita->fecha_hora->format('d/m/Y H:i')}.");
        }

        return redirect()->route('citas.index')->with('success', 'Cita cancelada.');
    }

    /** Una moto no puede tener dos citas pendientes y cada horario tiene un cupo máximo. */
    private function validarDisponibilidad(int $motoId, Carbon $fechaHora, ?int $exceptoId = null): ?array
    {
        $activas = Cita::whereIn('estado', Cita::ACTIVOS)
            ->when($exceptoId, fn ($q) => $q->where('id', '!=', $exceptoId));

        if ((clone $activas)->where('motocicleta_id', $motoId)->exists()) {
            return ['motocicleta_id' => 'Esta motocicleta ya tiene una cita pendiente. Cancélala o espera a que se atienda.'];
        }

        if ((clone $activas)->where('fecha_hora', $fechaHora)->count() >= Cita::CUPO_POR_HORARIO) {
            return ['hora' => 'Ese horario ya está lleno (máximo ' . Cita::CUPO_POR_HORARIO . ' citas). Elige otra hora.'];
        }

        return null;
    }

    /** Un cliente solo ve sus citas: las demás "no existen" para él. */
    private function soloSuya(Request $request, Cita $cita): void
    {
        $user = $request->user();

        abort_if($user->esCliente() && (int) $cita->cliente_id !== (int) $user->cliente_id, 404);
    }

    private function motosVisibles(Request $request)
    {
        $user = $request->user();

        return Motocicleta::with('cliente')
            ->when($user->esCliente(), fn ($q) => $q->where('cliente_id', $user->cliente_id))
            ->orderBy('marca')
            ->get();
    }

    private function mecanicos()
    {
        return Empleado::where('estado', 'activo')
            ->whereIn('cargo', ['mecanico', 'jefe_taller'])
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
    }
}
