@php
    $esCliente = Auth::user()->esCliente();
    $colores = ['vigente' => 'bg-green-100 text-green-800', 'vencida' => 'bg-gray-200 text-gray-700', 'anulada' => 'bg-red-100 text-red-800'];
    $coloresReclamo = ['pendiente' => 'bg-yellow-100 text-yellow-800', 'aprobado' => 'bg-green-100 text-green-800', 'rechazado' => 'bg-red-100 text-red-800'];
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $esCliente ? 'Mis garantías' : 'Garantías' }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="p-3 bg-red-100 text-red-800 rounded">{{ $errors->first() }}</div>
            @endif

            <form method="GET" class="flex flex-wrap gap-2 items-center">
                <select name="estado" class="border-gray-300 rounded-md shadow-sm w-full sm:w-auto">
                    <option value="">Todos los estados</option>
                    @foreach (\App\Models\Garantia::ESTADOS as $estado)
                        <option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ ucfirst($estado) }}</option>
                    @endforeach
                </select>
                <x-primary-button>Filtrar</x-primary-button>
            </form>

            @forelse ($garantias as $garantia)
                @php $orden = $garantia->ordenTrabajo; @endphp
                <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6" x-data="{ editar: false }">
                    <div class="flex flex-wrap justify-between gap-2">
                        <div>
                            <div class="font-semibold text-gray-800">Orden {{ $orden?->nro_orden }}</div>
                            <div class="text-sm text-gray-500">
                                {{ $orden?->motocicleta?->marca }} {{ $orden?->motocicleta?->modelo }} ({{ $orden?->motocicleta?->placa ?: 'sin placa' }})
                                @unless ($esCliente) · {{ $orden?->cliente?->nombre }} @endunless
                            </div>
                        </div>
                        <span class="self-start px-2 py-1 text-xs rounded {{ $colores[$garantia->estado] ?? 'bg-gray-100' }}">{{ ucfirst($garantia->estado) }}</span>
                    </div>

                    <dl class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                        <div><dt class="text-gray-500">Desde</dt><dd>{{ $garantia->fecha_inicio?->format('d/m/Y') }}</dd></div>
                        <div><dt class="text-gray-500">Hasta</dt><dd>{{ $garantia->fechaVencimiento()?->format('d/m/Y') }}</dd></div>
                        <div><dt class="text-gray-500">Plazo</dt><dd>{{ $garantia->dias }} días</dd></div>
                        <div><dt class="text-gray-500">Límite</dt><dd>{{ number_format($garantia->km) }} km</dd></div>
                        <div class="col-span-2 sm:col-span-4"><dt class="text-gray-500">Cobertura</dt><dd>{{ $garantia->cobertura ?: '—' }}</dd></div>
                    </dl>

                    @can('permiso', ['garantias', 'modificar'])
                        <button type="button" @click="editar = !editar" class="mt-3 text-sm text-indigo-600 underline">Editar condiciones</button>
                        <form x-show="editar" x-cloak method="POST" action="{{ route('garantias.update', $garantia) }}" class="mt-3 grid grid-cols-1 sm:grid-cols-4 gap-2 items-end">
                            @csrf
                            @method('PATCH')
                            <div><x-input-label value="Días" /><x-text-input type="number" name="dias" min="1" class="w-full" :value="$garantia->dias" required /></div>
                            <div><x-input-label value="Km" /><x-text-input type="number" name="km" min="1" class="w-full" :value="$garantia->km" required /></div>
                            <div>
                                <x-input-label value="Estado" />
                                <select name="estado" class="w-full border-gray-300 rounded-md shadow-sm">
                                    @foreach (\App\Models\Garantia::ESTADOS as $estado)
                                        <option value="{{ $estado }}" @selected($garantia->estado === $estado)>{{ ucfirst($estado) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <x-primary-button>Guardar</x-primary-button>
                            <div class="sm:col-span-4"><x-input-label value="Cobertura" /><x-text-input name="cobertura" class="w-full" :value="$garantia->cobertura" /></div>
                        </form>
                    @endcan

                    <div class="mt-4 border-t pt-3">
                        <div class="text-sm font-semibold text-gray-700 mb-2">Reclamos</div>
                        @forelse ($garantia->reclamos as $reclamo)
                            <div class="flex flex-wrap justify-between gap-2 py-2 border-b last:border-0 text-sm">
                                <div>
                                    <span class="text-gray-500">{{ $reclamo->fecha?->format('d/m/Y') }}</span> — {{ $reclamo->descripcion }}
                                    @if ($reclamo->km) <span class="text-gray-500">({{ number_format($reclamo->km) }} km)</span> @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 text-xs rounded {{ $coloresReclamo[$reclamo->estado] ?? 'bg-gray-100' }}">{{ ucfirst($reclamo->estado) }}</span>
                                    @if ($reclamo->estado === 'pendiente')
                                        @can('permiso', ['garantias', 'modificar'])
                                            @foreach (['aprobado' => 'Aprobar', 'rechazado' => 'Rechazar'] as $valor => $texto)
                                                <form method="POST" action="{{ route('reclamos-garantias.estado', $reclamo) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="estado" value="{{ $valor }}">
                                                    <button class="text-xs underline {{ $valor === 'aprobado' ? 'text-green-700' : 'text-red-700' }}">{{ $texto }}</button>
                                                </form>
                                            @endforeach
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Sin reclamos.</p>
                        @endforelse

                        @if ($garantia->estaVigente())
                            @can('permiso', ['garantias', 'crear'])
                                <form method="POST" action="{{ route('reclamos-garantias.store', $garantia) }}" class="mt-3 grid grid-cols-1 sm:grid-cols-4 gap-2 items-end">
                                    @csrf
                                    <div class="sm:col-span-2">
                                        <x-input-label value="Nuevo reclamo" />
                                        <x-text-input name="descripcion_problema" class="w-full" maxlength="255" placeholder="Describe el problema" required />
                                    </div>
                                    <div><x-input-label value="Km actual" /><x-text-input type="number" name="km" min="0" class="w-full" /></div>
                                    <x-primary-button>Registrar reclamo</x-primary-button>
                                </form>
                            @endcan
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    No hay garantías. Se generan al completar o entregar una orden de trabajo.
                </div>
            @endforelse

            <div>{{ $garantias->links() }}</div>
        </div>
    </div>
</x-app-layout>
