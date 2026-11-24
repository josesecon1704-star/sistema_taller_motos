<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Servicios</h2>

            @can('permiso', ['servicios', 'crear'])
                <a href="{{ route('servicios.create') }}"
                    class="px-4 py-2 bg-gray-800 text-white text-xs uppercase rounded-md hover:bg-gray-700">
                    Nuevo servicio
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
                <form method="GET" class="flex flex-wrap gap-2 items-center" >
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre"
                        class="border-gray-300 rounded-md shadow-sm w-64 w-full sm:w-auto">
                    <select name="categoria" class="border-gray-300 rounded-md shadow-sm">
                        <option value="">Todas las categorías</option>
                        @foreach (\App\Models\Servicio::CATEGORIAS as $cat)
                            <option value="{{ $cat }}" @selected(request('categoria') === $cat)>{{ ucfirst($cat) }}
                            </option>
                        @endforeach
                    </select>
                    <x-primary-button>Filtrar</x-primary-button>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-gray-600 border-b">
                            <tr>
                                <th class="py-2">Nombre</th>
                                <th>Categoría</th>
                                <th class="text-right">Precio base</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($servicios as $servicio)
                                <tr class="border-b">
                                    <td class="py-2">{{ $servicio->nombre }}</td>
                                    <td>{{ ucfirst($servicio->categoria) }}</td>
                                    <td class="text-right">Bs {{ number_format($servicio->precio_base, 2) }}</td>
                                    <td>
                                        <span
                                            class="px-2 py-1 text-xs rounded
                                        {{ $servicio->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                            {{ ucfirst($servicio->estado) }}
                                        </span>
                                    </td>
                                    <td class="text-right whitespace-nowrap">
                                        @can('permiso', ['servicios', 'modificar'])
                                            <a href="{{ route('servicios.edit', $servicio) }}"
                                                class="text-indigo-600 hover:underline">Editar</a>

                                            <form method="POST" action="{{ route('servicios.estado', $servicio) }}"
                                                class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button class="ms-3 text-gray-600 hover:underline">
                                                    {{ $servicio->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">No hay servicios.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $servicios->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
