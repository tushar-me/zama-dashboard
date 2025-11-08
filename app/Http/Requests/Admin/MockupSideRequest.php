<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MockupSideRequest extends FormRequest
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
            'mockup_id' => 'required|string|exists:mockups,id',
            'sides' => 'required|array',
            'sides.*.name' => 'required|string|max:100',
            'sides.*.image' => 'required|image|mimes:png|max:2048',
            'sides.*.bounding_box' => 'required|array',
            'sides.*.bounding_box.top' => 'required|numeric',
            'sides.*.bounding_box.left' => 'required|numeric',
            'sides.*.bounding_box.width' => 'required|numeric',
            'sides.*.bounding_box.height' => 'required|numeric',
            'sides.*.bounding_box.css' => 'nullable|string|max:255',
            'sides.*.bounding_box.border_color' => 'nullable|string|max:6',
            'sides.*.bounding_box.border_width' => 'nullable|numeric|max:5',
            'sides.*.bounding_box.background_color' => 'nullable|string|max:6',
            'sides.*.status' => 'required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'mockup_id.required' => 'The mockup is required.',
            'mockup_id.exists' => 'The selected mockup is invalid.',

            'sides.required' => 'At least one side is required.',
            'sides.*.name.required' => 'Side must have a name.',
            'sides.*.name.max' => 'Side name must not exceed 100 characters.',

            'sides.*.image.required' => 'Side image is required.',
            'sides.*.image.image' => 'Each side image must be a valid image.',
            'sides.*.image.mimes' => 'Each side image must be a PNG file.',
            'sides.*.image.max' => 'Each side image must not exceed 2MB.',
            'sides.*.image.dimensions' => 'Each side image must be square and between 400x400 and 1200x1200 pixels.',

            'sides.*.bounding_box.required' => 'Bounding box information is required for each side.',
            'sides.*.bounding_box.top.required' => 'Top position is required for each bounding box.',
            'sides.*.bounding_box.left.required' => 'Left position is required for each bounding box.',
            'sides.*.bounding_box.width.required' => 'Width is required for each bounding box.',
            'sides.*.bounding_box.height.required' => 'Height is required for each bounding box.',

            'sides.*.bounding_box.css.max' => 'CSS styles must not exceed 255 characters.',
            'sides.*.bounding_box.border_color.max' => 'Border color must be a valid 6-character hex value.',
            'sides.*.bounding_box.border_width.max' => 'Border width must not be greater than 5.',
            'sides.*.bounding_box.background_color.max' => 'Background color must be a valid 6-character hex value.',

            'sides.*.status.required' => 'Status is required for each side.',
            'sides.*.status.in' => 'Status must be either active (1) or inactive (0).',
        ];
    }
}
