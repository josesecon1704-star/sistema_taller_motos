@csrf

@unless (Auth::user()->esCliente())
    <div>
        <x-input-label for="cliente_id" value="Propietario" />
        <select id="cliente_id" name="cliente_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
            <option value="">— Selecciona un cliente —</option>
            @foreach ($clientes as $c)
                <option value="{{ $c->id }}"
                    @selected(old('cliente_id', $moto->cliente_id ?? request('cliente_id')) == $c->id)>
                    {{ $c->nombre }} — {{ strtoupper($c->tipo_documento) }} {{ $c->numero_documento }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />
    </div>
@endunless

<div class="mt-4">
    <x-input-label for="placa" value="Placa (opcional si la moto es nueva)" />
    <x-text-input id="placa" name="placa" type="text" maxlength="10" class="block mt-1 w-full uppercase"
                  :value="old('placa', $moto->placa ?? '')" />
    <x-input-error :messages="$errors->get('placa')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="nro_chasis" value="Número de chasis" />
    <x-text-input id="nro_chasis" name="nro_chasis" type="text" maxlength="17" class="block mt-1 w-full uppercase"
                  :value="old('nro_chasis', $moto->nro_chasis ?? '')" required />
    <x-input-error :messages="$errors->get('nro_chasis')" class="mt-2" />
</div>

<div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="marca" value="Marca" />
        <x-text-input id="marca" name="marca" type="text" list="marcas" class="block mt-1 w-full"
                      :value="old('marca', $moto->marca ?? '')" required />
        <datalist id="marcas">
            @foreach (\App\Models\Motocicleta::MARCAS as $m)
                <option value="{{ $m }}">
            @endforeach
        </datalist>
        <x-input-error :messages="$errors->get('marca')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="modelo" value="Modelo" />
        <x-text-input id="modelo" name="modelo" type="text" class="block mt-1 w-full"
                      :value="old('modelo', $moto->modelo ?? '')" required />
        <x-input-error :messages="$errors->get('modelo')" class="mt-2" />
    </div>
</div>

<div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
        <x-input-label for="anio" value="Año" />
        <x-text-input id="anio" name="anio" type="number" min="1950" max="{{ now()->year + 1 }}"
                      class="block mt-1 w-full" :value="old('anio', $moto->anio ?? '')" required />
        <x-input-error :messages="$errors->get('anio')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="color" value="Color" />
        <x-text-input id="color" name="color" type="text" class="block mt-1 w-full"
                      :value="old('color', $moto->color ?? '')" required />
        <x-input-error :messages="$errors->get('color')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="cilindrada" value="Cilindrada (cc)" />
        <x-text-input id="cilindrada" name="cilindrada" type="number" min="1"
                      class="block mt-1 w-full" :value="old('cilindrada', $moto->cilindrada ?? '')" required />
        <x-input-error :messages="$errors->get('cilindrada')" class="mt-2" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="nro_motor" value="Número de motor" />
    <x-text-input id="nro_motor" name="nro_motor" type="text" class="block mt-1 w-full uppercase"
                  :value="old('nro_motor', $moto->nro_motor ?? '')" required />
    <x-input-error :messages="$errors->get('nro_motor')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="kilometraje" value="Kilometraje actual" />
    <x-text-input id="kilometraje" name="kilometraje" type="number"
                  min="{{ $moto->kilometraje ?? 0 }}" class="block mt-1 w-full"
                  :value="old('kilometraje', $moto->kilometraje ?? 0)" required />
    @isset($moto)
        <p class="mt-1 text-xs text-gray-500">No puede ser menor al kilometraje ya registrado.</p>
    @endisset
    <x-input-error :messages="$errors->get('kilometraje')" class="mt-2" />
</div>

<div class="flex items-center justify-end mt-6">
    <a href="{{ route('motocicletas.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
    <x-primary-button class="ms-4">Guardar</x-primary-button>
</div>