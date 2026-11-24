<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\Notificacion;
use App\Models\Diagnostico;
use App\Models\Servicio;
use App\Models\Repuesto;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/** CU18 - Gestionar cotización (servicios, repuestos y cambio de estado). */
class CotizacionController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:cotizaciones,mostrar', only: ['index']),
            new Middleware('permiso:cotizaciones,crear', only: ['create', 'store']),
            // El cliente tiene "modificar" para aprobar o rechazar las suyas
            new Middleware('permiso:cotizaciones,modificar', only: ['cambiarEstado']),
        ];
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // Cargamos todas las relaciones necesarias para el listado y para el modal de detalle
        $cotizaciones = Cotizacion::with([
            'diagnostico.motocicleta',
            'servicios.servicio',
            'repuestos.repuesto',
            'ordenTrabajo',
        ])
            ->when($user->esCliente(), fn ($q) => $q->delCliente($user->cliente_id))
            ->latest()
            ->paginate(10);

        return view('cotizaciones.index', compact('cotizaciones'));
    }

    public function create()
    {
        $diagnosticos = Diagnostico::with('motocicleta')->latest()->get();
        $servicios = Servicio::all();
        $repuestos = Repuesto::all();

        // Generar un número de cotización automático (Ej: COT-202611-001)
        $ultimoId = Cotizacion::max('id') ?? 0;
        $nroCotizacion = 'COT-' . date('Ym') . '-' . str_pad($ultimoId + 1, 3, '0', STR_PAD_LEFT);

        return view('cotizaciones.create', compact('diagnosticos', 'servicios', 'repuestos', 'nroCotizacion'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'diagnostico_id' => 'required|exists:diagnosticos,id',
            'nro_cotizacion' => 'required|string|unique:cotizacions,nro_cotizacion',
            'tipo' => 'required|string',
            'fecha_emision' => 'required|date',
            'servicios' => 'array',
            'servicios.*.servicio_id' => 'nullable|exists:servicios,id',
            'servicios.*.precio_mano_obra' => 'nullable|numeric|min:0',
            'repuestos' => 'array',
            'repuestos.*.repuesto_id' => 'nullable|exists:repuestos,id',
            'repuestos.*.cantidad' => 'nullable|integer|min:1',
            'repuestos.*.precio_unitario' => 'nullable|numeric|min:0',
        ]);

        // Recotizar: cada cotización nueva de un mismo diagnóstico es una versión más
        $version = (int) Cotizacion::where('diagnostico_id', $request->diagnostico_id)->max('version') + 1;

        try {
            DB::beginTransaction();

            // Calcular totales
            $totalManoObra = 0;
            foreach ($request->input('servicios', []) as $serv) {
                if (! empty($serv['servicio_id'])) {
                    $totalManoObra += floatval($serv['precio_mano_obra'] ?? 0);
                }
            }

            $totalRepuestos = 0;
            foreach ($request->input('repuestos', []) as $rep) {
                if (! empty($rep['repuesto_id'])) {
                    $totalRepuestos += (int) ($rep['cantidad'] ?? 0) * floatval($rep['precio_unitario'] ?? 0);
                }
            }

            $montoTotal = $totalManoObra + $totalRepuestos;

            // Crear Cotización Principal
            $cotizacion = Cotizacion::create([
                'nro_cotizacion' => $request->nro_cotizacion,
                'version' => $version,
                'tipo' => $request->tipo,
                'fecha_emision' => $request->fecha_emision,
                'estado' => 'pendiente',
                'total_mano_obra' => $totalManoObra,
                'total_repuestos' => $totalRepuestos,
                'monto_total' => $montoTotal,
                'observaciones' => $request->observaciones,
                'diagnostico_id' => $request->diagnostico_id,
            ]);

            // Guardar Servicios / Mano de Obra
            if ($request->has('servicios')) {
                foreach ($request->servicios as $serv) {
                    if (!empty($serv['servicio_id'])) {
                        $cotizacion->servicios()->create([
                            'servicio_id' => $serv['servicio_id'],
                            'precio_mano_obra' => $serv['precio_mano_obra'],
                            'observaciones' => $serv['observaciones'] ?? null,
                        ]);
                    }
                }
            }

            // Guardar Repuestos
            if ($request->has('repuestos')) {
                foreach ($request->repuestos as $rep) {
                    if (!empty($rep['repuesto_id'])) {
                        $cotizacion->repuestos()->create([
                            'repuesto_id' => $rep['repuesto_id'],
                            'cantidad' => $rep['cantidad'],
                            'precio_unitario' => $rep['precio_unitario'],
                        ]);
                    }
                }
            }

            DB::commit();

            $cotizacion->load('diagnostico.motocicleta.cliente');
            Notificacion::aCliente($cotizacion->diagnostico?->motocicleta?->cliente, 'cotizacion_lista',
                "Tu cotización {$cotizacion->nro_cotizacion} está lista (Bs " . number_format($cotizacion->monto_total, 2) . '). Revísala para aprobarla o rechazarla.');

            return redirect()->route('cotizaciones.index')->with('success', 'Cotización generada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al registrar la cotización: ' . $e->getMessage()]);
        }
    }

    /** Aprobar o rechazar. El cliente solo puede responder sus cotizaciones pendientes. */
    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => ['required', Rule::in(Cotizacion::ESTADOS)],
        ]);

        $user = $request->user();
        $cotizacion = Cotizacion::with('diagnostico.motocicleta')
            ->when($user->esCliente(), fn ($q) => $q->delCliente($user->cliente_id))
            ->findOrFail($id);

        if ($user->esCliente() && ($cotizacion->estado !== 'pendiente' || $request->estado === 'pendiente')) {
            return back()->withErrors(['error' => 'Solo puedes aprobar o rechazar cotizaciones pendientes.']);
        }

        if ($cotizacion->ordenTrabajo()->exists()) {
            return back()->withErrors(['error' => 'Esta cotización ya tiene una orden de trabajo y no puede cambiar de estado.']);
        }

        $cotizacion->update([
            'estado' => $request->estado,
            'fecha_respuesta' => $request->estado === 'pendiente' ? null : now(),
        ]);

        if ($user->esCliente()) {
            Notificacion::aRol('Jefe de Taller', 'cotizacion',
                "El cliente {$request->estado} la cotización {$cotizacion->nro_cotizacion}.");
        }

        return redirect()->route('cotizaciones.index')->with('success', 'El estado de la cotización se ha actualizado a ' . strtoupper($request->estado) . '.');
    }
}
