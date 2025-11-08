<?php

namespace App\Http\Resources\Website;

use App\Http\Resources\Website\CategoryResource;
use App\Http\Resources\Website\ProductColorResource;
use App\Http\Resources\Website\ProductResource;
use App\Http\Resources\Website\ProductVariationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'product_color_id' => $this->product_color_id,
            'product_variation_id' => $this->product_variation_id,
            'quantity' => number_format($this->quantity),
            'total' => $this->total,
            'product' =>  ProductResource::make($this->whenLoaded('product')),
            'color' => ProductColorResource::make($this->whenLoaded('productColor')), 
            'variation' => ProductVariationResource::make($this->whenLoaded('productVariation')),
        ];
    }
}
