<?php

namespace App\Http\Services\Order;
use App\Library\SslCommerz\SslCommerzNotification;
use App\Models\Order;

class CreateSslCommerzPaymentService
{
    public function handle(Order $order)
    {
        // Extract product names from order_items
        $productNames = collect($order->orderDetails())->pluck('title')->implode(',');

        $post_data = [
            'total_amount' => $order->grand_total,
            'currency' => "BDT",
            'tran_id' => $order->code,
            'product_name' => $productNames,
            // Customer Information
            'cus_name' => $order->customer->name,
            'cus_email' =>$order->customer->email ?? 'N/A',
            'cus_add1' => $order->customer->street_address ?? 'N/A',
            'cus_country' => "Bangladesh",
            'cus_phone' => $order->customer->phone,
            'sub_total' => $order->sub_total,
            'grand_total' => $order->grand_total,
            // Shipment Information
            'shipping_method' => 'NO',
            'ship_name' => "Store Test",
            'ship_add1' => "Dhaka",
            'ship_city' => "Dhaka",
            'ship_postcode' => "1000",
            'ship_country' => "Bangladesh",
            'product_category' => "Goods",
            'product_profile' => "physical-goods",
            'value_a' => "ref001",
            'value_b' => "ref002",
        ];


        $sslc = new SslCommerzNotification();
        $payment_options = $sslc->makePayment($post_data, 'checkout', 'json');
        return $payment_options;
    }

}