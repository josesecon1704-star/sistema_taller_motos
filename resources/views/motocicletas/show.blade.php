<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $moto->marca }} {{ $moto->modelo }} — {{ $moto->placa ?? 'Sin placa' }}
            </h2>

            @can('permiso', ['motocicletas', 'modificar'])
                <a href="{{ route('motocicletas.edit', $moto) }}"
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

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Datos de la motocicleta</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div><dt class="text-gray-500">Placa</dt><dd>{{ $moto->placa ?? 'Sin placa' }}</dd></div>
                    <div><dt class="text-gray-500">Número de chasis</dt><dd>{{ $moto->nro_chasis }}</dd></div>
                    <div><dt class="text-gray-500">Marca y modelo</dt><dd>{{ $moto->marca }} {{ $moto->modelo }}</dd></div>
                    <div><dt class="text-gray-500">Año</dt><dd>{{ $moto->anio }}</dd></div>
                    <div><dt class="text-gray-500">Color</dt><dd>{{ $moto->color }}</dd></div>
                    <div><dt class="text-gray-500">Cilindrada</dt><dd>{{ $moto->cilindrada }} cc</dd></div>
                    <div><dt class="text-gray-500">Número de motor</dt><dd>{{ $moto->nro_motor }}</dd></div>
                    <div><dt class="text-gray-500">Kilometraje</dt>
                        <dd>{{ number_format($moto->kilometraje, 0, ',', '.') }} km</dd></div>
                </dl>
            </div>

            @unless (Auth::user()->esCliente())
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold mb-2">Propietario</h3>
                    <p class="text-sm">
                        <a href="{{ route('clientes.show', $moto->cliente) }}"
                           class="text-indigo-600 hover:underline">{{ $moto->cliente->nombre }}</a>
                        — {{ strtoupper($moto->cliente->tipo_documento) }} {{ $moto->cliente->numero_documento }}
                        · Tel. {{ $moto->cliente->telefono }}
                    </p>
                </div>
            @endunless

            <div>
                <a href="{{ route('motocicletas.index') }}"
                   class="underline text-sm text-gray-600 hover:text-gray-900">Volver a motocicletas</a>
            </div>
        </div>
    </div>
</x-app-layout>