<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplierId = $this->route('supplier')?->id ?? $this->route('supplier');

        return [
            'code'           => ['sometimes', 'string', 'max:20', "unique:suppliers,code,{$supplierId}"],
            'name'           => ['sometimes', 'string', 'max:150'],
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