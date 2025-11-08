<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
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
            'vendor_id' =>'required|exists:vendors,id',
            'plan_id' =>'required|exists:plans,id',
            'payment_amount' => 'nullable|numeric',
            'payment_method' =>'required|in:bkash,nagad,rocket,upay,bank_transfer,sslcommerz',
            'payment_status' =>'required|in:paid,unpaid',
            'payment_date' => 'nullable|date',
            'next_payment_date' => 'nullable|date',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'is_trial' => 'nullable|boolean',
        ];
    }
}
