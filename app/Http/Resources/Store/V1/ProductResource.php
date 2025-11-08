<?php

namespace App\Http\Resources\Store\V1;

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
            'price' => number_format($this->price, 2),
            'unit_price' => $this->unit_price,
            'profit_amount' => $this->profit_amount,
            'type' => $this->type,
            'sides' => ProductSideResource::collection($this->productSides),
            'artwork' => $this->artwork,
            'image' => $this?->colors()->where('is_default', true)->first()?->images()->latest()->first()->image 
                    ?? $this->productSides->first()->image 
                    ?? $this->artwork
                    ?? null,
            'colors' => $this->colors,    
            'mockup' => $this->mockup,
            'default_background' => $this->colors->where('is_default', true)->first()?->hex_code,
        ];
    }
}
