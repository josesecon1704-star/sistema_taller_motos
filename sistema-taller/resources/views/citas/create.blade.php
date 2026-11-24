@php $esCliente = Auth::user()->esCliente(); @endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $esCliente ? 'Solicitar cita' : 'Nueva cita' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                @if ($motocicletas->isEmpty())
                    <p class="text-sm text-gray-700">
                        {{ $esCliente ? 'Primero registra tu motocicleta para poder solicitar una cita.' : 'No hay motocicletas registradas.' }}
                    </p>
                    @can('permiso', ['motocicletas', 'crear'])
                        <a href="{{ route('motocicletas.create') }}"
                           class="inline-block mt-4 px-4 py-2 bg-gray-800 text-white text-xs uppercase rounded-md">
                            Registrar motocicleta
                        </a>
                    @endcan
                @else
                    <form method="POST" action="{{ route('citas.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="motocicleta_id" value="Motocicleta" />
                            <select id="motocicleta_id" name="motocicleta_id" required
                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Selecciona una motocicleta</option>
                                @foreach ($motocicletas as $moto)
                                    <option value="{{ $moto->id }}" @selected(old('motocicleta_id') == $moto->id)>
                                        {{ $moto->marca }} {{ $moto->modelo }} — {{ $moto->placa ?: 'sin placa' }}
                                        @unless ($esCliente) ({{ $moto->cliente?->nombre }}) @endunless
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('motocicleta_id')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="fecha" value="Fecha" />
                                <x-text-input id="fecha" type="date" name="fecha" class="block mt-1 w-full"
                                              :value="old('fecha')" min="{{ today()->format('Y-m-d') }}" required />
                                <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="hora" value="Hora" />
                                <x-text-input id="hora" type="time" name="hora" class="block mt-1 w-full"
                                              :value="old('hora')" required />
                                <x-input-error :messages="$errors->get('hora')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="motivo" value="Motivo de la visita" />
                            <textarea id="motivo" name="motivo" rows="2" maxlength="255" required
                                      class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"
                                      placeholder="Ej.: ruido en el motor, cambio de aceite...">{{ old('motivo') }}</textarea>
                            <x-input-error :messages="$errors->get('motivo')" class="mt-2" />
                        </div>

                        @unless ($esCliente)
                            <div>
                                <x-input-label for="empleado_id" value="Mecánico asignado (opcional)" />
                                <select id="empleado_id" name="empleado_id"
                                        class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Sin asignar</option>
                                    @foreach ($empleados as $empleado)
                                        <option value="{{ $empleado->id }}" @selected(old('empleado_id') == $empleado->id)>{{ $empleado->nombre }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('empleado_id')" class="mt-2" />
                            </div>
                        @endunless

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('citas.index') }}" class="text-sm text-gray-600 underline">Cancelar</a>
                            <x-primary-button>{{ $esCliente ? 'Solicitar cita' : 'Registrar cita' }}</x-primary-button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
