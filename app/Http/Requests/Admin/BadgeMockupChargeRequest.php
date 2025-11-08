<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BadgeMockupChargeRequest extends FormRequest
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
            
            'mockup_id' => 'required|integer|exists:mockups,id',
            'charges' => 'required|array',
            'charges.*.badge_id' => 'required|integer|exists:badges,id',
            'charges.*.type' => 'required|string|in:percentage,fixed',
            'charges.*.adjustment_type' => 'required|string|in:add,subtract',
            'charges.*.adjustment_value' => 'required|numeric',
            'charges.*.order_level' => 'nullable|integer',
            'charges.*.status' => 'required|in:0,1'
        ];
    }
}
