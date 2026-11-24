<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Repuestos de {{ $proveedor->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Vincular un repuesto</h3>

                @if ($disponibles->isEmpty())
                    <p class="text-gray-500 text-sm">Todos los repuestos activos ya están vinculados.</p>
                @else
                    <form method="POST" action="{{ route('proveedores.repuestos.store', $proveedor) }}"
                          class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                        @csrf

                        <div class="md:col-span-2">
                            <x-input-label for="repuesto_id" value="Repuesto" />
                            <select id="repuesto_id" name="repuesto_id"
                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">— Selecciona —</option>
                                @foreach ($disponibles as $r)
                                    <option value="{{ $r->id }}" @selected(old('repuesto_id') == $r->id)>
                                        {{ $r->codigo }} · {{ $r->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('repuesto_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="precio_compra" value="Precio de compra (Bs)" />
                            <x-text-input id="precio_compra" name="precio_compra" type="number" step="0.01" min="0"
                                          class="block mt-1 w-full" :value="old('precio_compra')" required />
                            <x-input-error :messages="$errors->get('precio_compra')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="codigo_proveedor" value="Código del proveedor" />
                            <x-text-input id="codigo_proveedor" name="codigo_proveedor" type="text"
                                          class="block mt-1 w-full" :value="old('codigo_proveedor')" />
                            <x-input-error :messages="$errors->get('codigo_proveedor')" class="mt-2" />
                        </div>

                        <div class="md:col-span-4 text-right">
                            <x-primary-button>Vincular</x-primary-button>
                        </div>
                    </form>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Repuestos que vende</h3>

                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b">
                        <tr>
                            <th class="py-2">Código</th>
                            <th>Repuesto</th>
                            <th>Precio de compra (Bs)</th>
                            <th>Código del proveedor</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vinculados as $r)
                            <tr class="border-b">
                                <form method="POST"
                                      action="{{ route('proveedores.repuestos.update', [$proveedor, $r]) }}">
                                    @csrf
                                    @method('PUT')
                                    <td class="py-2">{{ $r->codigo }}</td>
                                    <td>{{ $r->nombre }}</td>
                                    <td>
                                        <input type="number" name="precio_compra" step="0.01" min="0" required
                                               value="{{ $r->pivot->precio_compra }}"
                                               class="w-28 border-gray-300 rounded-md shadow-sm text-sm">
                                    </td>
                                    <td>
                                        <input type="text" name="codigo_proveedor" maxlength="30"
                                               value="{{ $r->pivot->codigo_proveedor }}"
                                               class="w-32 border-gray-300 rounded-md shadow-sm text-sm">
                                    </td>
                                    <td class="text-right whitespace-nowrap">
                                        <button class="text-indigo-600 hover:underline">Guardar</button>
                                </form>
                                        <form method="POST" class="inline"
                                              action="{{ route('proveedores.repuestos.destroy', [$proveedor, $r]) }}"
                                              onsubmit="return confirm('¿Desvincular este repuesto del proveedor?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="ms-3 text-red-600 hover:underline">Quitar</button>
                                        </form>
                                    </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-gray-500">Aún no hay repuestos vinculados.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    <a href="{{ route('proveedores.index') }}"
                       class="underline text-sm text-gray-600 hover:text-gray-900">Volver a proveedores</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>