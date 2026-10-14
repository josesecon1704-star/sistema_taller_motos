<?php

namespace App\Http\Requests;

use App\Models\MovimientoInventario;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MovimientoRequest extends FormRequest
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
            'movimiento'     => ['required', Rule::in(array_keys(MovimientoInventario::MOVIMIENTOS))],
            'cantidad'       => ['required', 'integer', 'min:1', 'max:100000'],
            'proveedor_id'   => ['nullable', 'exists:proveedors,id'],
            'costo_unitario' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'observaciones'  => [
                Rule::requiredIf($this->movimiento !== 'compra'),
                'nullable', 'string', 'max:500',
            ],
        ];
    }
}
