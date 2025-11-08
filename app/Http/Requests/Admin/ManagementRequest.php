<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ManagementRequest extends FormRequest
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
            'name' => $this->isMethod('POST') ? 'required|string|max:255|unique:admins,name' : 'required|string|max:255',
            'email' => $this->isMethod('POST') ? 'required|email|max:255|unique:admins,email' : 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'role' => 'required|string|in:superadmin,admin,manager,editor',
            'status' => 'required|string|in:0,1',
            'password' => $this->isMethod('POST') ? 'required|string|min:8|confirmed' : 'nullable|string|min:8',
        ];
    }
}
