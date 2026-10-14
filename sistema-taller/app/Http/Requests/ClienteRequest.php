<?php

namespace App\Http\Requests;

use App\Models\Cliente;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre'           => ['required', 'string', 'max:100'],
            'tipo_documento'   => ['required', Rule::in(Cliente::TIPOS_DOCUMENTO)],
            'numero_documento' => ['required', 'string', 'max:20',
                // único en combinación con el tipo de documento
                Rule::unique('clientes', 'numero_documento')
                    ->where('tipo_documento', $this->tipo_documento)
                    ->ignore($this->route('cliente'))],
            'telefono'         => ['required', 'string', 'max:20'],
            'direccion'        => ['nullable', 'string', 'max:255'],
            'tipo'             => ['required', Rule::in(Cliente::TIPOS)],
        ];
    }

    public function messages(): array
    {
        return [
            'numero_documento.unique' => 'Ya existe un cliente con ese tipo y número de documento.',
        ];
    }
}
