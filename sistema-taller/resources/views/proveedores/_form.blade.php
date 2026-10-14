@csrf

<div>
    <x-input-label for="nombre" value="Nombre o razón social" />
    <x-text-input id="nombre" name="nombre" type="text" class="block mt-1 w-full"
                  :value="old('nombre', $proveedor->nombre ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="nit" value="NIT (opcional)" />
    <x-text-input id="nit" name="nit" type="text" class="block mt-1 w-full"
                  :value="old('nit', $proveedor->nit ?? '')" />
    <x-input-error :messages="$errors->get('nit')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="telefono" value="Teléfono" />
    <x-text-input id="telefono" name="telefono" type="text" class="block mt-1 w-full"
                  :value="old('telefono', $proveedor->telefono ?? '')" required />
    <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="email" value="Correo (opcional)" />
    <x-text-input id="email" name="email" type="email" class="block mt-1 w-full"
                  :value="old('email', $proveedor->email ?? '')" />
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="direccion" value="Dirección (opcional)" />
    <x-text-input id="direccion" name="direccion" type="text" class="block mt-1 w-full"
                  :value="old('direccion', $proveedor->direccion ?? '')" />
    <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
</div>

<div class="flex items-center justify-end mt-6">
    <a href="{{ route('proveedores.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
    <x-primary-button class="ms-4">Guardar</x-primary-button>
</div>