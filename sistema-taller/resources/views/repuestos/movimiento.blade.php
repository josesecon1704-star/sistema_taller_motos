<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Stock: {{ $repuesto->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-600">Código: {{ $repuesto->codigo }}</p>
                <p class="text-2xl font-semibold mt-1">
                    Stock actual: {{ $repuesto->stock_actual }}
                    @if ($repuesto->stock_actual <= $repuesto->stock_minimo)
                        <span class="ms-2 px-2 py-1 text-xs rounded bg-red-100 text-red-800">Stock bajo</span>
                    @endif
                </p>
                <p class="text-sm text-gray-500">Stock mínimo: {{ $repuesto->stock_minimo }}</p>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Registrar movimiento</h3>

                <form method="POST" action="{{ route('repuestos.movimiento.store', $repuesto) }}"
                      x-data="{ mov: '{{ old('movimiento', 'compra') }}' }">
                    @csrf

                    <div>
                        <x-input-label for="movimiento" value="Tipo de movimiento" />
                        <select id="movimiento" name="movimiento" x-model="mov"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            @foreach (\App\Models\MovimientoInventario::MOVIMIENTOS as $clave => [$tipo, $motivo, $etiqueta])
                                <option value="{{ $clave }}">{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('movimiento')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="cantidad" value="Cantidad" />
                        <x-text-input id="cantidad" name="cantidad" type="number" min="1"
                                      class="block mt-1 w-full" :value="old('cantidad')" required />
                        <x-input-error :messages="$errors->get('cantidad')" class="mt-2" />
                    </div>

                    <div x-show="mov === 'compra'" class="mt-4 space-y-4">
                        <div>
                            <x-input-label for="proveedor_id" value="Proveedor (opcional)" />
                            <select id="proveedor_id" name="proveedor_id"
                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">— Sin proveedor —</option>
                                @foreach ($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" @selected(old('proveedor_id') == $proveedor->id)>
                                        {{ $proveedor->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('proveedor_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="costo_unitario" value="Costo por unidad (Bs, opcional)" />
                            <x-text-input id="costo_unitario" name="costo_unitario" type="number" step="0.01" min="0"
                                          class="block mt-1 w-full" :value="old('costo_unitario')" />
                            <x-input-error :messages="$errors->get('costo_unitario')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="observaciones">
                            Observaciones
                            <span x-show="mov !== 'compra'" class="text-red-600">(obligatorio)</span>
                        </x-input-label>
                        <x-text-input id="observaciones" name="observaciones" type="text"
                                      class="block mt-1 w-full" :value="old('observaciones')" />
                        <x-input-error :messages="$errors->get('observaciones')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('repuestos.index') }}"
                           class="underline text-sm text-gray-600 hover:text-gray-900">Volver</a>
                        <x-primary-button class="ms-4">Registrar</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Últimos movimientos</h3>

                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b">
                        <tr>
                            <th class="py-2">Fecha</th>
                            <th>Motivo</th>
                            <th class="text-right">Cantidad</th>
                            <th>Proveedor</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($historial as $mov)
                            <tr class="border-b">
                                <td class="py-2">{{ $mov->fecha_movimiento->format('d/m/Y H:i') }}</td>
                                <td>{{ ucfirst($mov->motivo) }}</td>
                                <td class="text-right font-semibold
                                    {{ $mov->tipo === 'entrada' ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $mov->tipo === 'entrada' ? '+' : '−' }}{{ $mov->cantidad }}
                                </td>
                                <td>{{ $mov->proveedor?->nombre ?? '—' }}</td>
                                <td>{{ $mov->observaciones ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-gray-500">Sin movimientos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>