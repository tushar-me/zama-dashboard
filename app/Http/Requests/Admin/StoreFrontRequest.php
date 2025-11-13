<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreFrontRequest extends FormRequest
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
            'name' => $this->isMethod('POST') ? 'required|string|max:255|unique:store_fronts,name' : 'required|string|max:255',
            'slug' => $this->isMethod('POST') ? 'nullable|string|max:255|unique:store_fronts,slug' : 'nullable|string|max:255',
            'banner' => 'nullable|image|mimes:png,jpg,jpeg,webp',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'og_image' => 'nullable|string|starts_with:data:image/,data:image/jpeg,data:image/png,data:image/webp',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'view' => 'nullable|integer|min:0',
            'status' => 'required|in:published,unpublished,resctricted',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'campaign_ids' => 'nullable|array'
        ];
    }
}
