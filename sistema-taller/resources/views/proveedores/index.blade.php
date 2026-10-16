<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Proveedores</h2>

            @can('permiso', ['proveedores', 'crear'])
                <a href="{{ route('proveedores.create') }}"
                   class="px-4 py-2 bg-gray-800 text-white text-xs uppercase rounded-md hover:bg-gray-700">
                    Nuevo proveedor
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
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre o NIT"
                           class="border-gray-300 rounded-md shadow-sm w-64 w-full sm:w-auto">
                    <x-primary-button>Buscar</x-primary-button>
                </form>

                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b">
                        <tr>
                            <th class="py-2">Nombre</th>
                            <th>NIT</th>
                            <th>Teléfono</th>
                            <th class="text-right">Repuestos</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($proveedores as $proveedor)
                            <tr class="border-b">
                                <td class="py-2">{{ $proveedor->nombre }}</td>
                                <td>{{ $proveedor->nit ?? '—' }}</td>
                                <td>{{ $proveedor->telefono }}</td>
                                <td class="text-right">{{ $proveedor->repuestos_count }}</td>
                                <td>
                                    <span class="px-2 py-1 text-xs rounded
                                        {{ $proveedor->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                        {{ ucfirst($proveedor->estado) }}
                                    </span>
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    @can('permiso', ['proveedores', 'modificar'])
                                        <a href="{{ route('proveedores.repuestos', $proveedor) }}"
                                           class="me-3 text-emerald-700 hover:underline">Repuestos</a>
                                        <a href="{{ route('proveedores.edit', $proveedor) }}"
                                           class="text-indigo-600 hover:underline">Editar</a>

                                        <form method="POST" action="{{ route('proveedores.estado', $proveedor) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="ms-3 text-gray-600 hover:underline">
                                                {{ $proveedor->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-6 text-center text-gray-500">No hay proveedores.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $proveedores->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>