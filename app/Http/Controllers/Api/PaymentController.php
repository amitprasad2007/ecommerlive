<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;


class PaymentController extends Controller
{
    public function createOrder(Request $request)
       {
       // dd('dfwerwerwerwe');

           $api = new Api(env('RAZOR_KEY_ID'), env('RAZOR_KEY_SECRET'));
            dd($api);
           $orderData = [
               'receipt'         => 'rcptid_11',
               'amount'          => 50000, // Amount in paise
               'currency'        => 'INR',
               'payment_capture' => 1 // Auto capture
           ];

           try {
               $order = $api->order->create($orderData);
               return response()->json($order);
           } catch (\Exception $e) {
               return response()->json(['error' => $e->getMessage()], 500);
           }
       }
}
