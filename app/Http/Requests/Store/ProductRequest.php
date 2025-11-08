<?php

namespace App\Http\Requests\Store;

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
            'vendor_id' => 'required|uuid|exists:vendors,id',
            'store_id' => 'required|uuid|exists:stores,id',
            'campaign_id' => 'nullable|uuid|exists:campaigns,id',
            'mockup_id' => 'nullable|uuid|exists:mockups,id',
            'category_id' => 'required|uuid|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'code' => $this->isMethod('POST') ? 'required|string|max:255|unique:products,code' : 'required|string|max:255',
            'name' => $this->isMethod('POST') ? 'required|string|max:255|unique:products,name' : 'required|string|max:255',
            'slug' => $this->isMethod('POST') ? 'nullable|string|max:255|unique:products,slug' : 'nullable|string|max:255',
            'cover_image' => 'nullable|string|starts_with:data:image/,data:image/jpeg,data:image/png,data:image/webp',
            'hover_image' => 'nullable|string|starts_with:data:image/,data:image/jpeg,data:image/png,data:image/webp',
            'size_chart' => 'nullable|string|starts_with:data:image/,data:image/jpeg,data:image/png,data:image/webp',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'specification' => 'nullable|array',
            'type' => 'required|in:campaign,all_over_print,ecommerce',
            'is_default' => 'boolean',
            'sides' => 'nullable|array',
            'price' => 'required|numeric|min:0|max:99999999.99',
            'compare_price' => 'nullable|numeric|min:0|max:99999999.99',
            'cost' => 'nullable|numeric|min:0|max:99999999.99',
            'tax' => 'nullable|numeric|min:0|max:99999999.99',
            'is_tax_included' => 'boolean',
            'inventory_quantity' => 'required|integer|min:0',
            'inventory_policy' => 'required|in:deny,continue',
            'weight' => 'nullable|numeric|min:0|max:999999.99',
            'dimensions' => 'nullable|array',
            'vendor_profit' => 'required|numeric|min:0|max:99999999.99',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|starts_with:data:image/,data:image/jpeg,data:image/png,data:image/webp',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:255',
            'alt_text' => 'nullable|string|max:255',
            'extra_meta' => 'nullable|array',
            'status' => 'required|in:published,unpublished,resctricted',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
        ];
    }
}
