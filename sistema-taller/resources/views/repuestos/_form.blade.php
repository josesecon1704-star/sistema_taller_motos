@csrf

<div>
    <x-input-label for="codigo" value="Código" />
    <x-text-input id="codigo" name="codigo" type="text" class="block mt-1 w-full"
                  :value="old('codigo', $repuesto->codigo ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('codigo')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="nombre" value="Nombre (incluye la presentación, ej. Aceite Motul 1.5 L)" />
    <x-text-input id="nombre" name="nombre" type="text" class="block mt-1 w-full"
                  :value="old('nombre', $repuesto->nombre ?? '')" required />
    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="categoria" value="Categoría" />
    <select id="categoria" name="categoria" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
        @foreach (\App\Models\Repuesto::CATEGORIAS as $cat)
            <option value="{{ $cat }}" @selected(old('categoria', $repuesto->categoria ?? '') === $cat)>
                {{ ucfirst($cat) }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('categoria')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="unidad_medida" value="Unidad" />
    <select id="unidad_medida" name="unidad_medida" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
        @foreach (\App\Models\Repuesto::UNIDADES as $u)
            <option value="{{ $u }}" @selected(old('unidad_medida', $repuesto->unidad_medida ?? 'unidad') === $u)>
                {{ ucfirst($u) }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('unidad_medida')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="precio_venta" value="Precio de venta (Bs)" />
    <x-text-input id="precio_venta" name="precio_venta" type="number" step="0.01" min="0"
                  class="block mt-1 w-full" :value="old('precio_venta', $repuesto->precio_venta ?? '')" required />
    <x-input-error :messages="$errors->get('precio_venta')" class="mt-2" />
</div>

@if (isset($repuesto))
    <div class="mt-4">
        <x-input-label value="Stock actual" />
        <p class="mt-1 text-gray-700">{{ $repuesto->stock_actual }}
            <span class="text-xs text-gray-500">(cambia con movimientos de inventario, no se edita aquí)</span>
        </p>
    </div>
@else
    <div class="mt-4">
        <x-input-label for="stock_actual" value="Stock inicial" />
        <x-text-input id="stock_actual" name="stock_actual" type="number" min="0"
                      class="block mt-1 w-full" :value="old('stock_actual', 0)" required />
        <x-input-error :messages="$errors->get('stock_actual')" class="mt-2" />
    </div>
@endif

<div class="mt-4">
    <x-input-label for="stock_minimo" value="Stock mínimo (aviso de reposición)" />
    <x-text-input id="stock_minimo" name="stock_minimo" type="number" min="0"
                  class="block mt-1 w-full" :value="old('stock_minimo', $repuesto->stock_minimo ?? 0)" required />
    <x-input-error :messages="$errors->get('stock_minimo')" class="mt-2" />
</div>

<div class="flex items-center justify-end mt-6">
    <a href="{{ route('repuestos.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
    <x-primary-button class="ms-4">Guardar</x-primary-button>
</div>