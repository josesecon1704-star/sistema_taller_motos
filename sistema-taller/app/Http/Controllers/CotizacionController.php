<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\Diagnostico;
use App\Models\Servicio;
use App\Models\Repuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CotizacionController extends Controller
{
    public function index()
    {
        // Cargamos todas las relaciones necesarias para el listado y para el modal de detalle
        $cotizaciones = Cotizacion::with([
            'diagnostico.motocicleta', 
            'servicios.servicio', 
            'repuestos.repuesto'
        ])->latest()->paginate(10);

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
            'repuestos' => 'array',
        ]);

        try {
            DB::beginTransaction();

            // Calcular totales
            $totalManoObra = 0;
            if ($request->has('servicios')) {
                foreach ($request->servicios as $serv) {
                    $totalManoObra += floatval($serv['precio_mano_obra'] ?? 0);
                }
            }

            $totalRepuestos = 0;
            if ($request->has('repuestos')) {
                foreach ($request->repuestos as $rep) {
                    $totalRepuestos += (int)($rep['cantidad'] ?? 0) * floatval($rep['precio_unitario'] ?? 0);
                }
            }

            $montoTotal = $totalManoObra + $totalRepuestos;

            // Crear Cotización Principal
            $cotizacion = Cotizacion::create([
                'nro_cotizacion' => $request->nro_cotizacion,
                'version' => 1,
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
            return redirect()->route('cotizaciones.index')->with('success', 'Cotización generada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al registrar la cotización: ' . $e->getMessage()]);
        }
    }

    // Método para cambiar el estado de la cotización (CU22)
    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,aprobada,rechazada',
        ]);

        $cotizacion = Cotizacion::findOrFail($id);

        $cotizacion->update([
            'estado' => $request->estado
        ]);

        return redirect()->route('cotizaciones.index')->with('success', 'El estado de la cotización se ha actualizado a ' . strtoupper($request->estado) . '.');
    }
}
