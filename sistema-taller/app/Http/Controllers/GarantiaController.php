<?php

namespace App\Http\Controllers;

use App\Models\Garantia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** CU23 - Registrar garantía (se genera al completar o entregar una orden). */
class GarantiaController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:garantias,mostrar', only: ['index']),
            new Middleware('permiso:garantias,modificar', only: ['update']),
        ];
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        // Las garantías vigentes cuyo plazo terminó pasan a vencidas
        Garantia::where('estado', 'vigente')->get()
            ->filter(fn (Garantia $g) => now()->greaterThan($g->fechaVencimiento()))
            ->each(fn (Garantia $g) => $g->update(['estado' => 'vencida']));

        $garantias = Garantia::with(['ordenTrabajo.diagnostico.motocicleta.cliente', 'reclamos'])
            ->when($user->esCliente(), fn ($q) => $q->delCliente($user->cliente_id))
            ->when($request->estado, fn ($q, $v) => $q->where('estado', $v))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('garantias.index', compact('garantias'));
    }

    public function update(Request $request, Garantia $garantia): RedirectResponse
    {
        $datos = $request->validate([
            'dias'      => 'required|integer|min:1|max:3650',
            'km'        => 'required|integer|min:1|max:65000',
            'cobertura' => 'nullable|string|max:255',
            'estado'    => ['required', Rule::in(Garantia::ESTADOS)],
        ]);

        $garantia->update($datos);

        return back()->with('success', 'Garantía actualizada correctamente.');
    }
}
