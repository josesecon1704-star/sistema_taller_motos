<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Repuesto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CU07 - Visualizar notificaciones.
 * Cada usuario ve solo sus notificaciones (las crean los demás módulos al ocurrir
 * un evento). El personal que maneja repuestos ve además las alertas de stock bajo.
 */
class NotificacionController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $notificaciones = Notificacion::where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        $stockBajo = $user->tienePermiso('repuestos', 'mostrar')
            ? Repuesto::where('estado', 'activo')->whereColumn('stock_actual', '<=', 'stock_minimo')->orderBy('nombre')->get()
            : collect();

        // Al abrir la pantalla, las notificaciones mostradas quedan como leídas.
        // (La vista recibe los objetos ya cargados, así que aún las muestra como nuevas.)
        Notificacion::where('user_id', $user->id)->where('leida', false)
            ->whereIn('id', $notificaciones->pluck('id'))
            ->update(['leida' => true]);

        return view('notifications.index', compact('notificaciones', 'stockBajo'));
    }

    public function leerTodas(Request $request): RedirectResponse
    {
        Notificacion::where('user_id', $request->user()->id)->where('leida', false)->update(['leida' => true]);

        return back()->with('success', 'Todas las notificaciones quedaron como leídas.');
    }
}
