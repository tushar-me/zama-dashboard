<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\OrderRequest;
use App\Http\Resources\Website\OrderResource;
use App\Services\Orders\CreateCustomerService;
use App\Services\Orders\CreateStripeCheckoutSessionService;
use App\Services\Orders\StoreOrderService;
use Illuminate\Http\Request;
use App\Models\CombinedOrder;
use App\Services\Orders\CreatePaypalPaymentService;
use Exception;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        OrderRequest $request,
        CreateStripeCheckoutSessionService $createStripeCheckoutSession,
        CreateCustomerService $createCustomer,
        CreatePaypalPaymentService $createPaypalPayment,
        StoreOrderService $storeOrder
    ) {
        $data = $request->validated();
        $customer = $createCustomer->handle($data);
        $data['customer_id'] = $customer->id;
        $order = $storeOrder->handle($data);
        
        $paymentUrl = null;
        try {
            if ($data['payment_method'] === 'stripe') {
                $session = $createStripeCheckoutSession->handle($data);
                $paymentUrl = $session->url;
            } elseif ($data['payment_method'] === 'paypal') {
                $paymentUrl = $createPaypalPayment->handle($data, $order);
            } else {
                return response()->json(['error' => 'Invalid payment method selected.'], 400);
            }
        } catch (Exception $e) {
            $order->delete();
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'data' => [
                'url' => $paymentUrl,
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $code)
    {
        $order = CombinedOrder::where('order_code', $code)->with('status')->first();

        return OrderResource::make($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
