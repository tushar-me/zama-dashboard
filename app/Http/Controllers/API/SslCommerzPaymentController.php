<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Cart;
use App\Mail\OrderConfirmationMail;


class SslCommerzPaymentController extends Controller
{
    public function success(Request $request)
    {
        $val_id = urlencode($request->input('val_id'));
        $store_id = env('SSLCZ_STORE_ID');
        $store_passwd = env('SSLCZ_STORE_PASSWORD');
        
        $url = "https://securepay.sslcommerz.com/validator/api/validationserverAPI.php?val_id={$val_id}&store_id={$store_id}&store_passwd={$store_passwd}&v=1&format=json";

        $response = Http::get($url);
        $data = $response->json();

        if ($data['status'] == 'VALID' || $data['status'] == 'VALIDATED') {
            $order = Order::query()->where('order_code', $request->input('tran_id'))->with('customer')->first();
            $status = OrderStatus::query()->where('key', 'PROCESSING')->first();

            if ($order->payment_status != 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'order_status_id' =>  $status->id,
                ]);
                if (Validator::make(['email' => $order->customer->email], ['email' => 'required|email:rfc,dns'])->passes()) {
                    Mail::to($order->customer->email)->send(new OrderConfirmationMail($order->customer->name, $order->order_code));
                }

                Cart::query()->where('customer_id', $order->customer->id)->delete();
            }

            return redirect()->to(env('FRONTEND_URL') . '/success?' . http_build_query([
                'order' => $order->order_code,
            ]));
        } else {
            return redirect()->to(env('FRONTEND_URL') . "/failed");
        }
    }

    public function fail(Request $request)
    {

        $order = Order::where('order_code', $request->input('tran_id'))->first();

        if ($order->payment_status == 'pending') {
            $order->update([
                'payment_status' => 'failed',
            ]);
            return redirect()->to(env('FRONTEND_URL') . "/failed");
        } else {
            return redirect()->to(env('FRONTEND_URL') . "/failed");
        }

    }

    public function cancel(Request $request)
    {
        $order = Order::where('order_code', $request->input('order_code'))->first();
        if ($order->payment_status == 'pending') {
            $order->update([
                'payment_status' => 'cancelled',
            ]);
            return redirect()->to(env('FRONTEND_URL') . "/cancel");
        } else {
            return redirect()->to(env('FRONTEND_URL') . "/cancel");
        }
    }
}