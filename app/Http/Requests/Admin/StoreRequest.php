<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'default_domain' => 'required|string|max:255',
            'custom_domain' => 'nullable|string|max:255',
            'custom_domain_verified' => 'boolean',
            'logo' => 'nullable|string|max:255',
            'favicon' => 'nullable|string|max:255',
            'banner' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'address' => 'nullable|string|max:500',
            'is_active' => 'integer|in:0,1',
            'is_verified' => 'integer|in:0,1',
            'is_suspend' => 'integer|in:0,1',
            'last_active_at' => 'nullable|date',
            'storage_used_mb' => 'nullable|integer|min:0',
        ];
    }
}
