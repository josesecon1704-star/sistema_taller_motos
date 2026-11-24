<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar / reprogramar cita</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <div class="mb-4 text-sm text-gray-700">
                    <div class="font-medium">{{ $cita->cliente?->nombre }}</div>
                    <div class="text-gray-500">
                        {{ $cita->motocicleta?->marca }} {{ $cita->motocicleta?->modelo }} — {{ $cita->motocicleta?->placa ?: 'sin placa' }}
                    </div>
                </div>

                <form method="POST" action="{{ route('citas.update', $cita) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="fecha" value="Fecha" />
                            <x-text-input id="fecha" type="date" name="fecha" class="block mt-1 w-full"
                                          :value="old('fecha', $cita->fecha_hora->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="hora" value="Hora" />
                            <x-text-input id="hora" type="time" name="hora" class="block mt-1 w-full"
                                          :value="old('hora', $cita->fecha_hora->format('H:i'))" required />
                            <x-input-error :messages="$errors->get('hora')" class="mt-2" />
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('motocicleta_id')" class="mt-2" />

                    <div>
                        <x-input-label for="motivo" value="Motivo" />
                        <textarea id="motivo" name="motivo" rows="2" maxlength="255" required
                                  class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('motivo', $cita->motivo) }}</textarea>
                        <x-input-error :messages="$errors->get('motivo')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="empleado_id" value="Mecánico asignado" />
                        <select id="empleado_id" name="empleado_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Sin asignar</option>
                            @foreach ($empleados as $empleado)
                                <option value="{{ $empleado->id }}" @selected(old('empleado_id', $cita->empleado_id) == $empleado->id)>{{ $empleado->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('empleado_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="observaciones" value="Observaciones internas" />
                        <textarea id="observaciones" name="observaciones" rows="3"
                                  class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('observaciones', $cita->observaciones) }}</textarea>
                        <x-input-error :messages="$errors->get('observaciones')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('citas.show', $cita) }}" class="text-sm text-gray-600 underline">Volver</a>
                        <x-primary-button>Guardar cambios</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
