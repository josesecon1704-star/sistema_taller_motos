<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Bitácora</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="GET" class="flex flex-wrap items-end gap-2 mb-4">
                    <select name="user_id" class="border-gray-300 rounded-md shadow-sm">
                        <option value="">Todos los usuarios</option>
                        @foreach ($usuarios as $u)
                            <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->email }}</option>
                        @endforeach
                    </select>
                    <select name="accion" class="border-gray-300 rounded-md shadow-sm">
                        <option value="">Todas las acciones</option>
                        @foreach ($acciones as $a)
                            <option value="{{ $a }}" @selected(request('accion') === $a)>{{ $a }}</option>
                        @endforeach
                    </select>
                    <select name="tabla" class="border-gray-300 rounded-md shadow-sm">
                        <option value="">Todas las tablas</option>
                        @foreach ($tablas as $t)
                            <option value="{{ $t }}" @selected(request('tabla') === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                    <div>
                        <label class="block text-xs text-gray-500">Desde</label>
                        <input type="date" name="desde" value="{{ request('desde') }}"
                               class="border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">Hasta</label>
                        <input type="date" name="hasta" value="{{ request('hasta') }}"
                               class="border-gray-300 rounded-md shadow-sm">
                    </div>
                    <x-primary-button>Filtrar</x-primary-button>
                </form>

                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b">
                        <tr>
                            <th class="py-2">Fecha y hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Tabla</th>
                            <th>Registro</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($registros as $r)
                            @php
                                $anterior = json_decode($r->valor_anterior ?? '', true) ?? [];
                                $nuevo = json_decode($r->valor_nuevo ?? '', true) ?? [];
                                $claves = array_unique(array_merge(array_keys($anterior), array_keys($nuevo)));
                            @endphp
                            <tr class="border-b align-top">
                                <td class="py-2 whitespace-nowrap">{{ $r->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $r->user?->name ?? 'Sin sesión' }}</td>
                                <td>
                                    <span class="px-2 py-1 text-xs rounded
                                        {{ $r->accion === 'login_fallido' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $r->accion }}
                                    </span>
                                </td>
                                <td>{{ $r->tabla_afectada ?? '—' }}</td>
                                <td>{{ $r->registro_id ?? '—' }}</td>
                                <td>
                                    @if ($claves)
                                        <details>
                                            <summary class="cursor-pointer text-indigo-600">Ver cambios</summary>
                                            <ul class="mt-1 text-xs text-gray-700 space-y-1">
                                                @foreach ($claves as $c)
                                                    <li>
                                                        <strong>{{ $c }}:</strong>
                                                        @if (array_key_exists($c, $anterior))
                                                            {{ $anterior[$c] ?? 'vacío' }} →
                                                        @endif
                                                        {{ array_key_exists($c, $nuevo) ? ($nuevo[$c] ?? 'vacío') : '—' }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </details>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-6 text-center text-gray-500">No hay registros.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $registros->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>