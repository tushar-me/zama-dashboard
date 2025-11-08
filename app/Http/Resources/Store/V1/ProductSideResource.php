<?php

namespace App\Http\Resources\Store\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSideResource extends JsonResource
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
            'image' => $this->image,
            'artwork' => $this->artwork,
            'bounding_box' => $this->bounding_box,
            'artwork_props' => $this->artwork_props
        ];
    }
}
