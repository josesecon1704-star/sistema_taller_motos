<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermisosRolRequest;
use App\Models\Bitacora;
use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RolController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:roles,mostrar', only: ['index']),
            new Middleware('permiso:roles,modificar', only: ['permisos', 'actualizarPermisos']),
        ];
    }

    public function index(): View
    {
        $roles = Rol::withCount(['permisos', 'usuarios'])->orderBy('id')->get();

        return view('roles.index', compact('roles'));
    }

    public function permisos(Request $request, Rol $rol): View|RedirectResponse
    {
        if ($motivo = $this->bloqueado($request, $rol)) {
            return redirect()->route('roles.index')->with('error', $motivo);
        }

        $porModulo = Permiso::orderBy('modulo')->orderBy('accion')->get()->groupBy('modulo');
        $asignados = $rol->permisos()->pluck('permisos.id')->all();

        return view('roles.permisos', compact('rol', 'porModulo', 'asignados'));
    }

    public function actualizarPermisos(PermisosRolRequest $request, Rol $rol): RedirectResponse
    {
        if ($motivo = $this->bloqueado($request, $rol)) {
            return redirect()->route('roles.index')->with('error', $motivo);
        }

        $ids = collect($request->validated('permisos', []))->map(fn ($id) => (int) $id)->unique()->values();

        DB::transaction(function () use ($rol, $ids, $request) {
            $etiqueta = fn ($p) => [$p->id => $p->modulo . '.' . $p->accion];

            $antes   = $rol->permisos()->get()->mapWithKeys($etiqueta);
            $despues = Permiso::whereIn('id', $ids)->get()->mapWithKeys($etiqueta);

            $agregados = $despues->diffKeys($antes)->values();
            $quitados  = $antes->diffKeys($despues)->values();

            $rol->permisos()->sync($ids->all());

            // sync() no dispara los eventos del modelo: se registra a mano
            if ($agregados->isNotEmpty() || $quitados->isNotEmpty()) {
                Bitacora::create([
                    'user_id'        => $request->user()->id,
                    'accion'         => 'asignar_permisos',
                    'tabla_afectada' => 'rol_permisos',
                    'registro_id'    => $rol->id,
                    'valor_nuevo'    => json_encode(array_filter([
                        'rol'       => $rol->nombre,
                        'agregados' => $agregados->implode(', '),
                        'quitados'  => $quitados->implode(', '),
                    ]), JSON_UNESCAPED_UNICODE),
                ]);
            }
        });

        return redirect()->route('roles.index')
            ->with('success', "Permisos del rol {$rol->nombre} actualizados.");
    }

    /** El Administrador y el propio rol del usuario no se pueden modificar */
    private function bloqueado(Request $request, Rol $rol): ?string
    {
        if ($rol->nombre === 'Administrador') {
            return 'El rol Administrador tiene siempre acceso total y no se puede modificar.';
        }

        if ($rol->id === $request->user()->rol_id) {
            return 'No puedes modificar los permisos de tu propio rol.';
        }

        return null;
    }
}