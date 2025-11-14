<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CollectionRequest extends FormRequest
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
            'name' => $this->isMethod('POST') ? 'required|string|unique:collections,name' : 'required|string',
            'order_level' => 'required|integer',
            'image' => $this->isMethod('POST') ?'required|image|mimes:png,jpg,jpeg.webp':'nullablr|image|mimes:png,jpg,jpeg.webp',
            'product_ids' => 'required|array'
        ];
    }
}
