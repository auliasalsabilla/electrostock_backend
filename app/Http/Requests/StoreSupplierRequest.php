<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'           => ['required', 'string', 'max:20', 'unique:suppliers,code'],
            'name'           => ['required', 'string', 'max:150'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'phone'          => ['nullable', 'string', 'max:30'],
            'email'          => ['nullable', 'email', 'max:150'],
            'address'        => ['nullable', 'string'],
            'city'           => ['nullable', 'string', 'max:100'],
            'bank_name'      => ['nullable', 'string', 'max:100'],
            'bank_account'   => ['nullable', 'string', 'max:50'],
            'notes'          => ['nullable', 'string'],
            'is_active'      => ['sometimes', 'boolean'],
        ];
    }
}