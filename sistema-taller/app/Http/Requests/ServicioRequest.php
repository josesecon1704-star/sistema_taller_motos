<?php

namespace App\Http\Requests;

use App\Models\Servicio;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServicioRequest extends FormRequest
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
            'nombre'      => ['required', 'string', 'max:100',
                              Rule::unique('servicios', 'nombre')->ignore($this->route('servicio'))],
            'categoria'   => ['required', Rule::in(Servicio::CATEGORIAS)],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'precio_base' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ];
    }
}
