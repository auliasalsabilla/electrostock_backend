<?php

namespace App\Http\Requests;

use App\Models\Item;                          // ← tambah ini
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $item = $this->route('item');
        $itemId = $item instanceof Item ? $item->id : $item;  // ← ubah ini

        return [
            'category_id'         => ['nullable', 'exists:categories,id'],
            'supplier_id'         => ['nullable', 'exists:suppliers,id'],
            'unit_id'             => ['nullable', 'exists:units,id'],
            'storage_location_id' => ['nullable', 'exists:storage_locations,id'],
            'code'                => ['sometimes', 'string', 'max:50', "unique:items,code,{$itemId}"],
            'name'                => ['sometimes', 'string', 'max:200'],
            'description'         => ['nullable', 'string'],
            'brand'               => ['nullable', 'string', 'max:100'],
            'stock'               => ['sometimes', 'integer', 'min:0'],
            'stock_minimum'       => ['sometimes', 'integer', 'min:0'],
            'stock_maximum'       => ['nullable', 'integer', 'min:0'],
            'purchase_price'      => ['sometimes', 'numeric', 'min:0'],
            'is_active'           => ['sometimes'],
            'image'               => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique'                => 'Kode barang sudah digunakan.',
            'category_id.exists'         => 'Kategori tidak valid.',
            'supplier_id.exists'         => 'Supplier tidak valid.',
            'unit_id.exists'             => 'Satuan tidak valid.',
            'storage_location_id.exists' => 'Lokasi penyimpanan tidak valid.',
            'image.image'                => 'File harus berupa gambar.',
            'image.mimes'                => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'image.max'                  => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}