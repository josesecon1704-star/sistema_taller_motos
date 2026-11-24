<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProveedorRepuestoRequest;
use App\Http\Requests\ProveedorRequest;
use App\Models\Proveedor;
use App\Models\Repuesto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProveedorController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:proveedores,mostrar', only: ['index']),
            new Middleware('permiso:proveedores,crear', only: ['create', 'store']),
            new Middleware('permiso:proveedores,modificar', only: [
                'edit', 'update', 'cambiarEstado',
                'repuestos', 'vincularRepuesto', 'actualizarVinculo', 'desvincularRepuesto',
            ]),
        ];
    }

    public function index(Request $request): View
    {
        $proveedores = Proveedor::query()
            ->withCount('repuestos')
            ->when($request->q, fn ($q, $texto) => $q->where(function ($w) use ($texto) {
                $w->where('nombre', 'like', "%{$texto}%")
                  ->orWhere('nit', 'like', "%{$texto}%");
            }))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('proveedores.index', compact('proveedores'));
    }

    public function create(): View
    {
        return view('proveedores.create');
    }

    public function store(ProveedorRequest $request): RedirectResponse
    {
        Proveedor::create($request->validated() + ['estado' => 'activo']);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    public function edit(Proveedor $proveedor): View
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(ProveedorRequest $request, Proveedor $proveedor): RedirectResponse
    {
        $proveedor->update($request->validated());

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function cambiarEstado(Proveedor $proveedor): RedirectResponse
    {
        $proveedor->update([
            'estado' => $proveedor->estado === 'activo' ? 'inactivo' : 'activo',
        ]);

        return back()->with('success', 'Estado actualizado.');
    }

    // ---- Repuestos que vende el proveedor ----

    public function repuestos(Proveedor $proveedor): View
    {
        $vinculados = $proveedor->repuestos()->orderBy('nombre')->get();

        $disponibles = Repuesto::where('estado', 'activo')
            ->whereNotIn('id', $vinculados->pluck('id'))
            ->orderBy('nombre')
            ->get();

        return view('proveedores.repuestos', compact('proveedor', 'vinculados', 'disponibles'));
    }

    public function vincularRepuesto(ProveedorRepuestoRequest $request, Proveedor $proveedor): RedirectResponse
    {
        $data = $request->validated();

        if ($proveedor->repuestos()->where('repuestos.id', $data['repuesto_id'])->exists()) {
            throw ValidationException::withMessages([
                'repuesto_id' => 'Este repuesto ya está vinculado al proveedor.',
            ]);
        }

        $proveedor->repuestos()->attach($data['repuesto_id'], [
            'precio_compra'    => $data['precio_compra'],
            'codigo_proveedor' => $data['codigo_proveedor'] ?? null,
        ]);

        return back()->with('success', 'Repuesto vinculado correctamente.');
    }

    public function actualizarVinculo(ProveedorRepuestoRequest $request, Proveedor $proveedor, Repuesto $repuesto): RedirectResponse
    {
        $data = $request->validated();

        $proveedor->repuestos()->updateExistingPivot($repuesto->id, [
            'precio_compra'    => $data['precio_compra'],
            'codigo_proveedor' => $data['codigo_proveedor'] ?? null,
        ]);

        return back()->with('success', 'Datos del repuesto actualizados.');
    }

    public function desvincularRepuesto(Proveedor $proveedor, Repuesto $repuesto): RedirectResponse
    {
        $proveedor->repuestos()->detach($repuesto->id);

        return back()->with('success', 'Repuesto desvinculado.');
    }
}
