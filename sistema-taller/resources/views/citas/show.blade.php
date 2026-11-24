@php
    $acciones = [
        'confirmada' => 'Confirmar',
        'completada' => 'Marcar completada',
        'no_asistio' => 'No asistió',
        'cancelada'  => 'Cancelar cita',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cita #{{ $cita->id }}</h2>
            @include('citas._estado', ['estado' => $cita->estado])
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Fecha y hora</dt>
                        <dd class="font-medium">{{ $cita->fecha_hora->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Cliente</dt>
                        <dd class="font-medium">{{ $cita->cliente?->nombre }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Motocicleta</dt>
                        <dd class="font-medium">
                            {{ $cita->motocicleta?->marca }} {{ $cita->motocicleta?->modelo }} — {{ $cita->motocicleta?->placa ?: 'sin placa' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Mecánico</dt>
                        <dd class="font-medium">{{ $cita->empleado?->nombre ?? 'Sin asignar' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Motivo</dt>
                        <dd>{{ $cita->motivo }}</dd>
                    </div>
                    @unless (Auth::user()->esCliente())
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500">Observaciones internas</dt>
                            <dd>{{ $cita->observaciones ?: '—' }}</dd>
                        </div>
                    @endunless
                </dl>

                <div class="mt-6 flex flex-wrap gap-2">
                    @can('permiso', ['citas', 'modificar'])
                        @foreach (\App\Models\Cita::TRANSICIONES[$cita->estado] ?? [] as $destino)
                            <form method="POST" action="{{ route('citas.estado', $cita) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado" value="{{ $destino }}">
                                <button type="submit"
                                        @if (in_array($destino, ['cancelada', 'no_asistio'])) onclick="return confirm('¿Seguro?')" @endif
                                        class="px-3 py-2 text-xs uppercase rounded-md text-white {{ in_array($destino, ['cancelada', 'no_asistio']) ? 'bg-red-600 hover:bg-red-500' : 'bg-gray-800 hover:bg-gray-700' }}">
                                    {{ $acciones[$destino] ?? $destino }}
                                </button>
                            </form>
                        @endforeach

                        @if ($cita->esActiva())
                            <a href="{{ route('citas.edit', $cita) }}"
                               class="px-3 py-2 text-xs uppercase rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                                Editar / reprogramar
                            </a>
                        @endif
                    @elseif (Auth::user()->esCliente() && $cita->puedeCambiarA('cancelada'))
                        <form method="POST" action="{{ route('citas.cancelar', $cita) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" onclick="return confirm('¿Cancelar esta cita?')"
                                    class="px-3 py-2 text-xs uppercase rounded-md text-white bg-red-600 hover:bg-red-500">
                                Cancelar cita
                            </button>
                        </form>
                    @endcan

                    <a href="{{ route('citas.index') }}" class="px-3 py-2 text-xs uppercase text-gray-600 underline">Volver</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
