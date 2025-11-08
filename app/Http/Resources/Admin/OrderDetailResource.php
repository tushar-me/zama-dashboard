<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_code' => $this->order_code,
            'customer_id' => $this->customer_id,
            'guest_id' => $this->guest_id,
            'order_status_id' => $this->order_status_id,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'payment_method' => $this->payment_method,
            'paypal_order_id' => $this->paypal_order_id,
            'payment_details' => $this->payment_details,
            'tax_percentage' => $this->tax_percentage,
            'shipping_charge' => $this->formatMoney($this->shipping_charge),
            'shipping_warranty' => $this->shipping_warranty,
            'rush_production' => $this->rush_production,
            'tax' => $this->formatMoney($this->tax),
            'vat' => $this->formatMoney($this->vat),
            'sub_total' => $this->formatMoney($this->sub_total),
            'grand_total' => $this->formatMoney($this->grand_total),
            'payment_status' => $this->payment_status,
            'status_note' => $this->status_note,
            'created_at' => $this->formatTimestamp($this->created_at),
            'updated_at' => $this->formatTimestamp($this->updated_at),

            // Customer (only if loaded)
            'customer' => $this->whenLoaded('customer', function () {
                $c = $this->customer;
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'email' => $c->email,
                    'phone' => $c->phone,
                    'otp_expires_at' => $this->formatTimestamp($c->otp_expires_at),
                    'guest_id' => $c->guest_id,
                    'state' => $c->state->name,
                    'country' => $c->country->name,
                    'city' => $c->city->name,
                    'province' => $c->province,
                    'street_address' => $c->street_address,
                    'address_line_2' => $c->address_line_2,
                    'post_code' => $c->post_code,
                    'status' => $c->status,
                    'created_at' => $this->formatTimestamp($c->created_at),
                    'updated_at' => $this->formatTimestamp($c->updated_at),
                ];
            }),

            // Orders (only if loaded)
            'orders' => $this->whenLoaded('orders', function () {
                return $this->orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'combined_order_id' => $order->combined_order_id,
                        'store_id' => $order->store_id,
                        'total_amount' => $this->formatMoney($order->total_amount),
                        'store_balance' => $this->formatMoney($order->store_balance ?? 0),
                        'admin_balance' => $this->formatMoney($order->admin_balance ?? 0),
                        'created_at' => $this->formatTimestamp($order->created_at),
                        'updated_at' => $this->formatTimestamp($order->updated_at),

                        'order_details' => isset($order->order_details)
                            ? $order->order_details->map(function ($detail) {
                                return [
                                    'id' => $detail->id,
                                    'combined_order_id' => $detail->combined_order_id,
                                    'order_id' => $detail->order_id,
                                    'product_id' => $detail->product_id,
                                    'category_id' => $detail->category_id,
                                    'mockup_id' => $detail->mockup_id,
                                    'campaign_id' => $detail->campaign_id,
                                    'product_name' => $detail?->product->name,
                                    'artworks' => $this->mapWithStorageUrl($detail->artworks),
                                    'product_images' => $this->mapWithStorageUrl($detail->product_images),
                                    'color' => $detail->color,
                                    'size' => $detail->size,
                                    'quantity' => (int) $detail->quantity,
                                    'price' => $this->formatMoney($detail->price),
                                    'total' => $this->formatMoney($detail->total),
                                    'created_at' => $this->formatTimestamp($detail->created_at),
                                    'updated_at' => $this->formatTimestamp($detail->updated_at),
                                ];
                            })->values()
                            : [],
                    ];
                })->values();
            }),
        ];
    }
    protected function mapWithStorageUrl($value): array
    {
        // Step 1: Decode if string JSON
        if (!is_array($value)) {
            $value = $value ? json_decode($value, true) : [];
        }
    
        // Step 2: Attach full URL using asset('storage/...')
        return collect($value)
            ->filter()
            ->map(function ($path) {
                // Remove leading slashes or 'public/' if exists
                $path = ltrim(str_replace('public/', '', $path), '/');
                return asset('storage/' . $path);
            })
            ->values()
            ->toArray();
    }
    /**
     * Format amount values consistently as string with 2 decimals.
     */
    protected function formatMoney($value): ?string
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        // If already numeric, format; if string numeric, cast then format
        if (!is_numeric($value)) {
            return (string) $value;
        }

        return number_format((float) $value, 2, '.', '');
    }

    /**
     * Format timestamps like "2025-10-30T18:32:58.000000Z"
     */
    protected function formatTimestamp($dt): ?string
    {
        if (is_null($dt)) {
            return null;
        }

        // $dt might be Carbon instance or string
        try {
            return \Carbon\Carbon::parse($dt)->format('Y-m-d\TH:i:s.u\Z');
        } catch (\Throwable $e) {
            return (string) $dt;
        }
    }
}
