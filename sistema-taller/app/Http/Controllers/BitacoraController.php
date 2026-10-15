<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BitacoraController extends Controller
{
    public function index(Request $request): View
    {
        $registros = Bitacora::query()
            ->with(['user.empleado', 'user.cliente'])
            ->when($request->user_id, fn ($q, $id) => $q->where('user_id', $id))
            ->when($request->accion, fn ($q, $a) => $q->where('accion', $a))
            ->when($request->tabla, fn ($q, $t) => $q->where('tabla_afectada', $t))
            ->when($request->desde, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->hasta, fn ($q, $h) => $q->whereDate('created_at', '<=', $h))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('bitacora.index', [
            'registros' => $registros,
            'usuarios'  => User::orderBy('email')->get(['id', 'email']),
            'acciones'  => Bitacora::distinct()->orderBy('accion')->pluck('accion'),
            'tablas'    => Bitacora::whereNotNull('tabla_afectada')->distinct()->orderBy('tabla_afectada')->pluck('tabla_afectada'),
        ]);
    }
}