<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\OrdenTrabajo;
use App\Models\Pago;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

/** CU22 - Registrar pago (se permiten pagos parciales). */
class PagoController extends Controller
{
    public const METODOS = ['efectivo', 'qr', 'transferencia'];

    public static function middleware(): array
    {
        return [
            new Middleware('permiso:pagos,crear', only: ['store']),
        ];
    }

    public function store(Request $request, OrdenTrabajo $ordenTrabajo): RedirectResponse
    {
        $request->validate([
            'monto'      => 'required|numeric|min:0.01',
            'metodo'     => 'required|string|in:' . implode(',', self::METODOS),
            'referencia' => 'nullable|string|max:15',
        ]);

        $saldoPendiente = round($ordenTrabajo->totalACobrar() - $ordenTrabajo->pagos()->sum('monto'), 2);

        if ($saldoPendiente <= 0) {
            return back()->withErrors(['error' => 'Esta orden ya está pagada por completo.']);
        }

        if ($request->monto > $saldoPendiente) {
            return back()->withErrors(['error' => 'El monto ingresado excede el saldo pendiente (Bs ' . number_format($saldoPendiente, 2) . ').']);
        }

        $pago = DB::transaction(function () use ($request, $ordenTrabajo) {
            $ultimoId = Pago::max('id') ?? 0;

            return Pago::create([
                'orden_trabajo_id' => $ordenTrabajo->id,
                'tipo'             => 'servicio',
                'monto'            => $request->monto,
                'metodo'           => $request->metodo,
                'fecha_pago'       => now(),
                'nro_comprobante'  => 'PAG-' . date('Ym') . '-' . str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT),
                'estado'           => 'registrado',
                'observaciones'    => $request->referencia ? 'Ref: ' . $request->referencia : 'Pago en ' . $request->metodo,
            ]);
        });

        $ordenTrabajo->load('diagnostico.motocicleta.cliente');
        Notificacion::aCliente($ordenTrabajo->cliente, 'pago',
            "Registramos tu pago {$pago->nro_comprobante} de Bs " . number_format($pago->monto, 2) . " para la orden {$ordenTrabajo->nro_orden}.");

        return back()->with('success', 'Pago registrado correctamente (' . $pago->nro_comprobante . ').')
            ->with('abrir_orden_id', $ordenTrabajo->id)->with('abrir_tab', 'pagos');
    }
}
