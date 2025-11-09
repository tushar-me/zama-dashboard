<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CombinedOrder;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaypalController extends Controller
{
    public function paymentSuccess(Request $request)
    {
        Log::info('PayPal Success Request Data', $request->all());
        $orderCode = $request->input('order_code');
        
        if (empty($orderCode)) {
            Log::error('PayPal Success: Missing order_code from request');
            return redirect(env('FRONTEND_URL') . "/order-cancel?gateway=paypal")->with('error', 'পেমেন্ট সম্পন্ন হয়নি (Invalid Order Code)।');
        }

        $combinedOrder = CombinedOrder::where('order_code', $orderCode)
                                    ->where('payment_status', '!=', 'paid') 
                                    ->first();

        if (!$combinedOrder || empty($combinedOrder->paypal_order_id)) {
            Log::warning('PayPal Success: Order not found or already paid.', ['order_code' => $orderCode]);
            return redirect(env('FRONTEND_URL') . "/order-cancel?gateway=paypal")->with('error', 'অর্ডারটি খুঁজে পাওয়া যায়নি বা এর পেমেন্ট ইতোমধ্যেই সম্পন্ন হয়েছে।');
        }

        $provider = new PayPalClient();
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $paypalOrderId = $combinedOrder->paypal_order_id; 

        try {
            $response = $provider->capturePaymentOrder($paypalOrderId);

            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                
                DB::beginTransaction();
                try {
                    $combinedOrder->payment_status = 'paid'; 
                    $combinedOrder->payment_details = json_encode($response);
                    $combinedOrder->save();
                    
                    DB::commit();
                    Log::info('PayPal Payment Successful & DB Updated', ['order_id' => $combinedOrder->id, 'paypal_order_id' => $paypalOrderId]);
                    
                    return redirect(env('FRONTEND_URL') . "/order-success?order_code={$orderCode}");

                } catch (\Exception $dbE) {
                    DB::rollBack();
                    Log::error('PayPal DB Update Error: ' . $dbE->getMessage(), ['paypal_order_id' => $paypalOrderId]);
                    return redirect(env('FRONTEND_URL') . "/order-cancel?gateway=paypal")->with('error', 'পেমেন্ট সফল হয়েছে কিন্তু অর্ডার আপডেট করতে সমস্যা হয়েছে।');
                }

            } else {
                Log::warning('PayPal Payment Not Completed', ['response' => $response, 'order_code' => $orderCode]);
                return redirect(env('FRONTEND_URL') . "/order-cancel?gateway=paypal")->with('error', 'পেমেন্টটি সম্পন্ন হয়নি।');
            }

        } catch (\Exception $e) {
            Log::error('PayPal Capture API Error: ' . $e->getMessage(), ['paypal_order_id' => $paypalOrderId]);
            return redirect(env('FRONTEND_URL') . "/order-cancel?gateway=paypal")->with('error', 'পেমেন্ট ক্যাপচার করার সময় একটি ত্রুটি ঘটেছে।');
        }
    }
    public function paymentCancel(Request $request)
    {
        //
    }
}
