<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStorageLocationRequest extends FormRequest
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
            'code'        => ['required', 'string', 'max:30', 'unique:storage_locations,code'],
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode lokasi wajib diisi.',
            'code.unique'   => 'Kode lokasi sudah digunakan.',
            'name.required' => 'Nama lokasi wajib diisi.',
        ];
    }
}