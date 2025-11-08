<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MockupRequest extends FormRequest
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
            'name'        => $this->isMethod('POST') ? 'required|string|max:200|unique:mockups,name' : 'required|string|max:200',
            'category_id' => 'required|string|exists:categories,id',
            'cost'       => 'required|numeric|min:0',
            'price'       => 'nullable|numeric|min:0',
            'sell_price'  => 'required|numeric|min:0',
            'image'       => $this->isMethod('POST') ? 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048' : 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'measurement_guide'  => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'description' => 'nullable|string|max:7000',
            'order_level' => 'nullable|integer|min:0',
            'status'      => 'required|in:0,1',
            'colors'      => 'nullable|array',
            'type'        => ['required', Rule::in(['default_print', 'all_over_print'])],
            'video_url'   => 'nullable:string',
            'charges'     => 'nullable|array',
            'charges.*.badge_id' => 'required|string|exists:badges,id',
            'charges.*.type' => 'required|in:percentage,fixed',
            'charges.*.adjustment_type' => 'required|in:add,substract',
            'charges.*.adjustment_value' => 'required|numeric|min:0',
            'charges.*.order_level' => 'nullable|integer|min:0',
            'charges.*.status' => 'boolean',
            'us_charge' => 'nullable|numeric',
            'us_add_charge_per_item' => 'nullable|numeric',
            'worldwide_charge' => 'nullable|numeric',
            'worldwide_add_charge_per_item' =>  'nullable|numeric',
            'variations' => 'nullable|array',
           'size_chart' => 'nullable|array',
            'size_chart.columns' => 'array',
            'size_chart.rows' => 'array',
        ];
    }
}
