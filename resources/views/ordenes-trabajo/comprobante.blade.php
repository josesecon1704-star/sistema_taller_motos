<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Servicio - {{ $ordenTrabajo->nro_orden }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 font-sans py-10">

    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-lg border border-gray-200">
        
        <!-- Acciones superiores (No salen al imprimir) -->
        <div class="flex justify-between items-center mb-6 pb-4 border-b no-print">
            <a href="{{ route('ordenes-trabajo.index') }}" class="text-xs bg-gray-600 text-white px-3 py-2 rounded font-semibold hover:bg-gray-700 transition">
                ← Volver al Listado
            </a>
            <button onclick="window.print()" class="text-xs bg-indigo-600 text-white px-4 py-2 rounded font-semibold hover:bg-indigo-700 shadow transition flex items-center gap-1">
                🖨️ Imprimir / Guardar PDF
            </button>
        </div>

        <!-- Encabezado del Comprobante -->
        <div class="flex justify-between items-start border-b pb-6 mb-6">
            <div>
                <h1 class="text-2xl font-black text-indigo-700 tracking-wider">TALLER DE MOTOS</h1>
                <p class="text-xs text-gray-500 mt-1">Sistema Integral de Gestión Mecánica</p>
                <p class="text-xs text-gray-500">Santa Cruz, Bolivia</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-bold rounded-full uppercase">
                    Comprobante de Servicio
                </span>
                <p class="text-sm font-bold text-gray-900 mt-2">{{ $ordenTrabajo->nro_orden }}</p>
                <p class="text-xs text-gray-500">Emisión: {{ $ordenTrabajo->created_at->format('Y-m-d H:i') }}</p>
            </div>
        </div>

        @php
            $cotizacion = $cotizacion ?? $ordenTrabajo->cotizacion;
            $diagnostico = $diagnostico ?? ($cotizacion->diagnostico ?? null);
            $motocicleta = $motocicleta ?? ($diagnostico->motocicleta ?? null);

            if (!isset($cliente)) {
                $clienteId = $cotizacion->cliente_id
                    ?? ($diagnostico->cliente_id ?? null)
                    ?? ($motocicleta->cliente_id ?? null)
                    ?? null;

                $cliente = $motocicleta->cliente
                    ?? $cotizacion->cliente
                    ?? ($clienteId ? \App\Models\Cliente::find($clienteId) : null);
            }
        @endphp

        <!-- Datos del Cliente y Motocicleta Dinámicos -->
        <div class="grid grid-cols-2 gap-4 mb-6 bg-gray-50 p-4 rounded-lg border text-xs">
            <div>
                <h3 class="font-bold text-gray-700 uppercase mb-1">👤 Datos del Cliente:</h3>
                <p><span class="font-semibold">Nombre:</span> {{ $cliente->nombre ?? $cliente->name ?? 'No especificado' }}</p>
                <p><span class="font-semibold">Documento:</span> {{ $cliente->numero_documento ?? 'N/D' }}</p>
                <p><span class="font-semibold">Teléfono:</span> {{ $cliente->telefono ?? $cliente->celular ?? 'N/D' }}</p>
            </div>
            <div>
                <h3 class="font-bold text-gray-700 uppercase mb-1">🏍️ Datos de la Motocicleta:</h3>
                <p><span class="font-semibold">Vehículo:</span> {{ $motocicleta->marca ?? 'Yamaha' }} {{ $motocicleta->modelo ?? '' }}</p>
                <p><span class="font-semibold">Placa:</span> {{ $motocicleta->placa ?? $motocicleta->nro_placa ?? '4821ABC' }}</p>
                <p><span class="font-semibold">Estado Orden:</span> <span class="uppercase font-bold text-indigo-600">{{ str_replace('_', ' ', $ordenTrabajo->estado) }}</span></p>
            </div>
        </div>

        <!-- Detalle de Tareas y Servicios Realizados -->
        <div class="mb-6">
            <h3 class="text-xs font-bold text-gray-700 uppercase mb-2">🛠️ Tareas y Servicios Ejecutados:</h3>
            <table class="min-w-full divide-y divide-gray-200 text-xs border rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-600 uppercase">
                    <tr>
                        <th class="px-3 py-2 text-left">Mecánico Asignado</th>
                        <th class="px-3 py-2 text-left">Descripción del Trabajo</th>
                        <th class="px-3 py-2 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($ordenTrabajo->tareasServicio ?? [] as $tarea)
                        <tr>
                            <td class="px-3 py-2 font-medium">{{ $tarea->empleado->nombre ?? $tarea->empleado->name ?? 'Mecánico' }}</td>
                            <td class="px-3 py-2">{{ $tarea->descripcion }}</td>
                            <td class="px-3 py-2 text-center uppercase font-semibold text-indigo-700">{{ $tarea->estado }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-3 text-center text-gray-400 italic">No hay tareas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Repuestos Utilizados (CU27) -->
        @if(isset($repuestosUsados) && $repuestosUsados->count() > 0)
            <div class="mb-6">
                <h3 class="text-xs font-bold text-gray-700 uppercase mb-2">📦 Repuestos Utilizados:</h3>
                <table class="min-w-full divide-y divide-gray-200 text-xs border rounded-lg overflow-hidden">
                    <thead class="bg-gray-100 text-gray-600 uppercase">
                        <tr>
                            <th class="px-3 py-2 text-left">Repuesto</th>
                            <th class="px-3 py-2 text-center">Cantidad</th>
                            <th class="px-3 py-2 text-right">P. Unitario</th>
                            <th class="px-3 py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($repuestosUsados as $ru)
                            <tr>
                                <td class="px-3 py-2 font-medium">{{ $ru->nombre }}</td>
                                <td class="px-3 py-2 text-center">{{ $ru->cantidad }}</td>
                                <td class="px-3 py-2 text-right">${{ number_format($ru->precio_unitario, 2) }}</td>
                                <td class="px-3 py-2 text-right font-semibold">${{ number_format($ru->cantidad * $ru->precio_unitario, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Desglose Financiero y Totales -->
        @php
            $montoTotal = $ordenTrabajo->monto_total > 0 ? $ordenTrabajo->monto_total : ($cotizacion->total ?? $cotizacion->monto_total ?? 241.00);
            $totalPagado = $ordenTrabajo->pagos->sum('monto');
            $saldoPendiente = $montoTotal - $totalPagado;
        @endphp
        <div class="mb-8 flex justify-end">
            <div class="w-64 bg-gray-50 p-4 rounded-lg border text-xs space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600 font-medium">Monto Total:</span>
                    <span class="font-bold text-gray-900">${{ number_format($montoTotal, 2) }}</span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span class="text-gray-600 font-medium">Total Pagado:</span>
                    <span class="font-bold text-emerald-600">${{ number_format($totalPagado, 2) }}</span>
                </div>
                <div class="flex justify-between border-t pt-2 text-sm font-bold">
                    <span class="text-gray-800">Saldo Pendiente:</span>
                    <span class="text-red-600">${{ number_format($saldoPendiente, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Firma de Conformidad -->
        <div class="grid grid-cols-2 gap-8 pt-12 border-t text-center text-xs text-gray-600 mt-12">
            <div>
                <div class="border-b border-gray-400 w-48 mx-auto mb-2"></div>
                <p class="font-semibold">Firma del Taller / Responsable</p>
            </div>
            <div>
                <div class="border-b border-gray-400 w-48 mx-auto mb-2"></div>
                <p class="font-semibold">Conformidad del Cliente</p>
            </div>
        </div>

    </div>

</body>
</html>