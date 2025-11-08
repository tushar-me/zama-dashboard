<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CityRequest extends FormRequest
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
            'name' => $this->isMethod('POST') ? 'required|string|max:255|unique:cities,name' : 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
            'country_id' => 'required|exists:countries,id',
            'code' => 'nullable|string|max:255',    
            'postal_code' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:255',
            'area_code' => 'nullable|string|max:255',
            'shipping_charge' => 'nullable|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0',
            'vat_percentage' => 'nullable|numeric|min:0',
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
            'is_default' => 'nullable|boolean',
            'order_level' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean'
        ];
    }
}
