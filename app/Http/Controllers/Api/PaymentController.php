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
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class PaymentController extends Controller
{
    public function createOrder(Request $request){
        $user = auth()->user();
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
        $customer_name = $request->shipping['firstName']." ".$request->shipping['lastName'];
        $amountto = $order->total_amount;
        $amounttotal = round($amountto * 100);

        $api = new Api(env('RAZOR_KEY_ID'), env('RAZOR_KEY_SECRET'));
        $orderData = [
            'receipt'         => $order->order_number,
            'amount'          => $amounttotal,
            'currency'        => 'INR',
            // 'payment_capture' => 1,
            'notes'=> array('customer_name'=> $customer_name,'Customer_mobile'=> $user->mobile)
        ];
      //  \Log::info('Creating Razorpay order with data:', $orderData);

        $rzorder = $api->order->create($orderData);
        //dd($rzorder);

        if (!$rzorder || !isset($rzorder->id)) {
            \Log::error('Razorpay order creation failed:', ['response' => $rzorder]);
            return response()->json(['message' => 'Failed to create Razorpay order'], 500);
        }
        $order->transaction_id = $rzorder->id;
        $order->payment_details = json_encode($rzorder->toArray());
        $order->save();

        return response()->json([
            'razorpayOrderId' => $rzorder->id,
            'orderId' => $order->order_number,
            'amount' => $amounttotal,
            'currency' => 'INR',
            'rzdetails' => $rzorder->toArray()
        ]);
    }

    public function paychecksave(Request $request){

        $payment_id = $request->response['razorpay_payment_id'];
        $order_id = $request->response['razorpay_order_id'];
        $signature = $request->response['razorpay_signature'];
        $api = new Api(env('RAZOR_KEY_ID'), env('RAZOR_KEY_SECRET'));
        $payment = $api->payment->fetch($payment_id);
        // Check if the payment is already captured
        if ($payment->status === 'captured') {
            $payment_save = Payment::create([
                'payment_id'=> $payment->id,
                'amount'=> $payment->amount,
                'status'=> $payment->status,
                'method'=> $payment->method,
                'order_id'=> $payment->description,
                'rzorder_id'=> $payment->order_id,
                'card_id' => $payment->card_id,
                'email' => $payment->email,
                'contact'=> $payment->contact,
                'user_id' =>auth()->user()->id,
                'payment_details'=> json_encode($payment->toArray())
            ]);
            Order::where('transaction_id', $payment->order_id)->update(['payment_status' => 'paid','status'=>'process']);
            return response()->json([
                'orderId' => $payment->description,
                'success' => true
            ]);
        } else {
            // Capture the payment
          //  $response = $payment->capture(array('amount' => 50000)); // Capture the payment
           // dd($response);
           // return response()->json(['paymentDetails' => $response->toArray()]);
        }
    }
}
