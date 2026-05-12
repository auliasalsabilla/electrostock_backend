<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStorageLocationRequest extends FormRequest
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
        $locationId = $this->route('storage_location');

        return [
            'code'        => ['sometimes', 'string', 'max:30', "unique:storage_locations,code,{$locationId}"],
            'name'        => ['sometimes', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'Kode lokasi sudah digunakan.',
        ];
    }
}