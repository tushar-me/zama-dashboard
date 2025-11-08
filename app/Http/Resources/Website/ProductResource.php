<?php

namespace App\Http\Resources\Website;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'thumbnail' => $this->cover_image,
            'price' => $this->price,
            'compare_price' => $this->compare_price,
            'category_id' => $this->category_id,
            'url' => '/'.$this->store?->url.'/'.$this->campaign?->slug.'/'.$this->slug.'?color='.$this->defaultColor->hex_code,
            'colors' => ProductColorResource::collection($this->whenLoaded('colors')),
            'selected_color_id' => $this->selected_color_id,   
            'selected_variation' => $this->selected_variation_id,
            'variations' => ProductVariationResource::collection($this->whenLoaded('variations')),
            'size_chart' => $this->mockup?->sizeChart?->chart_data,
            'description' => $this->mockup?->description,
         ];
    }
}