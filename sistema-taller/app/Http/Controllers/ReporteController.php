<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Mantenimiento;
use App\Models\OrdenTrabajo;
use App\Models\Pago;
use App\Models\Repuesto;
use App\Models\TareaServicio;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/** CU25 - Consultar reportes (se calculan con los datos de los demás módulos). */
class ReporteController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:finanzas,mostrar', only: ['index']),
        ];
    }

    public function index(Request $request): View
    {
        $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date|after_or_equal:desde',
        ]);

        $desde = $request->filled('desde') ? Carbon::parse($request->desde)->startOfDay() : now()->startOfMonth();
        $hasta = $request->filled('hasta') ? Carbon::parse($request->hasta)->endOfDay() : now()->endOfDay();

        // 1. Ingresos del período, por método de pago
        $pagos = Pago::whereBetween('fecha_pago', [$desde, $hasta]);
        $ingresosTotal = (clone $pagos)->sum('monto');
        $ingresosPorMetodo = (clone $pagos)
            ->select('metodo', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(monto) as total'))
            ->groupBy('metodo')
            ->orderByDesc('total')
            ->get();

        // 2. Órdenes de trabajo por estado (abiertas en el período)
        $ordenesPorEstado = OrdenTrabajo::whereBetween('created_at', [$desde, $hasta])
            ->select('estado', DB::raw('COUNT(*) as cantidad'))
            ->groupBy('estado')
            ->pluck('cantidad', 'estado');

        // 3. Citas por estado (programadas en el período)
        $citasPorEstado = Cita::whereBetween('fecha_hora', [$desde, $hasta])
            ->select('estado', DB::raw('COUNT(*) as cantidad'))
            ->groupBy('estado')
            ->pluck('cantidad', 'estado');

        // 4. Servicios más realizados (tareas de las órdenes del período)
        $serviciosTop = TareaServicio::join('servicios', 'servicios.id', '=', 'tarea_servicios.servicio_id')
            ->whereBetween('tarea_servicios.created_at', [$desde, $hasta])
            ->select('servicios.nombre', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(tarea_servicios.precio_mano_obra) as total'))
            ->groupBy('servicios.nombre')
            ->orderByDesc('cantidad')
            ->limit(10)
            ->get();

        // 5. Repuestos con stock bajo (situación actual)
        $stockBajo = Repuesto::where('estado', 'activo')
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->orderBy('stock_actual')
            ->get();

        // 6. Mantenimientos vencidos o que vencen en los próximos 30 días
        $mantenimientos = Mantenimiento::with('motocicleta.cliente')
            ->whereIn('estado', ['PENDIENTE', 'VENCIDO'])
            ->whereDate('proxima_fecha', '<=', now()->addDays(30))
            ->orderBy('proxima_fecha')
            ->get();

        return view('reportes.index', compact(
            'desde', 'hasta', 'ingresosTotal', 'ingresosPorMetodo', 'ordenesPorEstado',
            'citasPorEstado', 'serviciosTop', 'stockBajo', 'mantenimientos'
        ));
    }
}
