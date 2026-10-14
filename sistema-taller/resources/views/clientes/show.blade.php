<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $cliente->nombre }}</h2>

            @can('permiso', ['clientes', 'modificar'])
                <a href="{{ route('clientes.edit', $cliente) }}"
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
                <h3 class="font-semibold mb-4">Datos del cliente</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div><dt class="text-gray-500">Documento</dt>
                        <dd>{{ strtoupper($cliente->tipo_documento) }} {{ $cliente->numero_documento }}</dd></div>
                    <div><dt class="text-gray-500">Tipo</dt><dd>{{ ucfirst($cliente->tipo) }}</dd></div>
                    <div><dt class="text-gray-500">Teléfono</dt><dd>{{ $cliente->telefono }}</dd></div>
                    <div><dt class="text-gray-500">Dirección</dt><dd>{{ $cliente->direccion ?? '—' }}</dd></div>
                </dl>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Cuenta de acceso</h3>

                @if ($cliente->usuario)
                    <p class="text-sm">Correo: <strong>{{ $cliente->usuario->email }}</strong></p>
                    <p class="text-sm mt-1">Estado:
                        <span class="px-2 py-1 text-xs rounded
                            {{ $cliente->usuario->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                            {{ ucfirst($cliente->usuario->estado) }}
                        </span>
                    </p>
                @else
                    <p class="text-sm text-gray-600">Este cliente todavía no tiene cuenta para entrar al sistema.</p>
                    @can('permiso', ['usuarios', 'crear'])
                        <a href="{{ route('clientes.cuenta', $cliente) }}"
                           class="inline-block mt-3 px-4 py-2 bg-gray-800 text-white text-xs uppercase rounded-md hover:bg-gray-700">
                            Crear cuenta de acceso
                        </a>
                    @endcan
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Motocicletas</h3>

                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b">
                        <tr>
                            <th class="py-2">Placa</th>
                            <th>Marca y modelo</th>
                            <th>Año</th>
                            <th class="text-right">Kilometraje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cliente->motocicletas as $moto)
                            <tr class="border-b">
                                <td class="py-2">{{ $moto->placa ?? 'Sin placa' }}</td>
                                <td>{{ $moto->marca }} {{ $moto->modelo }}</td>
                                <td>{{ $moto->anio }}</td>
                                <td class="text-right">{{ number_format($moto->kilometraje) }} km</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-500">Sin motocicletas registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                <a href="{{ route('clientes.index') }}"
                   class="underline text-sm text-gray-600 hover:text-gray-900">Volver a clientes</a>
            </div>
        </div>
    </div>
</x-app-layout>