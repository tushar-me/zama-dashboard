<?php

namespace App\Http\Resources\Store\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentAccountResource extends JsonResource
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
            "account_name"=> $this->account_name,
            "account_number"=> $this->maskAccountNumber($this->account_number),
            "bank_name"=> $this->bank_name,
            "branch_name"=> $this->branch_name,
            "routing_number"=> $this->routing_number,
            "swift_code"=> $this->swift_code,
            "country"=> $this->country,
            "currency"=> $this->currency,
            "extra_details"=> $this->extra_details,
            "is_default"=> $this->is_default,
            "created_at"=> $this->created_at,
            "updated_at"=> $this->updated_at,
            'logo' => $this->method->logo
        ];
    }

    private function maskAccountNumber($number)
    {
        if (!$number) {
            return null;
        }
        $visibleDigits = 4;
        $maskedSection = str_repeat('*', max(strlen($number) - $visibleDigits, 0));
        $visibleSection = substr($number, -$visibleDigits);
        return trim(chunk_split($maskedSection, 4, ' ')) . ' ' . $visibleSection;
    }
}
