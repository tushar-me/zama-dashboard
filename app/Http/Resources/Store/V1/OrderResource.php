<?php

namespace App\Http\Resources\Store\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'code' => $this->combinedOrder->order_code,
            'order_amount' => $this->total_amount,
            'profit' => $this->store_balance,
            'customer_name' => $this->combinedOrder->customer->name,
            'customer_email' => $this->combinedOrder->customer->email,
            'customer_country' => $this->combinedOrder->country->name,
            'payment_status' => $this->combinedOrder->payment_status,
            'order_status'=> $this->combinedOrder->order_status,
            'date' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
