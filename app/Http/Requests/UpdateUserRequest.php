<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user');

        return [
            'name'      => ['sometimes', 'string', 'max:100'],
            'email'     => ['sometimes', 'email', "unique:users,email,{$userId}"],
            'password'  => ['sometimes', 'string', 'min:8'],
            'role'      => ['sometimes', 'in:admin,manager,staff'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'   => 'Email sudah digunakan.',
            'password.min'   => 'Password minimal 8 karakter.',
            'role.in'        => 'Role tidak valid.',
        ];
    }
}