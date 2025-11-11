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
            'name' => $this->name,
            'slug' => $this->slug,
            'thumbnail' => $this->cover_image,
            'hover_thumbnail' =>  $this->hover_image,
            'price' => $this->price,
            'compare_price' => $this->compare_price,
         ];
    }
}