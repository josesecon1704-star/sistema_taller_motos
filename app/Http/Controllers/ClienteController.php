<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Http\Requests\CuentaClienteRequest;
use App\Models\Cliente;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:clientes,mostrar', only: ['index', 'show']),
            new Middleware('permiso:clientes,crear', only: ['create', 'store']),
            new Middleware('permiso:clientes,modificar', only: ['edit', 'update']),
            // Crear la cuenta de acceso es un permiso del módulo de usuarios
            new Middleware('permiso:usuarios,crear', only: ['cuenta', 'guardarCuenta']),
        ];
    }

    public function index(Request $request): View
    {
        $clientes = Cliente::query()
            ->withCount('motocicletas')
            ->withExists('usuario')
            ->when($request->q, fn ($q, $texto) => $q->where(function ($w) use ($texto) {
                $w->where('nombre', 'like', "%{$texto}%")
                  ->orWhere('numero_documento', 'like', "%{$texto}%");
            }))
            ->when($request->tipo, fn ($q, $tipo) => $q->where('tipo', $tipo))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    public function create(): View
    {
        return view('clientes.create');
    }

    public function store(ClienteRequest $request): RedirectResponse
    {
        $cliente = Cliente::create($request->validated());

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function show(Cliente $cliente): View
    {
        $cliente->load(['motocicletas', 'usuario']);

        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente): View
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(ClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $cliente->update($request->validated());

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Cliente actualizado correctamente.');
    }

    // ---- Cuenta de acceso del cliente ----

    public function cuenta(Cliente $cliente): View|RedirectResponse
    {
        if ($cliente->usuario) {
            return redirect()->route('clientes.show', $cliente)
                ->with('error', 'Este cliente ya tiene una cuenta de acceso.');
        }

        return view('clientes.cuenta', compact('cliente'));
    }

    public function guardarCuenta(CuentaClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        if ($cliente->usuario) {
            return redirect()->route('clientes.show', $cliente)
                ->with('error', 'Este cliente ya tiene una cuenta de acceso.');
        }

        User::create([
            'email'      => $request->email,
            'password'   => $request->password,   // se cifra sola (cast hashed)
            'estado'     => 'activo',
            'rol_id'     => Rol::where('nombre', 'Cliente')->value('id'),
            'cliente_id' => $cliente->id,
        ]);

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Cuenta creada. Entrégale el correo y la contraseña al cliente.');
    }
}
