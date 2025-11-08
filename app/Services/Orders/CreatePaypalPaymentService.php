<?php

namespace App\Services\Orders;

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Exception;

class CreatePaypalPaymentService
{
    /**
     * @throws \Throwable
     */
    public function handle(array $data, $combinedOrder)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        try {
            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => env('APP_URL') . "/api/paypal-payment-success?order_code={$combinedOrder->order_code}",
                    "cancel_url" => env('FRONTEND_URL') . "/paypal-payment-cancel?order_code={$combinedOrder->order_code}",
                    "brand_name" => 'Tentomart',
                    "user_action" => "PAY_NOW",
                ],
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => strtoupper('usd'),
                            "value" => number_format($combinedOrder->grand_total, 2, '.', '')
                        ],
                      
                        "custom_id" => $combinedOrder->id,
                        "description" => "Payment for Order #{$combinedOrder->order_code}"
                    ]
                ]
            ]);


            if (isset($response['id']) && $response['id'] != null) {
                $combinedOrder->paypal_order_id = $response['id']; 
                $combinedOrder->save();
                foreach ($response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return $link['href'];
                    }
                }
            }

            throw new Exception($response['message'] ?? 'Could not find PayPal approval link.');

        } catch (Exception $e) {
            throw new Exception('PayPal error: ' . $e->getMessage());
        }
    }
}