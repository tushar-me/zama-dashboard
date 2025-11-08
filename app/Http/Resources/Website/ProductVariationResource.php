<?php

namespace App\Http\Resources\Website;

use App\Http\Resources\Website\SizeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationResource extends JsonResource
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
            'size_id' => $this->size_id,
            'size_name' =>  $this->size->name,
            'price' => $this->price,
            'sell_price' => $this->sell_price
        ];
    }
}
