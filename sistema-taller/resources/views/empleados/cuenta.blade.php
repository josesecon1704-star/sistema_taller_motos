<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Cuenta de acceso: {{ $empleado->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600 mb-4">
                    El rol de la cuenta será
                    <strong>{{ \App\Models\Empleado::CARGOS[$empleado->cargo] }}</strong>,
                    según su cargo. Entrega el correo y la contraseña al empleado y recomiéndale cambiarla al entrar.
                </p>

                <form method="POST" action="{{ route('empleados.cuenta.store', $empleado) }}" autocomplete="off">
                    @csrf

                    <div>
                        <x-input-label for="email" value="Correo electrónico" />
                        <x-text-input id="email" name="email" type="email" class="block mt-1 w-full"
                                      :value="old('email')" required autofocus />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="password" value="Contraseña" />
                        <x-text-input id="password" name="password" type="password"
                                      class="block mt-1 w-full" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="password_confirmation" value="Confirmar contraseña" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                      class="block mt-1 w-full" required autocomplete="new-password" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('empleados.show', $empleado) }}"
                           class="underline text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                        <x-primary-button class="ms-4">Crear cuenta</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>