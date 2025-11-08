<?php

namespace App\Http\Resources\Store\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreFrontResource extends JsonResource
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
            'banner' => $this->banner,
            'description' => $this->description,
            'view' => $this->view ?? 0,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'url' => env('WEBSITE_URL').'/'.$this->slug,
        ];
    }
}
