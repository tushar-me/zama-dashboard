<?php

namespace App\Http\Resources\Website;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductShowResource extends JsonResource
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
            'hover_thumbnail' =>  $this->hover_image,
            'price' => $this->price,
            'compare_price' => $this->compare_price,
            'images' => $this->whenLoaded('images', function ($images) {
                return ProductImageResource::collection($images);
            })
         ];
    }
}
