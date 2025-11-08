<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name' => $this->isMethod('POST') ? 'required|string|max:255|unique:categories,name' : 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'parent_id' => 'nullable|integer|exists:categories,id',
            'slug' => 'nullable|string|max:255',
            'order_level' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'type' => 'required|string|in:mockup,ecommerce',
            'print_type' => 'required|string|in:all_over_print,default_print,both_print',
            'us_charge' => 'nullable|numeric',
            'us_add_charge_per_item' => 'nullable|numeric',
            'worldwide_charge' => 'nullable|numeric',
            'worldwide_add_charge_per_item' =>  'nullable|numeric'
        ];
    }
}
