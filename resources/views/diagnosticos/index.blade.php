<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestión de Diagnósticos Técnicos (CU18)
        </h2>
    </x-slot>

    <div class="py-6" x-data="{ openModal: false, detalleActual: {} }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Listado de Diagnósticos y Checklists</h2>
                
                <a href="{{ route('diagnosticos.create') }}" style="background-color: #111827; color: #ffffff; padding: 8px 16px; border-radius: 6px; font-weight: bold; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;" class="shadow hover:opacity-90 transition flex items-center gap-2">
                    + Registrar Diagnóstico
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

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Motocicleta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Km Ingreso</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Fecha de Registro</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($diagnosticos as $diag)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition align-middle">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                    {{ $diag->motocicleta->marca ?? 'N/A' }} <span class="text-indigo-600 dark:text-indigo-400">({{ $diag->motocicleta->placa ?? '' }})</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                    {{ $diag->kilometraje_ingreso }} km
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ $diag->created_at->format('Y-m-d H:i') }}
                                </td>
                                <!-- Botón Ver Detalle con el mismo estilo institucional -->
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <button @click="openModal = true; detalleActual = {
                                        moto: '{{ $diag->motocicleta->marca ?? 'N/A' }} ({{ $diag->motocicleta->placa ?? '' }})',
                                        km: '{{ $diag->kilometraje_ingreso }} km',
                                        motor: '{{ $diag->estado_motor }}',
                                        notasMotor: '{{ $diag->notas_motor ?? 'Sin notas' }}',
                                        frenos: '{{ $diag->estado_frenos }}',
                                        notasFrenos: '{{ $diag->notas_frenos ?? 'Sin notas' }}',
                                        electrico: '{{ $diag->estado_electrico }}',
                                        notasElectrico: '{{ $diag->notas_electrico ?? 'Sin notas' }}',
                                        general: '{{ $diag->diagnostico_general }}',
                                        fecha: '{{ $diag->created_at->format('Y-m-d H:i') }}'
                                    }" 
                                            style="background-color: #111827; color: #ffffff; padding: 6px 12px; border-radius: 6px; font-weight: bold; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;" class="shadow hover:opacity-90 transition inline-flex items-center gap-1.5">
                                        Ver Detalle
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                    No hay diagnósticos técnicos registrados todavía.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $diagnosticos->links() }}
            </div>
        </div>

        <!-- VENTANA MODAL PARA VER DETALLES COMPLETOS -->
        <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
            <div @click.away="openModal = false" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full p-6 overflow-hidden transform transition-all">
                <div class="flex justify-between items-center border-b pb-3 mb-4 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Detalle del Diagnóstico Técnico
                    </h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Contenido del Detalle -->
                <div class="space-y-4 text-sm text-gray-700 dark:text-gray-300">
                    <div class="grid grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
                        <div><span class="font-semibold text-gray-500 dark:text-gray-400">Motocicleta:</span> <span x-text="detalleActual.moto" class="font-bold text-gray-900 dark:text-white"></span></div>
                        <div><span class="font-semibold text-gray-500 dark:text-gray-400">Km Ingreso:</span> <span x-text="detalleActual.km" class="font-bold text-gray-900 dark:text-white"></span></div>
                        <div class="col-span-2"><span class="font-semibold text-gray-500 dark:text-gray-400">Fecha de Registro:</span> <span x-text="detalleActual.fecha" class="text-gray-900 dark:text-white"></span></div>
                    </div>

                    <!-- Estado de Sistemas corregido con texto libre que baja de línea -->
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Evaluación por Sistemas:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="border dark:border-gray-700 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/30 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-xs font-bold text-gray-600 dark:text-gray-300">Motor</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="detalleActual.motor === 'BUENO' ? 'bg-emerald-100 text-emerald-800' : (detalleActual.motor === 'REGULAR' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')" x-text="detalleActual.motor"></span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-2 whitespace-normal break-words" x-text="detalleActual.notasMotor"></p>
                                </div>
                            </div>
                            <div class="border dark:border-gray-700 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/30 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-xs font-bold text-gray-600 dark:text-gray-300">Frenos</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="detalleActual.frenos === 'BUENO' ? 'bg-emerald-100 text-emerald-800' : (detalleActual.frenos === 'REGULAR' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')" x-text="detalleActual.frenos"></span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-2 whitespace-normal break-words" x-text="detalleActual.notasFrenos"></p>
                                </div>
                            </div>
                            <div class="border dark:border-gray-700 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/30 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-xs font-bold text-gray-600 dark:text-gray-300">Eléctrico</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="detalleActual.electrico === 'BUENO' ? 'bg-emerald-100 text-emerald-800' : (detalleActual.electrico === 'REGULAR' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')" x-text="detalleActual.electrico"></span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-2 whitespace-normal break-words" x-text="detalleActual.notasElectrico"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Diagnóstico General Completo -->
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Diagnóstico General:</h4>
                        <p class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg text-gray-700 dark:text-gray-300 text-sm leading-relaxed whitespace-normal break-words" x-text="detalleActual.general"></p>
                    </div>
                </div>

                <!-- Botón Cerrar Modal -->
                <div class="mt-6 text-right">
                    <button @click="openModal = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-4 py-2 rounded-lg text-sm transition">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>