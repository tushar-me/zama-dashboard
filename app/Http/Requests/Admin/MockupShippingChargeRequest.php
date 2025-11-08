<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MockupShippingChargeRequest extends FormRequest
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
            'mockup_id' => 'required|uuid|exists:mockups,id',
            'us_charge' => 'required|numeric|min:0',
            'us_add_charge_per_item' => 'required|numeric|min:0',
            'worldwide_charge' => 'required|numeric|min:0',
            'worldwide_add_charge_per_item' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'is_free' => 'required|boolean',
        ];
    }
}
