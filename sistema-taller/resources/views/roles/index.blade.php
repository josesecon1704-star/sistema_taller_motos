<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Roles y permisos</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600 mb-4">
                    Cada usuario recibe los permisos de su rol. Los cambios se aplican en la siguiente
                    pantalla que abra la persona, sin necesidad de volver a iniciar sesión.
                </p>

                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b">
                        <tr>
                            <th class="py-2">Rol</th>
                            <th>Descripción</th>
                            <th class="text-right">Usuarios</th>
                            <th class="text-right">Permisos</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $rol)
                            <tr class="border-b">
                                <td class="py-2 font-medium">{{ $rol->nombre }}</td>
                                <td>{{ $rol->descripcion }}</td>
                                <td class="text-right">{{ $rol->usuarios_count }}</td>
                                <td class="text-right">{{ $rol->permisos_count }}</td>
                                <td class="text-right whitespace-nowrap">
                                    @if ($rol->nombre === 'Administrador')
                                        <span class="text-xs text-gray-500">Acceso total (bloqueado)</span>
                                    @elseif ($rol->id === Auth::user()->rol_id)
                                        <span class="text-xs text-gray-500">Tu rol (bloqueado)</span>
                                    @else
                                        @can('permiso', ['roles', 'modificar'])
                                            <a href="{{ route('roles.permisos', $rol) }}"
                                               class="text-indigo-600 hover:underline">Permisos</a>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>