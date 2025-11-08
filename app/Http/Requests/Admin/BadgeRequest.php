<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BadgeRequest extends FormRequest
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
            'name' => $this->isMethod('POST') ? 'required|string|max:100|unique:badges,name' : 'required|string|max:100',
            'logo' => 'nullable|string|starts_with:data:image/,data:image/jpeg,data:image/png,data:image/webp',
            'type' => 'required|string|in:percentage,fixed',
            'adjustment_type' => 'required|string|in:add,subtract',
            'adjustment_value' => 'required|numeric|min:0',
            'order_level' => 'nullable|integer',
            'status' => 'required|in:0,1'
        ];
    }
}
