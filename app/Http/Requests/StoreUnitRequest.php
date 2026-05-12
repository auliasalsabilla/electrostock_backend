<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:50'],
            'abbreviation' => ['required', 'string', 'max:10', 'unique:units,abbreviation'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         => 'Nama satuan wajib diisi.',
            'abbreviation.required' => 'Singkatan wajib diisi.',
            'abbreviation.unique'   => 'Singkatan sudah digunakan.',
        ];
    }
}