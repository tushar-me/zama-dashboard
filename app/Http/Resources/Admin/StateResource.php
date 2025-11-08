<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateResource extends JsonResource
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
            'country_id' => $this->country_id,
            'shipping_charge' => $this->shipping_charge,
            'tax_percentage' => $this->tax_percentage,
            'vat_percentage' => $this->vat_percentage,
            'capital' => $this->capital,
            'timezone' => $this->timezone,
            'iso_code' => $this->iso_code,
            'currency' => $this->currency,
            'is_default' => $this->is_default,
            'order_level' => $this->order_level,
            'status' => $this->status,
            'country' => new CountryResource($this->whenLoaded('country')),
            'code' => $this->code
        ];
    }
}
