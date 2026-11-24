<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovimientoRequest;
use App\Http\Requests\RepuestoRequest;
use App\Models\MovimientoInventario;
use App\Models\Proveedor;
use App\Models\Repuesto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RepuestoController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:repuestos,mostrar', only: ['index']),
            new Middleware('permiso:repuestos,crear', only: ['create', 'store']),
            new Middleware('permiso:repuestos,modificar',
                only: ['edit', 'update', 'cambiarEstado', 'movimiento', 'registrarMovimiento']),
        ];
    }

    public function index(Request $request): View
    {
        $repuestos = Repuesto::query()
            ->when($request->q, fn ($q, $texto) => $q->where(function ($w) use ($texto) {
                $w->where('nombre', 'like', "%{$texto}%")
                  ->orWhere('codigo', 'like', "%{$texto}%");
            }))
            ->when($request->categoria, fn ($q, $cat) => $q->where('categoria', $cat))
            ->when($request->boolean('stock_bajo'), fn ($q) => $q->whereColumn('stock_actual', '<=', 'stock_minimo'))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('repuestos.index', compact('repuestos'));
    }

    public function create(): View
    {
        return view('repuestos.create');
    }

    public function store(RepuestoRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $repuesto = Repuesto::create($request->validated() + ['estado' => 'activo']);

            // El stock inicial queda registrado como movimiento
            if ($repuesto->stock_actual > 0) {
                MovimientoInventario::create([
                    'repuesto_id'      => $repuesto->id,
                    'tipo'             => 'entrada',
                    'motivo'           => 'ajuste',
                    'cantidad'         => $repuesto->stock_actual,
                    'fecha_movimiento' => now(),
                    'observaciones'    => 'Stock inicial',
                ]);
            }
        });

        return redirect()->route('repuestos.index')
            ->with('success', 'Repuesto creado correctamente.');
    }

    public function edit(Repuesto $repuesto): View
    {
        return view('repuestos.edit', compact('repuesto'));
    }

    public function update(RepuestoRequest $request, Repuesto $repuesto): RedirectResponse
    {
        $repuesto->update($request->validated());

        return redirect()->route('repuestos.index')
            ->with('success', 'Repuesto actualizado correctamente.');
    }

    public function cambiarEstado(Repuesto $repuesto): RedirectResponse
    {
        $repuesto->update([
            'estado' => $repuesto->estado === 'activo' ? 'inactivo' : 'activo',
        ]);

        return back()->with('success', 'Estado actualizado.');
    }



    public function movimiento(Repuesto $repuesto): View
{
    $historial = $repuesto->movimientos()
        ->with('proveedor')
        ->latest('fecha_movimiento')
        ->latest('id')
        ->limit(15)
        ->get();

    $proveedores = Proveedor::where('estado', 'activo')->orderBy('nombre')->get();

    return view('repuestos.movimiento', compact('repuesto', 'historial', 'proveedores'));
}

public function registrarMovimiento(MovimientoRequest $request, Repuesto $repuesto): RedirectResponse
{
    $data = $request->validated();

    [$tipo, $motivo] = MovimientoInventario::MOVIMIENTOS[$data['movimiento']];
    $esCompra = $data['movimiento'] === 'compra';;

    DB::transaction(function () use ($repuesto, $data, $tipo, $motivo, $esCompra) {
        // Bloquea la fila mientras se actualiza, para que dos movimientos
        // simultáneos no se pisen
        $actual = Repuesto::whereKey($repuesto->id)->lockForUpdate()->first();

        if ($tipo === 'salida' && $data['cantidad'] > $actual->stock_actual) {
            throw ValidationException::withMessages([
                'cantidad' => "Solo hay {$actual->stock_actual} unidades en stock.",
            ]);
        }

        MovimientoInventario::create([
            'repuesto_id'      => $actual->id,
            'proveedor_id'     => $esCompra ? ($data['proveedor_id'] ?? null) : null,
            'tipo'             => $tipo,
            'motivo'           => $motivo,
            'cantidad'         => $data['cantidad'],
            'costo_unitario'   => $esCompra ? ($data['costo_unitario'] ?? null) : null,
            'fecha_movimiento' => now(),
            'observaciones'    => $data['observaciones'] ?? null,
        ]);

        $tipo === 'entrada'
            ? $actual->increment('stock_actual', $data['cantidad'])
            : $actual->decrement('stock_actual', $data['cantidad']);
    });

    return redirect()->route('repuestos.movimiento', $repuesto)
        ->with('success', 'Movimiento registrado correctamente.');
    }
}
