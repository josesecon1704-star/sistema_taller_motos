<?php

namespace App\Http\Requests;

use App\Models\Empleado;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmpleadoRequest extends FormRequest
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
            'nombre'             => ['required', 'string', 'max:100'],
            'ci'                 => ['required', 'string', 'max:20',
                                     Rule::unique('empleados', 'ci')->ignore($this->route('empleado'))],
            'telefono'           => ['required', 'string', 'max:20'],
            'cargo'              => ['required', Rule::in(array_keys(Empleado::CARGOS))],
            'especialidad'       => ['nullable', 'string', 'max:100'],
            'fecha_contratacion' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
