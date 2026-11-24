@php $esCliente = Auth::user()->esCliente(); @endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $esCliente ? 'Mis citas' : 'Citas' }}
            </h2>

            @can('permiso', ['citas', 'crear'])
                <a href="{{ route('citas.create') }}"
                   class="px-4 py-2 bg-gray-800 text-white text-xs uppercase rounded-md hover:bg-gray-700">
                    {{ $esCliente ? 'Solicitar cita' : 'Nueva cita' }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <form method="GET" class="flex flex-wrap gap-2 items-center mb-4">
                    <select name="estado" class="border-gray-300 rounded-md shadow-sm w-full sm:w-auto">
                        <option value="">Todos los estados</option>
                        @foreach (\App\Models\Cita::ESTADOS as $clave => $etiqueta)
                            <option value="{{ $clave }}" @selected(request('estado') === $clave)>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="fecha" value="{{ request('fecha') }}"
                           class="border-gray-300 rounded-md shadow-sm w-full sm:w-auto">
                    <x-primary-button>Filtrar</x-primary-button>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-gray-600 border-b">
                            <tr>
                                <th class="py-2">Fecha y hora</th>
                                @unless ($esCliente)
                                    <th class="hidden sm:table-cell">Cliente</th>
                                @endunless
                                <th class="hidden md:table-cell">Motocicleta</th>
                                <th class="hidden lg:table-cell">Motivo</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($citas as $cita)
                                <tr class="border-b">
                                    <td class="py-2 whitespace-nowrap">
                                        {{ $cita->fecha_hora->format('d/m/Y H:i') }}
                                        <div class="text-xs text-gray-500 md:hidden">{{ $cita->motocicleta?->placa }}</div>
                                    </td>
                                    @unless ($esCliente)
                                        <td class="hidden sm:table-cell">{{ $cita->cliente?->nombre }}</td>
                                    @endunless
                                    <td class="hidden md:table-cell">
                                        {{ $cita->motocicleta?->marca }} {{ $cita->motocicleta?->modelo }}
                                        <span class="text-gray-500">({{ $cita->motocicleta?->placa ?: 'sin placa' }})</span>
                                    </td>
                                    <td class="hidden lg:table-cell">{{ \Illuminate\Support\Str::limit($cita->motivo, 40) }}</td>
                                    <td>@include('citas._estado', ['estado' => $cita->estado])</td>
                                    <td class="text-right whitespace-nowrap">
                                        <a href="{{ route('citas.show', $cita) }}" class="text-indigo-600 hover:underline">Ver</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-6 text-center text-gray-500">No hay citas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $citas->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
