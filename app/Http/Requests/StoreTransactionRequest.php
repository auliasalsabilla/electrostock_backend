<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'             => ['required', 'in:in,out'],
            'item_id'          => ['required', 'exists:items,id'],
            'quantity'         => ['required', 'integer', 'min:1'],
            'unit'             => ['nullable', 'string', 'max:50'],
            'price'            => ['nullable', 'numeric'],
            'note'             => ['nullable', 'string'],
            'transaction_date' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'             => 'Tipe transaksi wajib diisi.',
            'type.in'                   => 'Tipe transaksi harus in atau out.',
            'item_id.required'          => 'Barang wajib dipilih.',
            'item_id.exists'            => 'Barang tidak ditemukan.',
            'quantity.required'         => 'Jumlah wajib diisi.',
            'quantity.min'              => 'Jumlah minimal 1.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
        ];
    }
}