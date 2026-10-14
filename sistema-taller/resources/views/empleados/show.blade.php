<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $empleado->nombre }}</h2>

            @can('permiso', ['empleados', 'modificar'])
                <a href="{{ route('empleados.edit', $empleado) }}"
                   class="px-4 py-2 bg-gray-800 text-white text-xs uppercase rounded-md hover:bg-gray-700">
                    Editar
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Datos del empleado</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div><dt class="text-gray-500">CI</dt><dd>{{ $empleado->ci }}</dd></div>
                    <div><dt class="text-gray-500">Cargo</dt>
                        <dd>{{ \App\Models\Empleado::CARGOS[$empleado->cargo] ?? $empleado->cargo }}</dd></div>
                    <div><dt class="text-gray-500">Teléfono</dt><dd>{{ $empleado->telefono }}</dd></div>
                    <div><dt class="text-gray-500">Especialidad</dt><dd>{{ $empleado->especialidad ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Contratado el</dt>
                        <dd>{{ $empleado->fecha_contratacion->format('d/m/Y') }}</dd></div>
                    <div><dt class="text-gray-500">Estado</dt>
                        <dd>
                            <span class="px-2 py-1 text-xs rounded
                                {{ $empleado->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                {{ ucfirst($empleado->estado) }}
                            </span>
                        </dd></div>
                </dl>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Cuenta de acceso</h3>

                @if ($empleado->usuario)
                    <p class="text-sm">Correo: <strong>{{ $empleado->usuario->email }}</strong></p>
                    <p class="text-sm mt-1">Rol: <strong>{{ $empleado->usuario->rol->nombre }}</strong></p>
                    <p class="text-sm mt-1">Último acceso:
                        {{ $empleado->usuario->ultimo_acceso?->format('d/m/Y H:i') ?? 'nunca ha entrado' }}</p>
                @else
                    <p class="text-sm text-gray-600">
                        Este empleado no tiene cuenta para entrar al sistema. Es válido: hay empleados que no
                        necesitan acceso.
                    </p>
                    @can('permiso', ['usuarios', 'crear'])
                        <a href="{{ route('empleados.cuenta', $empleado) }}"
                           class="inline-block mt-3 px-4 py-2 bg-gray-800 text-white text-xs uppercase rounded-md hover:bg-gray-700">
                            Crear cuenta de acceso
                        </a>
                    @endcan
                @endif
            </div>

            <div>
                <a href="{{ route('empleados.index') }}"
                   class="underline text-sm text-gray-600 hover:text-gray-900">Volver a empleados</a>
            </div>
        </div>
    </div>
</x-app-layout>