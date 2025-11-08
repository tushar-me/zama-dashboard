<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MockupVariationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Assuming authorization is handled elsewhere or always allowed for this task
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mockup_id' => ['required', 'uuid', 'exists:mockups,id'],
            'size_id' => $this->isMethod('POST') ? ['required', 'uuid', 'exists:sizes,id'] : ['required', 'uuid', 'exists:sizes,id'],
            'sku' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'status' => ['boolean'],
        ];
    }
}
