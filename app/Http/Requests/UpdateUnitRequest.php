<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitRequest extends FormRequest
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
        $unitId = $this->route('unit');

        return [
            'name'         => ['sometimes', 'string', 'max:50'],
            'abbreviation' => ['sometimes', 'string', 'max:10', "unique:units,abbreviation,{$unitId}"],
        ];
    }

    public function messages(): array
    {
        return [
            'abbreviation.unique' => 'Singkatan sudah digunakan.',
        ];
    }
}