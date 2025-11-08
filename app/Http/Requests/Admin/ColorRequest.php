<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ColorRequest extends FormRequest
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
            'name' => $this->isMethod('POST') ? 'required|string|max:100|unique:colors,name' : 'required|string|max:100',
            'hex_code' => $this->isMethod('POST') ? 'required|string|max:100|unique:colors,hex_code' : 'required|string|max:100',
            'rgb' => $this->isMethod('POST') ? 'nullable|string|max:100|unique:colors,rgb' : 'nullable|string|max:100',
            'status' => 'required|in:0,1', 
        ];
    }
}
