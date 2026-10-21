<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:usuarios,mostrar', only: ['index']),
            new Middleware('permiso:usuarios,modificar', only: ['edit', 'update', 'cambiarEstado']),
        ];
    }

    public function index(Request $request): View
    {
        $usuarios = User::with(['rol', 'empleado', 'cliente'])
            ->when($request->q, function ($query, $texto) {
                $query->where(function ($w) use ($texto) {
                    $w->where('email', 'like', "%{$texto}%")
                        ->orWhereHas('empleado', fn ($e) => $e->where('nombre', 'like', "%{$texto}%"))
                        ->orWhereHas('cliente', fn ($c) => $c->where('nombre', 'like', "%{$texto}%"));
                });
            })
            ->when($request->rol_id, fn ($q, $v) => $q->where('rol_id', $v))
            ->when($request->estado, fn ($q, $v) => $q->where('estado', $v))
            ->orderBy('email')
            ->paginate(15)
            ->withQueryString();

        return view('usuarios.index', [
            'usuarios' => $usuarios,
            'roles' => Rol::orderBy('nombre')->get(),
        ]);
    }

    public function edit(User $usuario): View|RedirectResponse
    {
        if ($usuario->id === Auth::id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Tu propia cuenta se edita desde "Mi perfil".');
        }

        return view('usuarios.edit', ['usuario' => $usuario->load(['rol', 'empleado', 'cliente'])]);
    }

    public function update(UsuarioRequest $request, User $usuario): RedirectResponse
    {
        if ($usuario->id === Auth::id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Tu propia cuenta se edita desde "Mi perfil".');
        }

        $datos = $request->validated();

        $this->protegerUltimoAdministrador($usuario, $datos['estado']);

        DB::transaction(function () use ($usuario, $datos) {
            $usuario->email = $datos['email'];
            $usuario->estado = $datos['estado'];

            if (! empty($datos['password'])) {
                $usuario->password = $datos['password'];   // se cifra sola (cast hashed)
            }

            $usuario->save();

            // El empleado y su cuenta comparten el mismo estado
            $usuario->empleado?->update(['estado' => $datos['estado']]);
        });

        if ($datos['estado'] === 'inactivo') {
            $this->cerrarSesionesDe($usuario);
        }

        return redirect()->route('usuarios.index')->with('success', 'Cuenta actualizada correctamente.');
    }

    public function cambiarEstado(User $usuario): RedirectResponse
    {
        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $nuevo = $usuario->estado === 'activo' ? 'inactivo' : 'activo';

        $this->protegerUltimoAdministrador($usuario, $nuevo);

        DB::transaction(function () use ($usuario, $nuevo) {
            $usuario->update(['estado' => $nuevo]);
            $usuario->empleado?->update(['estado' => $nuevo]);
        });

        if ($nuevo === 'inactivo') {
            $this->cerrarSesionesDe($usuario);
        }

        return back()->with('success', $nuevo === 'activo' ? 'Cuenta activada.' : 'Cuenta desactivada.');
    }

    /** Siempre debe quedar al menos un administrador activo. */
    private function protegerUltimoAdministrador(User $usuario, string $nuevoEstado): void
    {
        $adminId = Rol::where('nombre', 'Administrador')->value('id');

        if ($usuario->rol_id === $adminId && $usuario->estado === 'activo' && $nuevoEstado !== 'activo') {
            $otros = User::where('rol_id', $adminId)
                ->where('estado', 'activo')
                ->where('id', '!=', $usuario->id)
                ->count();

            if ($otros === 0) {
                throw ValidationException::withMessages([
                    'estado' => 'Debe quedar al menos un administrador activo.',
                ]);
            }
        }
    }

    /** Si las sesiones están en la base de datos, expulsa al usuario desactivado. */
    private function cerrarSesionesDe(User $usuario): void
    {
        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))->where('user_id', $usuario->id)->delete();
        }
    }
}