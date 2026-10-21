<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Usuarios</h2>

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
                <p class="text-sm text-gray-600 mb-4">
                    Las cuentas se crean desde <strong>Empleados</strong> o <strong>Clientes</strong>. Aquí se consultan, se activan o desactivan y se restablece su contraseña.
                </p>
                <form method="GET" class="flex flex-wrap gap-2 items-center mb-4">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre o correo"
                           class="border-gray-300 rounded-md shadow-sm w-full sm:w-64">
                    <select name="rol_id" class="border-gray-300 rounded-md shadow-sm w-full sm:w-auto">
                        <option value="">Todos los roles</option>
                        @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}" @selected(request('rol_id') == $rol->id)>{{ $rol->nombre }}</option>
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
                            <th class="py-2">Usuario</th>
                            <th class="hidden md:table-cell">Rol</th>
                            <th class="hidden md:table-cell">Tipo</th>
                            <th class="hidden lg:table-cell">Último acceso</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($usuarios as $usuario)
                            <tr class="border-b">
                                <td class="py-2">
                                    <div class="font-medium">{{ $usuario->empleado?->nombre ?? $usuario->cliente?->nombre ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ $usuario->email }}</div>
                                </td>
                                <td class="hidden md:table-cell">{{ $usuario->rol?->nombre ?? 'Sin rol' }}</td>
                                <td class="hidden md:table-cell">{{ $usuario->cliente_id ? 'Cliente' : 'Empleado' }}</td>
                                <td class="hidden lg:table-cell">
                                    {{ $usuario->ultimo_acceso?->format('d/m/Y H:i') ?? 'Nunca' }}
                                </td>
                                <td>
                                    <span class="px-2 py-1 text-xs rounded
                                        {{ $usuario->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                        {{ ucfirst($usuario->estado) }}
                                    </span>
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    @can('permiso', ['usuarios', 'modificar'])
                                        @if ($usuario->id === Auth::id())
                                            <span class="text-xs text-gray-500">Tu cuenta</span>
                                        @else
                                            <a href="{{ route('usuarios.edit', $usuario) }}"
                                               class="text-indigo-600 hover:underline">Editar</a>

                                            <form method="POST" action="{{ route('usuarios.estado', $usuario) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button class="ms-3 text-gray-600 hover:underline">
                                                    {{ $usuario->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-6 text-center text-gray-500">No hay usuarios.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>

                <div class="mt-4">{{ $usuarios->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>