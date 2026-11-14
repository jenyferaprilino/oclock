<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreShippingRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'city_id' => ['required', 'exists:cities,id', 'unique:shipping_rates,city_id'],
            'price' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'city_id.required' => 'Kota tujuan wajib dipilih.',
            'city_id.exists' => 'Kota tujuan tidak valid.',
            'city_id.unique' => 'Tarif pengiriman untuk kota ini sudah ada.',
            'price.required' => 'Tarif pengiriman wajib diisi.',
            'price.min' => 'Tarif pengiriman tidak boleh bernilai negatif.',
        ];
    }
}
