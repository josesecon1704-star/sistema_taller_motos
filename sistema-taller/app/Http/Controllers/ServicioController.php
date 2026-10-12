<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServicioRequest;
use App\Models\Servicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ServicioController extends Controller
{
    
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:servicios,mostrar', only: ['index']),
            new Middleware('permiso:servicios,crear', only: ['create', 'store']),
            new Middleware('permiso:servicios,modificar', only: ['edit', 'update', 'cambiarEstado']),
        ];
    }

    public function index(Request $request): View
    {
        $servicios = Servicio::query()
            ->when($request->q, fn ($q, $texto) => $q->where('nombre', 'like', "%{$texto}%"))
            ->when($request->categoria, fn ($q, $cat) => $q->where('categoria', $cat))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('servicios.index', compact('servicios'));
    }

    public function create(): View
    {
        return view('servicios.create');
    }

    public function store(ServicioRequest $request): RedirectResponse
    {
        Servicio::create($request->validated() + ['estado' => 'activo']);

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio creado correctamente.');
    }

    public function edit(Servicio $servicio): View
    {
        return view('servicios.edit', compact('servicio'));
    }

    public function update(ServicioRequest $request, Servicio $servicio): RedirectResponse
    {
        $servicio->update($request->validated());

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function cambiarEstado(Servicio $servicio): RedirectResponse
    {
        $servicio->update([
            'estado' => $servicio->estado === 'activo' ? 'inactivo' : 'activo',
        ]);

        return back()->with('success', 'Estado actualizado.');
    }

    
}
