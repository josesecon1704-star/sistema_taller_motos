<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Programación de Mantenimiento Preventivo (CU17)
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Listado de Mantenimientos y Alertas</h2>
                <a href="{{ route('mantenimientos.create') }}" style="background-color: #111827; color: #ffffff; padding: 10px 20px; border-radius: 6px; font-weight: bold; text-decoration: display; font-size: 12px; text-transform: uppercase;" class="shadow-lg hover:opacity-90 transition">
                    + Programar Mantenimiento
                </a>
            </div>

            @if(session('success'))
                <div class="bg-emerald-500 text-white p-4 rounded-lg mb-4 shadow font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Motocicleta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tipo de Mantenimiento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Km Límite</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Fecha Límite</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Estado / Alerta</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($mantenimientos as $mant)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $mant->motocicleta->marca ?? 'N/A' }} <span class="text-indigo-600 dark:text-indigo-400">({{ $mant->motocicleta->placa ?? '' }})</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                    {{ $mant->tipo_mantenimiento }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $mant->proximo_km ?? 'N/A' }} km
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $mant->proxima_fecha }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full shadow-sm 
                                        {{ $mant->estado == 'PENDIENTE' ? 'bg-amber-100 text-amber-800 border border-amber-300' : ($mant->estado == 'COMPLETADO' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-rose-100 text-rose-800 border border-rose-300 animate-pulse') }}">
                                        {{ $mant->estado == 'VENCIDO' ? '⚠️ ALERTA: VENCIDO' : $mant->estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    @if($mant->estado != 'COMPLETADO')
                                        <form action="{{ route('mantenimientos.update', $mant->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="estado" value="COMPLETADO">
                                            <button type="submit" style="background-color: #4f46e5; color: #ffffff; padding: 6px 12px; border-radius: 6px; font-weight: bold; font-size: 11px; text-transform: uppercase; border: none; cursor: pointer;" class="shadow hover:opacity-90 transition">
                                                ✓ Marcar Completado
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 rounded">
                                            ✔ Finalizado
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                    No hay mantenimientos preventivos programados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $mantenimientos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>