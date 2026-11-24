<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Cotizacion;
use App\Models\Diagnostico;
use App\Models\Empleado;
use App\Models\Garantia; // CU23
use App\Models\Motocicleta;
use App\Models\OrdenTrabajo;
use App\Models\Repuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdenTrabajoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Asegurar que las órdenes completadas o entregadas tengan su garantía generada (CU23)
        $ordenesSinGarantia = OrdenTrabajo::whereIn('estado', ['completada', 'entregada'])
            ->doesntHave('garantia')
            ->get();

        foreach ($ordenesSinGarantia as $ot) {
            $clienteId = $ot->cotizacion->diagnostico->motocicleta->cliente_id
                ?? 1;

            Garantia::create([
                'orden_trabajo_id' => $ot->id,
                'cliente_id' => $clienteId,
                'fecha_inicio' => now(),
                'dias' => 90,
                'km' => 5000,
                'estado' => 'activa',
            ]);
        }

        $ordenes = OrdenTrabajo::with([
            'cotizacion.diagnostico.motocicleta.cliente',
            'tareasServicio.empleado',
            'garantia.reclamos' // Cargar garantía y reclamos asociados (CU23 / CU24)
        ])->latest()->get();

        $empleados = Empleado::all();
        $repuestosDisponibles = Repuesto::where('estado', 'activo')->get(); // Selector de repuestos (CU26)

        return view('ordenes-trabajo.index', compact('ordenes', 'empleados', 'repuestosDisponibles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cotizacion_id' => 'required|exists:cotizacions,id',
        ]);

        $cotizacion = Cotizacion::findOrFail($request->cotizacion_id);

        $ordenExistente = OrdenTrabajo::where('cotizacion_id', $cotizacion->id)->first();
        if ($ordenExistente) {
            return redirect()->back()->withErrors(['error' => 'Ya existe una orden de trabajo generada para esta cotización.']);
        }

        $ultimoId = OrdenTrabajo::max('id') ?? 0;
        $nroOrden = 'OT-' . date('Ym') . '-' . str_pad($ultimoId + 1, 3, '0', STR_PAD_LEFT);

        OrdenTrabajo::create([
            'nro_orden' => $nroOrden,
            'cotizacion_id' => $cotizacion->id,
            'diagnostico_id' => $cotizacion->diagnostico_id,
            'estado' => 'activa',
            'monto_total' => $cotizacion->monto_total,
            'fecha_inicio' => now(),
        ]);

        return redirect()->route('cotizaciones.index')->with('success', 'Orden de trabajo (' . $nroOrden . ') generada correctamente.');
    }

    /**
     * Actualiza el estado de la orden de trabajo (CU25) y genera garantía automáticamente (CU23).
     */
    public function updateEstado(Request $request, OrdenTrabajo $ordenTrabajo)
    {
        $request->validate([
            'estado' => 'required|string|in:activa,en_proceso,completada,entregada',
        ]);

        $nuevoEstado = strtolower($request->estado);
        $ordenTrabajo->estado = $nuevoEstado;
        $ordenTrabajo->save();

        // CU23: Generar garantía automáticamente al completar o entregar la orden
        if (in_array($nuevoEstado, ['completada', 'entregada'])) {
            if (!$ordenTrabajo->garantia) {
                $clienteId = $ordenTrabajo->cotizacion->diagnostico->motocicleta->cliente_id
                    ?? 1;

                Garantia::create([
                    'orden_trabajo_id' => $ordenTrabajo->id,
                    'cliente_id' => $clienteId,
                    'fecha_inicio' => now(),
                    'dias' => 90,
                    'km' => 5000,
                    'estado' => 'activa',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Estado de la orden actualizado correctamente.');
    }

    /**
     * Generar la vista de reporte/comprobante de servicio finalizado (CU25 / CU27).
     */
    public function comprobante(OrdenTrabajo $ordenTrabajo)
    {
        $ordenTrabajo->load([
            'cotizacion.diagnostico.motocicleta.cliente',
            'cotizacion.diagnostico.empleado',
            'tareasServicio.empleado',
            'garantia.reclamos',
            'pagos',
        ]);

        $cotizacion = $ordenTrabajo->cotizacion;
        $diagnostico = $cotizacion->diagnostico
            ?? ($ordenTrabajo->diagnostico_id ? Diagnostico::with('motocicleta.cliente', 'cita.cliente')->find($ordenTrabajo->diagnostico_id) : null);

        $motocicleta = $diagnostico->motocicleta ?? $cotizacion->motocicleta ?? null;

        $clienteId = ($diagnostico->cliente_id ?? null)
            ?? ($diagnostico->cita->cliente_id ?? null)
            ?? ($motocicleta->cliente_id ?? null)
            ?? null;

        $cliente = $motocicleta->cliente
            ?? ($clienteId ? Cliente::find($clienteId) : null);

        if (!$cliente) {
            $motoPorPlaca = $motocicleta ?? Motocicleta::with('cliente')->where('placa', '4821ABC')->first();
            $motocicleta = $motocicleta ?? $motoPorPlaca;
            $cliente = $motocicleta?->cliente ?? Cliente::find(1);
        }

        $repuestosUsados = DB::table('uso_repuestos')
            ->join('repuestos', 'uso_repuestos.repuesto_id', '=', 'repuestos.id')
            ->where('uso_repuestos.orden_trabajo_id', $ordenTrabajo->id)
            ->select('repuestos.nombre', 'uso_repuestos.cantidad', 'uso_repuestos.precio_unitario')
            ->get();

        return view('ordenes-trabajo.comprobante', compact(
            'ordenTrabajo',
            'cotizacion',
            'diagnostico',
            'motocicleta',
            'cliente',
            'repuestosUsados'
        ));
    }

    /**
     * Registrar repuestos utilizados en la orden y descontar stock (CU26).
     */
    public function storeRepuesto(Request $request, OrdenTrabajo $ordenTrabajo)
    {
        $request->validate([
            'repuesto_id' => 'required|exists:repuestos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $repuesto = Repuesto::findOrFail($request->repuesto_id);

        // Validar stock actual suficiente en inventario
        if ($repuesto->stock_actual < $request->cantidad) {
            return redirect()->back()->withErrors(['error' => 'Stock insuficiente. Stock actual disponible: ' . $repuesto->stock_actual]);
        }

        DB::transaction(function () use ($request, $ordenTrabajo, $repuesto) {
            // 1. Descontar el stock actual del inventario
            $repuesto->stock_actual -= $request->cantidad;
            $repuesto->save();

            // 2. Registrar el uso del repuesto en la tabla uso_repuestos
            DB::table('uso_repuestos')->insert([
                'orden_trabajo_id' => $ordenTrabajo->id,
                'repuesto_id' => $repuesto->id,
                'cantidad' => $request->cantidad,
                'precio_unitario' => $repuesto->precio_venta,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->back()->with('success', 'Repuesto agregado correctamente y stock descontado del inventario.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OrdenTrabajo $ordenTrabajo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrdenTrabajo $ordenTrabajo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrdenTrabajo $ordenTrabajo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrdenTrabajo $ordenTrabajo)
    {
        //
    }
}