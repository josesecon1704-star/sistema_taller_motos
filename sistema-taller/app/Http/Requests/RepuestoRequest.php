<?php

namespace App\Http\Requests;

use App\Models\Repuesto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RepuestoRequest extends FormRequest
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
        $rules = [
            'codigo'        => ['required', 'string', 'max:30',
                                Rule::unique('repuestos', 'codigo')->ignore($this->route('repuesto'))],
            'nombre'        => ['required', 'string', 'max:100'],
            'categoria'     => ['required', Rule::in(Repuesto::CATEGORIAS)],
            'unidad_medida' => ['required', Rule::in(Repuesto::UNIDADES)],
            'precio_venta'  => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'stock_minimo'  => ['required', 'integer', 'min:0'],
        ];

        // El stock solo se indica al crear
        if ($this->isMethod('post')) {
            $rules['stock_actual'] = ['required', 'integer', 'min:0'];
        }

        return $rules;
    }
}
