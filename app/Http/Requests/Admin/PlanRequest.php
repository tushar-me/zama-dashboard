<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PlanRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:plans,name',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'product_limit' => 'nullable|integer|min:0',
            'order_limit' => 'nullable|integer|min:0',
            'customer_limit' => 'nullable|integer|min:0',
            'storage_limit_mb' => 'nullable|integer|min:0',
            'store_limit' => 'required|integer|min:1',
            'custom_domain' => 'integer|in:0,1',
            'advanced_analytics' => 'integer|in:0,1',
            'staff_accounts' => 'integer|in:0,1',
            'priority_support' => 'integer|in:0,1',
            'features' => 'nullable|array',
            'status' => 'required|integer|in:0,1',
        ];
    }
}
