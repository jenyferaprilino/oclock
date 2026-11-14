<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'brand' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'integer', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
            'watch_type' => ['nullable', 'string', 'max:100'],
            'strap_material' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:100'],
            'case_diameter' => ['nullable', 'string', 'max:50'],
            'movement_type' => ['nullable', 'string', 'max:100'],
            'water_resistance' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'name.required' => 'Nama produk wajib diisi.',
            'sku.required' => 'SKU produk wajib diisi.',
            'sku.unique' => 'SKU ini sudah digunakan produk lain.',
            'brand.required' => 'Brand produk wajib diisi.',
            'description.required' => 'Deskripsi produk wajib diisi.',
            'price.required' => 'Harga produk wajib diisi.',
            'price.min' => 'Harga harus lebih besar dari Rp0.',
            'stock.required' => 'Stok produk wajib diisi.',
            'stock.min' => 'Stok tidak boleh bernilai negatif.',
            'image.image' => 'Berkas harus berupa gambar.',
            'image.mimes' => 'Format gambar yang didukung: JPEG, PNG, JPG, WEBP.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
