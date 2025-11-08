<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\CombinedOrder;
use App\Models\Country;
use App\Models\City;
use App\Models\State;
use App\Models\Cart;

class StoreOrderService
{
    public function handle(array $data): CombinedOrder
    {
        $cartQuery = Cart::query()
            ->where('guest_id', $data['guest_id'])
            ->with([
                'product:id,name,mockup_id,category_id,campaign_id,unit_price,price,artwork,store_id',
                'product.mockup:id',
                'product.category:id',
                'product.category.shippingCharge',
                'product.mockup.shippingCharge',
                'productVariation',
                'productColor.images',
                'product.productSides'
            ]);

        $country = Country::where('id', $data['country_id'])->first();
        $state = State::where('id', $data['state_id'])->first();
        $city = City::where('code', $data['city_id'])->first();
        $items = $cartQuery->get();
        $totalShippingCharge = 0;
        $shippingType = getSetting('shipping_charge_type') ?? 'mockup';
        $isUS = $country && $country->code == 6701;

        foreach ($items as $item) {
            $chargeModel = $shippingType === 'mockup'
                ? $item->product->mockup->shippingCharge
                : $item->product->category->shippingCharge;

            if (!$chargeModel) {
                continue; 
            }

            $quantity = max(0, $item->quantity - 1);

            $totalShippingCharge += $isUS
                ? $chargeModel->us_charge + ($chargeModel->us_add_charge_per_item * $quantity)
                : $chargeModel->worldwide_charge + ($chargeModel->worldwide_add_charge_per_item * $quantity);
        }
        $subTotal = $items->sum('total');
        $vatPercentage = $country->vat_percentage ?? 0;
        $vatAmount = $subTotal * $vatPercentage / 100;
        $taxPercentage = $state->tax_percentage ?? 0;
        $taxAmount = $subTotal * $taxPercentage / 100;
        $grandTotal = $subTotal + $vatAmount + $totalShippingCharge + $taxAmount;
        $data['order_code'] = rand(10000000, 99999999); 
        $data['payment_status'] = 'pending';
        $data['order_status'] = 'pending'; 
        $data['sub_total'] = $subTotal;
        $data['grand_total'] = $grandTotal;
        $data['vat'] = $vatAmount;
        $data['tax'] = $taxAmount;
        $data['tax_percentage'] = $taxPercentage;
        $data['shipping_charge'] = $totalShippingCharge;
        $combinedOrder = CombinedOrder::create($data);
        $storeOrders = [];
        foreach ($items as $item) {
            $storeId = $item->product->store_id; 

            if (!isset($storeOrders[$storeId])) {
                $storeOrders[$storeId] = Order::create([
                    'store_id' => $storeId,
                    'combined_order_id' => $combinedOrder->id,
                    'total_amount' => 0,
                    'store_balance' => 0,
                    'admin_balance' => 0,
                ]);
            }

            $basePrice = floatval($item->product->unit_price);
            $productPrice = floatval($item->product->price);
            $quantity = intval($item->quantity);

            $storeEarningPerItem = $productPrice - $basePrice;
            $storeTotalEarnings = $storeEarningPerItem * $quantity;
            $adminTotalEarnings = $basePrice * $quantity;

            $images = [];
            foreach ($item->productColor->images as $image) {
                $images[] = $image->getRawOriginal('image');
            }
            $artworks = [];
            foreach ($item->product->productSides as $side) {
                $artworks[] = $side->getRawOriginal('artwork');
            }
            OrderDetail::create([
                'order_id' => $storeOrders[$storeId]->id,
                'combined_order_id' => $combinedOrder->id,
                'product_id' => $item->product_id,
                'category_id' => $item->category_id ?? null,
                'mockup_id' => $item->mockup_id ?? null,
                'campaign_id' => $item->campaign_id ?? null,
                'artworks' => $artworks,
                'color' => $item->productColor->name,
                'product_images' => $images,
                'size' => $item->productVariation->size->name,
                'quantity' => $quantity,
                'price' => $productPrice,
                'total' => $productPrice * $quantity,
            ]);

            $storeOrders[$storeId]->total_amount += ($productPrice * $quantity);
            $storeOrders[$storeId]->store_balance += $storeTotalEarnings;
            $storeOrders[$storeId]->admin_balance += $adminTotalEarnings;
        }
        foreach ($storeOrders as $order) {
            $order->save();
        }
        return $combinedOrder;
    }
}
