<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class CampaignRequest extends FormRequest
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
            'name' => $this->isMethod('POST') ? 'required|string|max:255|unique:campaigns,name' : 'required|string|max:255',
            'slug' => $this->isMethod('POST') ? 'nullable|string|max:255|unique:campaigns,slug' : 'nullable|string|max:255',
            'image' => 'nullable|string|starts_with:data:image/,data:image/jpeg,data:image/png,data:image/webp',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'og_image' => 'nullable|string|starts_with:data:image/,data:image/jpeg,data:image/png,data:image/webp',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'sale' => 'nullable|integer|min:0',
            'view' => 'nullable|integer|min:0',
            'video_url' => 'nullable|url|max:1000',
            'status' => 'required|in:published,unpublished,resctricted',
            'published_at' => 'nullable|date',
            'storefront_ids' => 'nullable',
            'tags' => 'nullable|array',
        ];
    }
}