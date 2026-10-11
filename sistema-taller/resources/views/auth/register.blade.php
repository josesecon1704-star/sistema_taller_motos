<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="nombre" value="Nombre completo" />
            <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre')" required autofocus />
            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
        </div>

        <!-- tipo de documento -->
        <div class="mt-4">
            <x-input-label for="tipo_documento" value="Tipo de documento" />
            <select id="tipo_documento" name="tipo_documento" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="ci" @selected(old('tipo_documento') === 'ci')>CI</option>
                <option value="nit" @selected(old('tipo_documento') === 'nit')>NIT</option>
                <option value="pasaporte" @selected(old('tipo_documento') === 'pasaporte')>Pasaporte</option>
            </select>
            <x-input-error :messages="$errors->get('tipo_documento')" class="mt-2" />
        </div>

        <!-- ci numero -->
        <div class="mt-4">
            <x-input-label for="numero_documento" value="Número de documento" />
            <x-text-input id="numero_documento" class="block mt-1 w-full" type="text" name="numero_documento" :value="old('numero_documento')" required />
            <x-input-error :messages="$errors->get('numero_documento')" class="mt-2" />
        </div>
        
        <!-- telefono -->
        <div class="mt-4">
            <x-input-label for="telefono" value="Teléfono" />
            <x-text-input id="telefono" class="block mt-1 w-full" type="text" name="telefono" :value="old('telefono')" required />
            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
        </div>


        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('¿Ya tienes cuenta?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Registrarme') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
