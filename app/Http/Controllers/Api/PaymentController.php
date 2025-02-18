<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;


class PaymentController extends Controller
{
    public function createOrder(Request $request)
       {

           $api = new Api(env('RAZOR_KEY_ID'), env('RAZOR_KEY_SECRET'));
           $orderData = [
               'receipt'         => 'rcptid_11',
               'amount'          => 50000, // Amount in paise
               'currency'        => 'INR',
               'payment_capture' => 1 // Auto capture
           ];
           $order = $api->order->create($orderData);
           return response()->json([
            'orderIds' => $order->toArray() // Convert the order object to an array
            ]);
       }
}
