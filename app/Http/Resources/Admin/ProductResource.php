<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Store\V1\ProductSideResource;


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
            'price' => $this->price,
            'unit_price' => $this->unit_price,
            'type' => $this->type,
            'sides' => ProductSideResource::collection($this->productSides),
            'artwork' => $this->artwork,
            'image' => $this->images->first()->image 
                    ?? $this->productSides->first()->image 
                    ?? $this->artwork
                    ?? null,
            'colors' => $this->colors,
            'images' => $this->images,
            'store' => $this->store,        
        ];
    }
}
