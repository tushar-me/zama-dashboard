<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'category_id' => 'required|uuid|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'name' => $this->isMethod('POST') ? 'required|string|max:255|unique:products,name' : 'required|string|max:255',
            'cover_image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'hover_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'specification' => 'nullable|array',
            'price' => 'required|numeric|min:0|max:99999999.99',
            'compare_price' => 'nullable|numeric|min:0|max:99999999.99',
            'cost' => 'nullable|numeric|min:0|max:99999999.99',
            'tax' => 'nullable|numeric|min:0|max:99999999.99',
            'is_tax_included' => 'boolean',
            'images' => 'nullable|array',
            'inventory_quantity' => 'nullable|integer|min:0',
            'inventory_policy' => 'nullable|in:deny,continue',
            'weight' => 'nullable|numeric|min:0|max:999999.99',
            'dimensions' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|starts_with:data:image/,data:image/jpeg,data:image/png,data:image/webp',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:255',
            'alt_text' => 'nullable|string|max:255',
            'extra_meta' => 'nullable|array',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'variations' => 'nullable|array',
            'size_chart' => 'nullable|array',
            'size_chart.columns' => 'array',
            'size_chart.rows' => 'array',
        ];
    }
}
