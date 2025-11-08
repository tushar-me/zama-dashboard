<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            // 'country_code' => ['required', 'string', 'max:5'],
            'phone' => ['required', 'string', 'max:20'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'guest_id' => ['nullable', 'string'],
            'country_id' => ['required', 'exists:countries,id'],
            'state_id' => ['required', 'exists:states,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'province' => ['nullable', 'string', 'max:255'],
            'street_address' => ['required', 'string', 'max:255'], 
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'post_code' => ['nullable', 'string', 'max:20'],
            'payment_method' => ['required', 'string', Rule::in(['paypal', 'stripe'])], // Example payment methods
        ];
    }
}
