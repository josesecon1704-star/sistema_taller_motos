<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Repuestos</h2>

            @can('permiso', ['repuestos', 'crear'])
                <a href="{{ route('repuestos.create') }}"
                    class="px-4 py-2 bg-gray-800 text-white text-xs uppercase rounded-md hover:bg-gray-700">
                    Nuevo repuesto
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
                <form method="GET" class="flex flex-wrap gap-2 items-center">
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Buscar por nombre o código" class="border-gray-300 rounded-md shadow-sm w-64 w-full sm:w-auto">
                    <select name="categoria" class="border-gray-300 rounded-md shadow-sm">
                        <option value="">Todas las categorías</option>
                        @foreach (\App\Models\Repuesto::CATEGORIAS as $cat)
                            <option value="{{ $cat }}" @selected(request('categoria') === $cat)>{{ ucfirst($cat) }}
                            </option>
                        @endforeach
                    </select>
                    <label class="flex items-center gap-1 text-sm text-gray-700">
                        <input type="checkbox" name="stock_bajo" value="1" @checked(request()->boolean('stock_bajo'))
                            class="rounded border-gray-300">
                        Solo stock bajo
                    </label>
                    <x-primary-button>Filtrar</x-primary-button>
                </form>

                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b">
                        <tr>
                            <th class="py-2">Código</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th class="text-right">Precio</th>
                            <th class="text-right">Stock</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($repuestos as $repuesto)
                            <tr class="border-b">
                                <td class="py-2">{{ $repuesto->codigo }}</td>
                                <td>{{ $repuesto->nombre }}</td>
                                <td>{{ ucfirst($repuesto->categoria) }}</td>
                                <td class="text-right">Bs {{ number_format($repuesto->precio_venta, 2) }}</td>
                                <td class="text-right">
                                    @if ($repuesto->stock_actual <= $repuesto->stock_minimo)
                                        <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">
                                            {{ $repuesto->stock_actual }} (bajo)
                                        </span>
                                    @else
                                        {{ $repuesto->stock_actual }}
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="px-2 py-1 text-xs rounded
                                        {{ $repuesto->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                        {{ ucfirst($repuesto->estado) }}
                                    </span>
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    @can('permiso', ['repuestos', 'modificar'])
                                        <a href="{{ route('repuestos.edit', $repuesto) }}"
                                            class="text-indigo-600 hover:underline">Editar</a>

                                        <a href="{{ route('repuestos.movimiento', $repuesto) }}"
                                            class="me-3 text-emerald-700 hover:underline">Stock</a>

                                        <form method="POST" action="{{ route('repuestos.estado', $repuesto) }}"
                                            class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="ms-3 text-gray-600 hover:underline">
                                                {{ $repuesto->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-gray-500">No hay repuestos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $repuestos->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
