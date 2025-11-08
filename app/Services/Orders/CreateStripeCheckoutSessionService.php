<?php
namespace App\Services\Orders;

use Exception;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CreateStripeCheckoutSessionService
{
    public function handle($order)
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        try {
            $lineItems = [];
            foreach ($order->orderDetails as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => $item->product->price * 100,
                        'product_data' => [
                            'name' => $item->product->name,
                        ],
                    ],
                    'quantity' => $item->quantity,
                ];
            }

            $session = Session::create([
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => env('FRONTEND_URL') . '/order-success?session_id={CHECKOUT_SESSION_ID}&order='.$order->order_code,
                'cancel_url' => env('FRONTEND_URL') . '/order-cancel?session_id={CHECKOUT_SESSION_ID}',
                'metadata' => [
                    'combined_order_id' => $order->id,
                ],
            ]);

            return $session;
        } catch (Exception $e) {
            throw new Exception('Stripe error: ' . $e->getMessage());
        }
    }
}
