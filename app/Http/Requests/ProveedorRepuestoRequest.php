<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProveedorRepuestoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'precio_compra'    => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'codigo_proveedor' => ['nullable', 'string', 'max:30'],
        ];

        // El repuesto solo se elige al vincular; al actualizar ya está definido
        if ($this->isMethod('post')) {
            $rules['repuesto_id'] = ['required', 'exists:repuestos,id'];
        }

        return $rules;
    }
}
