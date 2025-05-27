<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;


class PaymentController extends Controller
{
    public function createOrder(Request $request){

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

    public function paychecksave(Request $request){

        $payment_id = $request->response['razorpay_payment_id'];    
        $order_id = $request->response['razorpay_order_id']; 
        $signature = $request->response['razorpay_signature'];

        // Add Razorpay payment capture logic
        $api = new Api(env('RAZOR_KEY_ID'), env('RAZOR_KEY_SECRET'));
        $payment = $api->payment->fetch($payment_id); // Fetch the payment details

        // Check if the payment is already captured
        if ($payment->status === 'captured') {
            // Payment is already captured, return the payment details
            return response()->json(['paymentDetails' => $payment->toArray()]);
        } else {
            // Capture the payment
            $response = $payment->capture(array('amount' => 50000)); // Capture the payment
            return response()->json(['paymentDetails' => $response->toArray()]);
        }
    }
}
