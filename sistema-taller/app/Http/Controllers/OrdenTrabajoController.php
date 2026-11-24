<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\Empleado;
use App\Models\Garantia;
use App\Models\MovimientoInventario;
use App\Models\Notificacion;
use App\Models\OrdenTrabajo;
use App\Models\Repuesto;
use App\Models\UsoRepuesto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** CU19 - Gestionar orden de trabajo y CU20 - Registrar repuestos utilizados. */
class OrdenTrabajoController extends Controller
{
    /** Orden en que avanza una orden de trabajo. */
    private const FLUJO = ['activa', 'en_proceso', 'completada', 'entregada'];

    public static function middleware(): array
    {
        return [
            new Middleware('permiso:ordenes,mostrar', only: ['index', 'comprobante']),
            new Middleware('permiso:ordenes,crear', only: ['store']),
            new Middleware('permiso:ordenes,modificar', only: ['updateEstado', 'storeRepuesto']),
        ];
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $ordenes = OrdenTrabajo::with([
            'cotizacion',
            'diagnostico.motocicleta.cliente',
            'tareasServicio.empleado',
            'usosRepuesto.repuesto',
            'pagos',
            'garantia.reclamos',
        ])
            ->when($user->esCliente(), fn ($q) => $q->delCliente($user->cliente_id))
            ->latest()
            ->get();

        $empleados = Empleado::where('estado', 'activo')->orderBy('nombre')->get();
        $repuestosDisponibles = Repuesto::where('estado', 'activo')->orderBy('nombre')->get();

        return view('ordenes-trabajo.index', compact('ordenes', 'empleados', 'repuestosDisponibles'));
    }

    /** Se genera desde una cotización aprobada. */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'cotizacion_id' => 'required|exists:cotizacions,id',
        ]);

        $cotizacion = Cotizacion::with('diagnostico.motocicleta.cliente', 'servicios')->findOrFail($request->cotizacion_id);

        if ($cotizacion->estado !== 'aprobada') {
            return back()->withErrors(['error' => 'Solo se puede generar una orden desde una cotización aprobada.']);
        }

        if (OrdenTrabajo::where('cotizacion_id', $cotizacion->id)->exists()) {
            return back()->withErrors(['error' => 'Ya existe una orden de trabajo generada para esta cotización.']);
        }

        $orden = DB::transaction(function () use ($cotizacion) {
            $ultimoId = OrdenTrabajo::max('id') ?? 0;

            $orden = OrdenTrabajo::create([
                'nro_orden'       => 'OT-' . date('Ym') . '-' . str_pad($ultimoId + 1, 3, '0', STR_PAD_LEFT),
                'cotizacion_id'   => $cotizacion->id,
                'diagnostico_id'  => $cotizacion->diagnostico_id,
                'estado'          => 'activa',
                'fecha_ingreso'   => now(),
                'fecha_inicio'    => now(),
                'km_ingreso'      => $cotizacion->diagnostico?->kilometraje_ingreso,
                'total_mano_obra' => $cotizacion->total_mano_obra,
                'total_repuestos' => $cotizacion->total_repuestos,
                'monto_total'     => $cotizacion->monto_total,
                'total_final'     => $cotizacion->monto_total,
            ]);

            $cotizacion->update(['orden_trabajo_id' => $orden->id]);

            return $orden;
        });

        Notificacion::aCliente($cotizacion->diagnostico?->motocicleta?->cliente, 'orden',
            "Se abrió la orden {$orden->nro_orden} para tu moto {$cotizacion->diagnostico?->motocicleta?->placa}.");

        return redirect()->route('cotizaciones.index')->with('success', 'Orden de trabajo (' . $orden->nro_orden . ') generada correctamente.');
    }

    /** Cambia el estado. Al completarse o entregarse se registra la garantía (CU23). */
    public function updateEstado(Request $request, OrdenTrabajo $ordenTrabajo): RedirectResponse
    {
        $request->validate([
            'estado' => ['required', Rule::in(self::FLUJO)],
        ]);

        $nuevo = $request->estado;

        if ($ordenTrabajo->estado === 'entregada') {
            return back()->withErrors(['error' => 'Una orden entregada ya no cambia de estado.']);
        }

        DB::transaction(function () use ($ordenTrabajo, $nuevo) {
            $ordenTrabajo->estado = $nuevo;

            if ($nuevo === 'entregada') {
                $ordenTrabajo->fecha_entrega_real = now();
            }

            $ordenTrabajo->save();

            if (in_array($nuevo, ['completada', 'entregada'], true) && ! $ordenTrabajo->garantia()->exists()) {
                Garantia::create([
                    'orden_trabajo_id' => $ordenTrabajo->id,
                    'fecha_inicio'     => now(),
                    'dias'             => Garantia::DIAS_POR_DEFECTO,
                    'km'               => Garantia::KM_POR_DEFECTO,
                    'cobertura'        => 'Mano de obra y repuestos instalados en la orden ' . $ordenTrabajo->nro_orden,
                    'estado'           => 'vigente',
                ]);
            }
        });

        $ordenTrabajo->load('diagnostico.motocicleta.cliente');

        if ($nuevo === 'completada') {
            Notificacion::aCliente($ordenTrabajo->cliente, 'moto_lista',
                "Tu moto {$ordenTrabajo->motocicleta?->placa} está lista para recoger (orden {$ordenTrabajo->nro_orden}).");
        }

        return back()->with('success', 'Estado de la orden actualizado correctamente.');
    }

    /** Comprobante / reporte de servicio de la orden. */
    public function comprobante(Request $request, OrdenTrabajo $ordenTrabajo): View
    {
        $ordenTrabajo->load([
            'cotizacion',
            'diagnostico.motocicleta.cliente',
            'diagnostico.empleado',
            'tareasServicio.empleado',
            'garantia.reclamos',
            'pagos',
            'usosRepuesto.repuesto',
        ]);

        $user = $request->user();
        abort_if($user->esCliente() && (int) $ordenTrabajo->cliente?->id !== (int) $user->cliente_id, 404);

        $cotizacion = $ordenTrabajo->cotizacion;
        $diagnostico = $ordenTrabajo->diagnostico;
        $motocicleta = $ordenTrabajo->motocicleta;
        $cliente = $ordenTrabajo->cliente;

        $repuestosUsados = $ordenTrabajo->usosRepuesto->map(fn ($uso) => (object) [
            'nombre'          => $uso->repuesto?->nombre,
            'cantidad'        => $uso->cantidad,
            'precio_unitario' => $uso->precio_unitario,
        ]);

        return view('ordenes-trabajo.comprobante', compact(
            'ordenTrabajo', 'cotizacion', 'diagnostico', 'motocicleta', 'cliente', 'repuestosUsados'
        ));
    }

    /**
     * CU20 - Registrar repuestos utilizados.
     * El stock se descuenta mediante un movimiento de inventario de salida (CU21).
     */
    public function storeRepuesto(Request $request, OrdenTrabajo $ordenTrabajo): RedirectResponse
    {
        $request->validate([
            'repuesto_id' => ['required', Rule::exists('repuestos', 'id')->where('estado', 'activo')],
            'cantidad'    => 'required|integer|min:1',
        ]);

        if (in_array($ordenTrabajo->estado, ['completada', 'entregada'], true)) {
            return back()->withErrors(['error' => 'No se pueden agregar repuestos a una orden completada o entregada.']);
        }

        $error = DB::transaction(function () use ($request, $ordenTrabajo) {
            // Bloquea la fila para que dos registros a la vez no dejen el stock negativo
            $repuesto = Repuesto::lockForUpdate()->findOrFail($request->repuesto_id);
            $cantidad = (int) $request->cantidad;

            if ($repuesto->stock_actual < $cantidad) {
                return 'Stock insuficiente. Stock actual disponible: ' . $repuesto->stock_actual;
            }

            $uso = UsoRepuesto::create([
                'orden_trabajo_id' => $ordenTrabajo->id,
                'repuesto_id'      => $repuesto->id,
                'cantidad'         => $cantidad,
                'precio_unitario'  => $repuesto->precio_venta,
            ]);

            MovimientoInventario::create([
                'repuesto_id'      => $repuesto->id,
                'uso_repuesto_id'  => $uso->id,
                'tipo'             => 'salida',
                'motivo'           => 'uso_orden',
                'cantidad'         => $cantidad,
                'costo_unitario'   => $repuesto->precio_venta,
                'fecha_movimiento' => now(),
                'observaciones'    => 'Usado en la orden ' . $ordenTrabajo->nro_orden,
            ]);

            $repuesto->decrement('stock_actual', $cantidad);

            // El monto a cobrar es el de la cotización aprobada; los repuestos
            // usados solo afectan al inventario. Lo no cotizado va en una cotización adicional.

            return null;
        });

        if ($error) {
            return back()->withErrors(['error' => $error])
                ->with('abrir_orden_id', $ordenTrabajo->id)->with('abrir_tab', 'repuestos');
        }

        return back()->with('success', 'Repuesto registrado y descontado del inventario.')
            ->with('abrir_orden_id', $ordenTrabajo->id)->with('abrir_tab', 'repuestos');
    }
}
