<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\OrdenTrabajo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function store(Request $request, OrdenTrabajo $ordenTrabajo)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'metodo' => 'required|string|in:efectivo,qr,transferencia',
            'referencia' => 'nullable|string|max:255',
        ]);

        // Calcular total de la orden
        $montoTotal = $ordenTrabajo->monto_total > 0 
            ? $ordenTrabajo->monto_total 
            : ($ordenTrabajo->cotizacion->total ?? $ordenTrabajo->cotizacion->monto_total ?? 241.00);

        // Calcular lo que ya se ha pagado
        $totalPagado = $ordenTrabajo->pagos()->sum('monto');
        $saldoPendiente = $montoTotal - $totalPagado;

        // Validar que no pague más de lo debido
        if ($request->monto > $saldoPendiente) {
            return redirect()->back()->withErrors(['error' => 'El monto ingresado excede el saldo pendiente ($ ' . number_format($saldoPendiente, 2) . ').']);
        }

        DB::transaction(function () use ($request, $ordenTrabajo) {
            // Generar número de comprobante único
            $comprobante = $request->referencia 
                ? 'COMP-' . strtoupper($request->referencia) 
                : 'COMP-' . strtoupper(uniqid());

            Pago::create([
                'orden_trabajo_id' => $ordenTrabajo->id,
                'tipo' => 'ingreso', // <--- Añadido para cumplir con la columna obligatoria 'tipo'
                'monto' => $request->monto,
                'metodo' => $request->metodo,
                'fecha_pago' => now(),
                'nro_comprobante' => $comprobante,
                'observaciones' => $request->referencia ? 'Ref: ' . $request->referencia : 'Pago en ' . $request->metodo,
            ]);

            // Si se cubrió el total, actualizar el estado de la orden
            $nuevoTotalPagado = $ordenTrabajo->pagos()->sum('monto');
            $montoTotalActual = $ordenTrabajo->monto_total > 0 ? $ordenTrabajo->monto_total : 241.00;

            if ($nuevoTotalPagado >= $montoTotalActual) {
                $ordenTrabajo->estado = 'completada';
                $ordenTrabajo->save();
            }
        });

        return redirect()->back()->with('success', 'Pago registrado correctamente.');
    }
}
