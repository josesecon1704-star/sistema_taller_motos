<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Registrar Diagnóstico Técnico (CU18)
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Nuevo Diagnóstico y Checklist</h2>

                <form action="{{ route('diagnosticos.store') }}" method="POST">
                    @csrf

                    <!-- Selección de Motocicleta / Orden -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Seleccionar Motocicleta</label>
                            <select name="motocicleta_id" class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-medium" required>
                                <option value="">— Seleccione una moto —</option>
                                @foreach($motocicletas as $moto)
                                    <option value="{{ $moto->id }}">{{ $moto->placa }} - {{ $moto->marca }} {{ $moto->modelo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Kilometraje de Ingreso</label>
                            <input type="number" name="kilometraje_ingreso" placeholder="Ej. 1500" class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-medium" required>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700 my-6">

                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Checklist Técnico por Sistemas</h3>

                    <!-- SECCIÓN: MOTOR -->
                    <div class="bg-gray-50 dark:bg-gray-750 p-4 rounded-lg border border-gray-200 dark:border-gray-700 mb-4">
                        <h4 class="font-bold text-indigo-600 dark:text-indigo-400 mb-3">🔧 Sistema de Motor</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Estado General Motor</label>
                                <select name="estado_motor" class="w-full border rounded p-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                    <option value="BUENO">Bueno</option>
                                    <option value="REGULAR">Regular</option>
                                    <option value="MALO">Malo</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Notas / Observaciones de Motor</label>
                            <textarea name="notas_motor" rows="2" class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="Ej. Nivel de aceite bajo, ruido leve en válvulas..."></textarea>
                        </div>
                    </div>

                    <!-- SECCIÓN: FRENOS -->
                    <div class="bg-gray-50 dark:bg-gray-750 p-4 rounded-lg border border-gray-200 dark:border-gray-700 mb-4">
                        <h4 class="font-bold text-indigo-600 dark:text-indigo-400 mb-3">🛑 Sistema de Frenos</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Estado General Frenos</label>
                                <select name="estado_frenos" class="w-full border rounded p-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                    <option value="BUENO">Bueno</option>
                                    <option value="REGULAR">Regular</option>
                                    <option value="MALO">Malo</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Notas / Observaciones de Frenos</label>
                            <textarea name="notas_frenos" rows="2" class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="Ej. Pastillas delanteras desgastadas al 70%..."></textarea>
                        </div>
                    </div>

                    <!-- SECCIÓN: ELÉCTRICO -->
                    <div class="bg-gray-50 dark:bg-gray-750 p-4 rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
                        <h4 class="font-bold text-indigo-600 dark:text-indigo-400 mb-3">⚡ Sistema Eléctrico</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Estado General Eléctrico</label>
                                <select name="estado_electrico" class="w-full border rounded p-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                    <option value="BUENO">Bueno</option>
                                    <option value="REGULAR">Regular</option>
                                    <option value="MALO">Malo</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Notas / Observaciones del Sistema Eléctrico</label>
                            <textarea name="notas_electrico" rows="2" class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="Ej. Batería con carga baja, luz intermitente trasera fallando..."></textarea>
                        </div>
                    </div>

                    <!-- Observaciones Generales del Diagnóstico -->
                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Diagnóstico General / Conclusión del Mecánico</label>
                        <textarea name="diagnostico_general" rows="3" class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="Resumen general del estado de la motocicleta..." required></textarea>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('diagnosticos.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded font-semibold hover:bg-gray-700 transition">Cancelar</a>
                        <button type="submit" style="background-color: #4f46e5; color: #ffffff;" class="px-4 py-2 rounded font-bold hover:opacity-90 transition">Guardar Diagnóstico</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>