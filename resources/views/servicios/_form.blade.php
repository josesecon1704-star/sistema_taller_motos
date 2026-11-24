@csrf

<div>
    <x-input-label for="nombre" value="Nombre" />
    <x-text-input id="nombre" name="nombre" type="text" class="block mt-1 w-full"
                  :value="old('nombre', $servicio->nombre ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="categoria" value="Categoría" />
    <select id="categoria" name="categoria" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
        @foreach (\App\Models\Servicio::CATEGORIAS as $cat)
            <option value="{{ $cat }}" @selected(old('categoria', $servicio->categoria ?? '') === $cat)>
                {{ ucfirst($cat) }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('categoria')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="precio_base" value="Precio base (Bs)" />
    <x-text-input id="precio_base" name="precio_base" type="number" step="0.01" min="0"
                  class="block mt-1 w-full" :value="old('precio_base', $servicio->precio_base ?? '')" required />
    <x-input-error :messages="$errors->get('precio_base')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="descripcion" value="Descripción (opcional)" />
    <x-text-input id="descripcion" name="descripcion" type="text" class="block mt-1 w-full"
                  :value="old('descripcion', $servicio->descripcion ?? '')" />
    <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
</div>

<div class="flex items-center justify-end mt-6">
    <a href="{{ route('servicios.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
    <x-primary-button class="ms-4">Guardar</x-primary-button>
</div>