@csrf

<div>
    <x-input-label for="nombre" value="Nombre completo" />
    <x-text-input id="nombre" name="nombre" type="text" class="block mt-1 w-full"
                  :value="old('nombre', $empleado->nombre ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="ci" value="CI" />
    <x-text-input id="ci" name="ci" type="text" class="block mt-1 w-full"
                  :value="old('ci', $empleado->ci ?? '')" required />
    <x-input-error :messages="$errors->get('ci')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="telefono" value="Teléfono" />
    <x-text-input id="telefono" name="telefono" type="text" class="block mt-1 w-full"
                  :value="old('telefono', $empleado->telefono ?? '')" required />
    <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="cargo" value="Cargo" />
    <select id="cargo" name="cargo" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
        @foreach (\App\Models\Empleado::CARGOS as $clave => $etiqueta)
            <option value="{{ $clave }}" @selected(old('cargo', $empleado->cargo ?? 'mecanico') === $clave)>
                {{ $etiqueta }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('cargo')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="especialidad" value="Especialidad (opcional, ej. Motor, Frenos)" />
    <x-text-input id="especialidad" name="especialidad" type="text" class="block mt-1 w-full"
                  :value="old('especialidad', $empleado->especialidad ?? '')" />
    <x-input-error :messages="$errors->get('especialidad')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="fecha_contratacion" value="Fecha de contratación" />
    <x-text-input id="fecha_contratacion" name="fecha_contratacion" type="date" class="block mt-1 w-full"
                  :value="old('fecha_contratacion', isset($empleado) ? $empleado->fecha_contratacion->format('Y-m-d') : now()->toDateString())"
                  required />
    <x-input-error :messages="$errors->get('fecha_contratacion')" class="mt-2" />
</div>

<div class="flex items-center justify-end mt-6">
    <a href="{{ route('empleados.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
    <x-primary-button class="ms-4">Guardar</x-primary-button>
</div>