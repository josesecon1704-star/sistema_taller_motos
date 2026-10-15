<?php

namespace App\Http\Controllers;

use App\Http\Requests\MotocicletaRequest;
use App\Models\Cliente;
use App\Models\Motocicleta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class MotocicletaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:motocicletas,mostrar', only: ['index', 'show']),
            new Middleware('permiso:motocicletas,crear', only: ['create', 'store']),
            new Middleware('permiso:motocicletas,modificar', only: ['edit', 'update']),
        ];
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $motos = Motocicleta::query()
            ->with('cliente')
            // Un cliente solo ve sus propias motos
            ->when($user->esCliente(), fn ($q) => $q->where('cliente_id', $user->cliente_id))
            ->when($request->q, fn ($q, $texto) => $q->where(function ($w) use ($texto) {
                $w->where('placa', 'like', "%{$texto}%")
                  ->orWhere('nro_chasis', 'like', "%{$texto}%")
                  ->orWhere('marca', 'like', "%{$texto}%")
                  ->orWhere('modelo', 'like', "%{$texto}%")
                  ->orWhereHas('cliente', fn ($c) => $c
                      ->where('nombre', 'like', "%{$texto}%")
                      ->orWhere('numero_documento', 'like', "%{$texto}%"));
            }))
            ->orderBy('marca')
            ->orderBy('modelo')
            ->paginate(10)
            ->withQueryString();

        return view('motocicletas.index', compact('motos'));
    }

    public function create(Request $request): View
    {
        return view('motocicletas.create', ['clientes' => $this->clientesParaElegir($request)]);
    }

    public function store(MotocicletaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Un cliente solo puede registrar motos a su nombre
        if ($request->user()->esCliente()) {
            $data['cliente_id'] = $request->user()->cliente_id;
        }

        $moto = Motocicleta::create($data);

        return redirect()->route('motocicletas.show', $moto)
            ->with('success', 'Motocicleta registrada correctamente.');
    }

    public function show(Request $request, Motocicleta $motocicleta): View
    {
        $this->autorizarAcceso($request, $motocicleta);

        $motocicleta->load('cliente');

        return view('motocicletas.show', ['moto' => $motocicleta]);
    }

    public function edit(Request $request, Motocicleta $motocicleta): View
    {
        $this->autorizarAcceso($request, $motocicleta);

        return view('motocicletas.edit', [
            'moto'     => $motocicleta,
            'clientes' => $this->clientesParaElegir($request),
        ]);
    }

    public function update(MotocicletaRequest $request, Motocicleta $motocicleta): RedirectResponse
    {
        $this->autorizarAcceso($request, $motocicleta);

        $data = $request->validated();

        // Solo el personal puede cambiar de dueño a una moto
        if ($request->user()->esCliente()) {
            unset($data['cliente_id']);
        }

        $motocicleta->update($data);

        return redirect()->route('motocicletas.show', $motocicleta)
            ->with('success', 'Motocicleta actualizada correctamente.');
    }

    /** Un cliente solo puede abrir sus propias motos; para el resto, la moto "no existe" */
    private function autorizarAcceso(Request $request, Motocicleta $moto): void
    {
        $user = $request->user();

        if ($user->esCliente() && $moto->cliente_id !== $user->cliente_id) {
            abort(404);
        }
    }

    /** El personal elige el dueño entre todos los clientes; el cliente no elige */
    private function clientesParaElegir(Request $request)
    {
        return $request->user()->esCliente()
            ? collect()
            : Cliente::orderBy('nombre')->get(['id', 'nombre', 'tipo_documento', 'numero_documento']);
    }
}