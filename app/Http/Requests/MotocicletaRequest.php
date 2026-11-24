<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MotocicletaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el permiso lo controla el middleware
    }

    protected function prepareForValidation(): void
    {
        // Placa y chasis siempre en mayúsculas; una placa vacía se guarda como NULL
        $this->merge([
            'placa'      => $this->placa ? strtoupper(trim($this->placa)) : null,
            'nro_chasis' => $this->nro_chasis ? strtoupper(trim($this->nro_chasis)) : $this->nro_chasis,
        ]);
    }

    public function rules(): array
    {
        $moto = $this->route('motocicleta');   // null al crear, el modelo al editar

        return [
            // El cliente elige el dueño solo si quien registra es del personal
            'cliente_id'  => [Rule::requiredIf(fn () => ! $this->user()->esCliente()),
                              'nullable', 'exists:clientes,id'],
            'placa'       => ['nullable', 'string', 'max:10',
                              Rule::unique('motocicletas', 'placa')->ignore($moto)],
            'nro_chasis'  => ['required', 'string', 'max:17',
                              Rule::unique('motocicletas', 'nro_chasis')->ignore($moto)],
            'marca'       => ['required', 'string', 'max:100'],
            'modelo'      => ['required', 'string', 'max:100'],
            'anio'        => ['required', 'integer', 'between:1950,' . (now()->year + 1)],
            'color'       => ['required', 'string', 'max:30'],
            'cilindrada'  => ['required', 'integer', 'min:1', 'max:65535'],
            'nro_motor'   => ['required', 'string', 'max:30'],
            // El odómetro no puede retroceder
            'kilometraje' => ['required', 'integer', 'min:' . ($moto?->kilometraje ?? 0), 'max:16777215'],
        ];
    }

    public function messages(): array
    {
        return [
            'placa.unique'      => 'Ya existe una moto registrada con esa placa.',
            'nro_chasis.unique' => 'Ya existe una moto registrada con ese número de chasis.',
            'kilometraje.min'   => 'El kilometraje no puede ser menor al ya registrado (:min km).',
            'cliente_id.required' => 'Selecciona el propietario de la moto.',
        ];
    }
}