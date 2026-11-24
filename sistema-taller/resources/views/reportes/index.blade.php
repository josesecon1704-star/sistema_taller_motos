<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reportes</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" class="bg-white shadow-sm sm:rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div>
                    <x-input-label for="desde" value="Desde" />
                    <x-text-input id="desde" type="date" name="desde" :value="$desde->format('Y-m-d')" />
                </div>
                <div>
                    <x-input-label for="hasta" value="Hasta" />
                    <x-text-input id="hasta" type="date" name="hasta" :value="$hasta->format('Y-m-d')" />
                </div>
                <x-primary-button>Ver reporte</x-primary-button>
                <button type="button" onclick="window.print()" class="text-sm text-gray-600 underline">Imprimir</button>
                <x-input-error :messages="$errors->all()" class="w-full" />
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Ingresos del período</div>
                    <div class="text-2xl font-semibold text-gray-900">Bs {{ number_format($ingresosTotal, 2) }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Órdenes abiertas en el período</div>
                    <div class="text-2xl font-semibold text-gray-900">{{ $ordenesPorEstado->sum() }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Repuestos con stock bajo</div>
                    <div class="text-2xl font-semibold {{ $stockBajo->isNotEmpty() ? 'text-red-700' : 'text-gray-900' }}">{{ $stockBajo->count() }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Ingresos por método de pago</h3>
                    <table class="w-full text-sm">
                        <thead class="text-gray-600 border-b"><tr><th class="py-2 text-left">Método</th><th class="text-right">Pagos</th><th class="text-right">Total</th></tr></thead>
                        <tbody>
                            @forelse ($ingresosPorMetodo as $fila)
                                <tr class="border-b"><td class="py-2">{{ ucfirst($fila->metodo) }}</td><td class="text-right">{{ $fila->cantidad }}</td><td class="text-right">Bs {{ number_format($fila->total, 2) }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="py-4 text-center text-gray-500">Sin pagos en el período.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Órdenes de trabajo por estado</h3>
                    @php $etiquetasOrden = \App\Models\OrdenTrabajo::ESTADOS; @endphp
                    <table class="w-full text-sm">
                        <tbody>
                            @foreach ($etiquetasOrden as $clave => $etiqueta)
                                <tr class="border-b"><td class="py-2">{{ $etiqueta }}</td><td class="text-right">{{ $ordenesPorEstado[$clave] ?? 0 }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>

                    <h3 class="font-semibold text-gray-800 mt-6 mb-3">Citas por estado</h3>
                    <table class="w-full text-sm">
                        <tbody>
                            @foreach (\App\Models\Cita::ESTADOS as $clave => $etiqueta)
                                <tr class="border-b"><td class="py-2">{{ $etiqueta }}</td><td class="text-right">{{ $citasPorEstado[$clave] ?? 0 }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Servicios más realizados</h3>
                    <table class="w-full text-sm">
                        <thead class="text-gray-600 border-b"><tr><th class="py-2 text-left">Servicio</th><th class="text-right">Veces</th><th class="text-right">Mano de obra</th></tr></thead>
                        <tbody>
                            @forelse ($serviciosTop as $fila)
                                <tr class="border-b"><td class="py-2">{{ $fila->nombre }}</td><td class="text-right">{{ $fila->cantidad }}</td><td class="text-right">Bs {{ number_format($fila->total, 2) }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="py-4 text-center text-gray-500">Sin tareas en el período.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Repuestos con stock bajo</h3>
                    <table class="w-full text-sm">
                        <thead class="text-gray-600 border-b"><tr><th class="py-2 text-left">Repuesto</th><th class="text-right">Stock</th><th class="text-right">Mínimo</th></tr></thead>
                        <tbody>
                            @forelse ($stockBajo as $repuesto)
                                <tr class="border-b"><td class="py-2">{{ $repuesto->codigo }} — {{ $repuesto->nombre }}</td><td class="text-right text-red-700">{{ $repuesto->stock_actual }}</td><td class="text-right">{{ $repuesto->stock_minimo }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="py-4 text-center text-gray-500">Todo el stock está sobre el mínimo.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6 lg:col-span-2">
                    <h3 class="font-semibold text-gray-800 mb-3">Mantenimientos vencidos o por vencer (próximos 30 días)</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="text-gray-600 border-b"><tr><th class="py-2 text-left">Fecha límite</th><th class="text-left">Motocicleta</th><th class="text-left hidden sm:table-cell">Cliente</th><th class="text-left">Mantenimiento</th><th class="text-left">Estado</th></tr></thead>
                            <tbody>
                                @forelse ($mantenimientos as $m)
                                    <tr class="border-b">
                                        <td class="py-2 whitespace-nowrap">{{ \Illuminate\Support\Carbon::parse($m->proxima_fecha)->format('d/m/Y') }}</td>
                                        <td>{{ $m->motocicleta?->marca }} ({{ $m->motocicleta?->placa ?: 'sin placa' }})</td>
                                        <td class="hidden sm:table-cell">{{ $m->motocicleta?->cliente?->nombre }}</td>
                                        <td>{{ $m->tipo_mantenimiento }}</td>
                                        <td>{{ $m->estado }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-4 text-center text-gray-500">No hay mantenimientos próximos.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
