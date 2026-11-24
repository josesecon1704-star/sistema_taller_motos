<?php

namespace App\Http\Controllers;

use App\Models\Mantenimiento;
use App\Models\Motocicleta;
use App\Models\Notificacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** CU16 - Programar mantenimiento. */
class MantenimientoController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:mantenimientos,mostrar', only: ['index']),
            new Middleware('permiso:mantenimientos,crear', only: ['create', 'store']),
            new Middleware('permiso:mantenimientos,modificar', only: ['update']),
        ];
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        // Los pendientes cuya fecha límite ya pasó se marcan como vencidos (y se avisa al cliente)
        Mantenimiento::with('motocicleta.cliente')
            ->where('estado', 'PENDIENTE')
            ->whereDate('proxima_fecha', '<', today())
            ->get()
            ->each(function (Mantenimiento $m) {
                $m->update(['estado' => 'VENCIDO']);
                Notificacion::aCliente($m->motocicleta?->cliente, 'mantenimiento',
                    "Venció el mantenimiento \"{$m->tipo_mantenimiento}\" de tu moto {$m->motocicleta?->placa}.");
            });

        $mantenimientos = Mantenimiento::with('motocicleta.cliente')
            ->when($user->esCliente(), fn ($q) => $q->delCliente($user->cliente_id))
            ->orderBy('proxima_fecha')
            ->paginate(10);

        return view('mantenimientos.index', compact('mantenimientos'));
    }

    public function create(): View
    {
        $motocicletas = Motocicleta::with('cliente')->orderBy('marca')->get();

        return view('mantenimientos.create', compact('motocicletas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'motocicleta_id'     => ['required', 'exists:motocicletas,id'],
            'tipo_mantenimiento' => ['required', 'string', 'max:255'],
            'proximo_km'         => ['nullable', 'integer', 'min:0'],
            'proxima_fecha'      => ['required', 'date', 'after_or_equal:today'],
        ]);

        $mantenimiento = Mantenimiento::create($datos + ['estado' => 'PENDIENTE']);

        $mantenimiento->load('motocicleta.cliente');
        Notificacion::aCliente($mantenimiento->motocicleta?->cliente, 'mantenimiento',
            "Se programó \"{$mantenimiento->tipo_mantenimiento}\" para tu moto {$mantenimiento->motocicleta?->placa} hasta el "
            . $mantenimiento->proxima_fecha . '.');

        return redirect()->route('mantenimientos.index')->with('success', 'Mantenimiento preventivo programado con éxito.');
    }

    public function update(Request $request, Mantenimiento $mantenimiento): RedirectResponse
    {
        $request->validate(['estado' => ['required', Rule::in(Mantenimiento::ESTADOS)]]);

        $mantenimiento->update(['estado' => $request->estado]);

        return redirect()->route('mantenimientos.index')->with('success', 'Estado del mantenimiento actualizado.');
    }
}
