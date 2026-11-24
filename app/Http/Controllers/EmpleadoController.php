<?php

namespace App\Http\Controllers;

use App\Http\Requests\CuentaClienteRequest;
use App\Http\Requests\EmpleadoRequest;
use App\Models\Empleado;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EmpleadoController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:empleados,mostrar', only: ['index', 'show']),
            new Middleware('permiso:empleados,crear', only: ['create', 'store']),
            new Middleware('permiso:empleados,modificar', only: ['edit', 'update', 'cambiarEstado']),
            // La cuenta de acceso es un permiso del módulo de usuarios
            new Middleware('permiso:usuarios,crear', only: ['cuenta', 'guardarCuenta']),
        ];
    }

    public function index(Request $request): View
    {
        $empleados = Empleado::query()
            ->withExists('usuario')
            ->when($request->q, fn ($q, $texto) => $q->where(function ($w) use ($texto) {
                $w->where('nombre', 'like', "%{$texto}%")
                  ->orWhere('ci', 'like', "%{$texto}%");
            }))
            ->when($request->cargo, fn ($q, $cargo) => $q->where('cargo', $cargo))
            ->when($request->estado, fn ($q, $estado) => $q->where('estado', $estado))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('empleados.index', compact('empleados'));
    }

    public function create(): View
    {
        return view('empleados.create');
    }

    public function store(EmpleadoRequest $request): RedirectResponse
    {
        $this->validarCargo($request);

        $empleado = Empleado::create($request->validated() + ['estado' => 'activo']);

        return redirect()->route('empleados.show', $empleado)
            ->with('success', 'Empleado registrado correctamente.');
    }

    public function show(Empleado $empleado): View
    {
        $empleado->load('usuario');

        return view('empleados.show', compact('empleado'));
    }

    public function edit(Empleado $empleado): View
    {
        return view('empleados.edit', compact('empleado'));
    }

    public function update(EmpleadoRequest $request, Empleado $empleado): RedirectResponse
    {
        $this->validarCargo($request, $empleado);

        DB::transaction(function () use ($request, $empleado) {
            $empleado->update($request->validated());

            // Si tiene cuenta, su rol sigue al cargo
            if ($empleado->usuario) {
                $empleado->usuario->update([
                    'rol_id' => Rol::where('nombre', Empleado::ROL_POR_CARGO[$empleado->cargo])->value('id'),
                ]);
            }
        });

        return redirect()->route('empleados.show', $empleado)
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function cambiarEstado(Request $request, Empleado $empleado): RedirectResponse
    {
        // Nadie puede desactivarse a sí mismo
        if ($empleado->usuario && $empleado->usuario->id === $request->user()->id) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        DB::transaction(function () use ($empleado) {
            $nuevo = $empleado->estado === 'activo' ? 'inactivo' : 'activo';

            $empleado->update(['estado' => $nuevo]);
            // La cuenta de acceso sigue el mismo estado
            $empleado->usuario?->update(['estado' => $nuevo]);
        });

        return back()->with('success', 'Estado actualizado.');
    }

    // ---- Cuenta de acceso del empleado ----

    public function cuenta(Empleado $empleado): View|RedirectResponse
    {
        if ($empleado->usuario) {
            return redirect()->route('empleados.show', $empleado)
                ->with('error', 'Este empleado ya tiene una cuenta de acceso.');
        }

        return view('empleados.cuenta', compact('empleado'));
    }

    public function guardarCuenta(CuentaClienteRequest $request, Empleado $empleado): RedirectResponse
    {
        if ($empleado->usuario) {
            return redirect()->route('empleados.show', $empleado)
                ->with('error', 'Este empleado ya tiene una cuenta de acceso.');
        }

        User::create([
            'email'       => $request->email,
            'password'    => $request->password,   // se cifra sola (cast hashed)
            'estado'      => $empleado->estado,
            'rol_id'      => Rol::where('nombre', Empleado::ROL_POR_CARGO[$empleado->cargo])->value('id'),
            'empleado_id' => $empleado->id,
        ]);

        return redirect()->route('empleados.show', $empleado)
            ->with('success', 'Cuenta creada. Entrégale el correo y la contraseña al empleado.');
    }

    /**
     * Solo quien puede modificar usuarios (el administrador) puede:
     *  - asignar el cargo de administrador
     *  - cambiar el cargo de un empleado que ya tiene cuenta
     * Así el jefe de taller no puede darse permisos de administrador.
     */
    private function validarCargo(Request $request, ?Empleado $empleado = null): void
    {
        $cargoCambia = ! $empleado || $empleado->cargo !== $request->cargo;

        if (! $cargoCambia || $request->user()->tienePermiso('usuarios', 'modificar')) {
            return;
        }

        if ($request->cargo === 'administrador' || $empleado?->usuario) {
            throw ValidationException::withMessages([
                'cargo' => 'Solo el administrador puede asignar ese cargo o cambiar el cargo de alguien con cuenta.',
            ]);
        }
    }
}
