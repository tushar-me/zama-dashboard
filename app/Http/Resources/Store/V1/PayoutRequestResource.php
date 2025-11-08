<?php

namespace App\Http\Resources\Store\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayoutRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "request_amount"=> $this->request_amount,
            "note"=> $this->note,
            // 'payment_method_logo' => $this->paymentAccount->method->logo,
           'date' => $this->created_at->format('d M Y'),
            'status' => $this->status,
        ];
    }
}
