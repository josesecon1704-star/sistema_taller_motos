<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Empleados</h2>

            @can('permiso', ['empleados', 'crear'])
                <a href="{{ route('empleados.create') }}"
                   class="px-4 py-2 bg-gray-800 text-white text-xs uppercase rounded-md hover:bg-gray-700">
                    Nuevo empleado
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
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre o CI"
                           class="border-gray-300 rounded-md shadow-sm w-full sm:w-64">
                    <select name="cargo" class="border-gray-300 rounded-md shadow-sm w-full sm:w-auto">
                        <option value="">Todos los cargos</option>
                        @foreach (\App\Models\Empleado::CARGOS as $clave => $etiqueta)
                            <option value="{{ $clave }}" @selected(request('cargo') === $clave)>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                    <select name="estado" class="border-gray-300 rounded-md shadow-sm w-full sm:w-auto">
                        <option value="">Todos los estados</option>
                        <option value="activo" @selected(request('estado') === 'activo')>Activo</option>
                        <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivo</option>
                    </select>
                    <x-primary-button>Filtrar</x-primary-button>
                </form>

                <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b">
                        <tr>
                            <th class="py-2">Nombre</th>
                            <th class="hidden sm:table-cell">CI</th>
                            <th>Cargo</th>
                            <th class="hidden md:table-cell">Teléfono</th>
                            <th class="hidden md:table-cell">Cuenta</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($empleados as $empleado)
                            <tr class="border-b">
                                <td class="py-2">
                                    <a href="{{ route('empleados.show', $empleado) }}"
                                       class="text-indigo-600 hover:underline">{{ $empleado->nombre }}</a>
                                </td>
                                <td class="hidden sm:table-cell">{{ $empleado->ci }}</td>
                                <td>{{ \App\Models\Empleado::CARGOS[$empleado->cargo] ?? $empleado->cargo }}</td>
                                <td class="hidden md:table-cell">{{ $empleado->telefono }}</td>
                                <td class="hidden md:table-cell">{{ $empleado->usuario_exists ? 'Con cuenta' : 'Sin cuenta' }}</td>
                                <td>
                                    <span class="px-2 py-1 text-xs rounded
                                        {{ $empleado->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                        {{ ucfirst($empleado->estado) }}
                                    </span>
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <a href="{{ route('empleados.show', $empleado) }}"
                                       class="text-gray-600 hover:underline">Ver</a>
                                    @can('permiso', ['empleados', 'modificar'])
                                        <a href="{{ route('empleados.edit', $empleado) }}"
                                           class="ms-3 text-indigo-600 hover:underline">Editar</a>

                                        <form method="POST" action="{{ route('empleados.estado', $empleado) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="ms-3 text-gray-600 hover:underline">
                                                {{ $empleado->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-6 text-center text-gray-500">No hay empleados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>

                <div class="mt-4">{{ $empleados->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>