<?php

namespace App\Services\Orders;

use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\Country;
use App\Models\City;
use App\Models\State;
use App\Models\Cart;

class StoreOrderService
{
    public function handle(array $data): Order
    {
        $cartQuery = Cart::query()
            ->where('guest_id', $data['guest_id'])
            ->with([
                'product:id,name,mockup_id,category_id,campaign_id,unit_price,price,artwork,store_id',
                'product.category:id',
                'productVariation',
                'productColor.images',
            ]);

        $country = Country::where('id', $data['country_id'])->first();
        $state = State::where('id', $data['state_id'])->first();
        $city = City::where('code', $data['city_id'])->first();
        $items = $cartQuery->get();
        $subTotal = $items->sum('total');
        $vatPercentage = $country->vat_percentage ?? 0;
        $vatAmount = $subTotal * $vatPercentage / 100;
        $taxPercentage = $state->tax_percentage ?? 0;
        $taxAmount = $subTotal * $taxPercentage / 100;
        $grandTotal = $subTotal + $vatAmount + $city->shipping_charge; + $taxAmount;
        $data['order_code'] = rand(10000000, 99999999); 
        $data['payment_status'] = 'pending';
        $data['order_status'] = 'pending'; 
        $data['sub_total'] = $subTotal;
        $data['grand_total'] = $grandTotal;
        $data['vat'] = $vatAmount;
        $data['tax'] = $taxAmount;
        $data['tax_percentage'] = $taxPercentage;
        $data['shipping_charge'] = $city->shipping_charge;
        $order = Order::create($data);

        foreach ($items as $item) {
            $productPrice = floatval($item->product->price);
            $quantity = intval($item->quantity);
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'category_id' => $item->category_id ?? null,
                'color' => $item->productColor->name,
                'product_image' => $item->getRawOriginal('product_image'),
                'size' => $item->productVariation->size->name,
                'quantity' => $quantity,
                'price' => $productPrice,
                'total' => $productPrice * $quantity,
            ]);
        }
        return $order;
    }
}
