<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProveedorRequest extends FormRequest
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
            'nombre'    => ['required', 'string', 'max:100'],
            'nit'       => ['nullable', 'string', 'max:20',
                            Rule::unique('proveedors', 'nit')->ignore($this->route('proveedor'))],
            'telefono'  => ['required', 'string', 'max:20'],
            'email'     => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ];
    }
}
