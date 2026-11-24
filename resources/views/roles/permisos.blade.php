<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Permisos del rol: {{ $rol->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('roles.permisos.update', $rol) }}">
                    @csrf
                    @method('PUT')

                    <div class="flex justify-between items-center mb-4">
                        <p class="text-sm text-gray-600">Marca lo que este rol puede hacer en cada módulo.</p>
                        <div class="text-sm space-x-3">
                            <button type="button" class="text-indigo-600 hover:underline"
                                    @click="$el.closest('form').querySelectorAll('input[type=checkbox]').forEach(c => c.checked = true)">
                                Marcar todo
                            </button>
                            <button type="button" class="text-gray-600 hover:underline"
                                    @click="$el.closest('form').querySelectorAll('input[type=checkbox]').forEach(c => c.checked = false)">
                                Desmarcar todo
                            </button>
                        </div>
                    </div>

                    <table class="w-full text-sm text-left">
                        <thead class="text-gray-600 border-b">
                            <tr>
                                <th class="py-2">Módulo</th>
                                @foreach (\App\Models\Permiso::ACCIONES as $etiqueta)
                                    <th class="text-center">{{ $etiqueta }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($porModulo as $modulo => $permisos)
                                <tr class="border-b">
                                    <td class="py-2">{{ \App\Models\Permiso::MODULOS[$modulo] ?? ucfirst($modulo) }}</td>
                                    @foreach (\App\Models\Permiso::ACCIONES as $accion => $etiqueta)
                                        @php $permiso = $permisos->firstWhere('accion', $accion); @endphp
                                        <td class="text-center">
                                            @if ($permiso)
                                                <input type="checkbox" name="permisos[]" value="{{ $permiso->id }}"
                                                       class="rounded border-gray-300"
                                                       @checked(in_array($permiso->id, old('permisos', $asignados)))>
                                            @else
                                                <span class="text-gray-300">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <x-input-error :messages="$errors->get('permisos')" class="mt-2" />

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('roles.index') }}"
                           class="underline text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                        <x-primary-button class="ms-4">Guardar permisos</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>