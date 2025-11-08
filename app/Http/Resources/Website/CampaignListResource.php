<?php

namespace App\Http\Resources\Website;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $defaultProduct = $this->defaultProduct;
        $color = $defaultProduct->colors()
            ->where('is_default', true)
            ->first()
            ?? $defaultProduct->colors()->first();

        return [
            'name' => $this->name . ' - ' . $defaultProduct->name,
            'price' => $defaultProduct->price > 0 ? $defaultProduct->price : $defaultProduct->unit_price,
            'compare_price' => $defaultProduct->compare_price,
            'thumbnail' => $defaultProduct->cover_image,
            'url' => '/' . $this->store->url . '/' . $this->slug . '/' . $defaultProduct->slug . '?color=' . ($color?->hex_code ?? ''),
        ];
    }
}
