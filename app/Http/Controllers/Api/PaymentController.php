<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;


class PaymentController extends Controller
{
    public function createOrder(Request $request){
        //dd( $request->items);
        
        $firstName = $request->shipping['firstName'];
        $lastName = $request->shipping['lastName']; 
        $customeremail = $request->shipping['email'];
        $mobile = $request->shipping['mobile'];
        $billingAddress = $request->shipping['address'].$request->shipping['address2'];
        $billingstate = $request->shipping['state'];
        $billingzip = $request->shipping['postal_code'];
        $TOTALAMT = $request->total*100;
       
        $productes = $request->items;
        $orderIds = [];
        $orderIdstirng = ''; // Initialize the orderIdstirng variable
        foreach( $productes as $products){
            $slug = $products['slug'];
            $quantity = $products['quantity'];
            $price = $products['price'];
            $productonly = Product::with('photoproduct')->where('slug', $slug)->first();
            if (!$productonly) {
                // Handle the case where the product is not found
                return response()->json(['error' => 'Product not found'], 404);
            }
            $Order = new Order;
            $Order->user_id = auth()->user()->id;
            $Order->order_number = 'ORD-' . time() . '-' . bin2hex(random_bytes(5));
            $Order->sub_total = $productonly->price;
            $Order->quantity = $products['quantity'];
            $Order->total_amount = $productonly->price * $products['quantity'];
            $Order->status = 'new';
            $Order->payment_method = 'online';
            $Order->payment_status = "unpaid";
            $Order->first_name = $firstName;
            $Order->last_name = $lastName;
            $Order->email = $customeremail;
            $Order->product_id =  $productonly->id;
            $Order->phone =$mobile;
            $Order->country ='India';
            $Order->post_code = $billingzip;
            $Order->address1 = $billingAddress;
            $Order->address2 = $billingstate;
            $Order->save();
            // Check if the order was saved successfully
            if (!$Order->wasRecentlyCreated) {
                return response()->json(['error' => 'Order creation failed'], 500);
            }
            $cart = Cart::find($products['cart_id']);
            if ($cart) {
                $cart->order_id = $Order->id;
                $cart->status='progress';
                $cart->save();
            }
            $orderIds[] = $Order->order_number;
            $orderIdstirng = $Order->order_number; // Concatenate order numbers
        }


           $api = new Api(env('RAZOR_KEY_ID'), env('RAZOR_KEY_SECRET'));
           $orderData = [
               'receipt'         => $orderIdstirng,
               'amount'          => $TOTALAMT, // Amount in paise
               'currency'        => 'INR',
               'payment_capture' => 1 // Auto capture
           ];

           $order = $api->order->create($orderData);
           return response()->json([
            'orderIds' => $order // Convert the order object to an array
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
