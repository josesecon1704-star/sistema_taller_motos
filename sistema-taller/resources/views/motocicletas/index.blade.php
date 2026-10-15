<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ Auth::user()->esCliente() ? 'Mis motocicletas' : 'Motocicletas' }}
            </h2>

            @can('permiso', ['motocicletas', 'crear'])
                <a href="{{ route('motocicletas.create') }}"
                   class="px-4 py-2 bg-gray-800 text-white text-xs uppercase rounded-md hover:bg-gray-700">
                    {{ Auth::user()->esCliente() ? 'Registrar mi moto' : 'Nueva motocicleta' }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="flex gap-2 mb-4">
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Buscar por placa, chasis, marca, modelo o dueño"
                           class="border-gray-300 rounded-md shadow-sm w-96">
                    <x-primary-button>Buscar</x-primary-button>
                </form>

                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b">
                        <tr>
                            <th class="py-2">Placa</th>
                            <th>Moto</th>
                            <th>Año</th>
                            <th>Color</th>
                            <th class="text-right">Kilometraje</th>
                            @unless (Auth::user()->esCliente())
                                <th class="ps-6">Propietario</th>
                            @endunless
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($motos as $moto)
                            <tr class="border-b">
                                <td class="py-2">
                                    <a href="{{ route('motocicletas.show', $moto) }}"
                                       class="text-indigo-600 hover:underline">{{ $moto->placa ?? 'Sin placa' }}</a>
                                </td>
                                <td>{{ $moto->marca }} {{ $moto->modelo }} {{ $moto->cilindrada }}cc</td>
                                <td>{{ $moto->anio }}</td>
                                <td>{{ $moto->color }}</td>
                                <td class="text-right">{{ number_format($moto->kilometraje, 0, ',', '.') }} km</td>
                                @unless (Auth::user()->esCliente())
                                    <td class="ps-6">{{ $moto->cliente->nombre }}</td>
                                @endunless
                                <td class="text-right whitespace-nowrap">
                                    <a href="{{ route('motocicletas.show', $moto) }}"
                                       class="text-gray-600 hover:underline">Ver</a>
                                    @can('permiso', ['motocicletas', 'modificar'])
                                        <a href="{{ route('motocicletas.edit', $moto) }}"
                                           class="ms-3 text-indigo-600 hover:underline">Editar</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-6 text-center text-gray-500">No hay motocicletas.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $motos->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>