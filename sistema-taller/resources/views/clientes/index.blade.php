<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Clientes</h2>

            @can('permiso', ['clientes', 'crear'])
                <a href="{{ route('clientes.create') }}"
                   class="px-4 py-2 bg-gray-800 text-white text-xs uppercase rounded-md hover:bg-gray-700">
                    Nuevo cliente
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
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre o documento"
                           class="border-gray-300 rounded-md shadow-sm w-64">
                    <select name="tipo" class="border-gray-300 rounded-md shadow-sm">
                        <option value="">Todos los tipos</option>
                        @foreach (\App\Models\Cliente::TIPOS as $t)
                            <option value="{{ $t }}" @selected(request('tipo') === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                    <x-primary-button>Filtrar</x-primary-button>
                </form>

                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b">
                        <tr>
                            <th class="py-2">Nombre</th>
                            <th>Documento</th>
                            <th>Teléfono</th>
                            <th>Tipo</th>
                            <th class="text-right">Motos</th>
                            <th>Cuenta</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clientes as $cliente)
                            <tr class="border-b">
                                <td class="py-2">
                                    <a href="{{ route('clientes.show', $cliente) }}"
                                       class="text-indigo-600 hover:underline">{{ $cliente->nombre }}</a>
                                </td>
                                <td>{{ strtoupper($cliente->tipo_documento) }} {{ $cliente->numero_documento }}</td>
                                <td>{{ $cliente->telefono }}</td>
                                <td>{{ ucfirst($cliente->tipo) }}</td>
                                <td class="text-right">{{ $cliente->motocicletas_count }}</td>
                                <td>
                                    <span class="px-2 py-1 text-xs rounded
                                        {{ $cliente->usuario_exists ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                        {{ $cliente->usuario_exists ? 'Con cuenta' : 'Sin cuenta' }}
                                    </span>
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <a href="{{ route('clientes.show', $cliente) }}"
                                       class="text-gray-600 hover:underline">Ver</a>
                                    @can('permiso', ['clientes', 'modificar'])
                                        <a href="{{ route('clientes.edit', $cliente) }}"
                                           class="ms-3 text-indigo-600 hover:underline">Editar</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-6 text-center text-gray-500">No hay clientes.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $clientes->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>