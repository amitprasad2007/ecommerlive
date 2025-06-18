<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class PaymentController extends Controller
{
    public function createOrder(Request $request){
        
        $order = Order::create([
            'order_number' => 'ORD-' . time() . '-' . bin2hex(random_bytes(5)), // Generate a unique order ID
            'user_id' => auth()->user()->id,
            'address_id' => $request->shipping['id'],
            'sub_total' => $request->subtotal,
            'quantity' => $request->totalquantity,
            'shippingcost' => $request->shippingcost,
            'tax' => $request->tax,
            'total_amount' => $request->total,
            'payment_method' => $request->paymentMethod,
            'payment_status' => 'unpaid',
            'status' => 'new',
            'transaction_id' => $request->payment_method === 'online' ? $request->razorpay_payment_id : null,
            'payment_details' => json_encode($request->all()),
            'shipping_id' => null // Set shipping_id to null since we're using address_id instead
        ]);
        foreach( $request->items as $product){
            OrderItem::create([
                'order_id' => $order->id,
                'product_id'=> $product['product_id'],
                'quantity'=> $product['quantity'],
                'price'=> $product['price'],
            ]);
            $cart = Cart::find($product['cart_id']);
            if ($cart) {
                $cart->order_id = $order->id;
                $cart->status='progress';
                $cart->save();
            }
        }
        $amountto = $order->total_amount;
        $amounttotal = round($amountto * 100);
        
        $api = new Api(env('RAZOR_KEY_ID'), env('RAZOR_KEY_SECRET'));
        $orderData = [
            'receipt'         => $order->order_number,
            'amount'          => $amounttotal, // Amount in paise
            'currency'        => 'INR',
            'payment_capture' => 1 // Auto capture
        ];
        $rzorder = $api->order->create($orderData);

        return response()->json([
            'razorpayOrderId' => $rzorder,
            'orderId' => $order->order_number,
            'amount'=> $amounttotal,
            'currency'=> 'INR',
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
