<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StateRequest extends FormRequest
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
            'name'            => $this->isMethod('POST') ? 'required|string|max:255|unique:states,name' : 'required|string|max:255',
            'country_id'      => 'required|exists:countries,id',
            'shipping_charge' => 'nullable|numeric|min:0',
            'tax_percentage'  => 'nullable|numeric|min:0',
            'vat_percentage'  => 'nullable|numeric|min:0',
            'capital'         => 'nullable|string|max:255',
            'timezone'        => 'nullable|string|max:255',
            'iso_code'        => 'nullable|string|max:255',
            'currency'        => 'nullable|string|max:255',
            'is_default'      => 'required|boolean',
            'order_level'     => 'required|integer|min:0',
            'status'          => 'required|boolean',
        ];
    }
}
