<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CartRequest extends FormRequest
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
            'product_id' => ['required', 'exists:products,id'],
            'product_color_id' => ['required', 'exists:product_colors,id'],
            'product_variation_id' => ['nullable', 'exists:product_variations,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'guest_id' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    // protected function prepareForValidation(): void
    // {
    //     if (Auth::guard('customer')->check()) {
    //         $this->merge([
    //             'customer_id' => Auth::guard('customer')->id(),
    //             'guest_id' => null,
    //         ]);
    //     } else {
    //         $this->merge([
    //             'customer_id' => null,
    //             'guest_id' => $this->guest_id ?? session()->getId(), // Use existing guest_id or generate one
    //         ]);
    //     }
    // }
}
