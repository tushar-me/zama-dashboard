<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MockupSideResource extends JsonResource
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
            'mockup_id' => $this->mockup_id,
            'status' => $this->status,
            'name' => $this->name,
            'image' => $this->image,
            'bounding_box' => $this->bounding_box,
            'mockup' => $this->mockup
        ];
    }
}
