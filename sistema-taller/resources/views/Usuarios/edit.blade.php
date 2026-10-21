<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar cuenta</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">

                <div class="mb-4 text-sm text-gray-700">
                    <div class="font-medium">{{ $usuario->empleado?->nombre ?? $usuario->cliente?->nombre }}</div>
                    <div class="text-gray-500">{{ $usuario->cliente_id ? 'Cuenta de cliente' : 'Cuenta de empleado' }}</div>
                </div>

                <form method="POST" action="{{ route('usuarios.update', $usuario) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="email" value="Correo electrónico" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                      :value="old('email', $usuario->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label value="Rol" />
                        <div class="mt-1 text-sm text-gray-700">{{ $usuario->rol?->nombre ?? 'Sin rol' }}</div>
                        <p class="text-xs text-gray-500 mt-1">
                            @if ($usuario->cliente_id)
                                Las cuentas de cliente siempre tienen el rol Cliente.
                            @else
                                El rol de un empleado sigue a su cargo. Para cambiarlo, edita el cargo en el módulo Empleados.
                            @endif
                        </p>
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="activo" @selected(old('estado', $usuario->estado) === 'activo')>Activo</option>
                            <option value="inactivo" @selected(old('estado', $usuario->estado) === 'inactivo')>Inactivo</option>
                        </select>
                        <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                    </div>

                    <div class="border-t pt-4">
                        <p class="text-sm text-gray-600 mb-2">
                            Restablecer contraseña (déjalo vacío para no cambiarla).
                        </p>
                        <x-input-label for="password" value="Nueva contraseña" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                                      autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />

                        <x-input-label for="password_confirmation" value="Confirmar nueva contraseña" class="mt-3" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                                      name="password_confirmation" autocomplete="new-password" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('usuarios.index') }}" class="text-sm text-gray-600 underline">Cancelar</a>
                        <x-primary-button>Guardar cambios</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>