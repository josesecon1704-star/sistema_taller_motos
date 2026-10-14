@csrf

<div>
    <x-input-label for="nombre" value="Nombre completo o razón social" />
    <x-text-input id="nombre" name="nombre" type="text" class="block mt-1 w-full"
                  :value="old('nombre', $cliente->nombre ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="tipo_documento" value="Tipo de documento" />
    <select id="tipo_documento" name="tipo_documento" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
        @foreach (\App\Models\Cliente::TIPOS_DOCUMENTO as $td)
            <option value="{{ $td }}" @selected(old('tipo_documento', $cliente->tipo_documento ?? 'ci') === $td)>
                {{ strtoupper($td) }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('tipo_documento')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="numero_documento" value="Número de documento" />
    <x-text-input id="numero_documento" name="numero_documento" type="text" class="block mt-1 w-full"
                  :value="old('numero_documento', $cliente->numero_documento ?? '')" required />
    <x-input-error :messages="$errors->get('numero_documento')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="telefono" value="Teléfono" />
    <x-text-input id="telefono" name="telefono" type="text" class="block mt-1 w-full"
                  :value="old('telefono', $cliente->telefono ?? '')" required />
    <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="direccion" value="Dirección (opcional)" />
    <x-text-input id="direccion" name="direccion" type="text" class="block mt-1 w-full"
                  :value="old('direccion', $cliente->direccion ?? '')" />
    <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="tipo" value="Tipo de cliente" />
    <select id="tipo" name="tipo" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
        @foreach (\App\Models\Cliente::TIPOS as $t)
            <option value="{{ $t }}" @selected(old('tipo', $cliente->tipo ?? 'particular') === $t)>
                {{ ucfirst($t) }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
</div>

<div class="flex items-center justify-end mt-6">
    <a href="{{ route('clientes.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
    <x-primary-button class="ms-4">Guardar</x-primary-button>
</div>