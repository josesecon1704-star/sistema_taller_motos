<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestión de Cotizaciones (CU19 / CU22 / CU23)
        </h2>
    </x-slot>

    <div class="py-6" x-data="{ openModal: false, cotizacionSeleccionada: null }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Listado de Cotizaciones</h2>
                
                <a href="{{ route('cotizaciones.create') }}" style="background-color: #111827; color: #ffffff; padding: 8px 16px; border-radius: 6px; font-weight: bold; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;" class="shadow hover:opacity-90 transition flex items-center gap-2">
                    + Registrar Cotización
                </a>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg mb-6 shadow-sm flex items-center justify-between" role="alert">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="font-medium text-emerald-900">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-lg mb-6 shadow-sm" role="alert">
                    <span class="font-medium">{{ $errors->first() }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Nro / Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Motocicleta</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Mano Obra</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Repuestos</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Estado</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($cotizaciones as $cot)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition align-middle">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                    {{ $cot->nro_cotizacion }}
                                    <span class="block text-xs text-gray-400 font-normal">{{ $cot->fecha_emision }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                    {{ $cot->diagnostico->motocicleta->marca ?? 'N/A' }} 
                                    <span class="text-indigo-600 dark:text-indigo-400 font-semibold">({{ $cot->diagnostico->motocicleta->placa ?? 'S/P' }})</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                    $ {{ number_format($cot->total_mano_obra, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                    $ {{ number_format($cot->total_repuestos, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white whitespace-nowrap">
                                    $ {{ number_format($cot->monto_total, 2) }}
                                </td>
                                <td class="px-6 py-4 text-xs whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full font-bold uppercase text-[10px] 
                                        {{ $cot->estado == 'pendiente' ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $cot->estado == 'aprobada' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        {{ $cot->estado == 'rechazada' ? 'bg-rose-100 text-rose-800' : '' }}">
                                        {{ $cot->estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap flex items-center justify-center gap-2">
                                    <!-- Botón Ver Detalle -->
                                    <button @click="cotizacionSeleccionada = {{ json_encode($cot) }}; openModal = true;" style="background-color: #111827; color: #ffffff; padding: 6px 12px; border-radius: 6px; font-weight: bold; font-size: 11px;" class="shadow hover:opacity-90 transition">
                                        Ver Detalle
                                    </button>

                                    <!-- Botones de Cambio de Estado (CU22) -->
                                    @if($cot->estado == 'pendiente')
                                        <form action="{{ route('cotizaciones.estado', $cot->id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="estado" value="aprobada">
                                            <button type="submit" style="background-color: #111827; color: #ffffff; padding: 6px 12px; border-radius: 6px; font-weight: bold; font-size: 11px;" class="shadow hover:opacity-90 transition uppercase" onclick="return confirm('¿Estás seguro de aprobar esta cotización?')">
                                                Aprobar
                                            </button>
                                        </form>

                                        <form action="{{ route('cotizaciones.estado', $cot->id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="estado" value="rechazada">
                                            <button type="submit" style="background-color: #111827; color: #ffffff; padding: 6px 12px; border-radius: 6px; font-weight: bold; font-size: 11px;" class="shadow hover:opacity-90 transition uppercase" onclick="return confirm('¿Estás seguro de rechazar esta cotización?')">
                                                Rechazar
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Botón Generar Orden de Trabajo (CU23) si está aprobada -->
                                    @if($cot->estado == 'aprobada')
                                        <form action="{{ route('ordenes-trabajo.store') }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="cotizacion_id" value="{{ $cot->id }}">
                                            <button type="submit" style="background-color: #059669; color: #ffffff; padding: 6px 12px; border-radius: 6px; font-weight: bold; font-size: 11px;" class="shadow hover:opacity-90 transition uppercase" onclick="return confirm('¿Deseas generar la orden de trabajo para esta cotización?')">
                                                + Generar Orden
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                    No hay cotizaciones registradas todavía. ¡Haz clic en "+ Registrar Cotización" para crear una!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $cotizaciones->links() }}
            </div>
        </div>

        <!-- VENTANA MODAL PARA VER DETALLE -->
        <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
            <div @click.away="openModal = false" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full p-6 space-y-6 relative border border-gray-200 dark:border-gray-700">
                
                <!-- Encabezado Modal -->
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        Detalle de Cotización: <span class="text-indigo-600 dark:text-indigo-400" x-text="cotizacionSeleccionada?.nro_cotizacion"></span>
                    </h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 font-bold text-lg">&times;</button>
                </div>

                <!-- Contenido del Detalle -->
                <template x-if="cotizacionSeleccionada">
                    <div class="space-y-4 text-sm text-gray-700 dark:text-gray-300">
                        <div class="grid grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
                            <div><strong>Tipo:</strong> <span x-text="cotizacionSeleccionada.tipo"></span></div>
                            <div><strong>Estado:</strong> <span class="uppercase font-bold" x-text="cotizacionSeleccionada.estado"></span></div>
                            <div><strong>Fecha Emisión:</strong> <span x-text="cotizacionSeleccionada.fecha_emision"></span></div>
                            <div><strong>Diagnóstico ID:</strong> <span x-text="cotizacionSeleccionada.diagnostico_id"></span></div>
                        </div>

                        <!-- Servicios / Mano de Obra -->
                        <div>
                            <h4 class="font-bold text-xs uppercase text-gray-500 mb-2">Servicios / Mano de Obra</h4>
                            <div class="border rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 text-xs">
                                    <thead class="bg-gray-100 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-3 py-2 text-left">Servicio</th>
                                            <th class="px-3 py-2 text-left">Observaciones</th>
                                            <th class="px-3 py-2 text-right">Costo</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template x-for="serv in cotizacionSeleccionada.servicios">
                                            <tr>
                                                <td class="px-3 py-2 font-medium text-gray-900 dark:text-white" x-text="serv.servicio?.nombre || serv.servicio?.descripcion || 'Servicio #' + serv.servicio_id"></td>
                                                <td class="px-3 py-2" x-text="serv.observaciones || 'Sin observaciones'"></td>
                                                <td class="px-3 py-2 text-right font-bold" x-text="`$ ${parseFloat(serv.precio_mano_obra).toFixed(2)}`"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Repuestos -->
                        <div>
                            <h4 class="font-bold text-xs uppercase text-gray-500 mb-2">Repuestos Requeridos</h4>
                            <div class="border rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 text-xs">
                                    <thead class="bg-gray-100 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-3 py-2 text-left">Repuesto</th>
                                            <th class="px-3 py-2 text-center">Cant.</th>
                                            <th class="px-3 py-2 text-right">P. Unitario</th>
                                            <th class="px-3 py-2 text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template x-for="rep in cotizacionSeleccionada.repuestos">
                                            <tr>
                                                <td class="px-3 py-2 font-medium text-gray-900 dark:text-white" x-text="rep.repuesto?.nombre || 'Repuesto #' + rep.repuesto_id"></td>
                                                <td class="px-3 py-2 text-center" x-text="rep.cantidad"></td>
                                                <td class="px-3 py-2 text-right" x-text="`$ ${parseFloat(rep.precio_unitario).toFixed(2)}`"></td>
                                                <td class="px-3 py-2 text-right font-bold" x-text="`$ ${(rep.cantidad * rep.precio_unitario).toFixed(2)}`"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Totales Finales -->
                        <div class="bg-gray-900 text-white p-4 rounded-lg flex justify-between items-center text-sm font-bold">
                            <div>Monto Total:</div>
                            <div class="text-emerald-400 text-lg" x-text="`$ ${parseFloat(cotizacionSeleccionada.monto_total).toFixed(2)}`"></div>
                        </div>
                    </div>
                </template>

                <!-- Pie de Modal -->
                <div class="flex justify-end pt-2">
                    <button @click="openModal = false" style="background-color: #111827; color: #ffffff;" class="px-4 py-2 rounded-lg font-bold text-xs uppercase tracking-wider hover:opacity-90">
                        Cerrar
                    </button>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>