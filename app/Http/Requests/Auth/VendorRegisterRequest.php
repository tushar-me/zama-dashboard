<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VendorRegisterRequest extends FormRequest
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
            'country_id'         => 'nullable|exists:countries,id',
            'division_id'        => 'nullable|exists:divisions,id',
            'district_id'        => 'nullable|exists:districts,id',
            'address'            => 'nullable|string|max:255',
            'name'               => 'nullable|string|max:255',
            'device_name'        => 'nullable|string',
            'email'              => 'required|email|email:filter,rfc,dns',
            'phone'              => 'nullable|string|unique:vendors,phone,',
            'password'           => 'nullable|string|min:8|confirmed',
            'is_active'          => 'boolean',
            'is_suspended'       => 'boolean',
        ];
    }
}
