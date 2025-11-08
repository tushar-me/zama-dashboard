<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => $this->isMethod('POST') ? 'required|string|max:255|unique:countries,name' : 'required|string|max:255',
            'phone_code' => 'required|string|max:10',
            'iso2' => 'nullable|string|max:2',
            'iso3' => 'nullable|string|max:3',
            'flag' => $this->isMethod('POST') ? 'required|image|mimes:jpeg,png,jpg,webp|max:2048' : 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'currency' => 'nullable|string|max:5',
            'currency_symbol' => 'nullable|string|max:2',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'shipping_charge' => 'nullable|numeric|min:0',
            'vat_percentage' => 'nullable|numeric|min:0|max:100',
            'order_level' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
        ];
    }
}
